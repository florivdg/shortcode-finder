#!/bin/bash

# WordPress Plugin Build Script for Shortcode Finder
# This script packages the plugin for submission to WordPress.org repository

set -e

# Configuration
PLUGIN_SLUG="shortcode-finder"
VERSION=$(grep "Version:" shortcode-finder.php | awk '{print $3}')
BUILD_DIR="build"
DIST_DIR="dist"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Building ${PLUGIN_SLUG} v${VERSION}...${NC}"

# Clean up previous builds
echo "Cleaning up previous builds..."
rm -rf "$BUILD_DIR"
rm -rf "$DIST_DIR"
mkdir -p "$BUILD_DIR"
mkdir -p "$DIST_DIR"

# Create temporary build directory
echo "Creating build directory..."
BUILD_PATH="$BUILD_DIR/$PLUGIN_SLUG"
mkdir -p "$BUILD_PATH"

# Copy plugin files to build directory
echo "Copying plugin files..."
# Copy all PHP files
cp -r *.php "$BUILD_PATH/"
# Copy includes directory
cp -r includes "$BUILD_PATH/"
# Copy assets directory
cp -r assets "$BUILD_PATH/"
# Copy readme.txt and license files
[ -f "readme.txt" ] && cp readme.txt "$BUILD_PATH/"
[ -f "README.txt" ] && cp README.txt "$BUILD_PATH/"
[ -f "license.txt" ] && cp license.txt "$BUILD_PATH/"
[ -f "LICENSE" ] && cp LICENSE "$BUILD_PATH/"
[ -f "LICENSE.txt" ] && cp LICENSE.txt "$BUILD_PATH/"

# Files and directories to exclude
echo "Cleaning build directory..."
# Remove development files
find "$BUILD_PATH" -name "*.git*" -exec rm -rf {} + 2>/dev/null || true
find "$BUILD_PATH" -name "*.DS_Store" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name "Thumbs.db" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name "*.map" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name "*.scss" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name "*.less" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name "*.log" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name "*.swp" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name "*~" -exec rm -f {} + 2>/dev/null || true
find "$BUILD_PATH" -name ".vscode" -type d -exec rm -rf {} + 2>/dev/null || true
find "$BUILD_PATH" -name "node_modules" -type d -exec rm -rf {} + 2>/dev/null || true
find "$BUILD_PATH" -name ".history" -type d -exec rm -rf {} + 2>/dev/null || true

# Remove CLAUDE.md and other development docs
rm -f "$BUILD_PATH/CLAUDE.md" 2>/dev/null || true
rm -f "$BUILD_PATH/build.sh" 2>/dev/null || true
rm -f "$BUILD_PATH/package.json" 2>/dev/null || true
rm -f "$BUILD_PATH/package-lock.json" 2>/dev/null || true
rm -f "$BUILD_PATH/composer.json" 2>/dev/null || true
rm -f "$BUILD_PATH/composer.lock" 2>/dev/null || true
rm -f "$BUILD_PATH/phpunit.xml" 2>/dev/null || true
rm -f "$BUILD_PATH/phpunit.xml.dist" 2>/dev/null || true
rm -f "$BUILD_PATH/.gitignore" 2>/dev/null || true
rm -f "$BUILD_PATH/.editorconfig" 2>/dev/null || true
rm -f "$BUILD_PATH/.eslintrc" 2>/dev/null || true
rm -f "$BUILD_PATH/.eslintrc.js" 2>/dev/null || true
rm -f "$BUILD_PATH/.eslintrc.json" 2>/dev/null || true
rm -f "$BUILD_PATH/.prettierrc" 2>/dev/null || true
rm -f "$BUILD_PATH/.prettierrc.js" 2>/dev/null || true
rm -f "$BUILD_PATH/.prettierrc.json" 2>/dev/null || true
rm -f "$BUILD_PATH/Gruntfile.js" 2>/dev/null || true
rm -f "$BUILD_PATH/gulpfile.js" 2>/dev/null || true
rm -f "$BUILD_PATH/webpack.config.js" 2>/dev/null || true

# Remove test directories
rm -rf "$BUILD_PATH/tests" 2>/dev/null || true
rm -rf "$BUILD_PATH/test" 2>/dev/null || true
rm -rf "$BUILD_PATH/bin" 2>/dev/null || true

# Create the zip file
ZIP_NAME="${PLUGIN_SLUG}.${VERSION}.zip"
echo "Creating zip file: ${ZIP_NAME}..."
cd "$BUILD_DIR"
zip -r "../$DIST_DIR/$ZIP_NAME" "$PLUGIN_SLUG" -q

# Also create a version without version number (for easy upload)
cp "../$DIST_DIR/$ZIP_NAME" "../$DIST_DIR/${PLUGIN_SLUG}.zip"

cd ..

# Display file size
SIZE=$(du -h "$DIST_DIR/$ZIP_NAME" | cut -f1)
echo -e "${GREEN}✓ Build complete!${NC}"
echo -e "Package created: ${YELLOW}$DIST_DIR/$ZIP_NAME${NC} (${SIZE})"
echo -e "Also created: ${YELLOW}$DIST_DIR/${PLUGIN_SLUG}.zip${NC}"

# List contents of the zip for verification
echo ""
echo "Package contents:"
unzip -l "$DIST_DIR/$ZIP_NAME" | head -20
echo "..."
echo ""
TOTAL_FILES=$(unzip -l "$DIST_DIR/$ZIP_NAME" | tail -1 | awk '{print $2}')
echo "Total files in package: ${TOTAL_FILES}"

# Clean up build directory
rm -rf "$BUILD_DIR"

echo ""
echo -e "${GREEN}Ready for upload to WordPress.org!${NC}"
echo "Upload file: $DIST_DIR/${PLUGIN_SLUG}.zip"