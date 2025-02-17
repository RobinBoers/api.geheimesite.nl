<?php

#
# Storing and updating high scores in the {du}punkto Kodo game.
#

header('Access-Control-Allow-Origin: *');

define('STORE', __DIR__ . "/score.txt");

if(isset($_POST['hs'])) {
  $old_highscore = file_get_contents(STORE);
  $new_highscore = $_POST['hs'];

  if($old_highscore < $new_highscore) {
    file_put_contents(STORE, $new_highscore);
    http_response_code(201);
    exit;
  } elseif($old_highscore == $new_highscore) {
    http_response_code(200);
    exit;
  }
}

http_response_code(400);
