#!/usr/bin/env bash

clear

./vendor/bin/phpmd src,plugins,bootstrap.php,build.php ansi cleancode,codesize,controversial,design,naming,unusedcode
