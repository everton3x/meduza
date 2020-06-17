#!/usr/bin/env bash

clear

./vendor/bin/phpmd src ansi cleancode,codesize,controversial,design,naming,unusedcode
