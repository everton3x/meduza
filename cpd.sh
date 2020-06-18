#!/usr/bin/env bash

clear

./vendor/bin/phpcpd --fuzzy --progress src utils bootstrap.php build.php
