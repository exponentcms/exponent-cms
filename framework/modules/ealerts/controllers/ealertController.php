<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class ealertController extends expController {
    public $basemodel_name = 'expeAlerts';
    public $useractions = array('showall'=>'Show all modules available for signup');
	
	public $remove_configs = array(
        'aggregation',
        'comments',
        'files',
        'rss',
        'tags'
    );
	
    function displayname() { return "E-Alerts"; }
    function description() { return "This module will allow your users to signup for email alerts on a module by module basis."; }
    
    public function showall() {
        $ealerts = new expeAlerts();
        $subscriptions = array();
        assign_to_template(array('ealerts'=>$ealerts->find('all'), 'subscriptions'=>$subscriptions));
    }
    
    public function send_confirm() {
        global $db;

        // find the content for the E-Alerts
        $record = new $this->params['model']($this->params['id']);
        
        // find this E-Alert in the database
        $src = empty($this->params['src']) ? null : $this->params['src'];
        $ealert = $db->selectObject('expeAlerts', 'module="'.$this->params['orig_controller'].'" AND src="'.$src.'"');
        
        // setup the content for the view
        $subject = $record->title;
        $body = $record->body;
        
        // figure out how many subscribers there are
        $number_of_subscribers = $db->countObjects('expeAlerts_subscribers', 'expeAlerts_id='.$ealert->id);
        
        assign_to_template(array('record'=>$record, 'number_of_subscribers'=>$number_of_subscribers, 'ealert'=>$ealert));
    }
    
    public function send_process() {
        global $db;
        
        $obj->subject = $this->params['subject'];
        $obj->body = $this->params['body'];
        $obj->created_at = time();
        $id = $db->insertObject($obj, 'expeAlerts_temp');
        
        $bot = new expBot(array(
            'url'=>URL_FULL."index.php?controller=ealert&action=send&id=".$id.'&ealert_id='.$this->params['id'],
            'method'=>'POST',
        ));
        
        $bot->fire();
        flash('message', gt("The E-Alerts are being sent to the subscribers."));
        expHistory::back();
    }
    
    public function send() {
        global $db, $router;
        
        // get the message body we saved in the temp table
        $message = $db->selectObject('expeAlerts_temp', 'id='.$this->params['id']);
        
        // look up the subscribers
        $sql  = 'SELECT s.* FROM '.DB_TABLE_PREFIX.'_expeAlerts_subscribers es ';
        $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'_subscribers s ON s.id=es.subscribers_id WHERE es.expeAlerts_id='.$this->params['ealert_id'];
        $subscribers = $db->selectObjectsBySql($sql);
        
        $count = 1;
        $total = count($subscribers);
        foreach($subscribers as $subscriber) {
            $link = $router->makelink(array('controller'=>'ealert', 'action'=>'subscriptions', 'id'=>$subscriber->id, 'key'=>$subscriber->hash));
            $body  = $message->body;
            $body .= '<br><a href="'.$link.'">Click here to change your E-Alert subscription settings.</a>';
            
            $mail = new expMail();
            $mail->quickSend(array(
                'html_message'=>$message->body,
		        'to'=>$subscriber->email,
		        'from'=>SMTP_FROMADDRESS,
		        'subject'=>$message->subject,
            ));
            
            $message->edited_at = time();
            $message->status = 'Sent message '.$count.' of '.$total;
            $db->updateObject($message, 'expeAlerts_temp');
            $count++;
        } 
        
        $db->delete('expeAlerts_temp', 'id='.$message->id);
    }
    
    public function subscriptions() {
        global $db;
        
        expHistory::set('manageable', $this->params);
        // make sure we have what we need.
        if (empty($this->params['key'])) expQueue::flashAndFlow('error', 'The security key for account was not supplied.');
        if (empty($this->params['id'])) expQueue::flashAndFlow('error', 'The subscriber id for account was not supplied.');
        
        // verify the id/key pair    
        $sub = new subscribers($this->params['id']);
        if (empty($sub->id)) expQueue::flashAndFlow('error', 'We could not find any subscriptions matching the ID and Key you provided.');
        
        // get this users subscriptions
        $subscriptions = $db->selectColumn('expeAlerts_subscribers', 'expeAlerts_id', 'subscribers_id='.$sub->id);
        
        // get a list of all available E-Alerts
        $ealerts = new expeAlerts();
        assign_to_template(array('subscriber'=>$sub, 'subscriptions'=>$subscriptions, 'ealerts'=>$ealerts->find('all')));
    }
    
    public function subscription_update() {
        global $db;
        
        // make sure we have what we need.
        if (empty($this->params['email'])) expQueue::flashAndFlow('error', 'You must supply an email address to sign up for email alerts.');
        if (empty($this->params['key'])) expQueue::flashAndFlow('error', 'The security key for account was not supplied.');
        if (empty($this->params['id'])) expQueue::flashAndFlow('error', 'The subscriber id for account was not supplied.');
        
        // find the subscriber and validate the security key
        $subscriber = new subscribers($this->params['id']);
        if ($subscriber->hash != $this->params['key']) expQueue::flashAndFlow('error', 'The security key you supplied does not match the one we have on file.');
        
        // delete any old subscriptions and add the user to new subscriptions
        $db->delete('expeAlerts_subscribers', 'subscribers_id='.$subscriber->id);
        foreach($this->params['ealerts'] as $ea_id) {
            $obj = null;
            $obj->subscribers_id = $subscriber->id;
            $obj->expeAlerts_id = $ea_id;
            $db->insertObject($obj, 'expeAlerts_subscribers');
        }
        
        $count = count($this->params['ealerts']);
        
        if ($count > 0) {
            flash('message', gt("Your subscriptions have been updated.  You are now subscriber to")." ".$count.' '.gt('E-Alerts.'));
        } else {
            flash('error', gt("You have been unsubscribed from all E-Alerts."));
        }
        
        expHistory::back();
    }
    
    public function signup() {
        global $db;
        // check the anti-spam control
        expValidator::check_antispam($this->params, "Anti-spam verification failed.  Please try again.");
        
        // make sure we have what we need.
        if (empty($this->params['email'])) expQueue::flashAndFlow('error', 'You must supply an email address to sign up for email alerts.');
        if (empty($this->params['ealerts'])) expQueue::flashAndFlow('error', 'You did not select any E-Alert topics to subscribe to.');        
        
        // find or create the subscriber
        $id = $db->selectValue('subscribers', 'id', 'email="'.$this->params['email'].'"');
        $subscriber = new subscribers($id);
        if (empty($subscriber->id)) {
            $subscriber->email = trim($this->params['email']);
            $subscriber->hash = md5($subscriber->email.time());
            $subscriber->save();
        }
        
        // delete any old subscriptions and add the user to new subscriptions
        $db->delete('expeAlerts_subscribers', 'subscribers_id='.$subscriber->id);
        foreach($this->params['ealerts'] as $ea_id) {
            $obj = null;
            $obj->subscribers_id = $subscriber->id;
            $obj->expeAlerts_id = $ea_id;
            $db->insertObject($obj, 'expeAlerts_subscribers');
        }
  
        // send a confirmation email to the user.    
        $ealerts = $db->selectObjects('expeAlerts', 'id IN ('.implode(',', $this->params['ealerts']).')');
        $body = get_template_for_action($this, 'confirmation_email', $this->loc);
        $body->assign('ealerts', $ealerts);
        $body->assign('subscriber', $subscriber);
        
        $mail = new expMail();
        $mail->quickSend(array(
                'html_message'=>$body->render(),
		        'to'=>$subscriber->email,
		        'from'=>SMTP_FROMADDRESS,
		        'subject'=>'Please confirm your E-Alert subscriptions',
        ));
        
        redirect_to(array('controller'=>'ealert', 'action'=>'pending', 'id'=>$subscriber->id));
    }
    
    public function pending() {
        global $db;
        
        // make sure we have what we need.
        if (empty($this->params['id'])) expQueue::flashAndFlow('error', 'Your subscriber ID was not supplied.');

        // find the subscriber and their pending subscriptions
        $ealerts = expeAlerts::getPendingBySubscriber($this->params['id']);
        $subscriber = new subscribers($this->params['id']);
        
        // render the template
        assign_to_template(array('subscriber'=>$subscriber, 'ealerts'=>$ealerts));
    }
    
    public function confirm() {
        global $db;
        
        // make sure we have what we need.
        if (empty($this->params['key'])) expQueue::flashAndFlow('error', 'The security key for account was not supplied.');
        if (empty($this->params['id'])) expQueue::flashAndFlow('error', 'The subscriber id for account was not supplied.');
        
        // verify the id/key pair    
        $id = $db->selectValue('subscribers','id', 'id='.$this->params['id'].' AND hash="'.$this->params['key'].'"');
        if (empty($id)) expQueue::flashAndFlow('error', 'We could not find any subscriptions matching the ID and Key you provided.');
        
        // activate this users pending subscriptions
        $sub->enabled = 1;
        $db->updateObject($sub, 'expeAlerts_subscribers', 'subscribers_id='.$id);
        
        // find the users active subscriptions
        $ealerts = expeAlerts::getBySubscriber($id);
        assign_to_template(array('ealerts'=>$ealerts));
    }
}

?>
