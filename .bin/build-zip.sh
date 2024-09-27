#!/bin/sh

PLUGIN_SLUG=$(node -p "require('./package.json').name") || exit "$?"
PROJECT_PATH=$(pwd)
BUILD_PATH="${PROJECT_PATH}/zip"
DEST_PATH="$BUILD_PATH/$PLUGIN_SLUG"

# Build the plugin
npm install
npm run build
composer update --no-dev --optimize-autoloader

echo "Generating build directory..."
rm -rf "$BUILD_PATH"
mkdir -p "$DEST_PATH"

echo "Syncing files..."
rsync -rc --exclude-from="$PROJECT_PATH/.distignore" "$PROJECT_PATH/" "$DEST_PATH/" --delete --delete-excluded
find "$DEST_PATH" -type d -empty -delete

echo "Generating zip file..."
cd "$BUILD_PATH" || exit
zip -q -r "${PLUGIN_SLUG}.zip" "$PLUGIN_SLUG/"

cd "$PROJECT_PATH" || exit
mv "$BUILD_PATH/${PLUGIN_SLUG}.zip" "$PROJECT_PATH"
rm -rf "$BUILD_PATH"
echo "${PLUGIN_SLUG}.zip file generated!"

echo "Build done!"
