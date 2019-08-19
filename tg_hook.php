<?php

require_once __DIR__ . "/telegram.php";
require_once __DIR__ . "/database.php";
require_once __DIR__ . "/jf_handler.php";
require_once __DIR__ . "/textbuilder.php";

$update = json_decode(file_get_contents('php://input'));

// MESSAGE
$chat_id = $update->message->chat->id;
$text_message = isset($update->message->text) ? $update->message->text : '';

// CALBACK
$callback_chat_id = $update->callback_query->message->chat->id;
$callback_message_id = $update->callback_query->message->message_id;
$callback_data = $update->callback_query->data; // form_id
$callback_text = $update->callback_query->message->text;

$tg = new Telegram();
$db = new Database();
$api_key = "";
if (!is_null($chat_id)) {
    $api_key = $db->get_api_key_with_chat_id($chat_id);
} else {
    $api_key = $db->get_api_key_with_chat_id($callback_chat_id);
}

$jf = new JotFormHandler($api_key);

if (!is_null($callback_data)) {

    $enabled = false;
    if (count($jf->get_self_webhooks(strval($callback_chat_id), $callback_data)) > 0) {

        $jf->reset_webhooks_on_single_form($callback_chat_id, $callback_data);
    } else {

        $access_code = $db->get_access_code($callback_chat_id);
        $webhook_url = TextBuilder::prepare_webhook($callback_chat_id, $access_code);
        $jf->add_webhook($callback_data, $webhook_url);
        $enabled = true;
    }

    $forms = $jf->get_user_forms($callback_chat_id);
    $tg->edit_forms($callback_chat_id, $callback_message_id, $callback_text, $forms);
    $form_title = $jf->get_form_title($callback_data);
    $tg->send_webhook_changed_message($callback_chat_id, $callback_data, $form_title, $enabled);
}

if ($text_message == '/start') {

    if ($db->user_exists($chat_id)) {
        $access_code = $db->get_access_code($chat_id);
    } else {
        $access_code = $db->create_new_user($chat_id);
    }
    $tg->send_start_message($chat_id, $access_code);
} else if ($text_message == '/reset') {

    $access_code = $db->reset_access_code($chat_id);
    $tg->send_reset_message($chat_id, $access_code);
    $jf->reset_webhooks($chat_id, $access_code);
} else if ($text_message == '/myforms') {

    $forms = $jf->get_user_forms($chat_id);
    $tg->send_forms($chat_id, $forms);
}
