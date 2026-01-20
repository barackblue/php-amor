#!/bin/bash

# ---- Configuration ----
PROJECT_PATH="/home/alpha/Pictures/Projects/progress/Dummy projects/my post php site"
PROD_PATH="/var/www/mypostsite"
NGINX_CONF="/etc/nginx/sites-available/mypostsite"
NGINX_LINK="/etc/nginx/sites-enabled/mypostsite"

# ---- Step 1: Copy project files ----
echo "[*] Copying files to production folder..."
sudo mkdir -p "$PROD_PATH"
sudo rsync -av --exclude '.git' "$PROJECT_PATH/" "$PROD_PATH/"

# ---- Step 2: Set permissions ----
echo "[*] Setting permissions..."
sudo chown -R www-data:www-data "$PROD_PATH"
sudo find "$PROD_PATH" -type d -exec chmod 755 {} \;
sudo find "$PROD_PATH" -type f -exec chmod 644 {} \;

# ---- Step 3: Update settings for production ----
echo "[*] Setting debug=false for production..."
sudo sed -i "s/'debug' => true/'debug' => false/" "$PROD_PATH/config/settings.php"

# ---- Step 4: Deploy Nginx config ----
echo "[*] Deploying Nginx config..."
sudo cp "$PROJECT_PATH/nginx_template.conf" "$NGINX_CONF"
sudo sed -i "s|{{ROOT_PATH}}|$PROD_PATH|g" "$NGINX_CONF"
sudo ln -sf "$NGINX_CONF" "$NGINX_LINK"

# ---- Step 5: Restart services ----
echo "[*] Restarting PHP-FPM and Nginx..."
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx

echo "[✔] Deployment complete! Visit http://mysite.local"
