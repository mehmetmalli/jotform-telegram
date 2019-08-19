<?php
header("Access-Control-Allow-Origin: https://www.jotform.com");

require_once '../database.php';
require_once '../telegram.php';
require_once '../jf_handler.php';

$_POST = (array) json_decode(file_get_contents('php://input')); //get JSON and cast to associative array

if (isset($_POST['chat_id']) && !empty($_POST['chat_id']) && isset($_POST['api_key']) && !empty($_POST['api_key'])) {
    $chat_id = $_POST['chat_id'];
    $api_key = $_POST['api_key'];
    $form_id = $_POST['form_id'];
    $username = $_POST['username'];
    $access_code;
    $db = new Database();
    $tg = new Telegram();
    $jf = new JotFormHandler($api_key);

    if ($db->user_exists($chat_id)) {
        $access_code = $db->get_access_code($chat_id);
    } else {
        $access_code = $db->create_new_user($chat_id);
    }

    if($db->set_api_key($chat_id, $access_code, $api_key) && $db->set_username($chat_id, $access_code, $username)) {
        $tg->send_api_successful_message($chat_id);
    }

    if (count($jf->get_self_webhooks(strval($chat_id), $form_id)) > 0) {
        $jf->reset_webhooks_on_single_form($chat_id, $form_id);
    }

    $webhook_url = TextBuilder::prepare_webhook($chat_id, $access_code);
    $jf->add_webhook($form_id, $webhook_url);

    $enabled = true;
    $form_title = $jf->get_form_title($form_id);
    $tg->send_webhook_changed_message($chat_id, $form_id, $form_title, $enabled);
}
