<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

/**
 * @subpackage Controllers
 * @package Modules
 */

class ealertController extends expController {
    public $basemodel_name = 'expeAlerts';
    protected $manage_permissions = array(
        'send'=>'Send E-Alert',
    );
	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    static function displayname() { return gt("E-Alerts"); }
    static function description() { return gt("This module will allow users to sign up for email alerts on a module by module basis."); }

    static function hasSources() {
        return false;
    }

    public function showall() {
        $ealerts = new expeAlerts();
        $subscriptions = array();
        assign_to_template(array(
            'ealerts'=>$ealerts->find('all'),
            'subscriptions'=>$subscriptions
        ));
    }

    public function send_confirm() {
        global $db;

        // find this E-Alert in the database
        $src = empty($this->params['src']) ? null : expString::escape($this->params['src']);
        $ealert = $db->selectObject('expeAlerts', 'module="'.$this->params['orig_controller'].'" AND src="'.$src.'"');
        if (!empty($ealert->autosend_ealerts)) {
            redirect_to(array('controller'=>'ealert','action'=>'send_auto','model'=>$this->params['model'],'id'=>$this->params['id'], 'src'=>$this->params['src']));
        }

        // find the content for the E-Alerts
        $model = $this->params['model'];
        $record = new $model($this->params['id']);
        // setup the content for the view
        $subject = $record->title;
        $body = $record->body;

        // figure out how many subscribers there are
        $number_of_subscribers = $db->countObjects('user_subscriptions', 'expeAlerts_id='.$ealert->id);

        assign_to_template(array(
            'record'=>$record,
            'number_of_subscribers'=>$number_of_subscribers,
            'ealert'=>$ealert
        ));
    }

    public function send_process() {
        global $db, $router;

        $obj = new stdClass();
        $obj->subject = $this->params['subject'];
        $obj->body = $this->params['body'];
        $link = $router->makelink(array('controller'=>$this->params['model'], 'action'=>'show', 'title'=>$this->params['sef_url']));
        $obj->body .= '<hr><a href="'.$link.'">'.gt('View posting').'.</a>';
        $obj->created_at = time();
        $id = $db->insertObject($obj, 'expeAlerts_temp');

        $bot = new expBot(array(
            'url'=>PATH_RELATIVE."index.php?controller=ealert&action=send&id=".$id.'&ealert_id='.$this->params['id'],
            'method'=>'POST',
        ));

        $bot->fire();
        flash('message', gt("E-Alerts are being sent to subscribers."));
        expHistory::back();
    }

    public function send_auto() {
        global $db, $router;

        // find this E-Alert in the database
        $src = empty($this->params['src']) ? null : expString::escape($this->params['src']);
        $ealert = $db->selectObject('expeAlerts', 'module="'.$this->params['model'].'" AND src="'.$src.'"');

         // find the content for the E-Alerts
        $model = $this->params['model'];
        $record = new $model($this->params['id']);
        $obj = new stdClass();
        $obj->subject = gt('Notification of New Content Posted to').' '.$ealert->ealert_title;
        $obj->body .= "<h3>".gt('New content was added titled')." '".$record->title."'</h3><hr>";
        if ($ealert->ealert_usebody == 0) {
            $obj->body .= $record->body;
        } elseif ($ealert->ealert_usebody == 1) {
            $obj->body .= expString::summarize($record->body,'html','paralinks');
        }
        $link = $router->makelink(array('controller'=>$this->params['model'], 'action'=>'show', 'title'=>$record->sef_url));
        $obj->body .= '<hr><a href="'.$link.'">'.gt('View posting').'.</a>';
        $obj->created_at = time();
        $id = $db->insertObject($obj, 'expeAlerts_temp');

        $bot = new expBot(array(
            'url'=>PATH_RELATIVE."index.php?controller=ealert&action=send&id=".$id.'&ealert_id='.$ealert->id,
            'method'=>'POST',
        ));

        $bot->fire();
        flash('message', gt("E-Alerts are being sent to subscribers."));
        expHistory::back();
    }

