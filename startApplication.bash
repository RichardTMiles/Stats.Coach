#!/usr/bin/env bash

APP_ROOT=$(pwd)

file="/etc/hosts"

if ! grep -q dev.stats.coach "$file"; then
  sudo -- sh -c "echo 127.0.0.1 dev.stats.coach >> $file"
fi

cd "$APP_ROOT" || exit

sudo php -S dev.stats.coach:80 index.php