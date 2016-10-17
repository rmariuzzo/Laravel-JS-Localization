#!/bin/bash

echo "🔌  Updating Lang.js git submodule..."
git submodule update --remote

echo "📄  Copying Lang.js lib into the project..."
cp ./Lang.js/dist/lang.min.js ./lib/lang.min.js

echo "👌  All good!"
