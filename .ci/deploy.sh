#!/usr/bin/env fish
# Bix handler to deploy :)

rsync -ciavuz --exclude-from=.deployignore --delete . geheimesite.nl:domains/api.geheimesite.nl/public_html

