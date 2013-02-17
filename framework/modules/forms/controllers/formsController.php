<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * @package    Modules
 */

class formsController extends expController {
    public $useractions = array(
        'enterdata' => 'Input Records',
        'showall'    => 'Show All Records',
        'show'       => 'Show a Single Record',
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
//        'pagination',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','pagination','rss','tags')
    public $add_permissions = array(
        'viewdata'  => "View Data",
        'enter_data' => "Enter Data"  // slight naming variatino to not fully restrict enterdata method
    );
    public $codequality = 'beta';

    static function displayname() {
        return gt("Forms");
    }

    static function description() {
        return gt("Allows the creation of forms that can be emailed, or even viewed if they are optionally stored in the database");
    }

    static function author() {
        return "Dave Leffler";
    }

    static function isSearchable() {
        return false;
    }

    function searchName() {
        return gt("Forms");
    }

    function searchCategory() {
        return gt('Form Data');
    }

    public function showall() {
        if (!empty($this->config['unrestrict_view']) || expPermissions::check('viewdata', $this->loc)) {

            global $db;

            expHistory::set('viewable', $this->params);
            if (!empty($this->config)) {
                $f = $this->forms->find('first', 'id=' . $this->config['forms_id']);
            } elseif (!empty($this->params['title'])) {
                $f = $this->forms->find('first', 'sef_url="' . $this->params['title'] . '"');
                $this->get_defaults($f);
            } elseif (!empty($this->params['id'])) {
                $f = $this->forms->find('first', 'id=' . $this->params['id']);
                $this->get_defaults($f);
            }
            $items = $db->selectObjects('forms_' . $f->table_name, 1);
            $columns = array();
            $fc = new forms_control();
            if (empty($this->config['column_names_list'])) {
                //define some default columns...
                $controls = $fc->find('all', 'forms_id=' . $f->id . ' and is_readonly=0 and is_static = 0','rank');
                foreach (array_slice($controls, 0, 5) as $control) {
                    $this->config['column_names_list'][] = $control->name;
                }
            }

            // pre-process records
            foreach ($this->config['column_names_list'] as $column_name) {
                if ($column_name == "ip") {
                    $columns[gt('IP Address')] = 'ip';
                } elseif ($column_name == "user_id") {
                    foreach ($items as $key => $item) {
                        if ($item->$column_name != 0) {
                            $locUser = user::getUserById($item->$column_name);
                            $item->$column_name = $locUser->username;
                        } else {
                            $item->$column_name = '';
                        }
                        $items[$key] = $item;
                    }
                    $columns[gt('Posted by')] = 'user_id';
                } elseif ($column_name == "timestamp") {
                    foreach ($items as $key => $item) {
                        $item->$column_name = strftime(DISPLAY_DATETIME_FORMAT, $item->$column_name);
                        $items[$key] = $item;
                    }
                    $columns[gt('Timestamp')] = 'timestamp';
                } else {
                    $control = $fc->find('first', "name='" . $column_name . "' and forms_id=" . $f->id,'rank');
                    if ($control) {
                        $ctl = unserialize($control->data);
                        $control_type = get_class($ctl);
                        foreach ($items as $key => $item) {
                            //We have to add special sorting for date time columns!!!
                            $item->$column_name = @call_user_func(array($control_type, 'templateFormat'), $item->$column_name, $ctl);
                            $items[$key] = $item;
                        }
                        $columns[$control->caption] = $column_name;
                    }
                }
            }

            $page = new expPaginator(array(
                'records' => $items,
                'where'   => 1,
                'limit'   => (isset($this->params['limit']) && $this->params['limit'] != '') ? $this->params['limit'] : 10,
                'order'   => (isset($this->params['order']) && $this->params['order'] != '') ? $this->params['order'] : 'id',
                'dir'     => (isset($this->params['dir']) && $this->params['dir'] != '') ? $this->params['dir'] : 'ASC',
                'page'    => (isset($this->params['page']) ? $this->params['page'] : 1),
                'controller'=>$this->baseclassname,
                'action'  => $this->params['action'],
                'src'=>$this->loc->src,
                'columns' => $columns
            ));

            assign_to_template(array(
                "backlink"    => expHistory::getLastNotEditable(),
                "f"           => $f,
                "page"        => $page,
                "title"       => !empty($this->config['report_name']) ? $this->config['report_name'] : '',
                "description" => !empty($this->config['report_desc']) ? $this->config['report_desc'] : null,
            ));
        } else {
            assign_to_template(array(
                "error" => 1,
            ));
        }
    }

    public function show() {

        if (!empty($this->config['unrestrict_view']) || expPermissions::check('viewdata', $this->loc)) {
            global $db;

            //FIXME we need to add a browse other records (next/prev) feature here
            //FIXME that would require a sort by which column and direction
            expHistory::set('viewable', $this->params);
            if (!empty($this->config)) {
                $f = $this->forms->find('first', 'id=' . $this->config['forms_id']);
            } elseif (!empty($this->params['title'])) {
                $f = $this->forms->find('first', 'sef_url="' . $this->params['title'] . '"');
                redirect_to(array('controller' => 'forms', 'action' => 'enterdata', 'forms_id' => $f->id));
            }

            $fc = new forms_control();
            $controls = $fc->find('all', 'forms_id=' . $f->id . ' and is_readonly=0 and is_static = 0','rank');
            $data = $db->selectObject('forms_' . $f->table_name, 'id=' . $this->params['id']);

            if ($controls && $data) {
                $fields = array();
                $captions = array();
                foreach ($controls as $c) {
                    $ctl = unserialize($c->data);
                    $control_type = get_class($ctl);
                    $name = $c->name;
                    $fields[$name] = call_user_func(array($control_type, 'templateFormat'), $data->$name, $ctl);
                    $captions[$name] = $c->caption;
                }

                // system added fields
                $captions['user_id'] = gt('Posted by');
                $captions['ip'] = gt('IP Address');
                $captions['timestamp'] = gt('Timestamp');
                $fields['ip'] = $data->ip;
                $locUser = user::getUserById($data->user_id);
                $fields['user_id'] = !empty($locUser->username) ? $locUser->username : '';
                $fields['timestamp'] = strftime(DISPLAY_DATETIME_FORMAT, $data->timestamp);
            }

            assign_to_template(array(
    //            "backlink"=>expHistory::getLastNotEditable(),
                'backlink'    => expHistory::getLast('editable'),
                "f"           => $f,
                "record_id"   => $this->params['id'],
                "title"       => !empty($this->config['report_name']) ? $this->config['report_name'] : gt('Viewing Record'),
                "description" => !empty($this->config['report_desc']) ? $this->config['report_desc'] : null,
                'fields'      => $fields,
                'captions'    => $captions,
                'is_email'    => 0,
                "css"         => file_get_contents(BASE . "framework/core/assets/css/tables.css"),
            ));
        } else {
            assign_to_template(array(
                "error" => 1,
            ));
        }
    }

    public function enter_data() {
        $this->enterdata();
    }

    public function enterdata() {
        if (empty($this->config['restrict_enter']) || expPermissions::check('enterdata', $this->loc)) {

            global $db, $user;

            expHistory::set('viewable', $this->params);

            if (!empty($this->config)) {
                $f = $this->forms->find('first', 'id=' . $this->config['forms_id']);
            } elseif (!empty($this->params['forms_id'])) {
                $f = $this->forms->find('first', 'id=' . $this->params['forms_id']);
                $this->get_defaults($f);
            }

            if (!empty($f)) {
                $form = new form();
                if (!empty($this->params['id'])) {
                    $fc = new forms_control();
                    $controls = $fc->find('all', 'forms_id=' . $f->id . ' and is_readonly=0 and is_static = 0','rank');
                    $data = $db->selectObject('forms_' . $f->table_name, 'id=' . $this->params['id']);
                    //            $data = $forms_record->find('first','id='.$this->params['id']);
                } else {
                    if (!empty($f->forms_control)) {
                        $controls = $f->forms_control;
                    } else {
                        $controls = array();
                    }
                    $data = expSession::get('forms_data_' . $f->id);
                }
                // display list of email addresses
                if (!empty($this->config['select_email'])) {
                    //Building Email List...
                    $emaillist = array();
                    if (!empty($this->config['user_list'])) foreach ($this->config['user_list'] as $c) {
                        $u = user::getUserById($c);
                        if (!empty($u->email)) {
                            if (!empty($u->firstname) || !empty($u->lastname)) {
                                $title = $u->firstname . ' ' . $u->lastname . ' ('. $u->email . ')';
                            } else {
                                $title = $u->username . ' ('. $u->email . ')';
                            }
                            $emaillist[$u->email] = $title;
                        }
                    }
                    if (!empty($this->config['group_list'])) foreach ($this->config['group_list'] as $c) {
//                        $grpusers = group::getUsersInGroup($c);
//                        foreach ($grpusers as $u) {
//                            $emaillist[] = $u->email;
//                        }
                        $g = group::getGroupById($c);
                        $emaillist[$c] = $g->name;
                    }
                    if (!empty($this->config['address_list'])) foreach ($this->config['address_list'] as $c) {
                        $emaillist[$c] = $c;
                    }
                    //This is an easy way to remove duplicates
                    $emaillist = array_flip(array_flip($emaillist));
                    $emaillist = array_map('trim', $emaillist);
                    $emaillist = array_reverse($emaillist, true);
                    $emaillist[0] = gt('All Addresses');
                    $emaillist = array_reverse($emaillist, true);
                    $form->register('email_dest', gt('Send Response to'), new radiogroupcontrol('', $emaillist));
                }
                foreach ($controls as $c) {
                    $ctl = unserialize($c->data);
                    $ctl->_id = $c->id;
                    $ctl->_readonly = $c->is_readonly;
                    if (!empty($this->params['id'])) {
                        if ($c->is_readonly == 0) {
                            $name = $c->name;
                            if ($c->is_static == 0) {
                                $ctl->default = $data->$name;
                            }
                        }
                    } else {
                        if (!empty($data[$c->name])) $ctl->default = $data[$c->name];
                    }
                    $form->register($c->name, $c->caption, $ctl);
                }

                // if we are editing an existing record we'll need to do recaptcha here since we won't call confirm_data
                if (!empty($this->params['id'])) {
                    $antispam = '';
                    if (SITE_USE_ANTI_SPAM && ANTI_SPAM_CONTROL == 'recaptcha') {
                        // make sure we have the proper config.
                        if (!defined('RECAPTCHA_PUB_KEY')) {
                            $antispam .= '<h2 style="color:red">' . gt('reCaptcha configuration is missing the public key.') . '</h2>';
                        }
                        if ($user->isLoggedIn() && ANTI_SPAM_USERS_SKIP == 1) {
                            // skip it for logged on users based on config
                        } else {
                            // include the library and show the form control
                            require_once(BASE . 'external/recaptchalib.php');
                            $antispam .= recaptcha_get_html(RECAPTCHA_PUB_KEY);
                            $antispam .= '<p>' . gt('Fill out the above security question to submit your form.') . '</p>';
                        }
                    }
                    $form->register(uniqid(''), '', new htmlcontrol($antispam));
                }

                if (empty($this->config['submitbtn'])) $this->config['submitbtn'] = gt('Submit');
                if (!empty($this->params['id'])) {
                    $cancel = gt('Cancel');
                    $form->meta('action', 'submit_data');
                    $form->meta('isedit', 1);
                    $form->meta('data_id', $data->id);
                    $form->location($this->loc);
                    assign_to_template(array(
                        'edit_mode' => 1,
                    ));
                } else {
                    $cancel = '';
                    $form->meta("action", "confirm_data");
                }
                if (empty($this->config['submitbtn'])) $this->config['submitbtn'] = gt('Submit');
                if (empty($this->config['resetbtn'])) $this->config['resetbtn'] = '';
                $form->register("submit", "", new buttongroupcontrol($this->config['submitbtn'], $this->config['resetbtn'], $cancel));

                $form->meta("m", $this->loc->mod);
                $form->meta("s", $this->loc->src);
                $form->meta("i", $this->loc->int);
                $form->meta("id", $f->id);
                $formmsg = '';
                $form->location(expCore::makeLocation("forms", $this->loc->src, $this->loc->int));
                if (count($controls) == 0) {
                    $form->controls['submit']->disabled = true;
                    $formmsg .= gt('This form is blank. Select "Design Form" to add input fields.') . '<br>';
                } elseif (empty($f->is_saved) && empty($this->config['is_email'])) {
                    $form->controls['submit']->disabled = true;
                    $formmsg .= gt('There are no actions assigned to this form. Select "Configure Settings" then either select "Email Form Data" and/or "Save Submissions to Database".');
                }
                $count = $db->countObjects("forms_" . $f->table_name);
                if ($formmsg) {
                    flash('notice', $formmsg);
                }
                if (empty($this->config['description'])) $this->config['description'] = '';
                assign_to_template(array(
                    "description" => $this->config['description'],
                    "form_html"   => $form->toHTML($f->id),
                    "form"        => $f,
                    "count"       => $count,
                ));
            }
        } else {
            assign_to_template(array(
                "error" => 1,
            ));
        }
    }

    public function confirm_data() {
        $f = new forms($this->params['id']);
        $cols = $f->forms_control;
        $counts = array();
        $responses = array();

        foreach ($cols as $col) {
            $coldef = unserialize($col->data);
            $coldata = new ReflectionClass($coldef);
            $coltype = $coldata->getName();
            if ($coltype == 'uploadcontrol') {
                $value = call_user_func(array($coltype, 'parseData'), $col->name, $_FILES, true);
            } else {
                $value = call_user_func(array($coltype, 'parseData'), $col->name, $this->params, true);
            }
            $value = call_user_func(array($coltype, 'templateFormat'), $value, $coldef);
            //eDebug($value);
            $counts[$col->caption] = isset($counts[$col->caption]) ? $counts[$col->caption] + 1 : 1;
            $num = $counts[$col->caption] > 1 ? $counts[$col->caption] : '';

            if (!empty($this->params[$col->name])) {
                if ($coltype == 'checkboxcontrol') {
                    $responses[$col->caption . $num] = 'Yes';
                } else {
                    $responses[$col->caption . $num] = $value;
                }
            } else {
                if ($coltype == 'checkboxcontrol') {
                    $responses[$col->caption . $num] = 'No';
                } elseif ($coltype == 'datetimecontrol') {
                    $responses[$col->name] = $value;
                } elseif ($coltype == 'uploadcontrol') {
                    $this->params[$col->name] = PATH_RELATIVE . call_user_func(array($coltype, 'moveFile'), $col->name, $_FILES, true);
        //            $value = call_user_func(array($coltype,'buildDownloadLink'),$this->params[$col->name],$_FILES[$col->name]['name'],true);
                    //eDebug($value);
                    $responses[$col->caption . $num] = $_FILES[$col->name]['name'];
                } elseif ($coltype != 'htmlcontrol') {
                    $responses[$col->caption . $num] = '';
                }
            }
        }

        // remove some post data we don't want to pass thru to the form
        unset($this->params['action']);
        unset($this->params['controller']);
        foreach ($this->params as $k => $v) {
        //    $this->params[$k]=htmlentities(htmlspecialchars($v,ENT_COMPAT,LANG_CHARSET));
            $this->params[$k] = htmlspecialchars($v, ENT_COMPAT, LANG_CHARSET);
        }
        expSession::set('forms_data_' . $this->params['id'], $this->params);

        assign_to_template(array(
            'recaptcha_theme' => RECAPTCHA_THEME,
            'responses'       => $responses,
            'postdata'        => $this->params,
        ));
    }

    public function submit_data() {
        // Check for form errors
        $post = $this->params;
        $post['manual_redirect'] = true;
        if (!expValidator::check_antispam($post)) {
            flash('error', gt('Security Validation Failed'));
            expHistory::back();
        }

        global $db, $user;
        $f = new forms($this->params['id']);
        $fc = new forms_control();
        $controls = $fc->find('all', "forms_id=" . $f->id . " and is_readonly=0",'rank');
        $this->get_defaults($f);

        $db_data = new stdClass();
        $emailFields = array();
        $captions = array();
        $attachments = array();
        foreach ($controls as $c) {
            $ctl = unserialize($c->data);
            $control_type = get_class($ctl);
            $def = call_user_func(array($control_type, "getFieldDefinition"));
            if ($def != null) {
                $emailValue = htmlspecialchars_decode(call_user_func(array($control_type, 'parseData'), $c->name, $this->params, true));
                $value = stripslashes($db->escapeString($emailValue));

                //eDebug($value);
                $varname = $c->name;
                $db_data->$varname = $value;
        //        $fields[$c->name] = call_user_func(array($control_type,'templateFormat'),$value,$ctl);
                $emailFields[$c->name] = call_user_func(array($control_type, 'templateFormat'), $value, $ctl);
                $captions[$c->name] = $c->caption;
                if ($c->name == "email" && expValidator::isValidEmail($value)) {
                    $from = $value;
                }
                if ($c->name == "name") {
                    $from_name = $value;
                }
                if (get_class($ctl) == 'uploadcontrol') {
                    $attachments[] = htmlspecialchars_decode($this->params[$c->name]);
                }
            }
        }

        if (!isset($this->params['data_id']) || (isset($this->params['data_id']) && expPermissions::check("editdata", $f->loc))) {
            if ($f->is_saved == 1) {
                if (isset($this->params['data_id'])) {
                    //if this is an edit we remove the record and insert a new one.
                    $olddata = $db->selectObject('forms_' . $f->table_name, 'id=' . $this->params['data_id']);
                    $db_data->ip = $olddata->ip;
                    $db_data->user_id = $olddata->user_id;
                    $db_data->timestamp = $olddata->timestamp;
                    $db_data->referrer = $olddata->referrer;
                    $db->delete('forms_' . $f->table_name, 'id=' . $this->params['data_id']);
                } else {
                    $db_data->ip = $_SERVER['REMOTE_ADDR'];
                    if (expSession::loggedIn()) {
                        $db_data->user_id = $user->id;
                        $from = $user->email;
                        $from_name = $user->firstname . " " . $user->lastname . " (" . $user->username . ")";
                    } else {
                        $db_data->user_id = 0;
                    }
                    $db_data->timestamp = time();
                    $referrer = $db->selectValue("sessionticket", "referrer", "ticket = '" . expSession::getTicketString() . "'");
                    $db_data->referrer = $referrer;
                }
                $db->insertObject($db_data, 'forms_' . $f->table_name);
            } else {
                $referrer = $db->selectValue("sessionticket", "referrer", "ticket = '" . expSession::getTicketString() . "'");
            }

            //Email stuff here...
            //Don't send email if this is an edit.
            if (!empty($this->config['is_email']) && !isset($this->params['data_id'])) {
                //Building Email List...
                $emaillist = array();
                if (!empty($this->config['select_email']) && !empty($this->params['email_dest'])) {
                    if (strval(intval($this->params['email_dest'])) == strval($this->params['email_dest'])) {
                        foreach (group::getUsersInGroup($this->params['email_dest']) as $locUser) {
                            if ($locUser->email != '') $emaillist[] = $locUser->email;
                        }
                    } else {
                        $emaillist[] = $this->params['email_dest'];
                    }
                } else { // send to all form addressee's
                    $emaillist = array();
                    if (!empty($this->config['user_list'])) foreach ($this->config['user_list'] as $c) {
                        $u = user::getUserById($c);
                        $emaillist[] = $u->email;
                    }
                    if (!empty($this->config['group_list'])) foreach ($this->config['group_list'] as $c) {
                        $grpusers = group::getUsersInGroup($c);
                        foreach ($grpusers as $u) {
                            $emaillist[] = $u->email;
                        }
                    }
                    if (!empty($this->config['address_list'])) foreach ($this->config['address_list'] as $c) {
                        $emaillist[] = $c;
                    }
                }
                //This is an easy way to remove duplicates
                $emaillist = array_flip(array_flip($emaillist));
                $emaillist = array_map('trim', $emaillist);

                if ($this->config['report_def'] == "") {
                    $msgtemplate = get_template_for_action($this, 'email/default_report', $this->loc);

                } else {
                    $msgtemplate = get_template_for_action($this, 'email/custom_report', $this->loc);
                    $msgtemplate->assign('template', $this->config['report_def']);
                }
                $msgtemplate->assign("fields", $emailFields);
                $msgtemplate->assign("captions", $captions);
                $msgtemplate->assign('title', $this->config['report_name']);
                $msgtemplate->assign("is_email", 1);
                if (!empty($referrer)) $msgtemplate->assign("referrer", $referrer);
                $emailText = $msgtemplate->render();
                $emailText = chop(strip_tags(str_replace(array("<br />", "<br>", "br/>"), "\n", $emailText)));
                $msgtemplate->assign("css", file_get_contents(BASE . "framework/core/assets/css/tables.css"));
                $emailHtml = $msgtemplate->render();

                if (empty($from)) {
                    $from = trim(SMTP_FROMADDRESS);
                }
                if (empty($from_name)) {
                    $from_name = trim(ORGANIZATION_NAME);
                }
                // $headers = array(
                // "MIME-Version"=>"1.0",
                // "Content-type"=>"text/html; charset=".LANG_CHARSET
                // );
                if (count($emaillist)) {
                    $mail = new expMail();
                    if (!empty($attachments)) {
                        foreach ($attachments as $attachment) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $relpath = str_replace(PATH_RELATIVE, '', BASE);
                            $ftype = finfo_file($finfo, $relpath . $attachment);
                            finfo_close($finfo);
                            $mail->attach_file_on_disk($relpath . $attachment, $ftype);
                        }
                    }
                    $mail->quickSend(array(
                        //	'headers'=>$headers,
                        'html_message' => $emailHtml,
                        "text_message" => $emailText,
                        'to'           => $emaillist,
                        'from'         => array(trim($from) => $from_name),
                        'subject'      => $this->config['subject'],
                    ));
                }
            }

            if (!empty($this->config['is_auto_respond']) && !isset($this->params['data_id']) && !empty($db_data->email)) {
                if (empty($from)) {
                    $from = trim(SMTP_FROMADDRESS);
                }
                if (empty($from_name)) {
                    $from_name = trim(ORGANIZATION_NAME);
                }
                $headers = array(
                    "MIME-Version" => "1.0",
                    "Content-type" => "text/html; charset=" . LANG_CHARSET
                );

                $mail = new expMail();
                $mail->quickSend(array(
                    'headers'      => $headers,
                    'html_message' => $this->config['auto_respond_body'],
                    'to'           => $db_data->email,
                    'from'         => array(trim($from) => $from_name),
                    'subject'      => $this->config['auto_respond_subject'],
                ));
            }

            // clear the users post data from the session.
            expSession::un_set('forms_data_' . $f->id);

            //If is a new post show response, otherwise redirect to the flow.
            if (!isset($this->params['data_id'])) {
                if (empty($this->config['response'])) $this->config['response'] = gt('Thanks for your submission');
                assign_to_template(array(
                    "backlink"=>expHistory::getLastNotEditable(),
                    "response_html"=>$this->config['response'],
                ));
            } else {
                flash('message', gt('Record was updated!'));
        //        expHistory::back();
                expHistory::returnTo('editable');
            }
        }
    }

