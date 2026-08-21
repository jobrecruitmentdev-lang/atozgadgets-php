#!/bin/bash
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
ROOT_DIR="$(dirname "$DIR")"

if [ ! -f "$DIR/.deploy-env" ]; then
    echo "Error: $DIR/.deploy-env not found."
    exit 1
fi

source "$DIR/.deploy-env"

if [ "$1" == "--list" ]; then
    rsync -avz -e "ssh -p $REMOTE_PORT -o StrictHostKeyChecking=no -o BatchMode=yes" --list-only "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/"
    exit 0
fi

echo "{\"timestamp\": \"$(date -u +'%Y-%m-%dT%H:%M:%SZ')\", \"commit\": \"$(git rev-parse --short HEAD 2>/dev/null || echo 'unknown')\"}" > "$DIR/.last-deploy"

# Non-interactive mode (used by super-dev.php): set CONFIRM=1 to skip the y/N prompt.
if [ "$CONFIRM" == "1" ]; then
    REPLY="y"
else
    read -p "Proceed with actual sync? (y/N) " -n 1 -r
    echo
fi

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "1/2 🚀 Syncing files via rsync..."
    rsync -avz -e "ssh -p $REMOTE_PORT -o StrictHostKeyChecking=no -o BatchMode=yes" --exclude-from="$ROOT_DIR/.rsyncignore" "$ROOT_DIR/" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH"
    
    echo "2/2 🔄 Running remote migrations & optimizing cache..."
    ssh -p $REMOTE_PORT -o StrictHostKeyChecking=no -o BatchMode=yes "$REMOTE_USER@$REMOTE_HOST" "cd $REMOTE_PATH && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache"
    
    echo "✅ Deploy & Migration complete!"
else
    echo "Deploy cancelled."
fi
