Write-Host "Preparing AtoZGadgets for Hostinger Deployment..."

# Remove any old builds
if (Test-Path "hostinger_build.zip") {
    Remove-Item "hostinger_build.zip" -Force
}

# Clear caches locally just in case
php artisan config:clear
php artisan route:clear
php artisan view:clear

Write-Host "Creating zip archive (excluding dev files)..."
# Zip the entire folder except unnecessary directories
Compress-Archive -Path "app", "bootstrap", "config", "database", "public", "resources", "routes", "storage", "vendor", ".env.example", ".htaccess", "artisan", "composer.json", "composer.lock", "server.php" -DestinationPath "hostinger_build.zip" -Force

Write-Host "Done! You can now upload 'hostinger_build.zip' to your Hostinger public_html folder and extract it."