    /**
     * delete item in module
     */
    function delete() {
        global $db;

        if (empty($this->params['id']) || empty($this->params['forms_id'])) {
            flash('error', gt('Missing id for the') . ' ' . gt('item') . ' ' . gt('you would like to delete'));
            expHistory::back();
        }

        $f = new forms($this->params['forms_id']);
        $db->delete('forms_' . $f->table_name, 'id=' . $this->params['id']);

        expHistory::back();
    }

    /**
     * Manage site forms
     *
     */
    public function manage() {
        global $db;

        $forms = $this->forms->find('all', 1);
        foreach($forms as $key=>$f) {
            if (!empty($f->table_name) && $db->tableExists("forms_" . $f->table_name) ) {
                $forms[$key]->count = $db->countObjects("forms_" . $f->table_name);
            }
            $forms[$key]->control_count = count($f->forms_control);
        }

        assign_to_template(array(
            'forms' => $forms
        ));
    }

    /**
     * Assign selected form to current module
     *
     */
    public function activate() {
        // assign new form assigned
        $this->config['forms_id'] = $this->params['id'];
        // set default settings for this form
        $f = new forms($this->params['id']);
        if (!empty($f->description)) $this->config['description'] = $f->description;
        if (!empty($f->response)) $this->config['response'] = $f->response;
        if (!empty($f->report_name)) $this->config['report_name'] = $f->report_name;
        if (!empty($f->report_desc)) $this->config['report_desc'] = $f->report_desc;
        if (!empty($f->column_names_list)) $this->config['column_names_list'] = $f->column_names_list;
        if (!empty($f->report_def)) $this->config['report_def'] = $f->report_def;

        // setup and save the config
        $config = new expConfig($this->loc);
        $config->update(array('config' => $this->config));

        expHistory::back();
    }

