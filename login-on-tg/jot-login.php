<?php
require_once '../database.php';
require_once '../telegram.php';

$tg = new Telegram();
$_POST = (array) json_decode(file_get_contents('php://input')); //get JSON and cast to associative array

if (
    isset($_POST['chat_id']) && !empty($_POST['chat_id']) && isset($_POST['api_key']) &&
    !empty($_POST['api_key']) && isset($_POST['access_code']) && !empty($_POST['access_code']) &&
    isset($_POST['username']) && !empty($_POST['username'])
) {
    $chat_id = $_POST['chat_id'];
    $api_key = $_POST['api_key'];
    $access_code = $_POST['access_code'];
    $username = $_POST['username'];
    $db = new Database();
    if ($db->set_api_key($chat_id, $access_code, $api_key) && $db->set_username($chat_id, $access_code, $username)) {

        $tg->send_api_successful_message($chat_id);
    }
}
