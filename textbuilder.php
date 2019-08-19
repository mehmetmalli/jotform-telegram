<?php


class TextBuilder
{

    public static function get_successful_connection_message()
    {
        return "<b>You have succesfully connected your JotForm account! Yaay!</b>";
    }

    public static function prepare_webhook_changed_message($form_id, $form_title, $enabled)
    {

        $msg = "A webhook to get notifications from <b>" . $form_title . " (" . $form_id . ")" . "</b> has been ";
        if ($enabled) {
            $msg .= "enabled.";
        } else {
            $msg .= "disabled.";
        }

        return $msg;
    }

    public static function prepare_proper_message($chat_id, $access_code, $reset)
    {
        $login_link = TextBuilder::prepare_login_link($chat_id, $access_code);
        $webhook = TextBuilder::prepare_webhook($chat_id, $access_code);
        if ($reset) {
            return "We have changed your access code.\nYour old subscriptions from your friends' forms will be cancelled, and they won't be able to send you notifications anymore.\n\nYour user ID: <b>" . $chat_id . "</b>\nNew Access Code: <b>" . $access_code . "</b>\n\nNow you can connect your JotForm account with <a href='" . $login_link . "'>this URL</a>\n\nor use this new URL with Webhooks in Integrations: " . $webhook;
        }
        return "<b>Welcome to JotForm Submission Telegram Notifier!</b>\n\nPlease use this config on JotForm Integration:\nYour user ID: <b>" . $chat_id . "</b>\nAccess Code: <b>" . $access_code . "</b>\n\nNow you can connect your JotForm account with <a href='" . $login_link . "'>this URL</a>\n\nor use this URL with Webhooks in Integrations: " . $webhook;
    }

    public static function prepare_submission_message($submission)
    {

        $msg = "<b>NEW SUBMISSION\n" . $submission["formTitle"] . " - " . $submission["formID"] . "\n\nSubmission ID: </b>" . $submission["submissionID"] . "\n\n<b>IP: </b>" . $submission["ip"] . "<b> - Type: </b>" . $submission["type"] . "\n\n";

        $pretty_arr = explode(", ", $submission["pretty"]);
        foreach ($pretty_arr as $key => $field) {
            $field = explode(":", $field);
            $q = $field[0];
            $a = implode(array_slice($field, 1));
            $msg .= "<b>" . $q . "</b> : " . $a . "\n";
        }

        return $msg;
    }

    public static function prepare_api_submission_message($content, $form_title, $type)
    {
        $msg = "<b>NEW SUBMISSION\n" . $form_title . " - " . $content["form_id"] . "\n\nSubmission ID: </b>" . $content["id"] . "\n\n<b>IP: </b>" . $content["ip"] . "<b> - Type: </b>" . $type . "\n\n";
        $answers = $content['answers'];
        $ordered_answers = [];
        foreach ($answers as $val) {
            $ordered_answers[(int) $val['order']] = $val;
        }

        for ($i = 0; $i < count($ordered_answers); $i++) {
            $val = $ordered_answers[$i];
            if (isset($val['answer'])) {
                $msg .= "<b>{$val['text']}</b> : ";
                if (isset($val['prettyFormat'])) {
                    $msg .= $val['prettyFormat'] . "\n";
                } else {
                    $msg .= $val['answer'] . "\n";
                }
            }
        }

        return $msg;
    }

    public static function prepare_login_link($chat_id, $access_code)
    {
        return "https://telegram.jotform.io/login-on-tg/?chat_id={$chat_id}&access_code={$access_code}";
    }

    public static function prepare_webhook($chat_id, $access_code)
    {
        return "https://telegram.jotform.io/?chat_id={$chat_id}&access_code={$access_code}";
    }
}