    public function send() {
        global $db, $router;

        // get the message body we saved in the temp table
        $message = $db->selectObject('expeAlerts_temp', 'id='.$this->params['id']);

        // look up the subscribers
        $sql  = 'SELECT s.* FROM '.$db->prefix.'user_subscriptions es ';
        $sql .= 'LEFT JOIN '.$db->prefix.'user s ON s.id=es.user_id WHERE es.expeAlerts_id='.$this->params['ealert_id'];
        $subscribers = $db->selectObjectsBySql($sql);

        $count = 1;
        $total = count($subscribers);
        foreach($subscribers as $subscriber) {
//            $link = $router->makelink(array('controller'=>'ealert', 'action'=>'subscriptions', 'id'=>$subscriber->id, 'key'=>$subscriber->hash));
//            $body  = $message->body;
//            $body .= '<br><a href="'.$link.'">'.gt('Click here to change your E-Alert subscription settings').'.</a>';

            $mail = new expMail();
            $mail->quickSend(array(
                'html_message'=>$message->body,
		        'to'=>array(trim($subscriber->email) => trim(user::getUserAttribution($subscriber->id))),
                'from'=>array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
		        'subject'=>$message->subject,
            ));

            $message->edited_at = time();
            $message->status = 'Sent message '.$count.' of '.$total;
            $db->updateObject($message, 'expeAlerts_temp');
            $count++;
        }

        $db->delete('expeAlerts_temp', 'id='.$message->id);
    }

    public function subscribe() {
        global $user,$db;

        // make sure we have what we need.
        if (empty($this->params['id'])) expQueue::flashAndFlow('error', gt('The id was not supplied.'));
        if (empty($user->id)) expQueue::flashAndFlow('error', gt('You must be logged on to subscribe.'));
        if (!$db->selectObject('user_subscriptions','user_id='.$user->id.' AND expeAlerts_id='.$this->params['id'])) {
            $subscription = new stdClass();
            $subscription->user_id = $user->id;
            $subscription->expeAlerts_id = $this->params['id'];
            $db->insertObject($subscription,'user_subscriptions');
            $ealert = $db->selectObject('expeAlerts','id='.$this->params['id']);
            flash('message', gt("You are now subscribed to receive email alerts for updates to"." ".$ealert->ealert_title));
        }
        expHistory::back();
    }

    public function unsubscribe() {
        global $user,$db;

        // make sure we have what we need.
        if (empty($this->params['id'])) expQueue::flashAndFlow('error', gt('The id was not supplied.'));
        if (empty($user->id)) expQueue::flashAndFlow('error', gt('You must be logged on to un-subscribe.'));
        $db->delete('user_subscriptions','user_id='.$user->id.' AND expeAlerts_id='.$this->params['id']);
        $ealert = $db->selectObject('expeAlerts','id='.$this->params['id']);
        flash('message', gt("You are now un-subscribed from email alerts to"." ".$ealert->ealert_title));
        expHistory::back();
    }

    /**
     * @deprecated
     */
//    public function subscriptions() {
//        global $db;
//
//        expHistory::set('manageable', $this->params);
//        // make sure we have what we need.
//        if (empty($this->params['key'])) expQueue::flashAndFlow('error', gt('The security key for account was not supplied.'));
//        if (empty($this->params['id'])) expQueue::flashAndFlow('error', gt('The subscriber id for account was not supplied.'));
//
//        // verify the id/key pair
//        $sub = new subscribers($this->params['id']);
//        if (empty($sub->id)) expQueue::flashAndFlow('error', gt('We could not find any subscriptions matching the ID and Key you provided.'));
//
//        // get this users subscriptions
//        $subscriptions = $db->selectColumn('expeAlerts_subscribers', 'expeAlerts_id', 'subscribers_id='.$sub->id);
//
//        // get a list of all available E-Alerts
//        $ealerts = new expeAlerts();
//        assign_to_template(array(
//            'subscriber'=>$sub,
//            'subscriptions'=>$subscriptions,
//            'ealerts'=>$ealerts->find('all')
//        ));
//    }

    /**
     * @deprecated
     */
//    public function subscription_update() {
//        global $db;
//
//        // make sure we have what we need.
//        if (empty($this->params['email'])) expQueue::flashAndFlow('error', gt('You must supply an email address to sign up for email alerts.'));
//        if (empty($this->params['key'])) expQueue::flashAndFlow('error', gt('The security key for account was not supplied.'));
//        if (empty($this->params['id'])) expQueue::flashAndFlow('error', gt('The subscriber id for account was not supplied.'));
//
//        // find the subscriber and validate the security key
//        $subscriber = new subscribers($this->params['id']);
//        if ($subscriber->hash != $this->params['key']) expQueue::flashAndFlow('error', gt('The security key you supplied does not match the one we have on file.'));
//
//        // delete any old subscriptions and add the user to new subscriptions
//        $db->delete('expeAlerts_subscribers', 'subscribers_id='.$subscriber->id);
//        foreach($this->params['ealerts'] as $ea_id) {
//            $obj = new stdClass();
//            $obj->subscribers_id = $subscriber->id;
//            $obj->expeAlerts_id = $ea_id;
//            $db->insertObject($obj, 'expeAlerts_subscribers');
//        }
//
//        $count = count($this->params['ealerts']);
//
//        if ($count > 0) {
//            flash('message', gt("Your subscriptions have been updated.  You are now subscriber to")." ".$count.' '.gt('E-Alerts.'));
//        } else {
//            flash('error', gt("You have been unsubscribed from all E-Alerts."));
//        }
//
//        expHistory::back();
//    }

