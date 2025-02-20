<?php

#
# Storing and updating high scores in the {du}punkto Kodo game.
#

header('Access-Control-Allow-Origin: *');

define('STORE', __DIR__ . "/score.txt");

function maybe_write_new($new_highscore) {
  $old_highscore = file_get_contents(STORE);

  if($old_highscore < $new_highscore) {
    file_put_contents(STORE, $new_highscore);
    http_response_code(201);
    exit;
  } elseif($old_highscore == $new_highscore) {
    http_response_code(200);
    exit;
  }
}

if(isset($_POST['hs'])) maybe_write_new($_POST['hs']);
elseif(isset($_GET['hs'])) maybe_write_new($_GET['hs']);

http_response_code(400);