    public function edit_form() {
        expHistory::set('editable', $this->params);
        if (!empty($this->params['id'])) {
            $f = $this->forms->find('first', 'id=' . $this->params['id']);
        } else {
            $f = new forms();
        }
        $fields = array();
        $column_names = array();
        $cols = array();

        if (!empty($f->column_names_list)) {
            $cols = explode('|!|', $f->column_names_list);
        }
        $fc = new forms_control();
        foreach ($fc->find('all', 'forms_id=' . $f->id . ' and is_readonly=0','rank') as $control) {
            $ctl = unserialize($control->data);
            $control_type = get_class($ctl);
            $def = call_user_func(array($control_type, 'getFieldDefinition'));
            if ($def != null) {
                $fields[$control->name] = $control->caption;
                if (in_array($control->name, $cols)) {
                    $column_names[$control->name] = $control->caption;
                }
            }
        }
        $fields['ip'] = gt('IP Address');
        if (in_array('ip', $cols)) $column_names['ip'] = gt('IP Address');

        if (isset($field['field_user_id']))
            $fields['user_id'] = $field['field_user_id'];

        if (in_array('user_id', $cols)) $column_names['user_id'] = gt('Posted by');
        $fields['timestamp'] = gt('Timestamp');
        if (in_array('timestamp', $cols)) $column_names['timestamp'] = gt('Timestamp');

        if (!empty($this->params['copy'])) {
            $f->old_id = $f->id;
            $f->id = null;
            $f->sef_url = null;
            $f->is_saved = false;
            $f->table_name = null;
        }

        assign_to_template(array(
            'column_names' => $column_names,
            'fields'       => $fields,
            'form'         => $f,
        ));
    }

