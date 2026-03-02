#!/bin/bash

echo "🚀 Building Laravel Docker Staging..."
echo ""

# Create docker directory if not exists
mkdir -p docker

# Copy nginx config to docker directory if not exists
if [ ! -f "docker/nginx.conf" ]; then
    echo "⚠️  nginx.conf not found in docker/ directory"
    exit 1
fi

# Check if .env.docker exists
if [ ! -f ".env.docker" ]; then
    echo "⚠️  .env.docker not found! Please create it first."
    exit 1
fi

# Stop existing container
echo "🛑 Stopping existing container..."
docker-compose down

# Clean up old build artifacts di host (optional)
echo "🧹 Cleaning old build artifacts..."
rm -rf public/build 2>/dev/null || true
rm -rf node_modules/.vite 2>/dev/null || true

# Build and start
echo "🔨 Building Docker image..."
docker-compose build --no-cache

echo "🚀 Starting container..."
docker-compose up -d

echo "⏳ Waiting for container to be ready..."
sleep 8

echo ""
echo "🔑 Generating APP_KEY if needed..."
docker exec dashboard-warehouse php artisan key:generate --force

echo ""
echo "📦 Building Vite assets..."
docker exec dashboard-warehouse npm run build

echo ""
echo "✅ Verifying manifest.json..."
if docker exec dashboard-warehouse test -f /var/www/public/build/manifest.json; then
    echo "✅ manifest.json found!"
else
    echo "⚠️  manifest.json not found, retrying build..."
    docker exec dashboard-warehouse npm install
    docker exec dashboard-warehouse npm run build
fi

echo ""
echo "🔧 Fixing permissions..."
docker exec dashboard-warehouse chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public/build 2>/dev/null || true
docker exec dashboard-warehouse chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo ""
echo "🧹 Clearing Laravel cache..."
docker exec dashboard-warehouse php artisan config:clear
docker exec dashboard-warehouse php artisan view:clear
docker exec dashboard-warehouse php artisan cache:clear

echo ""
echo "🔄 Final restart..."
docker-compose restart

echo ""
echo "⏳ Waiting for final startup..."
sleep 5

echo ""
echo "=========================================="
echo "✅ Laravel staging is ready!"
echo "=========================================="
echo ""
echo "📍 Access URL:"
echo "   Local:     http://localhost:2345"
echo "   Tailscale: http://YOUR_TAILSCALE_IP:2345"
echo ""
echo "📊 Useful commands:"
echo "   Logs:      docker-compose logs -f"
echo "   Enter:     docker exec -it dashboard-warehouse bash"
echo "   Rebuild:   ./build.sh"
echo "   Stop:      docker-compose down"
echo ""
echo "🗄️  Database Configuration:"
echo "   Host: host.docker.internal"
echo "   Update credentials in .env.docker before build"
echo ""