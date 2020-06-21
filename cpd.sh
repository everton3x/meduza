#!/usr/bin/env bash

clear

./vendor/bin/phpcpd --fuzzy --progress src utils plugins bootstrap.php build.php
