<?php

#
# Webmention endpoint
#

include "config.php";

$API_ROOT = "/webmentions";

if (!isset($_POST['source'])) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
  echo "Missing 'source' parameter.";
  exit;
}

if (!isset($_POST['target'])) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
  echo "Missing 'target' parameter.";
  exit;
}

ob_start();
$ch = curl_init($_POST['source']);
curl_setopt($ch, CURLOPT_USERAGENT, $USER_AGENT);
curl_setopt($ch, CURLOPT_HEADER,0);
$ok = curl_exec($ch);
curl_close($ch);
$source = ob_get_contents();
ob_end_clean();

if (!stristr($source, $_POST['target'])) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
  echo "Your page doesn't actually mention mine.";
  exit;
}

header($_SERVER['SERVER_PROTOCOL'] . ' 202 Accepted');

$headers = "From: " . $PUBLIC_EMAIL;
$subject = "Hooray! New webmention on <" . $_POST['source'] . ">";
$message = "Your page <" . $_POST['target'] . "> was mentioned on <" . $_POST['source'] . ">." . "\n\r" . 
  "I guess you're not that unpopular after all (yay!)";

mail($PRIVATE_EMAIL, $subject, $message, $headers);