#!/usr/bin/env bash
# Build a reproducible, installable staging-alpha plugin ZIP.

set -euo pipefail

cd "$(dirname "$0")/.."

SLUG="promptbridge-for-divi"
BUILD_DIR="build"
VERSION="$(grep -m1 -E '^\s*\*\s*Version:' promptbridge-for-divi.php | sed -E 's/.*Version:[[:space:]]*//' | tr -d '[:space:]')"

if [[ -z "$VERSION" ]]; then
	echo "error: could not read Version from promptbridge-for-divi.php" >&2
	exit 1
fi

ZIP_NAME="${SLUG}-${VERSION}.zip"

echo "==> Building ${ZIP_NAME} (version ${VERSION})"
rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR/$SLUG"

rsync -a --exclude-from=.distignore ./ "$BUILD_DIR/$SLUG/"
cp ../../LICENSE "$BUILD_DIR/$SLUG/LICENSE"
cp ../../NOTICE.md "$BUILD_DIR/$SLUG/NOTICE.txt"

find "$BUILD_DIR/$SLUG" -exec touch -t 202001010000 {} +
( cd "$BUILD_DIR" && zip -Xqr "$ZIP_NAME" "$SLUG" )
rm -rf "${BUILD_DIR:?}/${SLUG:?}"

echo "==> Built ${BUILD_DIR}/${ZIP_NAME}"
echo "version=${VERSION}" >> "${GITHUB_OUTPUT:-/dev/null}"
echo "zip=${BUILD_DIR}/${ZIP_NAME}" >> "${GITHUB_OUTPUT:-/dev/null}"
