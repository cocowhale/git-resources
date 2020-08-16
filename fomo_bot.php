<?php

include_once('Bitfinex.php');

$botToken = "Your_bot_token_here";
$website = "https://api.telegram.org/bot".$botToken;
 
$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);
 
 
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
 
function url_get_contents ($Url) {
    if (!function_exists('curl_init')){ 
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);