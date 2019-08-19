<?php

require_once __DIR__ . "/config/tg_config.php";
require_once __DIR__ . "/textbuilder.php";

class Telegram {

    public function send_webhook_changed_message($chat_id, $form_id, $form_title, $enabled) {
        $msg = TextBuilder::prepare_webhook_changed_message($form_id, $form_title, $enabled);
        $this->send_message($chat_id, $msg);
    }

    public function send_reset_message($chat_id, $pass) {
        
        $msg = TextBuilder::prepare_proper_message($chat_id, $pass, true);
        $this->send_message($chat_id, $msg);
    }

    public function send_start_message($chat_id, $pass) {

        $msg = TextBuilder::prepare_proper_message($chat_id, $pass, false);
        $this->send_message($chat_id, $msg);
    }

    public function send_api_successful_message($chat_id){

        $msg =  TextBuilder::get_successful_connection_message();
        $this->send_message($chat_id, $msg);
    }

    public function send_notification_message($chat_id, $submission) {

        $msg = TextBuilder::prepare_submission_message($submission);
        $this->send_message($chat_id, $msg);
    }

    public function send_api_notification_message($chat_id, $submission, $form_title, $type) {

        $msg = TextBuilder::prepare_api_submission_message($submission, $form_title, $type);
        $this->send_message($chat_id, $msg);
    }

    public function edit_forms($chat_id, $message_id, $text, $forms) {
        $this->edit_keyboard($chat_id, $message_id, "<b>{$text}</b>", $forms);
    }

    public function send_forms($chat_id, $forms) {
        $loop_count = ((int) (count($forms)/100)) + 1;
        for($i = 0; $i < $loop_count; $i++){
            $j = $i*100 + 1;
            $k = ($i+1) * 100 > count($forms) ? count($forms) : ($i+1) * 100;
            $txt = "<b>Your forms ({$j}-{$k}):</b>";
            $this->send_keyboard($chat_id, $txt, array_slice($forms, $i*100, 100));
        }
    }

    private function make_req($method, $datas=[]) {
        $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
        $res = curl_exec($ch);
        if (curl_error($ch)) {
            var_dump(curl_error($ch));
        } else {
            return json_decode($res);
        }
    }

    public function send_message($chat_id, $msg) {
        $this->make_req('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>$msg,
            'parse_mode'=>"HTML"
        ]);
    }

    private function send_keyboard($chat_id, $text, $keyboard) {
        $this->make_req('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=> $text,
            'parse_mode'=>"HTML",
            'reply_markup' =>json_encode([
                'inline_keyboard'=> $keyboard
            ])
        ]);
    }

    private function edit_keyboard($chat_id, $message_id, $text, $keyboard) {
        $this->make_req('editMessageText',[
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'text'=> $text,
            'parse_mode'=>"HTML",
            'reply_markup' =>json_encode([
                'inline_keyboard'=> $keyboard
            ])
        ]);
    }
}
