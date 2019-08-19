<?php

require_once "jotform-api-php/JotForm.php";
require_once "textbuilder.php";

class JotFormHandler
{

    private $jf;

    public function __construct($api_key)
    {
        $this->jf = new JotForm($api_key);
    }

    public function get_user_forms($chat_id)
    {
        $forms = $this->jf->getForms(0, 1000);
        $returnForms = [];
        foreach ($forms as $form) {
            $tmpArr = [];
            if ($form['status'] === "ENABLED") {
                if (count($this->get_self_webhooks(strval($chat_id), $form['id'])) > 0) {
                    $tmpArr['text'] = $form['title'] . " ✅";
                } else {
                    $tmpArr['text'] = $form['title'] . " ❌";
                }
                $tmpArr['callback_data'] = $form['id'];
                array_push($returnForms, [$tmpArr]);
            }
        }

        return $returnForms;
    }

    public function get_submission($submission_id)
    {
        return $this->jf->getSubmission($submission_id);
    }

    public function get_form_title($form_id)
    {
        return $this->jf->getForm($form_id)['title'];
    }

    // GET WEBHOOK FROM FORM ID
    private function get_webhooks($form_id)
    {
        return $this->jf->getFormWebhooks($form_id);
    }


    // ADD WEBHOOK TO FORM
    public function add_webhook($form_id, $url)
    {
        $this->jf->createFormWebhook($form_id, $url);
    }


    // REMOVE WEBHOOK FROM FORM
    public function remove_webhook($form_id, $webhook_id)
    {
        $this->jf->deleteFormWebhook($form_id, $webhook_id);
    }


    // RE-SET ALL WEBHOOKS
    public function reset_webhooks($chat_id, $access_code)
    {
        $url = TextBuilder::prepare_webhook($chat_id, $access_code);
        $forms = $this->jf->getForms(0, 1000);
        foreach ($forms as $form) {
            $should_add_new_webhook = false;
            $self_hooks = $this->get_self_webhooks(strval($chat_id), $form['id']);

            foreach (array_reverse($self_hooks) as $hook_id) {
                $this->remove_webhook($form['id'], $hook_id);
                $should_add_new_webhook = true;
            }
            if ($should_add_new_webhook) {
                $this->add_webhook($form['id'], $url);
            }
        }
    }

    // CHECK IF OLD WEBHOOKS EXIST
    public function get_self_webhooks($chat_id, $form_id)
    {
        $self_hooks = [];
        $hooks = $this->get_webhooks($form_id);
        foreach ($hooks as $hook_id => $hook) {
            if (strpos($hook, $chat_id) > 0) {
                $self_hooks[$hook_id] = $hook_id;
            }
        }
        return $self_hooks;
    }


    public function reset_webhooks_on_single_form($chat_id, $form_id)
    {
        $self_hooks = $this->get_self_webhooks(strval($chat_id), $form_id);
        foreach (array_reverse($self_hooks) as $hook_id) {
            $this->remove_webhook($form_id, $hook_id);
        }
    }
}
