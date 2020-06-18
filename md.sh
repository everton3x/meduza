#!/usr/bin/env bash

clear

./vendor/bin/phpmd src,bootstrap.php,build.php ansi cleancode,codesize,controversial,design,naming,unusedcode