    /**
     * Updates the form
     */
    public function update_form() {
        $this->forms->update($this->params);
        if (!empty($this->params['old_id'])) {
            // copy all the controls to the new form
            $fc = new forms_control();
            $controls = $fc->find('all','forms_id='.$this->params['old_id'],'rank');
            foreach ($controls as $control) {
                $control->id = null;
                $control->forms_id = $this->forms->id;
                $control->update();
            }
        }
        if (!empty($this->params['is_saved']) && empty($this->params['table_name'])) {
            // we are now saving data to the database and need to create it first
//            $form = new forms($this->params['id']);
            $this->params['table_name'] = $this->forms->updateTable();
//            $this->params['_validate'] = false;  // we don't want a check for unique sef_name
//            parent::update();  // now with a form tablename
        }
    }

    public function delete_form() {
        global $db;

        expHistory::set('editable', $this->params);
        $modelname = $this->basemodel_name;
        if (empty($this->params['id'])) {
            flash('error', gt('Missing id for the') . ' ' . $modelname . ' ' . gt('you would like to delete'));
            expHistory::back();
        }
        $form = new $modelname($this->params['id']);

        $db->delete("forms_control", "forms_id=" . $form->id);
        if ($form->is_saved == 1) {
            $db->dropTable("forms_" . $form->table_name);
        }

        $form->delete();
        expHistory::back();
    }

