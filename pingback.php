<?php
// I am lazy.

$_ENDPOINT = "https://api.geheimesite.nl/webmentions";
$_PROXY = "https://webmention.io/webmention";

header("Location: $_PROXY?forward=$_ENDPOINT", true, 302);
exit;