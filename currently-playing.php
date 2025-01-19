<?php

// Public API to retrieve song data for currently playing song
// from the Spotify API.

// To generate the refresh token:
//
// 1. Visit https://accounts.spotify.com/authorize?response_type=code&client_id=CLIENT_ID&redirect_uri=http://localhost:4000
//
// 2. Copy the `code` parameter from the URL, and run the following command:
//
//    curl -X POST https://accounts.spotify.com/api/token \
//      -H "Authorization: Basic $(echo -n "CLIENT_ID:CLIENT_SECRET" | base64)" \
//      -d "grant_type=authorization_code" \
//      -d "code=CODE" \
//      -d "redirect_uri=http://localhost:4000"

header('Content-Type: application/json; charset=utf-8');

$CLIENT_ID = '';
$CLIENT_SECRET = '';
$REFRESH_TOKEN = '';

$TOKEN_ENDPOINT = 'https://accounts.spotify.com/api/token';
$DATA_ENDPOINT = 'https://api.spotify.com/v1/me/player/currently-playing';

// Stolen from Pubb core.

function dbg($input) {
  var_dump($input);
  return $input;
}

function flatten($separator, $array) {
  $keys = array_keys($array);
  $values = array_values($array);
  
  return array_map(function($key, $value) use ($separator) {
    return $key . $separator . $value;
  }, $keys, $values);
}

function request($uri, $headers = [], $options = []) {
  $ch = curl_init();

  curl_setopt_array($ch, [
    CURLOPT_URL => $uri,
    CURLOPT_USERAGENT => 'api.geheimesite.nl',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 5,

    // Request headers
    CURLOPT_HTTPHEADER => flatten(": ", $headers)
  ]);
  
  curl_setopt_array($ch, $options);

  // Response headers
  $headers = [];
  curl_setopt($ch, CURLOPT_HEADERFUNCTION,
    function ($ch, $header) use (&$headers) {
      if(!str_contains($header, ":"))
        return strlen($header);

      [$name, $value] = explode(":", $header, 2);
      $name = strtolower(trim($name));
      $headers[$name][] = trim($value);

      return strlen($header);
    }
  );

  $response = curl_exec($ch);

  $state = $response ? "success" : "failed";
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $body = $response ? $response : curl_error($ch);

  curl_close($ch);

  return [
    "state" => $state,
    "status" => $status,
    "headers" => $headers,
    "body" => $body 
  ];
}

function get($uri, $headers = []) {
  $options = [
    CURLOPT_HTTPGET => true,
    CURLOPT_RETURNTRANSFER => true
  ];

  return request($uri, $headers, $options);
}

function post($uri, $data = [], $headers = []) {
  $options = [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_RETURNTRANSFER => true
  ];

  return request($uri, $headers, $options);
}

// Helpers

function parse_json($response) {
  if($response['status'] >= 400) 
    serve_error_response($response);

  $data = json_decode($response['body'], associative: true) 
    or serve_error_response($response);

  return $data;
}

function serve_error_response($response) {
  http_response_code(500);
  die(json_encode([
    'http_code' => $response['status'],
    'error_message' => $response['body']
  ]));
}

function clean_album_name($name) {
  $bullshit = [
      '\d{4} remastered',
      'remastered \d{4}',
      'remastered',
      'limited edition',
      'special edition',
      'deluxe edition',
      'bonus tracks?',
      'expanded edition',
      'anniversary edition',
      'exclusive',
      'feat\.',
      'featuring',
      'digital remaster',
      'reissue',
      '\(\d{4} reissue\)',
      '\[.*?\]',
      '\(.*?\)',
  ];

  $pattern = '/' . implode('|', $bullshit) . '/i';
  return trim(preg_replace('/\s+/', ' ', preg_replace($pattern, '', $name)));
}

// Obtain access token

$headers = [
    'Authorization' => 'Basic ' . base64_encode($CLIENT_ID . ':' . $CLIENT_SECRET),
    'Content-Type' => 'application/x-www-form-urlencoded',
];

$data = http_build_query([
    'grant_type' => 'refresh_token',
    'refresh_token' => $REFRESH_TOKEN,
]);

$response = post($TOKEN_ENDPOINT, $data, $headers);
$tokens = parse_json($response);

// Fetch data from API

$headers = [
    'Authorization' => 'Bearer ' . $tokens['access_token'],
];

$response = get($DATA_ENDPOINT, $headers);

if($response['status'] == 204) {
  echo json_encode(['playing' => false]);
  exit;
}

$data = parse_json($response);

echo json_encode([
  'playing' => $data['is_playing'],
  'track' => $data['item']['name'],
  'album' => clean_album_name($data['item']['album']['name']),
  'artists' => array_map(fn($artist) => $artist['name'], $data['item']['artists']),
  'url' => $data['item']['external_urls']['spotify'],
  'duration' => $data['item']['duration_ms'],
  'progress' => $data['progress_ms']
]);