    public function design_form() {
        if (!empty($this->params['id'])) {
            expHistory::set('editable', $this->params);
            $f = new forms($this->params['id']);
            $controls = $f->forms_control;

            $form = new fakeform();
            foreach ($controls as $c) {
                $ctl = unserialize($c->data);
                $ctl->_id = $c->id;
                $ctl->_readonly = $c->is_readonly;
                $ctl->_controltype = get_class($ctl);
                $form->register($c->name, $c->caption, $ctl);
            }

            $types = expTemplate::listControlTypes();
            $types[".break"] = gt('Spacer');
            $types[".line"] = gt('Horizontal Line');
            uasort($types, "strnatcmp");
            array_unshift($types, '[' . gt('Please Select' . ']'));

            $forms_list = array();
            $forms = $f->find('all', 1);
            if (!empty($forms)) foreach ($forms as $frm) {
                if ($frm->id != $f->id) $forms_list[$frm->id] = $frm->title;
            }

            assign_to_template(array(
                'form'       => $f,
                'forms_list' => $forms_list,
                'form_html'  => $form->toHTML($f->id),
                'backlink'   => expHistory::getLastNotEditable(),
                'types'      => $types,
            ));
        }
    }

    public function edit_control() {
        $f = new forms($this->params['forms_id']);
        if ($f) {
            expCSS::pushToHead(array(
                    "unique"  => "forms",
                    "corecss" => "forms",
                )
            );

            if (isset($this->params['control_type']) && $this->params['control_type']{0} == ".") {
                // there is nothing to edit for these type controls, so add it then return
                $htmlctl = new htmlcontrol();
                $htmlctl->identifier = uniqid("");
                $htmlctl->caption = "";
                switch ($this->params['control_type']) {
                    case ".break":
                        $htmlctl->html = "<br />";
                        break;
                    case ".line":
                        $htmlctl->html = "<hr size='1' />";
                        break;
                }
                $ctl = new forms_control();
                $ctl->name = uniqid("");
                $ctl->caption = "";
                $ctl->data = serialize($htmlctl);
                $ctl->forms_id = $f->id;
                $ctl->is_readonly = 1;
                $ctl->update();
                expHistory::returnTo('editable');
            } else {
                $control_type = "";
                $ctl = null;
                if (isset($this->params['id'])) {
                    $control = new forms_control($this->params['id']);
                    if ($control) {
                        $ctl = unserialize($control->data);
                        $ctl->identifier = $control->name;
                        $ctl->caption = $control->caption;
                        $ctl->id = $control->id;
                        $control_type = get_class($ctl);
                        $f->id = $control->forms_id;
                    }
                }
                if ($control_type == "") $control_type = $this->params['control_type'];
                $form = call_user_func(array($control_type, "form"), $ctl);
                $form->location($this->loc);
                if ($ctl) {
                    $form->controls['identifier']->disabled = true;
                    $form->meta("id", $ctl->id);
                    $form->meta("identifier", $ctl->identifier);
                }
                $form->meta("action", "save_control");
                $form->meta('control_type', $control_type);
                $form->meta('forms_id', $f->id);
                $types = expTemplate::listControlTypes();
                assign_to_template(array(
                    'form_html' => $form->toHTML($f->id),
                    'type'      => $types[$control_type],
                    'is_edit'   => ($ctl == null ? 0 : 1),
                ));
            }
        }
    }

