#!/bin/bash

# Script de build pour Nuxt Tracker avec fix node-datachannel
# Usage: ./build-production.sh

echo "🏗️ Building Nuxt Tracker for production..."
yarn build

if [ $? -ne 0 ]; then
  echo "❌ Build failed!"
  exit 1
fi

echo "🔧 Fixing node-datachannel dependency in build output..."
cd .output/server
npm install node-datachannel

if [ $? -ne 0 ]; then
  echo "❌ Failed to install node-datachannel!"
  exit 1
fi

echo "📄 Copying environment configuration..."
cd ../..
if [ -f ".env" ]; then
  cp .env .output/server/.env
  echo "✅ .env file copied to build output"
else
  echo "⚠️  No .env file found, make sure to set environment variables manually"
fi

echo "✅ Build completed successfully!"
echo ""
echo "You can now run the production server with:"
echo "  cd .output/server && node index.mjs"
echo ""
echo "Or with environment variables:"
echo "  NUXT_MONGODB_URI=mongodb://localhost:27017 NUXT_MONGODB_DB=nuxt-tracker NUXT_SESSION_PASSWORD=your_session_password node .output/server/index.mjs"