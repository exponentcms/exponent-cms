<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
    protected $add_permissions = array(
        'viewdata'  => "View Data",
        'enter_data' => "Enter Data",  // slight naming variation to not fully restrict enterdata method
    );
    protected $manage_permissions = array(
        'design' => 'Design Form',
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
//        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)
//    public $codequality = 'beta';

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
        return true; // implemented v2.7.0
    }

    function searchName() {
        return gt("Forms");
    }

    function searchCategory() {
        return gt('Form Data');
    }

    static function requiresConfiguration()
    {
        return true;
    }

    /**
     * Display Report
     *
     * @return void
     */
    public function showall() {
        global $db;

        expHistory::set('viewable', $this->params);
        $f = null;
        if (!empty($this->config)) {
            $f = $this->forms->find('first', 'id=' . $this->config['forms_id']);
        } elseif (!empty($this->params['title'])) {
            $f = $this->forms->find('first', 'sef_url=\'' . expString::escape($this->params['title']) . '\'');
            if (!empty($f))
                $this->get_defaults($f);
        } elseif (!empty($this->params['id'])) {
            $f = $this->forms->find('first', 'id=' . $this->params['id']);
            if (!empty($f))
                $this->get_defaults($f);
        }

        if ((!empty($this->config['unrestrict_view']) || expPermissions::check('viewdata', $this->loc))) {
            if (isset($this->config['order_dropdown_all'])) {
                $all_text = $this->config['order_dropdown_all'];
            } else {
                $all_text = gettext('(All)');
            }

            if (!empty($f)) {
                if (!empty($this->params['filter'])) {
                    if (is_numeric($this->params['filter'])) {
                        $where = expString::escape($this->params['filter']);
                    } elseif ($this->params['filter'] === $all_text) {
                        $where = '1';
                    } else {
                        $where = $this->config['order'] . "='" . expString::escape($this->params['filter']) . "'";
                    }
//                    if (!empty($this->config['report_filter'])) {
//                        $where = $this->config['report_filter'] . ' AND ' . $where;
//                    }
                } elseif (!empty($this->config['report_filter'])) {
                    $where = $this->config['report_filter'];
                } else {
                    $where = '1';
                }
                $fc = new forms_control();
                // account for portfolio view & column list
                $controls = $fc->find('all', 'forms_id=' . $f->id . ' AND is_readonly=0 AND is_static = 0', 'rank');
                if (isset($this->params['view']) && $this->params['view'] === 'showall_portfolio') {
                    $this->config['column_names_list'] = array();
                    foreach ($controls as $control) {  // we need to output all columns for portfolio view
                        $this->config['column_names_list'][] = $control->name;
                    }
                } elseif (empty($this->config['column_names_list'])) {
                    $this->config['column_names_list'] = array();
                    //define some default columns...
                    foreach (array_slice($controls, 0, 5) as $control) {  // default to only first 5 columns
                        $this->config['column_names_list'][] = $control->name;
                    }
                }

                // pre-process records
                if (!empty($this->config['order']))
                    $where .= ' ORDER BY ' . $this->config['order'];
                $items = $f->selectRecordsArray($where);
                // resort the items if using a second column to sort
                if (!empty($this->config['order2'])) {
//                    usort($items, function ($a, $b): int {
//                        if ($a[$this->config['order']] === $b[$this->config['order']]) {
//                            return $a[$this->config['order2']] <=> $b[$this->config['order2']];
//                        }
//                        return $a[$this->config['order']] <=> $b[$this->config['order']];
//                    });
                    usort($items, array("formsController", "sortOrder"));
                    if (!empty($this->config['dir2'])) {
                        $items = array_reverse($items);
                    }
                }
                $columns = array();
                foreach ($this->config['column_names_list'] as $column_name) {
                    if ($column_name === "ip") {
                        $columns['ip'] = gt('IP Address');
                    } elseif ($column_name === "sef_url") {
                        $columns['sef_url'] = gt('SEF URL');
                    } elseif ($column_name === "referrer") {
                        $columns['referrer'] = gt('Referrer');
                    } elseif ($column_name === "location_data") {
                        $columns['location_data'] = gt('Entry Point');
                    } elseif ($column_name === "user_id") {
                        foreach ($items as $key => $item) {
                            if ($item[$column_name] != 0) {
                                $locUser = user::getUserById($item[$column_name]);
                                $item[$column_name] = $locUser->username;
                            } else {
                                $item[$column_name] = '';
                            }
                            $items[$key] = $item;
                        }
                        $columns['user_id'] = gt('Posted by');
                    } elseif ($column_name === "timestamp") {
                        foreach ($items as $key => $item) {
                            $item[$column_name] = date(strftime_to_date_format(DISPLAY_DATETIME_FORMAT), $item[$column_name]);
                            $items[$key] = $item;
                        }
                        $columns['timestamp'] = gt('Timestamp');
                    } else {
                        $control = $fc->find('first', "name='" . $column_name . "' AND forms_id=" . $f->id, 'rank');
                        if ($control) {
                            $ctl = expUnserialize($control->data);
                            $control_type = get_class($ctl);
                            foreach ($items as $key => $item) {
                                //We have to add special sorting for date time columns!!!
                                $item[$column_name] = @call_user_func(
                                    array($control_type, 'templateFormat'),
                                    $item[$column_name],
                                    $ctl
                                );
                                $items[$key] = $item;
                            }
//                            $columns[$control->caption] = $column_name;
                            $columns[$column_name] = $control->caption;
                        }
                    }
                }
                // create a show link
                foreach ($items as $key => $item) {
                    $items[$key]['link'] = makeLink(array(
                        'controller'=>'forms',
                        'action'=>'show',
//                        'forms_id'=>$f->id,
                        'title'=>$f->sef_url,
//                        'id'=>$items[$key]['id'],
                        'item'=>$items[$key]['sef_url'],
                        'src'=>$this->loc->src
                    ));
                }

                $limit = (isset($this->params['limit']) && $this->params['limit'] != '') ? $this->params['limit'] : 10;
                if (empty($this->params['view']))
                    $this->params['view'] = null;
                if ($this->params['view'] !== 'showall_portfolio')
                    $limit = 0;

                $page = new expPaginator(
                    array(
                        'records' => $items,
                        'where' => 1,
                        'limit'   => $limit,
//                        'order' => (isset($this->params['order']) && $this->params['order'] != '') ? $this->params['order'] : (!empty($this->config['order']) ? $this->config['order'] : 'id'),
//                        'dir' => (isset($this->params['dir']) && $this->params['dir'] != '') ? $this->params['dir'] : (!empty($this->config['dir']) ? $this->config['dir'] : 'ASC'),
                        'dontsort' => true,  // we've already sorted them
                        'page' => (isset($this->params['page']) ? $this->params['page'] : 1),
                        'controller' => $this->baseclassname,
                        'action' => $this->params['action'],
                        'src' => $this->loc->src,
                        'columns' => $columns,
                        'view' => $this->params['view']
                    )
                );

                if (isset($this->config['order'])) {
                    $list = $db->selectColumn('forms_' . $f->table_name, $this->config['order']);
                    $list = array_unique($list);
                    sort($list);
                    assign_to_template(
                        array(
                            "list" => array_merge(array('-1'=>$all_text), $list),
                        )
                    );
                }
                if (isset($this->params['filter'])) {
                    $selected = $this->params['filter'];
                } else {
                    $selected = $all_text;
                }
                assign_to_template(
                    array(
//                "backlink"    => expHistory::getLastNotEditable(),
                        "backlink" => expHistory::getLast('viewable'),
                        "f" => $f,
                        "page" => $page,
                        "title" => !empty($this->config['report_name']) ? $this->config['report_name'] : '',
                        "description" => !empty($this->config['report_desc']) ? $this->config['report_desc'] : null,
                        "filtered" => !empty($this->config['report_filter']) ? $this->config['report_filter'] : '',
                        "count" => $f->countRecords(),
                        "config" => $this->config,
                        "selected" => $selected,
//                        "all_text" => $all_text
                    )
                );
            }
        } else {
            assign_to_template(array(
                "error" => 1,
            ));
        }
    }

    function sortOrder($a, $b) {
        if ($a[$this->config['order']] === $b[$this->config['order']]) {
//            return $a[$this->config['order2']] <=> $b[$this->config['order2']];
            if ($a[$this->config['order2']] < $b[$this->config['order2']])
                return -1;
            else if ($a[$this->config['order2']] > $b[$this->config['order2']])
                return 1;
            else if ($a[$this->config['order2']] == $b[$this->config['order2']])
                return 0;
        }
//        return $a[$this->config['order']] <=> $b[$this->config['order']];
        if ($a[$this->config['order']] < $b[$this->config['order']])
            return -1;
        else if ($a[$this->config['order']] > $b[$this->config['order']])
            return 1;
        else if ($a[$this->config['order']] == $b[$this->config['order']])
            return 0;

    }

    /**
     * Display Record
     *
     * @return void
     */
    public function show() {
        expHistory::set('viewable', $this->params);
        $f = null;
        if (!empty($this->config)) {
            $f = $this->forms->find('first', 'id=' . $this->config['forms_id']);
        } elseif (!empty($this->params['forms_id'])) {
            $f = $this->forms->find('first', 'id=' . $this->params['forms_id']);
            if (!empty($f))
                $this->get_defaults($f);
        } elseif (!empty($this->params['title'])) {
            $f = $this->forms->find('first', 'sef_url=\'' . expString::escape($this->params['title']) . '\'');
            if (!empty($f))
                $this->get_defaults($f);
//                if (!empty($f))
//                    redirect_to(array('controller' => 'forms', 'action' => 'enterdata', 'forms_id' => $f->id));
        }

        if (!empty($this->config['unrestrict_view']) || expPermissions::check('viewdata', $this->loc)) {
            if (!empty($f)) {
                $fc = new forms_control();
                $controls = $fc->find('all', 'forms_id=' . $f->id . ' AND is_readonly=0 AND is_static = 0', 'rank');
                $id = !empty($this->params['id']) ? $this->params['id'] : null;
                if (!empty($this->params['item']))
                    $id = 'sef_url="' . expString::escape($this->params['item']) . '"';
                $data = $f->getRecord($id);

                $fields = array();
                $captions = array();
                if ($controls && $data) {
                    foreach ($controls as $c) {
                        $ctl = expUnserialize($c->data);
                        $control_type = get_class($ctl);
                        $name = $c->name;
                        $fields[$name] = call_user_func(array($control_type, 'templateFormat'), $data->$name, $ctl);
                        $captions[$name] = $c->caption;
                    }

                    // system added fields
                    $captions['ip'] = gt('IP Address');
                    $captions['sef_url'] = gt('SEF URL');
                    $captions['timestamp'] = gt('Timestamp');
                    $captions['user_id'] = gt('Posted by');
                    $fields['ip'] = $data->ip;
                    $fields['sef_url'] = $data->sef_url;
                    $fields['timestamp'] = date(strftime_to_date_format(DISPLAY_DATETIME_FORMAT), $data->timestamp);
                    $locUser = user::getUserById($data->user_id);
                    $fields['user_id'] = !empty($locUser->username) ? $locUser->username : '';

                    // add a browse other records (next/prev) feature here
                    $field = !empty($this->config['order']) ? $this->config['order'] : 'id';
                    $data->next = $f->getRecord($field . ' > ' . $data->$field . ' ORDER BY ' . $field);
                    if (!empty($data->next) && $data->next != $data->id) {
                        assign_to_template(
                            array(
                                "next" => $data->next,
                            )
                        );
                    }
                    $data->prev = $f->getRecord($field . ' < ' . $data->$field . ' ORDER BY ' . $field . ' DESC');
                    if (!empty($data->prev) && $data->prev != $data->id) {
                        assign_to_template(
                            array(
                                "prev" => $data->prev,
                            )
                        );
                    }
                }

                assign_to_template(
                    array(
                        //            "backlink"=>expHistory::getLastNotEditable(),
    //                'backlink'    => expHistory::getLast('editable'),
                        'backlink' => makeLink(expHistory::getBack(1)),
                        "f" => $f,
    //                "record_id"   => $this->params['id'],
                        "record_id" => !empty($data->id) ? $data->id : null,
                        "title" => !empty($this->config['report_name']) ? $this->config['report_name'] : gt(
                            'Viewing Record'
                        ),
                        "description" => !empty($this->config['report_desc']) ? $this->config['report_desc'] : null,
                        'fields' => $fields,
                        'captions' => $captions,
                        "count"       => $f->countRecords(),
                        'is_email' => 0,
                        "css" => file_get_contents(BASE . "framework/core/assets/css/tables.css"),
                        "config" => $this->config,
                    )
                );
            }
        } else {
            assign_to_template(array(
                "error" => 1,
            ));
        }
    }

    /**
     * Old entry point
     * @deprecated
     *
     * @return void
     */
    public function enter_data() {
        $this->enterdata();
    }

    /**
     * Display Form
     *
     * @return void
     */
    public function enterdata() {
        if (empty($this->config['restrict_enter']) || expPermissions::check('enter_data', $this->loc)) {

            global $user;

            expHistory::set('editable', $this->params);
            $f = null;
            if (!empty($this->config)) {
                $f = $this->forms->find('first', 'id=' . $this->config['forms_id']);
            } elseif (!empty($this->params['forms_id'])) {
                $f = $this->forms->find('first', 'id=' . $this->params['forms_id']);
                if (!empty($f))
                    $this->get_defaults($f);
            }

            if (!empty($f)) {
                $form = new form();
                $form->id = $f->sef_url;
                $form->horizontal = !empty($this->config['style']);
                if (!empty($this->params['id'])) {
                    $fc = new forms_control();
                    $controls = $fc->find('all', 'forms_id=' . $f->id . ' AND is_readonly = 0 AND is_static = 0','rank');
                    $data = $f->getRecord($this->params['id']);
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
                    if (empty($this->config['select_exclude_all']))
                        $emaillist[0] = gt('All Addresses');
                    $emaillist = array_reverse($emaillist, true);
                    if (!empty($this->config['select_dropdown']))
                        $form->register('email_dest', gt('Send Response to'), new dropdowncontrol('', $emaillist));
                    else
                        $form->register('email_dest', gt('Send Response to'), new radiogroupcontrol('', $emaillist));
                }
//                $paged = false;
                foreach ($controls as $key=>$c) {
                    $ctl = expUnserialize($c->data);
                    $ctl->_id = $c->id;
                    $ctl->_readonly = $c->is_readonly;
                    $ctl->_ishidden = !empty($ctl->is_hidden) && empty($this->params['id']);  // hide it if entering new data
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
                    if ($key == 0) $ctl->focus = true;  // first control gets the focus
                    $form->register($c->name, $c->caption, $ctl);
//                    if (get_class($ctl) == 'pagecontrol') $paged = true;
                }
                if (!empty($data->sef_url))
                    $form->register("sef_url", gt('SEF URL'), new textcontrol($data->sef_url));

                // if we are editing an existing record or doing quick submission we'll need to do recaptcha here since we won't call confirm_data
                if (!empty($this->params['id']) || !empty($this->config['quick_submit'])) {
                    $antispam = '';
                    if (SITE_USE_ANTI_SPAM && (ANTI_SPAM_CONTROL === 'recaptcha' || ANTI_SPAM_CONTROL === 'recaptcha_v2')) {
                        // make sure we have the proper config.
                        if (!defined('RECAPTCHA_PUB_KEY')) {
                            $antispam .= '<h2 style="color:red">' . gt('reCaptcha configuration is missing the public key.') . '</h2>';
                        }
                        if (!($user->isLoggedIn() && ANTI_SPAM_USERS_SKIP == 1)) {
                            // skip it for logged on users based on config
                            $re_theme = (RECAPTCHA_THEME === 'dark') ? 'dark' : 'light';
                            // show the form control
                            $antispam .= '<input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">';
                            if (ANTI_SPAM_CONTROL === 'recaptcha') { // reCaptcha v2 Checkbox
                                //create unique recaptcha blocks
                                $randomNumber = mt_rand(10000000, 99999999);
                                $antispam .= '<div class="g-recaptcha" id="recaptcha-block-' . $randomNumber . '" data-sitekey="' . RECAPTCHA_PUB_KEY . '" data-theme="' . $re_theme . '"></div>';
                                $antispam .= '<p>' . gt('Fill out the above security question to submit your form.') . '</p>';
                                $content = "
                                var captcha;
                                var grecaptcha_onload = function() {
                                    var recaptchas = document.querySelectorAll('div[id^=recaptcha-block-]');
                                    for (i = 0; i < recaptchas.length; i++) {
                                        captcha = grecaptcha.render(recaptchas[i].id, {
                                          'sitekey' : '" . RECAPTCHA_PUB_KEY . "',
                                          'theme'   : '" . $re_theme . "'
                                        });
                                    }
                                };";
                            } else {  // reCaptcha v2 Invisible
                                // need an input field to return token
                                $antispam .= '<div class="g-recaptcha" name="g-recaptcha-response"></div>';
                                // we do explicit loading to allow for multiple recaptcha widgets on a page
                                $content = "
                                var grecaptcha_onload = function() {
                                    [].forEach.call(document.querySelectorAll('.g-recaptcha'), function(el){
                                        $(el).parents('form').find(':submit').attr('disabled', true);
                                        var renderCaptcha = grecaptcha.render(el, {
                                            sitekey: '" . RECAPTCHA_PUB_KEY . "',
                                            callback: function(token) {
                                                el.parentNode.querySelector('.g-recaptcha').value = token;
                                                console.log('success! ' + token);
                                                $(el).parents('form').find(':submit').removeAttr('disabled');
                                            },
                                            size: 'invisible',
//                                            badge: 'inline'
                                        }, true);
                                        grecaptcha.execute(renderCaptcha);
                                        $(el).parents('form').on('submit',function(e) {
                                            grecaptcha.execute(renderCaptcha);
                                        });
                                    });
                                }";
                            }
                            expJavascript::pushToFoot(array(
                                "unique" => 'recaptcha',
                                "content" => $content,
                                "src" => "https://www.google.com/recaptcha/api.js?onload=grecaptcha_onload&render=explicit&hl=" . LOCALE
                            ));
                            $form->register(uniqid(''), '', new htmlcontrol($antispam));
                        }
                    }
                }

//                if (empty($this->config['submitbtn'])) $this->config['submitbtn'] = gt('Submit');
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
                $form->register("submit", "", new buttongroupcontrol($this->config['submitbtn'], $this->config['resetbtn'], $cancel, 'finish'));

//                $form->meta("m", $this->loc->mod);
//                $form->meta("s", $this->loc->src);
//                $form->meta("i", $this->loc->int);
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
                if ($formmsg) {
                    flash('notice', $formmsg);
                }
                if (empty($this->config['description'])) $this->config['description'] = '';
                assign_to_template(array(
                    "description" => $this->config['description'],
                    "form_html"   => $form->toHTML(),
                    "form"        => $f,
                    "count"       => $f->countRecords(),
//                    'paged'       => $paged,
                ));
            }
        } else {
            assign_to_template(array(
                "error" => 1,
            ));
        }
    }

    /**
     * Form data is parsed and displayed for confirmation before entering into database and/pr sending email
     *  used for only initial entry
     */
    public function confirm_data() {
        $f = new forms($this->params['id']);
        $cols = $f->forms_control;
        $responses = array();
        $captions = array();

        foreach ($cols as $col) {
            $newupload = false;
            $coldef = expUnserialize($col->data);
            $coldata = new ReflectionClass($coldef);
            if (empty($coldef->is_hidden)) {
                $coltype = $coldata->getName();
                // first we clean up the entered data //fixme these should probably be FALSE!
                if ($coltype === 'uploadcontrol' && !empty($_FILES)) {
                    $newupload = true;
                    $value = call_user_func(array($coltype, 'parseData'), $col->name, $_FILES, true);  // this will move the new file
                } else {
                    $value = call_user_func(array($coltype, 'parseData'), $col->name, $this->params, true);
                }
                // then we format it for display
                $value = call_user_func(array($coltype, 'templateFormat'), $value, $coldef);  // convert parsed value to user readable
                //eDebug($value);
//                $counts[$col->caption] = isset($counts[$col->caption]) ? $counts[$col->caption] + 1 : 1;
//                $num = $counts[$col->caption] > 1 ? $counts[$col->caption] : '';

                if (!empty($this->params[$col->name])) {
//                if ($coltype == 'checkboxcontrol') {
//                    $responses[$col->caption . $num] = gt('Yes');
//                } else {
//                    $responses[$col->caption . $num] = $value;
                    $responses[$col->name] = $value;
                    $captions[$col->name] = $col->caption;
//                }
                } else {
                    if ($coltype === 'checkboxcontrol') {
//                        $responses[$col->caption . $num] = gt('No');
                        $responses[$col->name] = gt('No');  // default for checkboxes is No
                        $captions[$col->name] = $col->caption;
                    } elseif ($coltype === 'datetimecontrol' || $coltype === 'calendarcontrol' || $coltype === 'popupdatetimecontrol') {
                        $responses[$col->name] = $value;  // allows for a default phrase for no date entered
                        $captions[$col->name] = $col->caption;
                    } elseif ($coltype === 'uploadcontrol') {
                        if ($newupload) {  //note: an uploaded file is returned in $_FILEs, NOT in the control name
                            $newfile = call_user_func(
                                    array($coltype, 'moveFile'),
                                    $col->name,
                                    $_FILES,
                                    true
                                );
                            if (!empty($newfile)) {
                                $this->params[$col->name] = PATH_RELATIVE . $newfile;
                            } else {
                                $this->params[$col->name] = "";
                            }
                        }
//                        $value = call_user_func(array($coltype,'buildDownloadLink'),$this->params[$col->name],$_FILES[$col->name]['name'],true);
                        //eDebug($value);
//                        $responses[$col->caption . $num] = $_FILES[$col->name]['name'];
//                        $responses[$col->name] = $_FILES[$col->name]['name'];
//                        $responses[$col->name] = $this->params[$col->name];
                        $responses[$col->name] = call_user_func(array($coltype, 'templateFormat'), $this->params[$col->name], null);  // convert parsed value to user readable
                        $captions[$col->name] = $col->caption;
                    } elseif ($coltype !== 'htmlcontrol' && $coltype !== 'pagecontrol') {
//                        $responses[$col->caption . $num] = '';
                        $responses[$col->name] = '';  // all other empty controls have empty value
                        $captions[$col->name] = $col->caption;
                    }
                }
            }
        }

        // remove some post data we don't want to pass thru to the form
        unset(
            $this->params['controller'],
            $this->params['action'],
            $this->params['view']
        );
        foreach ($this->params as $k => $v) {
        //    $this->params[$k]=htmlentities(htmlspecialchars($v,ENT_COMPAT,LANG_CHARSET));
            $this->params[$k] = htmlspecialchars($v, ENT_COMPAT, LANG_CHARSET);
        }
        expSession::set('forms_data_' . $this->params['id'], $this->params);

        if (!empty($this->config['quick_submit'])) {  // for quick submission skip to next step
            redirect_to(array('controller'=>'forms', 'action'=>'submit_data', 'skip'=>1, 'id'=>$f->id, 'src' => $this->loc->src));
        }

        assign_to_template(array(
            'responses'       => $responses,
            'captions'        => $captions,
            'postdata'        => $this->params,
        ));
    }

    /**
     * Form data is parsed and entered into database and/or email is sent
     *  used for both initial entry and editing records
     */
    public function submit_data() {
        if (!empty($this->params['skip'])) {
            $this->params = expSession::get('forms_data_' . $this->params['id']);
            $this->params['controller'] = 'forms';
        }

        // Check for form errors
        $this->params['manual_redirect'] = true;  //fixme is this desired effect or should we go back to the failed form?
        if (!expValidator::check_antispam($this->params)) {
            flash('error', gt('Security Validation Failed'));
            expHistory::back();
        }

        global $db, $user;
        $f = new forms($this->params['id']);
        $fc = new forms_control();
        $controls = $fc->find('all', "forms_id=" . $f->id . " AND is_readonly=0",'rank');
        $this->get_defaults($f);

        $db_data = new stdClass();
        $emailFields = array();
        $captions = array();
        $attachments = array();
        foreach ($controls as $c) {
            $ctl = expUnserialize($c->data);
            $control_type = get_class($ctl);
            $def = call_user_func(array($control_type, "getFieldDefinition"));
            if ($def != null) {
                $emailValue = htmlspecialchars_decode(call_user_func(array($control_type, 'parseData'), $c->name, $this->params, true));
//                if ($emailValue !== $this->params[$c->name])  //fixme should this be done, isn't data already parsed? only when editing an existing record
//                    eLog($emailValue.' : '.$this->params[$c->name], 'Mismatch');
                if ($ctl instanceof \texteditorcontrol) {
//                    $value = expString::escape($emailValue); //fixme does this need to occur later?
//                    $value = str_replace(array('\r\n', '\n', '\r'), array("\r\n", "\n", "\r"), $value);
                    $value = stripslashes($emailValue);  //fixme does this need to occur later?
                } elseif ($ctl instanceof \htmleditorcontrol) {
                    $value = stripslashes($emailValue);  //fixme does this need to occur later?
                    $value = str_replace(array("\r\n", "\n", "\r"), '',  $value);
                } else {
                    $value = stripslashes(expString::escape($emailValue));  //fixme does this need to occur later?
                }

                //eDebug($value);
                $varname = $c->name;
                $db_data->$varname = $value;
        //        $fields[$c->name] = call_user_func(array($control_type,'templateFormat'),$value,$ctl);
                if (!$ctl->is_hidden) {
                    $emailFields[$c->name] = call_user_func(array($control_type, 'templateFormat'), $value, $ctl);
                    $captions[$c->name] = $c->caption;
                    if (strtolower($c->name) === "email" && expValidator::isValidEmail($value)) {
                        $from = $value;
                    }
                    if (strtolower($c->name) === "name") {
                        $from_name = $value;
                    }
                    if ($ctl instanceof \uploadcontrol) {
                        $attachments[] = htmlspecialchars_decode($this->params[$c->name]);
                    }
                }
            }
        }

        if (!isset($this->params['data_id']) || (isset($this->params['data_id']) && expPermissions::check("editdata", $f->loc))) {
            if (!empty($f->is_saved)) {
                if (isset($this->params['data_id'])) {
                    //if this is an edit we remove the record and insert a new one, keeping some original data
//                    $olddata = $f->getRecord($this->params['data_id']);
//                    $db_data->ip = $olddata->ip;
//                    $db_data->user_id = $olddata->user_id;
//                    $db_data->timestamp = $olddata->timestamp;
//                    $db_data->referrer = $olddata->referrer;
//                    $db_data->location_data = $olddata->location_data;
//                    $f->deleteRecord($this->params['data_id']);  //fixme we delete old record/id to make this easier??
                    $db_data->id = $this->params['data_id'];
                    $db_data->sef_url = $this->params['sef_url'];
                    $f->updateRecord($db_data);
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
                    $db_data->location_data = serialize(expCore::makeLocation('forms', null, $this->id));
                    $this->params['data_id'] = $f->insertRecord($db_data);
                }
                if ($f->is_searchable) {
                    $this->forms = $f;
                    $this->addContentToSearch();
                }

//                if (empty($db_data->sef_url)) {
//                    $needles = array(
//                        'name',
//                        'title',
//                        'last',
//                        'first',
//                        'email'
//                    );
//                    $field = 'sef_url';
//                    foreach ($needles as $needle) {
//                        foreach ($this->params as $key => $value) {
//                            if (false !== stripos($key, $needle)) {
//                                $field = $key;
//                                break;
//                            }
//                        }
//                        if ($field !== 'sef_url')
//                            break;
//                    }
//                }
//                $db_data->sef_url = expCore::makeSefUrl($db_data->$field, $f->table_name);
//                $f->insertRecord($db_data);
            } else {
                $referrer = $db->selectValue("sessionticket", "referrer", "ticket = '" . expSession::getTicketString() . "'");
            }

            //Email stuff here...
            //Don't send email if this is an edit.
            if (!empty($this->config['is_email']) && !isset($this->params['data_id'])) {
                //Building Email List...
                $emaillist = array();
                if (!empty($this->config['select_email']) && !empty($this->params['email_dest'])) {
                    if ((string)((int)($this->params['email_dest'])) == (string)($this->params['email_dest'])) {
                        foreach (group::getUsersInGroup($this->params['email_dest']) as $locUser) {
                            if ($locUser->email != '') $emaillist[$locUser->email] = trim(user::getUserAttribution($locUser->id));
                        }
                    } else {
                        $emaillist[] = $this->params['email_dest'];
                    }
                } else { // send to all form addressee's
                    $emaillist = array();
                    if (!empty($this->config['user_list'])) foreach ($this->config['user_list'] as $c) {
                        $u = user::getUserById($c);
                        $emaillist[$u->email] = trim(user::getUserAttribution($u->id));
                    }
                    if (!empty($this->config['group_list'])) foreach ($this->config['group_list'] as $c) {
                        $grpusers = group::getUsersInGroup($c);
                        foreach ($grpusers as $u) {
                            $emaillist[$u->email] = trim(user::getUserAttribution($u->id));
                        }
                    }
                    if (!empty($this->config['address_list'])) foreach ($this->config['address_list'] as $c) {
                        $emaillist[] = $c;
                    }
                }
                //This is an easy way to remove duplicates
                $emaillist = array_flip(array_flip($emaillist));
                $emaillist = array_map('trim', $emaillist);

                if (empty($this->config['report_def'])) {
                    $msgtemplate = expTemplate::get_template_for_action($this, 'email/default_report', $this->loc);

                } else {
                    $msgtemplate = expTemplate::get_template_for_action($this, 'email/custom_report', $this->loc);
                    $msgtemplate->assign('template', $this->config['report_def']);
                }
                $msgtemplate->assign("fields", $emailFields);
                $msgtemplate->assign("captions", $captions);
                $msgtemplate->assign('title', $this->config['report_name']);
                $msgtemplate->assign("is_email", 1);
                if (!empty($referrer)) $msgtemplate->assign("referrer", $referrer);
//                $emailText = $msgtemplate->render();
//                $emailText = trim(strip_tags(str_replace(array("<br />", "<br>", "br/>"), "\n", $emailText)));
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
                            if (!empty($attachment)) {
                                if (strlen(PATH_RELATIVE) != 1)
                                    $attachment = expFile::fixName(str_replace(PATH_RELATIVE, '', $attachment));  // strip relative path for links coming from templates
                                if (file_exists(BASE . $attachment)) {
//                                $relpath = str_replace(PATH_RELATIVE, '', BASE);
//                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
//                            $ftype = finfo_file($finfo, $relpath . $attachment);
//                            finfo_close($finfo);
                                    $mail->attach_file_on_disk(BASE . $attachment, expFile::getMimeType(BASE . $attachment));
                                }
                            }
                        }
                    }
                    $mail->quickSend(array(
                        //	'headers'=>$headers,
                        'html_message' => $emailHtml,
//                        "text_message" => $emailText,
                        "text_message" => expString::html2text($emailHtml),
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
//                $headers = array(
//                    "MIME-Version" => "1.0",
//                    "Content-type" => "text/html; charset=" . LANG_CHARSET
//                );

//                $tmsg = trim(strip_tags(str_replace(array("<br />", "<br>", "br/>"), "\n", $this->config['auto_respond_body'])));
//                if ($this->config['auto_respond_form'])
//                    $tmsg .= "\n" . $emailText;
                $hmsg = $this->config['auto_respond_body'];
                if (!empty($this->config['auto_respond_form']))
                    $hmsg .= "\n" . $emailHtml;
                $mail = new expMail();
                $mail->quickSend(array(
//                    'headers'      => $headers,
//                    "text_message" => $tmsg,
                    'html_message' => $hmsg,
                    "text_message" => expString::html2text($hmsg),
                    'to'           => $db_data->email,
                    'from'         => array(trim($from) => $from_name),
                    'subject'      => $this->config['auto_respond_subject'],
                ));
            }

            // clear the users post data from the session.
            expSession::un_set('forms_data_' . $f->id);

            //If is a new post show response, otherwise redirect to the flow.
            if (!isset($this->params['data_id'])) {
//                if (empty($this->config['response'])) $this->config['response'] = gt('Thanks for your submission');
//                assign_to_template(array(
//                    "backlink"=>expHistory::getLastNotEditable(),
//                    "response_html"=>$this->config['response'],
//                ));
                $response = !empty($this->config['response']) ? $this->config['response'] : gt('Thanks for your submission');
            } else {
//                flash('message', gt('Record was updated!'));
//                expHistory::back();
//                expHistory::returnTo('editable');
                $response =  gt('Record was updated!');
            }
            flash('message', $response);
            expHistory::back();
        }
    }

    /**
     * delete item in saved data
     *
     */
    function delete() {
        if (empty($this->params['id']) || empty($this->params['forms_id'])) {
            flash('error', gt('Missing id for the') . ' ' . gt('item') . ' ' . gt('you would like to delete'));
            expHistory::back();
        }

        $f = new forms($this->params['forms_id']);
        $f->deleteRecord($this->params['id']);

        expHistory::back();
    }

    /**
     * delete all items in saved data
     *
     */
    function delete_records() {
        if (empty($this->params['forms_id'])) {
            flash('error', gt('Missing id for the') . ' ' . gt('form records') . ' ' . gt('you would like to delete'));
            expHistory::back();
        }

        $f = new forms($this->params['forms_id']);
        $recs = $f->getRecords();
        foreach ($recs as $rec) {
            $f->deleteRecord($rec->id);
        }

        flash('message', gt('All form records were deleted!'));
        expHistory::back();
    }

    /**
     * Manage site forms
     *
     */
    public function manage() {
        expHistory::set('manageable', $this->params);
        $forms = $this->forms->find('all', 1);
        foreach($forms as $key=>$f) {
            if (!empty($f->table_name) && $f->tableExists() ) {
                $forms[$key]->count = $f->countRecords();
            }
            $forms[$key]->control_count = count($f->forms_control);
        }

        assign_to_template(array(
            'select' => !empty($this->params['select']),
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
        if (!empty($f->column_names_list)) {
            if (is_string($f->column_names_list)){
                $f->column_names_list = expUnserialize($f->column_names_list);
            }
            $this->config['column_names_list'] = $f->column_names_list;
        }
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
            if (is_string($f->column_names_list)){
                $cols = expUnserialize($f->column_names_list);
            } else {
                $cols = explode('|!|', $f->column_names_list);
            }
        }
        $fieldlist = '[';
        // build fields, column_names, and fieldlist
        $fc = new forms_control();
        foreach ($fc->find('all', 'forms_id=' . $f->id . ' AND is_readonly=0','rank') as $control) {
            $ctl = expUnserialize($control->data);
            $control_type = get_class($ctl);
            $def = call_user_func(array($control_type, 'getFieldDefinition'));
            if ($def != null) {
                $fields[$control->name] = $control->caption;
                if (in_array($control->name, $cols)) {
                    $column_names[$control->name] = $control->caption;
                }
            }
            if ($control_type !== 'pagecontrol' && $control_type !== 'htmlcontrol') {
                $fieldlist .= '["{\$fields[\'' . $control->name . '\']}","' . $control->caption . '","' . gt('Insert') . ' ' . $control->caption . ' ' . gt('Field') . '"],';
            }
        }
        $list = array(
            'ip' => gt('IP Address'),
            'sef_url' => gt('SEF URL'),
            'user_id' => gt('Posted by'),
            'timestamp' => gt('Timestamp')
        );
        foreach ($list as $name=>$caption) {
            $fields[$name] = $caption;
            if (in_array('ip', $cols)) {
                $column_names[$name] = $caption;
            }
            $fieldlist .= '["{\$fields[\'' . $name . '\']}","' . $caption . '","' . gt('Insert') . ' ' . $caption . ' ' . gt('Field') . '"],';
        }
        // add report title
        $fieldlist .= '["{$title}","' . gt('Report Title') . '","' . gt('Insert') . ' ' . gt('Report Title') . '"],';
        // add link to record
        $fieldlist .= '["{\$fields[\'' . 'link' . '\']}","' . gt('Link to Record') . '","' . gt('Insert') . ' ' . gt('Link to Record') . ' ' . gt('Field') . '"],';
        $fieldlist .= ']';

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
            'fieldlist'    => $fieldlist,
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
//        if (!empty($this->params['is_saved']) && empty($this->params['table_name'])) {
        if (!empty($this->params['is_saved'])) {
            // we are now saving data to the database and need to create it first
//            $form = new forms($this->params['id']);
            $this->params['table_name'] = $this->forms->updateTable();
//            $this->params['_validate'] = false;  // we don't want a check for unique sef_name
//            parent::update();  // now with a form tablename
            if (!empty($this->params['is_searchable'])) {
                $this->addContentToSearch();
            }
        }
        expHistory::back();
    }

    public function delete_form() {
        expHistory::set('editable', $this->params);
        $modelname = $this->basemodel_name;
        if (empty($this->params['id'])) {
            flash('error', gt('Missing id for the') . ' ' . $modelname . ' ' . gt('you would like to delete'));
            expHistory::back();
        }
        $form = new $modelname($this->params['id']);

        $form->delete();
        expHistory::returnTo('manageable');
    }

    public function design_form() {
        if (!empty($this->params['id'])) {
            expHistory::set('editable', $this->params);
            $f = new forms($this->params['id']);
            $controls = $f->forms_control;

            $form = new fakeform();
            $form->horizontal = !empty($this->config['style']) ? $this->config['style'] : false;
            if (isset($this->params['style']))
                $form->horizontal = !empty($this->params['style']);
            foreach ($controls as $c) {
                $ctl = expUnserialize($c->data);
                $ctl->_id = $c->id;
                $ctl->_readonly = $c->is_readonly;
                $ctl->_controltype = get_class($ctl);
                $form->register($c->name, $c->caption, $ctl);
            }

            $types = expTemplate::listControlTypes();
            $types[".break"] = gt('Static - Spacer');
            $types[".line"] = gt('Static - Horizontal Line');
            uasort($types, "strnatcmp");
            if (!bs3() && !bs4() && !bs5())
                array_unshift($types, '[' . gt('Please Select' . ']'));

            $forms_list = array();
            $forms = $f->find('all', 1);
            if (!empty($forms)) foreach ($forms as $frm) {
                if ($frm->id != $f->id)
                    $forms_list[$frm->id] = $frm->title;
            }

            assign_to_template(array(
                'form'       => $f,
                'forms_list' => $forms_list,
                'form_html'  => $form->toHTML($f->id),
                'backlink'   => expHistory::getLastNotEditable(),
                'types'      => $types,
                'style'      => $form->horizontal
            ));
        }
    }

    public function edit_control() {
        $f = new forms($this->params['forms_id']);
        if ($f) {
            if (bs2()) {
                expCSS::pushToHead(array(
                    "corecss"=>"forms-bootstrap"
                ));
            } elseif (bs3()) {
                expCSS::pushToHead(array(
                    "corecss"=>"forms-bootstrap3"
                ));
            } elseif (bs4()) {
                expCSS::pushToHead(array(
                    "corecss"=>"forms-bootstrap4"
                ));
            } elseif (bs5()) {
                expCSS::pushToHead(array(
                    "corecss"=>"forms-bootstrap5"
                ));
            } else {
                expCSS::pushToHead(array(
                    "corecss" => "forms",
                ));
            }

            if (isset($this->params['control_type']) && $this->params['control_type'][0] === ".") {
                // there is nothing to edit for these type controls, so add it then return
                $htmlctl = new htmlcontrol();
                $htmlctl->identifier = uniqid("");
                $htmlctl->caption = "";
                if (!empty($this->params['rank']))
                    $htmlctl->rank = $this->params['rank'];
                switch ($this->params['control_type']) {
                    case ".break":
                        $htmlctl->html = "<br />";
                        break;
                    case ".line":
                        $htmlctl->html = "<hr>";
                        break;
                }
                $ctl = new forms_control();
                $ctl->name = uniqid("");
                $ctl->caption = "";
                $ctl->data = serialize($htmlctl);
                $ctl->forms_id = $f->id;
                $ctl->is_readonly = 1;
                if (!empty($this->params['rank']))
                    $ctl->rank = $this->params['rank'];
                $ctl->update();
                if (!expJavascript::inAjaxAction())
                    expHistory::returnTo('editable');
                else { // we need a graceful exit for inAjaxAction
                    assign_to_template(array(
                        'form_html' => ucfirst(substr($this->params['control_type'],1)) . ' ' . gt('control was added to form') . '<input type="hidden" name="staticcontrol" id="'.$ctl->id.'" />',
                        'type'      => 'static',
                    ));
                }
            } else {
                $control_type = "";
                $ctl = null;
                if (isset($this->params['id'])) {
                    $control = new forms_control($this->params['id']);
                    if ($control) {
                        $ctl = expUnserialize($control->data);
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
                    if (isset($form->controls['identifier']->disabled)) $form->controls['identifier']->disabled = true;
                    $form->meta("id", $ctl->id);
                    $form->meta("identifier", $ctl->identifier);
                }
                $form->meta("action", "save_control");
//                $form->meta('control_type', $control_type);
                $form->meta('forms_id', $f->id);
                $types = expTemplate::listControlTypes();
                $othertypes = expTemplate::listSimilarControlTypes($control_type);
                if (count($othertypes) > 1) {
                    $otherlist = new dropdowncontrol($control_type,$othertypes);
                    $form->registerBefore('identifier','control_type',gt('Control Type'),$otherlist);
                } else {
                    $form->registerBefore('identifier','control_type',gt('Control Type'),new genericcontrol('hidden',$control_type));
                }
                assign_to_template(array(
                    'form_html' => $form->toHTML(),
                    'type'      => $types[$control_type],
                    'is_edit'   => ($ctl == null ? 0 : 1),
                ));
            }
        }
    }

    public function save_control() {
        $f = new forms($this->params['forms_id']);
        if ($f) {
            $ctl = null;
            $control = null;
            // get previous data from existing control
            if (isset($this->params['id'])) {
                $control = new forms_control($this->params['id']);
                if ($control) {
                    $ctl = expUnserialize($control->data);
                    $ctl->identifier = $control->name;
                    $ctl->caption = $control->caption;
                }
            } else {
                $control = new forms_control();
            }

            // update control with data from form
//            $ctl1 = new $this->params['control_type']();
//            $ctl1 = expCore::cast($ctl1,$ctl);
            if (!empty($ctl)) {
                $ctl1 = expCore::cast($ctl,$this->params['control_type']);
            } else {
                $ctl1 = $ctl;
            }
            if (call_user_func(array($this->params['control_type'], 'useGeneric')) == true) {
                $ctl1 = genericcontrol::update($this->params, $ctl1);
            } else {
                $ctl1 = call_user_func(array($this->params['control_type'], 'update'), $this->params, $ctl1);
            }
            $ctl1->type = str_replace('control', '', $this->params['control_type'], );  // update the control type
            if (!empty($this->params['rank']))
                $ctl1->rank = $this->params['rank'];

            //lets make sure the name submitted by the user is not a duplicate. if so we will fail back to the form
            if (!empty($control->id)) {
                //FIXME change this to an expValidator call
                $check = $control->getControl('name="' . $ctl1->identifier . '" AND forms_id=' . $f->id . ' AND id != ' . $control->id);
                if (!empty($check) && empty($this->params['id'])) {
                    //expValidator::failAndReturnToForm(gt('A field with the same name already exists for this form'), $_$this->params
                    flash('error', gt('A field by the name")." "' . $ctl1->identifier . '" ".gt("already exists on this form'));
                    expHistory::returnTo('editable');
                }
            }

            if ($ctl1 != null) {
                $name = substr(preg_replace('/[^A-Za-z0-9]/', '_', $ctl1->identifier), 0, 20);
                if (!isset($this->params['id']) && $control->countControls("name='" . $name . "' AND forms_id=" . $this->params['forms_id']) > 0) {
                    $this->params['_formError'] = gt('Identifier must be unique.');
                    expSession::set('last_POST', $this->params);
                } elseif ($name === 'id' || $name === 'ip' || $name === 'sef_url' || $name === 'user_id' || $name === 'timestamp' || $name === 'location_data') {
                    $this->params['_formError'] = sprintf(gt("Identifier cannot be '%s'."), $name);
                    expSession::set('last_POST', $this->params);
                } else {
                    if (!isset($this->params['id'])) {
                        $control->name = $name;
                    }
                    $control->caption = $ctl1->caption;
                    $control->forms_id = $this->params['forms_id'];
                    $control->is_static = (!empty($ctl1->is_static) ? $ctl1->is_static : 0);
                    if (!empty($ctl1->pattern)) $ctl1->pattern = addslashes($ctl1->pattern);
                    $control->data = serialize($ctl1);

                    if (!empty($this->params['rank']))
                        $control->rank = $this->params['rank'];
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
        if (!expJavascript::inAjaxAction())
            expHistory::returnTo('editable');
        else {
            echo $control->id;
        }
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
            if (!expJavascript::inAjaxAction())
                expHistory::returnTo('editable');
        }
    }

    public function rerank_control() {
        if (!empty($this->params['id'])) {
            $fc = new forms_control($this->params['id']);
            $fc->rerank_control($this->params['rank']);
        }
    }

    /**
     * Output a single control to an ajax request
     */
    public function build_control() {
        if (!empty($this->params['id'])) {
            $control = new forms_control($this->params['id']);
            $form = new fakeform();
            $form->horizontal = !empty($this->config['style']) ? $this->config['style'] : false;
            $ctl = expUnserialize($control->data);
            $ctl->_id = $control->id;
            $ctl->_readonly = $control->is_readonly;
            $ctl->_controltype = get_class($ctl);
            if (isset($this->params['style']))
                $form->horizontal = $this->params['style'];
            $form->register($control->name, $control->caption, $ctl);
            $form->style_form();
            echo $form->controlToHTML($control->name);
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
        $fieldlist = '[';
        if (isset($this->config['forms_id'])) {
            // build fields, column_names, and fieldlist
            $fc = new forms_control();
            foreach ($fc->find('all', 'forms_id=' . $this->config['forms_id'] . ' AND is_readonly=0','rank') as $control) {
                $ctl = expUnserialize($control->data);
                $control_type = get_class($ctl);
                $def = call_user_func(array($control_type, 'getFieldDefinition'));
                if ($def != null) {
                    $fields[$control->name] = $control->caption;
                    if (in_array($control->name, $cols)) {
                        $column_names[$control->name] = $control->caption;
                    }
                }
                if ($control_type !== 'pagecontrol' && $control_type !== 'htmlcontrol') {
                    $fieldlist .= '["{\$fields[\'' . $control->name . '\']}","' . $control->caption . '","' . gt('Insert') . ' ' . $control->caption . ' ' . gt('Field') . '"],';
                }
            }
            $list = array(
                'ip' => gt('IP Address'),
                'sef_url' => gt('SEF URL'),
                'user_id' => gt('Posted by'),
                'timestamp' => gt('Timestamp')
            );
            foreach ($list as $name=>$caption) {
                $fields[$name] = $caption;
                if (in_array('ip', $cols)) {
                    $column_names[$name] = $caption;
                }
                $fieldlist .= '["{\$fields[\'' . $name . '\']}","' . $caption . '","' . gt('Insert') . ' ' . $caption . ' ' . gt('Field') . '"],';
            }
            // add report title
            $fieldlist .= '["{$title}","' . gt('Report Title') . '","' . gt('Insert') . ' ' . gt('Report Title') . '"],';
            // add record link
            $fieldlist .= '["{\$fields[\'' . 'link' . '\']}","' . gt('Link to Record') . '","' . gt('Insert') . ' ' . gt('Link to Record') . '"],';
        }
        $fieldlist .= ']';

        $title = gt('No Form Assigned Yet!');
        if (!empty($this->config['forms_id'])) {
            $form = $this->forms->find('first', 'id=' . $this->config['forms_id']);
            $this->config['is_saved'] = $form->is_saved;
            $this->config['table_name'] = $form->table_name;
            $this->config['is_searchable'] = $form->is_searchable;
            $title = $form->title;
        }
        assign_to_template(array(
//            'forms_list'   => $forms_list,
            'form_title'   => $title,
            'column_names' => $column_names,
            'fields'       => $fields,
            'fieldlist'    => $fieldlist,
        ));

        parent::configure();
    }

    /**
     * create a new default config array using the form defaults
     * @param $form
     */
    private function get_defaults($form) {
        if (empty($this->config)) { // NEVER overwrite an existing config
            $this->config = array();
            $config = get_object_vars($form);
            if (!empty($config['column_names_list']) && is_string($config['column_names_list'])) {
                if (stripos($config['column_names_list'], '|!|') !== false) {
                    $config['column_names_list'] = explode('|!|', $config['column_names_list']);
                } else {
                    $config['column_names_list'] = expUnserialize($config['column_names_list']);
                }
            }
            unset ($config['forms_control']);
            $this->config = $config;
        }
    }

    /**
     * add saved form item or all saved form items to search index
     *
     * @return int number of items added to search index
     * @throws ReflectionException
     */
    public function addContentToSearch() {
        global $db;

        // first find if it's a single item or all the searchable forms
        if (isset($this->params['data_id'])) {
            // single record
            $content = array($this->forms->getRecord($this->params['data_id']));
            // next add/update searchable form items to search index
            $count = $this->add_search_record($content, $this->forms);
        } else {
            // all records
            $forms = $this->forms->find('all', 'is_searchable=1');
            $count = 0;
            foreach ($forms as $form) {
                $content = array();
                foreach ($form->getRecords() as $record) {
                    $content[] = $record;
                }
                // next add/update searchable form items to search index
                $count += $this->add_search_record($content, $form);
            }
        }
        return $count;
    }

    /**
     * Create a Search record from a Form record
     *
     * @param $content
     * @param $form
     * @return int
     * @throws ReflectionException
     */
    function add_search_record($content, $form) {
        global $db;

        $count = 0;
        foreach ($content as $cnt) {
            $origid = $cnt->id;
            unset($cnt->id);
            //build the search record and save it.
            $cnt->location_data = serialize(expCore::makeLocation($this->baseclassname, null, $form->id));
            $sql = "original_id=" . $origid . " AND location_data=" . $cnt->location_data . " AND ref_module='" . $this->baseclassname . "'";
            $oldindex = $db->selectObject('search', $sql);
            if (!empty($oldindex)) {
                $search_record = new search($oldindex->id, false, false);
                $search_record->update($cnt);
            } else {
                $search_record = new search($cnt, false, false);
            }

            // get a title from 1st matching field
            $needles = array(  // fields we use to develop an sef_url
                'name',
                'title',
                'last',
                'first',
                'email'
            );
            $search_record->title = 'Untitled';
            foreach ($needles as $needle) {
                foreach (get_object_vars($cnt) as $key => $value) {
                    if (false !== stripos($key, $needle)) {
                        if (empty($value))
                            continue;
                        $search_record->title = $value;
                        break 2;
                    }
                }
            }

            // build some search body content from all matching fields
            $needles = array(
                'body',
                'detail',
                'note',
                'desc',
                'name',  // we only add the first needed to the title...
                'title',
                'address',
                'city',
            );
            $search_record->body = '';
            foreach ($needles as $needle) {
                foreach (get_object_vars($cnt) as $key => $value) {
                    if (false !== stripos($key, $needle)) {
                        if (empty($value))
                            continue;
                        $search_record->body .= $value . ', ';
                    }
                }
            }

            //build the search record and save it.
            $search_record->original_id = $origid;
            $search_record->posted = empty($cnt->timestamp) ? null : $cnt->timestamp;
            if (!empty($cnt->sef_url)) {
                $link = str_replace(URL_FULL, '', makeLink(array('controller' => $this->baseclassname, 'action' => 'show', 'title' => $form->sef_url, 'item' => $cnt->sef_url)));
            } else {
                $link = str_replace(URL_FULL, '', makeLink(array('controller' => $this->baseclassname, 'action' => 'show', 'forms_id' => $form->id, 'id' => $cnt->id)));
            }
            $search_record->view_link = $link;
            $search_record->ref_module = $this->baseclassname;
            $search_record->category = $this->searchName();
            $search_record->ref_type = $this->searchCategory();
            $search_record->save();
            $count++;
        }
        return $count;
    }

    /**
     * get the metainfo for this module
     *
     * @return array|boolean
     */
    function metainfo() {
        global $router;

        if (empty($router->params['action']))
            return false;
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'', 'canonical'=> '', 'noindex' => false, 'nofollow' => false);

        // figure out what metadata to pass back based on the action we are in.
        switch ($router->params['action']) {
            case 'showall':
                $metainfo['title'] = gt("Form Records") . ' - ' . SITE_TITLE;
                $metainfo['keywords'] = SITE_KEYWORDS;
                $metainfo['description'] = SITE_DESCRIPTION;
                if (!empty($router->params['id'])) {
                    $f = $this->forms->find('first', 'id=' . $router->params['id']);
                } elseif (!empty($router->params['title'])) {
                    $f = $this->forms->find('first', 'sef_url=\'' . expString::escape($router->params['title']) . '\'');
                }
                if (!empty($f)) {
                    $this->get_defaults($f);
                    $metainfo['title'] = $f->title . ' ' . gt("Records");
                }
                break;
            case 'show':
                $metainfo['title'] = gt("Form Record") . ' - ' . SITE_TITLE;
                $metainfo['keywords'] = SITE_KEYWORDS;
                $metainfo['description'] = SITE_DESCRIPTION;
                if (!empty($router->params['forms_id'])) {
                    $f = $this->forms->find('first', 'id=' . $router->params['forms_id']);
                } elseif (!empty($router->params['title'])) {
                    $f = $this->forms->find('first', 'sef_url=\'' . expString::escape($router->params['title']) . '\'');
                }
                if (!empty($f)) {
                    $this->get_defaults($f);
                    $id = !empty($router->params['id']) ? $router->params['id'] : null;
                    if (!empty($router->params['item']))
                        $id = 'sef_url="' . expString::escape($router->params['item']) . '"';
                    $data = $f->getRecord($id);
                    $needles = array(
                        'name',
                        'title',
                        'last',
                        'first',
                    );
                    foreach ($needles as $needle) {
                        foreach ($data as $key => $value) {
                            if (false !== stripos($key, $needle)) {
                                $metainfo['title'] = $f->title . ' - ' . $value;
                                break 2;
                            }
                        }
                    }
                }
                break;
            default:
                $metainfo = parent::metainfo();
        }
        return $metainfo;
    }

    public function export_csv() {
        if (!empty($this->params['id'])) {
            $f = new forms($this->params['id']);
            $this->get_defaults($f);  // fills $this->config with form defaults if needed
            $items = $f->getRecords();

            $fc = new forms_control();
            //$f->column_names_list is a serialized array
            //$this->config['column_names_list'] is an array
            if ($this->config['export_all'] || $this->config['column_names_list'] == '') {
                //define some default columns...
                $controls = $fc->find('all', "forms_id=" . $f->id . " AND is_readonly = 0 AND is_static = 0", "rank");
                //FIXME should we default to only 5 columns or all columns? and should we pick up modules columns ($this->config) or just form defaults ($f->)
//                foreach (array_slice($controls, 0, 5) as $control) {
                foreach ($controls as $control) {
//                    if ($this->config['column_names_list'] != '')
//                        $this->config['column_names_list'] .= '|!|';
//                    $this->config['column_names_list'] .= $control->name;
                    $this->config['column_names_list'][$control->name] = $control->name;
                }
            }

//            $rpt_columns2 = explode("|!|", $this->config['column_names_list']);

            $rpt_columns = array();
            // popuplate field captions/labels
            foreach ($this->config['column_names_list'] as $column) {
                $control = $fc->find('first', "forms_id=" . $f->id . " AND name = '" . $column . "' AND is_readonly = 0 AND is_static = 0", "rank");
                if (!empty($control)) {
                    $rpt_columns[$control->name] = $control->caption;
                } else {
                    switch ($column) {
                        case 'ip':
                            $rpt_columns[$column] = gt('IP Address');
                            break;
                        case 'sef_url':
                            $rpt_columns[$column] = gt('SEF URL');
                            break;
                        case 'referrer':
                            $rpt_columns[$column] = gt('Event ID');
                            break;
                        case 'user_id':
                            $rpt_columns[$column] = gt('Posted by');
                            break;
                        case 'timestamp':
                            $rpt_columns[$column] = gt('Timestamp');
                            break;
                    }
                }
            }

            // populate field data
            foreach ($rpt_columns as $column_name=>$column_caption) {
                if ($column_name === "ip" || $column_name === "sef_url" || $column_name === "referrer" || $column_name === "location_data") {
                } elseif ($column_name === "user_id") {
                    foreach ($items as $key => $item) {
                        if ($item->$column_name != 0) {
                            $locUser = user::getUserById($item->$column_name);
                            $item->$column_name = $locUser->username;
                        } else {
                            $item->$column_name = '';
                        }
                        $items[$key] = $item;
                    }
                } elseif ($column_name === "timestamp") {
//                    $srt = $column_name . "_srt";
                    foreach ($items as $key => $item) {
//                        $item->$srt = $item->$column_name;
                        $item->$column_name = date('m/d/y H:i:s', $item->$column_name);  // needs to be in a machine readable format
                        $items[$key] = $item;
                    }
                } else {
                    $control = $fc->find('first', "name='" . $column_name . "' AND forms_id=" . $this->params['id'],'rank');
                    if ($control) {
//                        $ctl = unserialize($control->data);
                        $ctl = expUnserialize($control->data);
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

            expCore::save_csv($items, $rpt_columns, "report.csv");

            //fixme old routine, not called

            if (LANG_CHARSET === 'UTF-8') {
                $file = chr(0xEF) . chr(0xBB) . chr(0xBF); // add utf-8 signature to file to open appropriately in Excel, etc...
            } else {
                $file = "";
            }

            $file .= self::sql2csv($items, $rpt_columns);

            // CREATE A TEMP FILE
            $tmpfname = tempnam(BASE.'/tmp', "rep"); // Rig

            $handle = fopen($tmpfname, "wb");
            fwrite($handle, $file);
            fclose($handle);

            if (file_exists($tmpfname)) {

                ob_end_clean();

                // This code was lifted from phpMyAdmin, but this is Open Source, right?
                // 'application/octet-stream' is the registered IANA type but
                //        MSIE and Opera seems to prefer 'application/octetstream'
                // It seems that other headers I've added make IE prefer octet-stream again. - RAM

                $mime_type = (EXPONENT_USER_BROWSER === 'IE' || EXPONENT_USER_BROWSER === 'OPERA') ? 'application/octet-stream;' : 'text/comma-separated-values;';
                header('Content-Type: ' . $mime_type . ' charset=' . LANG_CHARSET . "'");
                header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                $filesize = filesize($tmpfname);
                header('Content-length: ' . $filesize);
                header('Content-Transfer-Encoding: binary');
//                header('Content-Encoding:');
                header('Content-Disposition: attachment; filename="report.csv"');
                if ($filesize) header('Content-length: ' . $filesize); // for some reason the webserver cant run stat on the files and this breaks.
                // IE need specific headers
                if (EXPONENT_USER_BROWSER === 'IE') {
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Vary: User-Agent');
                } else {
                    header('Pragma: no-cache');
                }
                //Read the file out directly
                readfile($tmpfname);

//                if (DEVELOPMENT == 0) exit();
                unlink($tmpfname);
                exit();
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
     * So by nature of the array, the keys are repeated in each line (id, name, etc)
     * So if we want to make a header row, we just run through once at the beginning and
     * use the array_keys function to strip out a functional header
     *
     * @param      $items
     *
     * @param array|null $rptcols
     *
     * @return string
     */
    public static function sql2csv($items, $rptcols = null) {
        $str = "";
        // create header row
        foreach ($rptcols as $individual_Header) {
            if (!is_array($rptcols) || in_array($individual_Header, $rptcols)) $str .= $individual_Header . ",";  //FIXME $individual_Header is ALWAYS in $rptcols?
        }
        $str .= "\r\n";
        // create item rows
        foreach ($items as $item) {
            foreach ($rptcols as $key => $rowitem) {
                if (!is_array($rptcols) || property_exists($item, $key)) {
                    $rowitem = str_replace(",", " ", $item->$key);
                    $str .= $rowitem . ",";
                }
            } //foreach rowitem
            $str = substr($str, 0, -1);
            $str .= "\r\n";
        } //end of foreach loop
        return $str;
    }

    /**
     * Export form, controls and optionally the data table
     *
     */
    public function export_eql() {
        assign_to_template(array(
            "id" => $this->params['id'],
        ));
    }

    /**
     * Export form, controls and optionally the data table
     *
     */
    public function export_eql_process() {
        if (!empty($this->params['id'])) {
            $f = new forms($this->params['id']);

            $filename = preg_replace('/[^A-Za-z0-9_.-]/','-',$f->sef_url.'.eql');

            ob_end_clean();
            ob_start();

            // This code was lifted from phpMyAdmin, but this is Open Source, right?

            // 'application/octet-stream' is the registered IANA type but
            //        MSIE and Opera seems to prefer 'application/octetstream'
            $mime_type = (EXPONENT_USER_BROWSER === 'IE' || EXPONENT_USER_BROWSER === 'OPERA') ? 'application/octetstream' : 'application/octet-stream';

            header('Content-Type: ' . $mime_type);
            header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            // IE need specific headers
            if (EXPONENT_USER_BROWSER === 'IE') {
                header('Content-Disposition: inline; filename="' . $filename . '"');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
            } else {
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
            }
            $tables = array(
                'forms',
                'forms_control'
            );
            if (!empty($this->params['include_data'])) {
                $tables[] = 'forms_'.$f->table_name;
            }
            echo expFile::dumpDatabase($tables, 'Form', $this->params['id']);  //FIXME we need to echo inside call
            exit; // Exit, since we are exporting
        }
//        expHistory::back();
    }

    /**
     * Import form, controls and optionally the data table
     *
     */
    public function import_eql() {
    }

    /**
     * Import form, controls and optionally the data table
     *
     */
    public function import_eql_process() {
        $errors = array();

        //FIXME check for duplicate form data table name before import?
        expFile::restoreDatabase($_FILES['file']['tmp_name'], $errors, 'Form');

        if (empty($errors)) {
            flash('message',gt('Form was successfully imported'));
        } else {
            $message = gt('Form import encountered the following errors') . ':<br>';
            foreach ($errors as $error) {
                $message .= '* ' . $error . '<br>';
            }
            flash('error', $message);
        }
        expHistory::back();
    }

    public function import_csv() {
        if (expFile::canCreate(BASE . "tmp/test") != SYS_FILES_SUCCESS) {
            assign_to_template(array(
                "error" => "The /tmp directory is not writable.  Please contact your administrator.",
            ));
        } else {
            //Setup the arrays with the name/value pairs for the dropdown menus
            $delimiterArray = Array(
                ',' => gt('Comma'),
                ';' => gt('Semicolon'),
                ':' => gt('Colon'),
                ' ' => gt('Space'));

            $forms = $this->forms->find('all', 1);
            $formslist = array();
            $formslist[0] = gt('--Create a New Form--');
            foreach ($forms as $aform) {
                if (!empty($aform->is_saved)) {
                    $formslist[$aform->id] = $aform->title;
                    if (empty($formslist[$aform->id])) $formslist[$aform->id] = gt('Untitled');
                }
            }

//            //Setup the meta data (hidden values)
//            $form = new form();
//            $form->meta("controller", "forms");
//            $form->meta("action", "import_csv_mapper");
//
//            //Register the dropdown menus
//            $form->register("delimiter", gt('Delimiter Character'), new dropdowncontrol(",", $delimiterArray));
//            $form->register("upload", gt('CSV File to Upload'), new uploadcontrol());
//            $form->register("use_header", gt('First Row is a Header'), new checkboxcontrol(0, 0));
//            $form->register("rowstart", gt('Forms Data begins in Row'), new textcontrol("1", 1, 0, 6));
//            $form->register("forms_id", gt('Target Form'), new dropdowncontrol("0", $formslist));
//            $form->register("submit", "", new buttongroupcontrol(gt('Next'), "", gt('Cancel')));

            assign_to_template(array(
//                "form_html" => $form->tohtml(),
                'delimiters' => $delimiterArray,
                'forms_list' => $formslist,
            ));
        }
    }

    public function import_csv_mapper() {
        //Check to make sure the user filled out the required input.
        if (!is_numeric($this->params["rowstart"])) {
            unset($this->params["rowstart"]);
            $this->params['_formError'] = gt('The starting row must be a number.');
            expSession::set("last_POST", $this->params);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit('Redirecting...');
        }

        if (!empty($this->params['forms_id'])) {
            // if we are importing to an existing form, jump to that step
            $this->import_csv_data_mapper();
        } else {
            //Get the temp directory to put the uploaded file
            $directory = "tmp";

            //Get the file save it to the temp directory
            if ($_FILES["upload"]["error"] == UPLOAD_ERR_OK) {
                //	$file = file::update("upload",$directory,null,time()."_".$_FILES['upload']['name']);
                $file = expFile::fileUpload("upload", false, false, time() . "_" . $_FILES['upload']['name'], $directory.'/'); //FIXME quick hack to remove file model
                if ($file == null) {
                    switch ($_FILES["upload"]["error"]) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                        $this->params['_formError'] = gt('The file you attempted to upload is too large.  Contact your system administrator if this is a problem.');
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $this->params['_formError'] = gt('The file was only partially uploaded.');
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $this->params['_formError'] = gt('No file was uploaded.');
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                        case UPLOAD_ERR_CANT_WRITE:
                            $this->params['_formError'] = gt('Server Temp File Error.');
                            break;
                        case UPLOAD_ERR_EXTENSION:
                        default:
                            $this->params['_formError'] = gt('A strange internal error has occurred.  Please contact the Exponent Developers.');
                            break;
                    }
                    expSession::set("last_POST", $this->params);
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit("");
                }
            }
            /*
            if (mime_content_type(BASE.$directory."/".$file->filename) != "text/plain"){
                $this->params['_formError'] = "File is not a delimited text file.";
                expSession::set("last_POST",$this->params);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit("");
            }
            */

            //split the line into its columns
            $headerinfo = null;
            $line_end = ini_get('auto_detect_line_endings');
            ini_set('auto_detect_line_endings',TRUE);
            $fh = fopen(BASE . $directory . "/" . $file->filename, "rb");
            if (!empty($this->params["use_header"])) $this->params["rowstart"]++;
            for ($x = 0; $x < $this->params["rowstart"]; $x++) {
                $lineInfo = fgetcsv($fh, 2000, $this->params["delimiter"]);
                if ($x == 0 && !empty($this->params["use_header"])) $headerinfo = $lineInfo;
            }
            fclose($fh);
            ini_set('auto_detect_line_endings',$line_end);

            // get list of simple non-static controls if we are also creating a new form
            $types = expTemplate::listControlTypes(false);
            uasort($types, "strnatcmp");
            $types = array_merge(array('none'=>gt('--Disregard this column--')),$types);

            //Check to see if the line got split, otherwise throw an error
            if ($lineInfo == null) {
                $this->params['_formError'] = sprintf(gt("This file does not appear to be delimited by '%s'. <br />Please specify a different delimiter.<br /><br />"), $this->params["delimiter"]);
                expSession::set("last_POST", $this->params);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit("");
            } else {
                //Setup the meta data (hidden values)
                $form = new form();
                $form->meta("controller", "forms");
                $form->meta("action", "import_csv_form_prep");  // we are creating a new form first
    //            $form->meta("action", "import_csv_data");  // we are importing into an existing form  //FIXME
                $form->meta("delimiter", $this->params["delimiter"]);
                $form->meta("filename", $directory . "/" . $file->filename);
                $form->meta("use_header", $this->params["use_header"]);
                $form->meta("rowstart", $this->params["rowstart"]);
                for ($i = 0, $iMax = count($headerinfo); $i < $iMax; $i++) {
                    if ($headerinfo != null) {
                        $title = $headerinfo[$i];
                        if (!empty($lineInfo[$i])) {
                            $title .=  ' (' . $lineInfo[$i] .')';
                        }
    //                    $label = str_replace('&', 'and', $headerinfo[$i]);
    //                    $label = preg_replace("/(-)$/", "", preg_replace('/(-){2,}/', '-', strtolower(preg_replace("/([^0-9a-z-_\+])/i", '-', $label))));
    //                    $form->register("name[$i]", null, new genericcontrol('hidden',$label));
                        $form->register("name[$i]", null, new genericcontrol('hidden',$headerinfo[$i]));
                    } else {
                        $form->register("name[$i]", null, new genericcontrol('hidden','Field'.$i));
                        $title = $lineInfo[$i];
                    }
                    if (!empty($lineInfo[$i])) {
                        $form->register("data[$i]", null, new genericcontrol('hidden', $lineInfo[$i]));
                    } else {
                        $form->register("data[$i]", null, new genericcontrol('hidden', gt('empty record')));
                    }
                    $form->register("control[$i]", $title, new dropdowncontrol("none", $types));
                }
                $form->register("submit", "", new buttongroupcontrol(gt('Next'), "", gt('Cancel')));

                assign_to_template(array(
                    "form_html" => $form->tohtml(),
                ));
            }
        }
    }

    public function import_csv_form_prep() {
        $form = new form();
        $form->meta("controller", "forms");
        $form->meta("action", "import_csv_form_add");
        $form->meta("delimiter", $this->params["delimiter"]);
        $form->meta("filename", $this->params["filename"]);
        $form->meta("use_header", $this->params["use_header"]);
        $form->meta("rowstart", $this->params["rowstart"]);

         // condense our responses to present form shell for confirmation
        $form->register("title", gt('Form Title'), new textcontrol(''));
        $formcontrols = array();
        foreach ($this->params['control'] as $key=>$control) {
            if ($control !== "none") {
                $formcontrols[$key] = new stdClass();
                $formcontrols[$key]->control = $control;
                $label = str_replace('&', 'and', $this->params['name'][$key]);
                $label = preg_replace("/(-)$/", "", preg_replace('/(-){2,}/', '_', strtolower(preg_replace("/([^0-9a-z-_\+])/i", '_', $label))));
                $formcontrols[$key]->name = $label;
                $formcontrols[$key]->caption = $this->params['name'][$key];
                $formcontrols[$key]->data = $this->params['data'][$key];
            }
        }

        foreach ($formcontrols as $i=>$control) {
            $form->register("column[$i]", ucfirst($control->control) . ' ' . gt('Field Identifier') . ' (' . $control->caption . ' - ' . $control->data . ')', new textcontrol($control->name));
            $form->register("control[$i]", null, new genericcontrol('hidden',$control->control));
            $form->register("caption[$i]", null, new genericcontrol('hidden',$control->caption));
            $form->register("data[$i]", null, new genericcontrol('hidden',$control->data));
        }

        $form->register("submit", "", new buttongroupcontrol(gt('Next'), "", gt('Cancel')));

        assign_to_template(array(
            "form_html" => $form->tohtml(),
        ));
    }

    public function import_csv_form_add() {

        // create the form
        $f = new forms();
        $f->title = $this->params['title'];
        $f->is_saved = true;
        $f->update();

        // create the form controls
        foreach ($this->params['control'] as $key=>$control) {
            $params = array();
            $fc = new forms_control();
            $this->params['column'][$key] = str_replace('&', 'and', $this->params['column'][$key]);
            $this->params['column'][$key] = preg_replace("/(-)$/", "", preg_replace('/(-){2,}/', '-', strtolower(preg_replace("/([^0-9a-z-_\+])/i", '-', $this->params['column'][$key]))));
            $fc->name = $params['identifier'] = $this->params['column'][$key];
            $fc->caption = $params['caption'] = $this->params['caption'][$key];
            $params['description'] = '';
            if ($control === 'datetimecontrol') {
                $params['showdate'] = $params['showtime'] = true;
            }
//            if ($control == 'htmlcontrol') {
//                $params['html'] = $this->params['data'][$key];
//            }
            if ($control === 'radiogroupcontrol' || $control === 'dropdowncontrol') {
                $params['default'] = $params['items'] = $this->params['data'][$key];
            }
            $fc->forms_id = $f->id;
            $ctl = null;
            $ctl = call_user_func(array($control, 'update'), $params, $ctl);
            $fc->data = serialize($ctl);
            $fc->update();
        }

        flash('notice', gt('New Form Created'));
        $this->params['forms_id'] = $f->id;
//        unset($this->params['caption']);
        unset($this->params['control']);
        $this->import_csv_data_display();
    }

    public function import_csv_data_mapper() {
//        global $template;
        //Get the temp directory to put the uploaded file
        $directory = "tmp";

        //Get the file save it to the temp directory
        if ($_FILES["upload"]["error"] == UPLOAD_ERR_OK) {
            //	$file = file::update("upload",$directory,null,time()."_".$_FILES['upload']['name']);
            $file = expFile::fileUpload("upload", false, false, time() . "_" . $_FILES['upload']['name'], $directory.'/'); //FIXME quick hack to remove file model
            if ($file == null) {
                switch ($_FILES["upload"]["error"]) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->params['_formError'] = gt('The file you attempted to upload is too large.  Contact your system administrator if this is a problem.');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->params['_formError'] = gt('The file was only partially uploaded.');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->params['_formError'] = gt('No file was uploaded.');
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                        $this->params['_formError'] = gt('Server Temp File Error.');
                        break;
                    case UPLOAD_ERR_EXTENSION:
                    default:
                        $this->params['_formError'] = gt('A strange internal error has occurred.  Please contact the Exponent Developers.');
                        break;
                }
                expSession::set("last_POST", $this->params);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit("");
            }
        }
        /*
        if (mime_content_type(BASE.$directory."/".$file->filename) != "text/plain"){
            $this->params['_formError'] = "File is not a delimited text file.";
            expSession::set("last_POST",$this->params);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit("");
        }
        */

        //split the line into its columns
        $headerinfo = null;
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $fh = fopen(BASE . $directory . "/" . $file->filename, "rb");
        if (!empty($this->params["use_header"])) $this->params["rowstart"]++;
        for ($x = 0; $x < $this->params["rowstart"]; $x++) {
            $lineInfo = fgetcsv($fh, 2000, $this->params["delimiter"]);
            if ($x == 0 && !empty($this->params["use_header"])) $headerinfo = $lineInfo;
        }
        fclose($fh);
        ini_set('auto_detect_line_endings',$line_end);

        // pull in the form control definitions here
        $f = new forms($this->params['forms_id']);
        $fields = array(
            "none"      => gt('--Disregard this column--'),
        );
        foreach ($f->forms_control as $control) {
            $fields[$control->name] = $control->caption;
        }

        //Check to see if the line got split, otherwise throw an error
        if ($lineInfo == null) {
            $this->params['_formError'] = sprintf(gt("This file does not appear to be delimited by '%s'. <br />Please specify a different delimiter.<br /><br />"), $this->params["delimiter"]);
            expSession::set("last_POST", $this->params);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit("");
        } else {
            //Setup the meta data (hidden values)
            $form = new form();
            $form->meta("controller", "forms");
            $form->meta("action", "import_csv_data_display");
            $form->meta("rowstart", $this->params["rowstart"]);
            $form->meta("use_header", $this->params["use_header"]);
            $form->meta("filename", $directory . "/" . $file->filename);
            $form->meta("delimiter", $this->params["delimiter"]);
            $form->meta("forms_id", $this->params["forms_id"]);

            for ($i = 0, $iMax = count($lineInfo); $i < $iMax; $i++) {
                if ($headerinfo != null) {
                    $title = $headerinfo[$i] . ' (' . $lineInfo[$i] .')';
                } else {
                    $title = $lineInfo[$i];
                }
                $form->register("column[$i]", $title, new dropdowncontrol("none", $fields));
            }
            $form->register("submit", "", new buttongroupcontrol(gt('Next'), "", gt('Cancel')));

            assign_to_template(array(
                "form_html" => $form->tohtml(),
            ));
        }
    }

    public function import_csv_data_display() {
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $file = fopen(BASE . $this->params["filename"], 'rb');
        $record = array();
        $records = array();
        $linenum = 1;

        // pull in the form control definitions here
        $f = new forms($this->params['forms_id']);
        $fields = array();
        foreach ($f->forms_control as $control) {
            $fields[$control->name] = $control->caption;
        }

        while (($filedata = fgetcsv($file, 2000, $this->params["delimiter"])) != false) {
            if ($linenum >= $this->params["rowstart"]) {
                $i = 0;
                foreach ($filedata as $field) {
                    if (!empty($this->params["column"][$i]) && $this->params["column"][$i] !== "none") {
                        $colname = $this->params["column"][$i];
                        $record[$colname] = trim($field);
                        $this->params['caption'][$i] = $fields[$colname];
                    } else {
                        unset($this->params['column'][$i]);
                    }
                    $i++;
                }
                $record['linenum'] = $linenum;
                $records[] = $record;
            }
            $linenum++;
        }
        fclose($file);
        ini_set('auto_detect_line_endings',$line_end);

        assign_to_template(array(
            "records" => $records,
            "params" => $this->params,
        ));
    }

    public function import_csv_data_add() {
        global $user;

        if (!empty($this->params['filename']) && (strpos($this->params['filename'], 'tmp/') === false || strpos($this->params['folder'], '..') !== false)) {
            header('Location: ' . URL_FULL);
            exit();  // attempt to hack the site
        }
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $file = fopen(BASE . $this->params["filename"], 'rb');
        $recordsdone = 0;
        $linenum = 1;
        $f = new forms($this->params['forms_id']);
        $f->updateTable();

        $fields = array();
        $multi_item_control_items = array();
        $multi_item_control_ids = array();
        foreach ($f->forms_control as $control) {
            $fields[$control->name] = expUnserialize($control->data);
            $ctltype = get_class($fields[$control->name]);
            if (in_array($ctltype,array('radiogroupcontrol','dropdowncontrol'))) {
                if (!array_key_exists($control->id,$multi_item_control_items)) {
                    $multi_item_control_items[$control->name] = null;
                    $multi_item_control_ids[$control->name] = $control->id;
                }
            }
        }

        while (($filedata = fgetcsv($file, 2000, $this->params["delimiter"])) != false) {
            if ($linenum >= $this->params["rowstart"] && in_array($linenum,$this->params['importrecord'])) {
                $i = 0;
                $db_data = new stdClass();
                $db_data->ip = '';
                $db_data->sef_url = '';
                $db_data->user_id = $user->id;
                $db_data->timestamp = time();
                $db_data->referrer = '';
                $db_data->location_data = '';
                foreach ($filedata as $field) {
                    if (!empty($this->params["column"][$i]) && $this->params["column"][$i] !== "none") {
                        $colname = $this->params["column"][$i];
                        $control_type = get_class($fields[$colname]);
                        $params[$colname] = $field;
                        $def = call_user_func(array($control_type, "getFieldDefinition"));
                        if (!empty($def)) {
                            $db_data->$colname = call_user_func(array($control_type, 'convertData'), $colname, $params);
                        }
                        if (!empty($db_data->$colname) && array_key_exists($colname, $multi_item_control_items) && !in_array($db_data->$colname, $multi_item_control_items[$colname])) {
                            $multi_item_control_items[$colname][$db_data->$colname] = $db_data->$colname;
                        }
                    }
                    $i++;
                }
                $f->insertRecord($db_data);
                $recordsdone++;
            }
            $linenum++;
        }

        fclose($file);
        ini_set('auto_detect_line_endings',$line_end);

        // update multi-item forms controls
        if (!empty($multi_item_control_ids)) {
            foreach ($multi_item_control_ids as $key=>$control_id) {
                $fc = new forms_control($control_id);
                $ctl = expUnserialize($fc->data);
                ksort($multi_item_control_items[$key]);
                $ctl->items = $multi_item_control_items[$key];
                $fc->data = serialize($ctl);
                $fc->update();
            }
        }
        unlink(BASE . $this->params["filename"]);
        flash('notice', $recordsdone.' '.gt('Records Imported'));
        expHistory::back();
    }

}

?>