    public function save_control() {
        global $db;

        $f = new forms($this->params['forms_id']);
        if ($f) {
            $ctl = null;
            $control = null;
            // get previous data from existing control
            if (isset($this->params['id'])) {
                $control = new forms_control($this->params['id']);
                if ($control) {
                    $ctl = unserialize($control->data);
                    $ctl->identifier = $control->name;
                    $ctl->caption = $control->caption;
                }
            } else {
                $control = new forms_control();
            }

            // update control with data from form
            if (call_user_func(array($this->params['control_type'], 'useGeneric')) == true) {
                $ctl = call_user_func(array('genericcontrol', 'update'), $this->params, $ctl);
            } else {
                $ctl = call_user_func(array($this->params['control_type'], 'update'), $this->params, $ctl);
            }

            //lets make sure the name submitted by the user is not a duplicate. if so we will fail back to the form
            if (!empty($control->id)) {
                //FIXME change this to an expValidator call
                $check = $db->selectObject('forms_control', 'name="' . $ctl->identifier . '" AND forms_id=' . $f->id . ' AND id != ' . $control->id);
                if (!empty($check) && empty($this->params['id'])) {
                    //expValidator::failAndReturnToForm(gt('A field with the same name already exists for this form'), $_$this->params
                    flash('error', gt('A field by the name")." "' . $ctl->identifier . '" ".gt("already exists on this form'));
                    expHistory::returnTo('editable');
                }
            }

            if ($ctl != null) {
                $name = substr(preg_replace('/[^A-Za-z0-9]/', '_', $ctl->identifier), 0, 20);
                if (!isset($this->params['id']) && $db->countObjects('forms_control', "name='" . $name . "' and forms_id=" . $this->params['forms_id']) > 0) {
                    $post = $this->params;
                    $post['_formError'] = gt('Identifier must be unique.');
                    expSession::set('last_POST', $post);
                } elseif ($name == 'id' || $name == 'ip' || $name == 'user_id' || $name == 'timestamp') {
                    $post = $this->params;
                    $post['_formError'] = sprintf(gt('Identifier cannot be "%s".'), $name);
                    expSession::set('last_POST', $post);
                } else {
                    if (!isset($this->params['id'])) {
                        $control->name = $name;
                    }
                    $control->caption = $ctl->caption;
                    $control->forms_id = $this->params['forms_id'];
                    $control->is_static = (!empty($ctl->is_static) ? $ctl->is_static : 0);
                    $control->data = serialize($ctl);

                    if (!empty($control->id)) {
                        $control->update();
                    } else {
                        $control->update();
                        // reset summary report to all columns
                        if (!$control->is_static) {
                            $f->column_names_list = null;
                            $f->update();
                            //FIXME we also need to update any config column_names_list settings?
                        }
                    }
                    $f->updateTable();
                }
            }
        }
        expHistory::returnTo('editable');
    }

