<?php

#
# Sub—a simple email subscription service for my blog.
#

include "config.php";

$API_ROOT = "/sub";
$TOKEN = "../token.txt";
$LIST = "../subscribers.csv";

if(!file_exists($LIST)) {
  touch($LIST);
}

// Send an email for double opt-in before subscribing.
if($_POST['action'] === "new") {
  if(!isset($_POST['planet'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'planet' parameter.";
    exit;
  }

  if($_POST['planet'] === "Moon" || $_POST['planet'] === "SagittariusA") {
    header($_SERVER["SERVER_PROTOCOL"] . " 418 I'm a teapot");
    echo "That's not a planet, you nitwit!";    
    exit;
  }

  if($_POST['planet'] !== "Earth") {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Sorry, I don't allow aliens to spy on me via my email subscription service.";    
    exit;
  }

  if(!isset($_POST['email'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'email' parameter.";
    exit;
  }

  if(!already_subscribed($_POST['email'])) {
    send_confirmation_email($_POST['email']);
    render_next_steps_message();
  } else {
    render_already_message();
  }

  exit;
}

function send_confirmation_email($to) {
  global $API_HOST, $API_ROOT, $PUBLIC_EMAIL;
  $link = $API_HOST . $API_ROOT . "?action=confirm&email=" . $to;

  $headers = "From: " . $PUBLIC_EMAIL;
  $subject = "Subscribing to my blog";
  $message = "Hi. You entered your email in the subscribe form for my blog. Thank you for subscribing, it means a lot to me. It's nice to know someone is interested in what I write. To confirm your subscription, visit the link below: " . "\n\r" .  $link . "\n\r\n\r" .  "I always double check the email addresses entered in my form, because I know there's a lot of nasty bots on the Web--hence this confirmation email. I sincerely apologize if you get this email without actually trying to subscribe; a bot must have come through my (pretty bad) filtering." . "\n\r\n\r" . "Again, thank you for subscribing. If you change your mind later, just send me an email and I'll remove you from the list." . "\n\r\n\r" . "Bye," . "\n\r" . "Robin";

  mail($to, $subject, $message, $headers);
}

function render_next_steps_message() {
  ?>
    <!DOCTYPE html>
    <link rel="stylesheet" href="https://roblog.nl/main.css" />

    <p>Hi, I just want to make sure the email address you provided is actually yours. No need to worry, I do this for all email addresses.</p>
    <p>Please check your email :)</p>
  <?php
}

// Confirm a subscription and add subscriber to list.
if($_GET['action'] === "confirm") {
  if(!isset($_GET['email'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'email' parameter.";
    exit;
  }

  if(!already_subscribed($_GET['email'])) {
    add_to_list($_GET['email']);
    render_success_message();
    hooray($_GET['email']);
  } else {
    render_already_message();
  }
  
  exit;
}

function already_subscribed($subscriber) {
  global $LIST;
  $subscribers = file_get_contents($LIST);
  return str_contains($subscribers, $subscriber);
}

function add_to_list($email) {
  global $LIST;
  
  $handle = fopen($LIST, "a+");
  fputcsv($handle, [$email]);
  fclose($handle);
}

function render_success_message() {
  ?>
    <!DOCTYPE html>
    <link rel="stylesheet" href="https://roblog.nl/main.css" />

    <p>You're now subscribed to my blog. Whenever I post something new, you'll automatically get an email.</p>
    <p>Again, thanks for subscribing; it means a lot to me. It's nice to know someone reads my posts.</p>
    <p>If you ever change your mind, just send me an email and I'll remove you from the list. My contact details are on the homepage. No hard feelings :)</p>

    <p><a href="https://roblog.nl">Back to my site 🡢</a></p>
  <?php
}

function render_already_message() {
  ?>
    <!DOCTYPE html>
    <link rel="stylesheet" href="https://roblog.nl/main.css" />

    <p>You were already subscribed. I appreciate your enthusiasm tho :D</p>
    <p><a href="https://roblog.nl">Back to my site 🡢</a></p>
  <?php
}

function hooray($subscriber) {
  global $PUBLIC_EMAIL, $PRIVATE_EMAIL;

  $headers = "From: " . $PUBLIC_EMAIL;
  $subject = "Hooray! New subscriber: <" . $subscriber . ">";
  $message = "<" . $subscriber . "> subscribed to your blog; I guess you're not that unpopular after all (yay!)";

  mail($PRIVATE_EMAIL, $subject, $message, $headers);
}

// Download list.
if($_GET['action'] === "download") {
  global $LIST, $TOKEN;

  if(!isset($_GET['token'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'token' parameter.";
    exit;
  }

  if(trim($_GET['token']) !== trim(file_get_contents($TOKEN))) {
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo "Sowwy, can't let you do that... ";
    echo "I'm afraid it would severly breach the GDPR :(";
    exit;
  }

  header('Content-Type: text/csv');
  echo file_get_contents($LIST);
  exit;
}

// Push message to list.
if($_POST['action'] === "push") {
  global $TOKEN;

  if(!isset($_POST['token'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'token' parameter.";
    exit;
  }

  if(trim($_POST['token']) !== trim(file_get_contents($TOKEN))) {
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo "Sowwy, can't let you do that... ";
    echo "I'm afraid it would severly breach the GDPR :(";
    exit;
  }

  if(!isset($_POST['title'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'title' parameter.";
    exit;
  }

  if(!isset($_POST['message'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing 'message' parameter.";
    exit;
  }

  push_message_to_subscribers($_POST['title'], $_POST['message']);

  header($_SERVER["SERVER_PROTOCOL"] . " 201 Created");
  echo "Sent email to all recipients.";

  exit;
}

function push_message_to_subscribers($title, $message) {
  global $PUBLIC_EMAIL, $LIST;
  $subscribers = file_get_contents($LIST);

  $headers = "From: " . $PUBLIC_EMAIL . "\r\nBCC: " . str_replace("\n", ",", $subscribers);
  $subject = $title;
  $message = $message;

  mail($PUBLIC_EMAIL, $subject, $message, $headers);
}

function render_generic_homepage() {
  ?>
    <!DOCTYPE html>
    <link rel="stylesheet" href="https://roblog.nl/main.css" />

    <h1>Sub<small>—a simple email subscription service for my blog.</small></h1>
    <p><a href="https://roblog.nl/rss#anyway">Wanna subscribe?</a></p>
  <?php
}

render_generic_homepage();
