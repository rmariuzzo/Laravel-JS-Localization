#!/bin/bash

# Update git modules for Lang.js
git submodule update --recursive

# Copy the dist release to the `lib` directory.
cp `dirname $0`/../Lang.js/dist/lang.min.js `dirname $0`/../lib/