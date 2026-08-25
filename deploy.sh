#!/bin/bash

# AtoZGadgets Hostinger Rsync & Migration Deployment Script
# Targets: atozgadgetz.com (u390470426@217.21.74.188)

ACTION=${1:-push}
SSH_TARGET="u390470426@217.21.74.188"
SSH_PORT="65002"
REMOTE_PATH="domains/atozgadgetz.com/public_html"

if [ "$ACTION" == "push" ]; then
    echo "1/2 🚀 Syncing files to Live Production (atozgadgetz.com)..."
    wsl.exe rsync -avz -e "ssh -p $SSH_PORT -o StrictHostKeyChecking=no -o BatchMode=yes" --exclude-from='.rsyncignore' . $SSH_TARGET:$REMOTE_PATH
    
    echo "2/2 🔄 Executing Remote Database Migrations & Production Optimization Caches..."
    wsl.exe ssh -p $SSH_PORT -o StrictHostKeyChecking=no -o BatchMode=yes $SSH_TARGET "cd $REMOTE_PATH && (php artisan storage:link 2>/dev/null || true) && php artisan migrate --force && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache"
    
    echo "✅ Deployment & Automated Migrations Complete!"
elif [ "$ACTION" == "pull" ]; then
    echo "Pulling from Live Production..."
    wsl.exe rsync -avz -e "ssh -p $SSH_PORT -o StrictHostKeyChecking=no -o BatchMode=yes" --exclude-from='.rsyncignore' $SSH_TARGET:$REMOTE_PATH/ .
elif [ "$ACTION" == "list" ]; then
    echo "Listing Live Files on Hostinger..."
    wsl.exe rsync -avz -e "ssh -p $SSH_PORT -o StrictHostKeyChecking=no -o BatchMode=yes" --list-only $SSH_TARGET:$REMOTE_PATH/
else
    echo "Usage: ./deploy.sh [push|pull|list]"
fi