    /**
     * @deprecated
     */
//    public function signup() {
//        global $db;
//        // check the anti-spam control
//        expValidator::check_antispam($this->params, gt("Anti-spam verification failed.  Please try again."));
//
//        // make sure we have what we need.
//        if (empty($this->params['email'])) expQueue::flashAndFlow('error', gt('You must supply an email address to sign up for email alerts.'));
//        if (empty($this->params['ealerts'])) expQueue::flashAndFlow('error', gt('You did not select any E-Alert topics to subscribe to.'));
//
//        // find or create the subscriber
//        $id = $db->selectValue('subscribers', 'id', 'email="'.$this->params['email'].'"');
//        $subscriber = new subscribers($id);
//        if (empty($subscriber->id)) {
//            $subscriber->email = trim($this->params['email']);
//            $subscriber->hash = md5($subscriber->email.time());
//            $subscriber->save();
//        }
//
//        // delete any old subscriptions and add the user to new subscriptions
//        $db->delete('expeAlerts_subscribers', 'subscribers_id='.$subscriber->id);
//        foreach($this->params['ealerts'] as $ea_id) {
//            $obj = new stdClass();
//            $obj->subscribers_id = $subscriber->id;
//            $obj->expeAlerts_id = $ea_id;
//            $db->insertObject($obj, 'expeAlerts_subscribers');
//        }
//
//        // send a confirmation email to the user.
//        $ealerts = $db->selectObjects('expeAlerts', 'id IN ('.implode(',', $this->params['ealerts']).')');
//        $body = expTemplate::get_template_for_action($this, 'email/confirmation_email', $this->loc);
//        $body->assign('ealerts', $ealerts);
//        $body->assign('subscriber', $subscriber);
//
//        $mail = new expMail();
//        $mail->quickSend(array(
//                'html_message'=>$body->render(),
//		        'to'=>$subscriber->email,
//                'from'=>array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
//		        'subject'=>gt('Please confirm your E-Alert subscriptions'),
//        ));
//
//        redirect_to(array('controller'=>'ealert', 'action'=>'pending', 'id'=>$subscriber->id));
//    }

    /**
     * @deprecated
     */
//    public function pending() {
////        global $db;
//
//        // make sure we have what we need.
//        if (empty($this->params['id'])) expQueue::flashAndFlow('error', gt('Your subscriber ID was not supplied.'));
//
//        // find the subscriber and their pending subscriptions
//        $ealerts = expeAlerts::getPendingBySubscriber($this->params['id']);
//        $subscriber = new subscribers($this->params['id']);
//
//        // render the template
//        assign_to_template(array(
//            'subscriber'=>$subscriber,
//            'ealerts'=>$ealerts
//        ));
//    }

    /**
     * @deprecated
     */
//    public function confirm() {
//        global $db;
//
//        // make sure we have what we need.
//        if (empty($this->params['key'])) expQueue::flashAndFlow('error', gt('The security key for account was not supplied.'));
//        if (empty($this->params['id'])) expQueue::flashAndFlow('error', gt('The subscriber id for account was not supplied.'));
//
//        // verify the id/key pair
//        $id = $db->selectValue('subscribers','id', 'id='.$this->params['id'].' AND hash="'.$this->params['key'].'"');
//        if (empty($id)) expQueue::flashAndFlow('error', gt('We could not find any subscriptions matching the ID and Key you provided.'));
//
//        // activate this users pending subscriptions
//        $sub = new stdClass();
//        $sub->enabled = 1;
//        $db->updateObject($sub, 'expeAlerts_subscribers', 'subscribers_id='.$id);
//
//        // find the users active subscriptions
//        $ealerts = expeAlerts::getBySubscriber($id);
//        assign_to_template(array(
//            'ealerts'=>$ealerts
//        ));
//    }

}

?>