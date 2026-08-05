#!/bin/bash

# AtoZGadgets Hostinger Rsync Deployment Script
# Targets: atozgadgetz.com (u390470426@217.21.74.188)

ACTION=${1:-push}

if [ "$ACTION" == "push" ]; then
    echo "Deploying to Live Production (atozgadgetz.com)..."
    wsl.exe rsync -avz -e "ssh -p 65002 -o StrictHostKeyChecking=no -o BatchMode=yes" --exclude-from='.rsyncignore' . u390470426@217.21.74.188:domains/atozgadgetz.com/public_html
elif [ "$ACTION" == "pull" ]; then
    echo "Pulling from Live Production..."
    wsl.exe rsync -avz -e "ssh -p 65002 -o StrictHostKeyChecking=no -o BatchMode=yes" --exclude-from='.rsyncignore' u390470426@217.21.74.188:domains/atozgadgetz.com/public_html/ .
elif [ "$ACTION" == "list" ]; then
    echo "Listing Live Files on Hostinger..."
    wsl.exe rsync -avz -e "ssh -p 65002 -o StrictHostKeyChecking=no -o BatchMode=yes" --list-only u390470426@217.21.74.188:domains/atozgadgetz.com/public_html/
else
    echo "Usage: ./deploy.sh [push|pull|list]"
fi
