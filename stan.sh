#!/usr/bin/env bash

clear

./vendor/bin/phpstan analyse --level=8 src utils plugins bootstrap.php build.php