    public function delete_control() {
        $ctl = null;
        if (isset($this->params['id'])) {
            $ctl = new forms_control($this->params['id']);
        }

        if ($ctl) {
            $f = new forms($ctl->forms_id);
            $ctl->delete();
            $f->updateTable();
            expHistory::returnTo('editable');
        }
    }

    function configure() {
        $fields = array();
        $column_names = array();
        $cols = array();
//        $forms_list = array();
//        $forms = $this->forms->find('all', 1);
//        if (!empty($forms)) foreach ($forms as $form) {
//            $forms_list[$form->id] = $form->title;
//        } else {
//            $forms_list[0] = gt('You must select a form1');
//        }
        if (!empty($this->config['column_names_list'])) {
            $cols = $this->config['column_names_list'];
        }
        if (isset($this->config['forms_id'])) {
            $fc = new forms_control();
            foreach ($fc->find('all', 'forms_id=' . $this->config['forms_id'] . ' and is_readonly=0','rank') as $control) {
                $ctl = unserialize($control->data);
                $control_type = get_class($ctl);
                $def = call_user_func(array($control_type, 'getFieldDefinition'));
                if ($def != null) {
                    $fields[$control->name] = $control->caption;
                    if (in_array($control->name, $cols)) {
                        $column_names[$control->name] = $control->caption;
                    }
                }
            }
            $fields['ip'] = gt('IP Address');
            if (in_array('ip', $cols)) $column_names['ip'] = gt('IP Address');

            if (isset($field['field_user_id']))
                $fields['user_id'] = $field['field_user_id'];

            if (in_array('user_id', $cols)) $column_names['user_id'] = gt('Posted by');
            $fields['timestamp'] = gt('Timestamp');
            if (in_array('timestamp', $cols)) $column_names['timestamp'] = gt('Timestamp');
        }
        $title = gt('No Form Assigned Yet!');
        if (!empty($this->config['forms_id'])) {
            $form = $this->forms->find('first', 'id=' . $this->config['forms_id']);
            $this->config['is_saved'] = $form->is_saved;
            $this->config['table_name'] = $form->table_name;
            $title = $form->title;
        }
        assign_to_template(array(
//            'forms_list'   => $forms_list,
            'form_title'   => $title,
            'column_names' => $column_names,
            'fields'       => $fields,
        ));

        parent::configure();
    }

    /**
     * create a new default config array using the form defaults
     */
    private function get_defaults($form) {
        if (empty($this->config)) { // NEVER overwrite an existing config
            $this->config = array();
            $config = get_object_vars($form);
            unset ($config['forms_control']);
            $this->config = $config;
        }
    }

    /**
     * get the metainfo for this module
     *
     * @return array
     */
    function metainfo() {
        global $router;

        if (empty($router->params['action'])) return false;

        // figure out what metadata to pass back based on the action we are in.
        switch ($router->params['action']) {
            case 'showall':
                $metainfo = array(
                    'title'       => gt("Showing all Form Records"),
                    'keywords'    => SITE_KEYWORDS,
                    'description' => SITE_DESCRIPTION
                );
                break;
            case 'show':
                $metainfo = array(
                    'title'       => gt("Showing Form Record"),
                    'keywords'    => SITE_KEYWORDS,
                    'description' => SITE_DESCRIPTION
                );
                break;
            default:
                $metainfo = parent::metainfo();
        }
        return $metainfo;
    }

