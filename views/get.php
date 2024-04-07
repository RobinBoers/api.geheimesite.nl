<?php

include "../connection.php";

header('Access-Control-Allow-Origin: https://blog.geheimesite.nl');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json; charset=utf-8');

function get_views($url) {
    global $conn;

    $url = $conn->real_escape_string($url);

    $select_query = "SELECT * FROM viewcount WHERE url = '$url'";
    $result = $conn->query($select_query);

    if($result->num_rows != 0) {
        $views = 0;

        while($row = $result->fetch_object()) {
            $views += $row->views;                
        }

        echo json_encode(array(
            views => $views
        ));
    } else {
        echo json_encode(array(
            views => 0
        ));
    }
}
