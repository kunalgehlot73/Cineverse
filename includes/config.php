<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'cineverse_db';

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}


function format_duration($minutes) {
    $hours = floor($minutes / 60);
    $remaining = $minutes % 60;
    
    $result = '';
    if ($hours > 0) {
        $result .= $hours . 'h ';
    }
    if ($remaining > 0 || $hours === 0) {
        $result .= $remaining . 'm';
    }
    return trim($result);
}

function format_rating($rating) {
    $num = (float)$rating;
    return $num == floor($num) ? (int)$num : $num;
}
?>