    public function export_csv() {
        global $db;

        if (!empty($this->params['id'])) {
            $f = new forms($this->params['id']);
            $items = $db->selectObjects("forms_" . $f->table_name);

            $fc = new forms_control();
            if ($f->column_names_list == '') {
                //define some default columns...
                $controls = $fc->find('all', "forms_id=" . $f->id . " and is_readonly = 0 and is_static = 0", "rank");
                foreach (array_slice($controls, 0, 5) as $control) {
                    if ($f->column_names_list != '') $f->column_names_list .= '|!|';
                    $f->column_names_list .= $control->name;
                }
            }

            $rpt_columns = explode("|!|", $f->column_names_list);
            foreach ($rpt_columns as $column_name) {
                if ($column_name == "ip") {
                } elseif ($column_name == "user_id") {
                    foreach ($items as $key => $item) {
                        if ($item->$column_name != 0) {
                            $locUser = user::getUserById($item->$column_name);
                            $item->$column_name = $locUser->username;
                        } else {
                            $item->$column_name = '';
                        }
                        $items[$key] = $item;
                    }
                } elseif ($column_name == "timestamp") {
//                    $srt = $column_name . "_srt";
                    foreach ($items as $key => $item) {
//                        $item->$srt = $item->$column_name;
                        $item->$column_name = strftime(DISPLAY_DATETIME_FORMAT, $item->$column_name);
                        $items[$key] = $item;
                    }
                } else {
                    $control = $fc->find('first', "name='" . $column_name . "' and forms_id=" . $this->params['id'],'rank');
                    if ($control) {
                        $ctl = unserialize($control->data);
                        $control_type = get_class($ctl);
//                        $srt = $column_name . "_srt";
//                        $datadef = call_user_func(array($control_type, 'getFieldDefinition'));
                        foreach ($items as $key => $item) {
                            //We have to add special sorting for date time columns!!!
//                            if (isset($datadef[DB_FIELD_TYPE]) && $datadef[DB_FIELD_TYPE] == DB_DEF_TIMESTAMP) {
//                                $item->$srt = $item->$column_name;
//                            }
                            $item->$column_name = call_user_func(array($control_type, 'templateFormat'), $item->$column_name, $ctl);
                            $items[$key] = $item;
                        }
                    }
                }
            }

            if (LANG_CHARSET == 'UTF-8') {
                $file = chr(0xEF) . chr(0xBB) . chr(0xBF); // add utf-8 signature to file to open appropriately in Excel, etc...
            } else {
                $file = "";
            }

            $file .= self::sql2csv($items, $rpt_columns);

            // CREATE A TEMP FILE
            $tmpfname = tempnam(getcwd(), "rep"); // Rig

            $handle = fopen($tmpfname, "w");
            fwrite($handle, $file);
            fclose($handle);

            if (file_exists($tmpfname)) {

                ob_end_clean();

                // This code was lifted from phpMyAdmin, but this is Open Source, right?
                // 'application/octet-stream' is the registered IANA type but
                //        MSIE and Opera seems to prefer 'application/octetstream'
                // It seems that other headers I've added make IE prefer octet-stream again. - RAM

                $mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octet-stream;' : 'text/comma-separated-values;';
                header('Content-Type: ' . $mime_type . ' charset=' . LANG_CHARSET . "'");
                header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                $filesize = filesize($tmpfname);
                header("Content-length: " . $filesize);
                header('Content-Transfer-Encoding: binary');
//                header('Content-Encoding:');
                header('Content-Disposition: attachment; filename="report.csv"');
                if ($filesize) header("Content-length: " . $filesize); // for some reason the webserver cant run stat on the files and this breaks.
                // IE need specific headers
                if (EXPONENT_USER_BROWSER == 'IE') {
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Vary: User-Agent');
                } else {
                    header('Pragma: no-cache');
                }
                //Read the file out directly
                readfile($tmpfname);

                if (DEVELOPMENT == 0) exit();
                unlink($tmpfname);

            } else {
                error_log("error file doesn't exist", 0);
            }
        }
//        expHistory::back();
    }

    /**
     * This converts the sql statement into a nice CSV.
     * We grab the items array which is stored funkily in the DB in an associative array when we pull it.
     * So basically our aray looks like this:
     *
     * ITEMS
     * {[id]=>myID, [Name]=>name, [Address]=>myaddr}
     * {[id]=>myID1, [Name]=>name1, [Address]=>myaddr1}
     * {[id]=>myID2, [Name]=>name2, [Address]=>myaddr2}
     * {[id]=>myID3, [Name]=>name3, [Address]=>myaddr3}
     * {[id]=>myID4, [Name]=>name4, [Address]=>myaddr4}
     * {[id]=>myID5, [Name]=>name5, [Address]=>myaddr5}
     *
     * So by nature of the array, the keys are repetated in each line (id, name, etc)
     * So if we want to make a header row, we just run through once at the beginning and
     * use the array_keys function to strip out a functional header
     *
     * @param      $items
     *
     * @param null $rptcols
     *
     * @return string
     */
    public static function  sql2csv($items, $rptcols = null) {
        $str = "";
        foreach ($items as $key => $item) {
            if ($str == "") {
                $header_Keys = array_keys((array)$item);
                foreach ($header_Keys as $individual_Header) {
                    if (!is_array($rptcols) || in_array($individual_Header, $rptcols)) $str .= $individual_Header . ",";
                }
                $str .= "\r\n";
            }
            foreach ($item as $key => $rowitem) {
                if (!is_array($rptcols) || in_array($key, $rptcols)) {
                    $rowitem = str_replace(",", " ", $rowitem);
                    $str .= $rowitem . ",";
                }
            } //foreach rowitem
            $str = substr($str, 0, strlen($str) - 1);
            $str .= "\r\n";
        } //end of foreach loop
        return $str;
    }

}

?>