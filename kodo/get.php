<?php

#
# Retrieving the high scores in the {du}punkto Kodo game.
#

header('Access-Control-Allow-Origin: *');

define('STORE', __DIR__ . "/score.txt");
readfile(STORE);
