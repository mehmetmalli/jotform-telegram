<?php
require_once "database.php";
require_once "telegram.php";
require_once "jf_handler.php";

if (!empty($_POST)) {
    //parse request
    $temp = explode('=', $_SERVER['REQUEST_URI']);
    $chat_id = explode('&', $temp[1])[0];
    $access_code = $temp[2];

    //validate request
    if (!empty($chat_id) && !empty($access_code)) {

        $db = new Database();

        if ($db->user_exists($chat_id) && $access_code == $db->get_access_code($chat_id) && isset($_POST['username'])) {
            $username = $_POST['username'];
            $tg = new Telegram();
            if ($db->has_api_key($username)) {

                $jf = new JotFormHandler($db->get_api_key($username));
                $submission = $jf->get_submission($_POST['submissionID']);
                $tg->send_api_notification_message($chat_id, $submission, $_POST['formTitle'], $_POST['type']);
                $db->log($chat_id, $_POST['formID'], $_POST['submissionID'], $_POST['username']);
            } else {

                $tg->send_notification_message($chat_id, $_POST);
                $db->log($chat_id, $_POST['formID'], $_POST['submissionID'], $_POST['username']);
            }
        }
    }
}
