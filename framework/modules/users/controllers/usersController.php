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
 * @package    Modules
 */
/** @define "BASE" "../../../.." */

class usersController extends expController {
    public $basemodel_name = 'user';
//    protected $remove_permissions = array(
//        'create',
//        'edit'
//    );
    protected $manage_permissions = array(
        'toggle_extension' => 'Activate Extensions',
        'kill_session'     => 'End Sessions',
        'boot_user'        => 'Boot Users',
        'userperms'        => 'User Permissions',
        'groupperms'       => 'Group Permissions',
        'import'           => 'Import Users',
        'export'           => 'Export Users',
        'update'           => 'Update Users',
        'show'             => 'Show User',
        'showall'          => 'Show Users',
        'getUsersByJSON'   => 'Get Users',
    );

    static function displayname() {
        return gt("User Manager");
    }

    static function description() {
        return gt("This is the user management module. It allows for creating user, editing user, etc.");
    }

    static function hasSources() {
        return false;
    }

    static function hasContent() {
        return false;
    }

    static function canImportData() {
        return true;
    }

    public function manage() {
        global $user;

        expHistory::set('manageable', $this->params);
//        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
//        $order = empty($this->config['order']) ? 'username' : $this->config['order'];
        if ($user->is_system_user == 1) {
//            $filter = 1; //'1';
            $where = '';
        } elseif ($user->isSuperAdmin()) {
//            $filter = 2; //"is_system_user != 1";
            $where = "is_system_user != 1";
        } else {
//            $filter = 3; //"is_admin != 1";
            $where = "is_admin != 1";
        }
        $page = new expPaginator(array(
                    'model'=>'user',
                    'where'=>$where,
//                    'limit'=>$limit,
//                    'order'=>$order,
                    'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array(
                        gt('Username')=>'username',
                        gt('First Name')=>'firstname',
                        gt('Last Name')=>'lastname',
                        gt('Is Admin')=>'is_acting_admin',
                    )
                ));

        assign_to_template(array('page'=>$page));
//        assign_to_template(array(
//            'filter' => $filter
//        ));
    }

    public function create() {
        redirect_to(array('controller' => 'users', 'action' => 'edituser'));
//        $this->edituser();
    }

    public function edituser() {
        global $user, $db;

        // set history
        expHistory::set('editable', $this->params);
        expSession::set("userkey", sha1(microtime()));
        expSession::clearCurrentUserSessionCache();

        $id = !empty($this->params['id']) ? $this->params['id'] : null;

        // check to see if we should be editing.  You either need to be an admin, or editing own account.
        if ($user->isAdmin() || ($user->id == $id && !$user->globalPerm('prevent_profile_change'))) {
            $u = new user($id);
            if ($u->isSuperAdmin() && $user->isActingAdmin()) {  // prevent regular admin's from editing super-admins
                flash('error', gt('You do not have the proper permissions to edit this user'));
                expHistory::back();
            }
        } else {
            flash('error', gt('You do not have the proper permissions to edit this user'));
            expHistory::back();
        }
        $active_extensions = $db->selectObjects('profileextension', 'active=1', 'rank');

        //If there is no image uploaded, use the default avatar
        if (empty($u->image)) $u->image = PATH_RELATIVE . "framework/modules/users/assets/images/avatar_not_found.jpg";

        assign_to_template(array(
            'edit_user'  => $u,
            'extensions' => $active_extensions,
            "userkey"    => expSession::get("userkey")
        ));

        if ($user->isAdmin()) {
            $page = new expPaginator(array(
                'model'      => 'group',
                'where'      => 1,
                'limit'      => (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
                'order'      => empty($this->config['order']) ? 'name' : $this->config['order'],
                'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
                'columns'    => array(
                    gt('Name')        => 'name',
                    gt('Description') => 'description',
                ),
                'controller' => $this->baseclassname,
                'action'     => $this->params['action'],
            ));

            assign_to_template(array(
                'groups' => $page,
                'mygroups' => $u->getGroupMemberships(),
            ));
        }
    }

    public function update() {
        global $user, $db;

        // get the id of user we are editing, if there is one
        $id = !empty($this->params['id']) ? $this->params['id'] : null;
        if ((($user->id == $id) || $user->isAdmin()) && $this->params['userkey'] != expSession::get("userkey")) expHistory::back();

        // make sure this user should be updating user accounts
        if (!$user->isLoggedIn() && SITE_ALLOW_REGISTRATION == 0) {
            flash('error', gt('This site does not allow user registrations'));
            expHistory::back();
        } elseif (!$user->isAdmin() && ($user->isLoggedIn() && $user->id != $id) && !$user->globalPerm('prevent_profile_change')) {
            flash('error', gt('You do not have permission to edit this user account'));
            expHistory::back();
        }

        // if this is a new user account we need to check the password.
        // the password fields wont come thru on an edit. Otherwise we will
        // just update the existing account.
        if (!empty($id)) {
            $u = new user($id);
            $u->update($this->params);
            if ($user->isAdmin() && $user->id != $id) {
                flash('message', gt('Account information for') . ' ' . $u->username . ' ' . gt('has been updated.'));
            } else {
                flash('message', gt('Thank you') . ' ' . $u->firstname . '.  ' . gt('Your account information has been updated.'));
            }
            if ($user->id == $id) {
                $_SESSION[SYS_SESSION_KEY]['user'] = $u;
                $user = $u;
            }
        } else {
            $u = new user($this->params);
            $ret = $u->setPassword($this->params['pass1'], $this->params['pass2']);
            if ($ret != true) expValidator::failAndReturnToForm($ret, $this->params);
            $u->save();
            if ($user->isAdmin()) {
                flash('message', gt('Created new user account for') . ' ' . $u->username);
            } else {
                user::login($u->username, $this->params['pass1']);
                flash('message', gt('Thank you') . ' ' . $u->firstname . '.  ' . gt('Your new account has been created.'));
            }
        }

        // update the user profiles
        if (!empty($u->id)) {
            $this->params['user_id'] = $u->id;
            // get the active profile extensions and save them out
            $active_extensions = $db->selectObjects('profileextension', 'active=1');
            foreach ($active_extensions as $pe) {
                if (is_file(BASE . $pe->classfile)) {
                    include_once(BASE . $pe->classfile);
                    $ext = new $pe->classname();
                    $db->delete($ext->tablename, 'user_id=' . $u->id);
                    $ext->update($this->params);
                }
            }
        }

        // update group membership assignment
        if (!empty($this->params['member'])) {
            $old_groups = $db->selectObjects('groupmembership', 'member_id=' . $u->id);
//            $db->delete('groupmembership', 'member_id=' . $u->id);  // start from scratch
            $memb = new stdClass();
            $memb->member_id = $u->id;
            foreach ($this->params['member'] as $grp) {
                $memb->group_id = $grp;
                $memb->is_admin = false;
                foreach ($old_groups as $oldgroup) {
                    if ($oldgroup->group_id == $grp) {
                        if ($oldgroup->is_admin) $memb->is_admin = true;  // retain group admin setting
                    }
                }
                $db->insertObject($memb, 'groupmembership');
            }
            if ($u->id == $user->id) expSession::triggerRefresh();
        }

        // if this is a new account then we will check to see if we need to send
        // a welcome message or admin notification of new accounts.
        if (empty($id)) {
            // Calculate Group Memberships for newly created users.  Any groups that
            // are marked as 'inclusive' automatically pick up new users.  This is the part
            // of the code that goes out, finds those groups, and makes the new user a member
            // of them.
            $memb = new stdClass();
            $memb->member_id = $u->id;
            // Also need to process the groupcodes, for promotional signup
//            $code_where = '';
//            if (isset($this->params['groupcode']) && $this->params['groupcode'] != '') {
//                $code_where = " OR code='" . $this->params['groupcode'] . "'";
//            }
            // Add to default plus any groupcode groups
//            foreach ($db->selectObjects('group', 'inclusive=1' . $code_where) as $g) {
            foreach ($db->selectObjects('group', 'inclusive=1') as $g) {
                $memb->group_id = $g->id;
                $db->insertObject($memb, 'groupmembership');
            }

            // if we added the user to any group than we need to reload their permissions
//            expPermissions::load($u);  //FIXME why are we doing this? this loads the edited user perms over the current user???

            //signup email stuff
            if (USER_REGISTRATION_SEND_WELCOME && !empty($u->email)) {
                $msg = $u->firstname . ", \n\n";
                $msg .= sprintf(USER_REGISTRATION_WELCOME_MSG, $u->firstname, $u->lastname, $u->username);

                $mail = new expMail();
                $mail->quickSend(array(
                    'text_message' => $msg,
                    'to'           => array(trim($u->email) => trim(user::getUserAttribution($u->id))),
                    'from'         => array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
                    'subject'      => USER_REGISTRATION_WELCOME_SUBJECT,
                ));

                flash('message', gt('A welcome email has been sent to') . ' ' . $u->email);
            }

            // send and email notification to the admin of the site.
            if (USER_REGISTRATION_SEND_NOTIF && !$user->isAdmin()) {
                $msg = gt("When") . ": " . date("F j, Y, g:i a") . "\n\n";
                $msg .= gt("Their name is") . ": " . $u->firstname . " " . $u->lastname . "\n\n";

                $mail = new expMail();
                $mail->quickSend(array(
                    'text_message' => $msg,
                    'to'           => trim(USER_REGISTRATION_ADMIN_EMAIL),
                    'from'         => array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
                    'subject'      => USER_REGISTRATION_NOTIF_SUBJECT,
                ));
            }
        }

        // we need to reload our updated profile if we just edited our own account
        if ($id == $user->id) {
            $user->getUserProfile();
//            expPermissions::load($user);  // not sure this is necessary since we can't add groups here
        }

        expHistory::back();
    }

    public function delete() {
        global $user, $db;
        if (!$user->isAdmin()) {
            flash('error', gt('You do not have permission to delete user accounts'));
            expHistory::back();
        }

        if (empty($this->params['id'])) {
            flash('error', gt('No user selected.'));
            expHistory::back();
        }

        // remove group memeberships
        $db->delete('groupmembership', 'member_id=' . $this->params['id']);

        // remove user permissions
        $db->delete('userpermission', 'uid=' . $this->params['id']);

        //remove user profiles
        $active_extensions = $db->selectObjects('profileextension', 'active=1');
        foreach ($active_extensions as $pe) {
            if (is_file(BASE . $pe->classfile)) {
                include_once(BASE . $pe->classfile);
                $ext = new $pe->classname();
                $db->delete($ext->table, 'user_id=' . $this->params['id']);
            }
        }

        // remove user address
        $address = new address();
        $db->delete($address->table, 'user_id=' . $this->params['id']);

        parent::delete();
    }

    public function manage_sessions() {
//        global $db, $user;
        global $db;

        expHistory::set('manageable', $this->params);

        //cleans up any old sessions
        if (SESSION_TIMEOUT_ENABLE == true) {
            $db->delete('sessionticket', 'last_active < ' . (time() - SESSION_TIMEOUT));
//        } else {
//            $db->delete('sessionticket', '1');
        }

        if (isset($this->params['id']) && $this->params['id'] == 0) {
            $sessions = $db->selectObjects('sessionticket', "uid<>0");
            $filtered = 1;
        } else {
            $sessions = $db->selectObjects('sessionticket');
            $filtered = 0;
        }

//	    $sessions = $db->selectObjects('sessionticket');
        for ($i = 0, $iMax = count($sessions); $i < $iMax; $i++) {
            $sessions[$i]->user = new user($sessions[$i]->uid);
            if ($sessions[$i]->uid == 0) {
                $sessions[$i]->user->id = 0;
            }
            $sessions[$i]->duration = expDateTime::duration($sessions[$i]->last_active, $sessions[$i]->start_time);
        }

        assign_to_template(array(
            'sessions' => $sessions,
            'filter'   => $filtered
        ));
    }

    public function kill_session() {
        global $user, $db;
        $ticket = $db->selectObject('sessionticket', "ticket='" . preg_replace('/[^A-Za-z0-9.]/', '', $this->params['ticket']) . "'");
        if ($ticket) {
            $u = new user($ticket->uid);
            if ($user->isSuperAdmin() || ($user->isActingAdmin() && !$u->isAdmin())) {
                // We can only kick the user if they are A) not an acting admin, or
                // B) The current user is a super user and the kicked user is not.
                $db->delete('sessionticket', "ticket='" . $ticket->ticket . "'");
            }
        }
        expHistory::back();
    }

    public function boot_user() {
        global $user, $db;
        if (!empty($this->params['id'])) {
            $u = new user($this->params['id']);
            if ($user->isSuperAdmin() || ($user->isActingAdmin() && !$u->isAdmin())) {
                // We can only kick the user if they are A) not an acting admin, or
                // B) The current user is a super user and the kicked user is not.
                $db->delete('sessionticket', 'uid=' . $u->id);
            }
        }
        expHistory::back();
    }

    /**
     * This function scans two directories and searches for php files to add to the extensions database.
     * If you have added new extensions since the last time you have visited the page, it will add them to the database
     * in effect enabling your new extension to be tacked onto users profiles. You then have to enable it in the menu, but at least
     * now it is in the system and when the user goes to edit his profile, it will check for extensions and this one will be in!
     *
     * @global string This function uses the global $db save information through the Exponenet database connection.
     */
    public function manage_extensions() {
        global $db;

        // set history
        expHistory::set('manageable', $this->params);

        // Lets find all the user profiles availabe and then see if they are
        // in the database yet.  If not we will add them.
        $ext_dirs = array(
            'framework/modules/users/extensions',
            'themes/' . DISPLAY_THEME . '/modules/users/extensions'
        );
        foreach ($ext_dirs as $dir) {
            if (is_readable(BASE . $dir)) {
                $dh = opendir(BASE . $dir);
                while (($file = readdir($dh)) !== false) {
                    if (is_file(BASE . "$dir/$file") && is_readable(BASE . "$dir/$file") && substr($file, 0, 1) != '_' && substr($file, 0, 1) != '.') {
                        include_once(BASE . "$dir/$file");
                        $classname = substr($file, 0, -4);
                        $class = new $classname();
                        $extension = $db->selectObject('profileextension', "title='" . $class->name() . "'");
                        if (empty($extension->id)) {
                            $pe = new profileextension();
                            $pe->title = $class->name();
                            $pe->body = $class->description();
                            $pe->classfile = "$dir/$file";
                            $pe->classname = $classname;
                            $pe->save();
                        }
                    }
                }
            }
        }

        $page = new expPaginator(array(
            'model'      => 'profileextension',
            'where'      => 1,
            'limit'      => 25,
            'order'      => 'title',
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns'    => array(
                gt('Name')        => 'title',
                gt('Description') => 'body',
                gt('Active')      => 'active'
            ),
            'controller' => $this->baseclassname,
            'action'     => $this->params['action'],
        ));

        assign_to_template(array(
            'page' => $page
        ));
    }

    public function manage_groups() {
        expHistory::set('manageable', $this->params);
        $page = new expPaginator(array(
            'model'      => 'group',
            'where'      => 1,
//            'limit'      => (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
            'order'      => empty($this->config['order']) ? 'name' : $this->config['order'],
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns'    => array(
                gt('Name')        => 'name',
                gt('Description') => 'description',
                gt('Type')        => 'inclusive',
            ),
            'controller' => $this->baseclassname,
            'action'     => $this->params['action'],
        ));

        foreach ($page->records as $key=>$group) {
            $page->records[$key]->members = group::getUsersInGroup($group->id);
        }

        assign_to_template(array(
            'page' => $page,
        ));
    }

    public function reset_password() {
        expHistory::set('editable', $this->params);
    }

    public function send_new_password() {
        global $db;

        // find the user
        $this->params['username'] = expString::escape($this->params['username']);
        $u = user::getUserByName($this->params['username']);
        if (empty($u)) {
            $u = user::getUserByEmail($this->params['username']);
            if (!empty($u) && $u->count > 1) {
                expValidator::failAndReturnToForm(gt('That email address applies to more than one user account, please enter your username instead.'));
            }
        }
        $u = new user($u->id);

        if (!expValidator::check_antispam($this->params)) {
            expValidator::failAndReturnToForm(gt('Anti-spam verification failed.  Please try again.'), $this->params);
        } elseif (empty($u->id)) {
            expValidator::failAndReturnToForm(gt('We were unable to find an account with that username/email'), $this->params);
        } elseif (empty($u->email)) {
            expValidator::failAndReturnToForm(gt('Your account does not appear to have an email address.  Please contact the site administrators to reset your password'), $this->params);
        } elseif ($u->isAdmin()) {
            expValidator::failAndReturnToForm(gt('You cannot reset passwords for an administrator account.'), $this->params);
        }

        $tok = new stdClass();
        $tok->uid = $u->id;
        $tok->expires = time() + 2 * 3600;
        $tok->token = md5(time()) . uniqid('');

        $email = $template = expTemplate::get_template_for_action($this, 'email/password_reset_email', $this->loc);
        $email->assign('token', $tok);
        $email->assign('username', $u->username);
        $msg = $email->render();
        $mail = new expMail();
        $mail->quickSend(array(
            'html_message' => $msg,
            'text_message' => expString::html2text($msg),
            'to'           => array(trim($u->email) => trim(user::getUserAttribution($u->id))),
            'from'         => array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
            'subject'      => gt('Password Reset Requested'),
        ));

        $db->delete('passreset_token', 'uid=' . $u->id);
        $db->insertObject($tok, 'passreset_token');
        flash('message', gt('An email has been sent to you with instructions on how to finish resetting your password.') . '<br><br>' .
            gt('This new password request is only valid for 2 hours.  If you have not completed the password reset process within 2 hours, the new password request will expire.'));

        expHistory::back();
    }

    public function confirm_password_reset() {
        global $db;

        $db->delete('passreset_token', 'expires < ' . time());
        $tok = $db->selectObject('passreset_token', 'uid=' . intval($this->params['uid']) . " AND token='" . preg_replace('/[^A-Za-z0-9]/', '', expString::escape($this->params['token'])) . "'");
        if ($tok == null) {
            flash('error', gt('Your password reset request has expired.  Please try again.'));
            expHistory::back();
        }

        // create the password
        $newpass = '';
        for ($i = 0, $iMax = mt_rand(12, 20); $i < $iMax; $i++) {
            $num = mt_rand(48, 122);
            if (($num > 97 && $num < 122) || ($num > 65 && $num < 90) || ($num > 48 && $num < 57)) $newpass .= chr($num);
            else $i--;
        }

        // look up the user
        $u = new user($tok->uid);

        // get the email message body and render it
        $email = $template = expTemplate::get_template_for_action($this, 'email/confirm_password_email', $this->loc);
        $email->assign('newpass', $newpass);
        $email->assign('username', $u->username);
        $msg = $email->render();

        // send the new password to the user
        $mail = new expMail();
        $mail->quickSend(array(
            'html_message' => $msg,
            'text_message' => expString::html2text($msg),
            'to'           => array(trim($u->email) => trim(user::getUserAttribution($u->id))),
            'from'         => array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
            'subject'      => gt('The account password for') . ' ' . HOSTNAME . ' ' . gt('was reset'),
        ));

        // Save new password
        $u->update(array('password' => user::encryptPassword($newpass)));

        // cleanup the reset token
        $db->delete('passreset_token', 'uid=' . $tok->uid);

        flash('message', gt('Your password has been reset and the new password has been emailed to you.'));

        // send the user the login page.
        redirect_to(array('controller' => 'login', 'action' => 'loginredirect'));
    }

    public function change_password() {
        global $user;

        expHistory::set('editable', $this->params);
        $id = isset($this->params['id']) ? $this->params['id'] : $user->id;

        if ($user->isAdmin() || ($user->id == $id)) {
            $isuser = ($user->id == $id) ? 1 : 0;
            $u = new user($id);
        } else {
            flash('error', gt('You do not have the proper permissions to do that'));
            expHistory::back();
        }
        assign_to_template(array(
            'u'      => $u,
            'isuser' => $isuser
        ));
    }

    public function save_change_password() {
        global $user;

        $isuser = ($this->params['uid'] == $user->id) ? 1 : 0;

        if (!$user->isAdmin() && !$isuser) {
            flash('error', gt('You do not have permissions to change this users password.'));
            expHistory::back();
        }

        if (($isuser && empty($this->params['password'])) || (!empty($this->params['password']) && $user->password != user::encryptPassword($this->params['password']))) {
            flash('error', gt('The current password you entered is not correct.'));
            expHistory::returnTo('editable');
        }
        //eDebug($user);
        $u = new user(intval($this->params['uid']));

        $ret = $u->setPassword($this->params['new_password1'], $this->params['new_password2']);
        //eDebug($u, true);
        if (is_string($ret)) {
            flash('error', $ret);
            expHistory::returnTo('editable');
        } else {
            $params = array();
            $params['is_admin'] = !empty($u->is_admin);
            $params['is_acting_admin'] = !empty($u->is_acting_admin);
            $u->update($params);
        }

        if (!$isuser) {
            flash('message', gt('The password for') . ' ' . $u->username . ' ' . gt('has been changed.'));
        } else {
            $user->password = $u->password;
            flash('message', gt('Your password has been changed.'));
        }
        expHistory::back();
    }

    public function edit_userpassword() {
        expHistory::set('editable', $this->params);
        if (empty($this->params['id'])) {
            flash('error', gt('You must specify the user whose password you want to change'));
            expHistory::back();
        }

        $u = new user($this->params['id']);
        assign_to_template(array(
            'u' => $u
        ));
    }

    public function update_userpassword() {
        global $user;

        if (!$user->isAdmin() && $this->params['id'] != $user->id) {
            flash('error', gt('You do not have permissions to change this users password.'));
            expHistory::back();
        }

        if (empty($this->params['id'])) {
            expValidator::failAndReturnToForm(gt('You must specify the user whose password you want to change'), $this->params);
        }

        if (empty($this->params['new_password1'])) {
            expValidator::setErrorField('new_password1');
            expValidator::failAndReturnToForm(gt('You must specify a new password for this user.'), $this->params);
        }

        if (empty($this->params['new_password2'])) {
            expValidator::setErrorField('new_password2');
            expValidator::failAndReturnToForm(gt('You must confirm the password.'), $this->params);

        }

        $u = new user($this->params['id']);
        $ret = $u->setPassword($this->params['new_password1'], $this->params['new_password2']);
        if (is_string($ret)) {
            expValidator::setErrorField('new_password1');
            $this->params['new_password1'] = '';
            $this->params['new_password2'] = '';
            expValidator::failAndReturnToForm($ret, $this->params);
        } else {
            $u->save(true);
        }

        flash('message', gt('Password reset for user') . ' ' . $u->username);
        expHistory::back();
    }

    public function edit_group() {
        global $db;

        expHistory::set('editable', $this->params);
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $group = new group($id);
        $group->redirect = $db->selectValue('section', 'id', "sef_name='" . $group->redirect . "'");
        assign_to_template(array(
            'record' => $group
        ));
    }

    public function manage_group_memberships() {
        global $db, $user;
//        expHistory::set('manageable', $this->params);

        $memb = $db->selectObject('groupmembership', 'member_id=' . $user->id . ' AND group_id=' . $this->params['id'] . ' AND is_admin=1');

        $perm_level = 0;
        if ($memb) $perm_level = 1;
        if (expPermissions::check('user_management', expCore::makeLocation('administrationmodule'))) $perm_level = 2;

        $group = $db->selectObject('group', 'id=' . $this->params['id']);
        $users = user::getAllUsers(0);

        $members = array();
        $admins = array();
        foreach ($db->selectObjects('groupmembership', 'group_id=' . $group->id) as $m) {
            $members[] = $m->member_id;
            if ($m->is_admin == 1) {
                $admins[] = $m->member_id;
            }
        }

        for ($i = 0, $iMax = count($users); $i < $iMax; $i++) {
            if (in_array($users[$i]->id, $members)) {
                $users[$i]->is_member = 1;
            } else {
                $users[$i]->is_member = 0;
            }

            if (in_array($users[$i]->id, $admins)) {
                $users[$i]->is_admin = 1;
            } else {
                $users[$i]->is_admin = 0;
            }
        }

        //$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $page = new expPaginator(array(
//          'model'=>'user',
            'records'    => $users,
            'where'      => 1,
//          'limit'=>9999,  // unless we're showing all users on a page at once, there's no way to
            // add all users to a group, since it's rebuilding the group on save...
            'order'      => empty($this->config['order']) ? 'username' : $this->config['order'],
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns'    => array(
                gt('Username')   => 'username',
                gt('First Name') => 'firstname',
                gt('Last Name')  => 'lastname',
                gt('Is Member')  => 'is_member',
                gt('Is Admin')   => 'is_admin',
            ),
            'controller' => $this->baseclassname,
            'action'     => $this->params['action'],
        ));

        assign_to_template(array(
            'page'       => $page,
            'group'      => $group,
            'users'      => $users,
            'canAdd'     => (count($members) < count($users) ? 1 : 0),
            'hasMember'  => (count($members) > 0 ? 1 : 0),
            'perm_level' => $perm_level,
        ));
    }

    public function update_group() {
        global $db;

        $group = new group();
        if (!empty($this->params['redirect'])) {
            $this->params['redirect'] = $db->selectValue('section', 'sef_name', 'id=' . intval($this->params['redirect']));
        }
        $group->update($this->params);
        expHistory::back();
    }

    public function delete_group() {
        global $user, $db;
        if (!$user->isAdmin()) {
            flash('error', gt('You do not have permission to delete user groups'));
            expHistory::back();
        }

        if (empty($this->params['id'])) {
            flash('error', gt('No group selected.'));
            expHistory::back();
        }

        // remove group members
        $db->delete('groupmembership', 'group_id=' . $this->params['id']);

        // remove group permissions
        $db->delete('grouppermission', 'gid=' . $this->params['id']);

        // remove group
        $db->delete('group', 'id=' . $this->params['id']);
        expHistory::back();
    }

    public function toggle_extension() {
        global $db;
        if (isset($this->params['id'])) $db->toggle('profileextension', 'active', 'id=' . $this->params['id']);
        expHistory::back();
    }

    public function update_memberships() {
//        global $user, $db;
        global $db;

        //$memb = $db->selectObject('groupmembership','member_id='.$user->id.' AND group_id='.$this->params['id'].' AND is_admin=1');
        $group = $db->selectObject('group', 'id=' . $this->params['id']);

        $db->delete('groupmembership', 'group_id=' . $group->id);
        $memb = new stdClass();
        $memb->group_id = $group->id;
        if ($this->params['memdata'] != "") {
            foreach ($this->params['memdata'] as $u => $str) {
                $memb->member_id = $u;
                $memb->is_admin = $str['is_admin'];
                $db->insertObject($memb, 'groupmembership');
            }
        }
        expSession::triggerRefresh();
        expHistory::back();
    }

    public function getUsersByJSON() {
        $modelname = $this->basemodel_name;
        $results = 25; // default get 25
        $startIndex = 0; // default start at 0
        $sort = null; // default don't sort
        $dir = 'asc'; // default sort dir is asc
        $sort_dir = SORT_ASC;

        // How many records to get?
        if (strlen($this->params['results']) > 0) {
            $results = intval($this->params['results']);
        }

        // Start at which record?
        if (strlen($this->params['startIndex']) > 0) {
            $startIndex = intval($this->params['startIndex']);
        }

        // Sorted?
        if (strlen($this->params['sort']) > 0) {
            $sort = expString::escape($this->params['sort']);
            if ($sort = 'id') $sort = 'username';
        }

        if (!empty($this->params['filter'])) {
            switch ($this->params['filter']) {
                case '1' :
                    $filter = '';
                    break;
                case '2' :
                    $filter = "is_system_user != 1";
                    break;
                case '3' :
                    $filter = "is_admin != 1";
            }
        }

//        if (!empty($_GET['filter'])) {
//            switch ($_GET['filter']) {
//                case '1' :
//                    $filter = '';
//                    break;
//                case '2' :
//                    $filter = "is_system_user != 1";
//                    break;
//                case '3' :
//                    $filter = "is_admin != 1";
//            }
//        }

        // Sort dir?
        if ((strlen($this->params['dir']) > 0) && ($this->params['dir'] == 'desc')) {
            $dir = 'desc';
            $sort_dir = SORT_DESC;
        } else {
            $dir = 'asc';
            $sort_dir = SORT_ASC;
        }

        if (!empty($this->params['query'])) {

            $this->params['query'] = expString::escape($this->params['query']);
            $totalrecords = $this->$modelname->find('count', (empty($filter) ? '' : $filter . " AND ") . "(username LIKE '%" . $this->params['query'] . "%' OR firstname LIKE '%" . $this->params['query'] . "%' OR lastname LIKE '%" . $this->params['query'] . "%' OR email LIKE '%" . $this->params['query'] . "%')");

            $users = $this->$modelname->find('all', (empty($filter) ? '' : $filter . " AND ") . "(username LIKE '%" . $this->params['query'] . "%' OR firstname LIKE '%" . $this->params['query'] . "%' OR lastname LIKE '%" . $this->params['query'] . "%' OR email LIKE '%" . $this->params['query'] . "%')", $sort . ' ' . $dir, $results, $startIndex);
            for ($i = 0, $iMax = count($users); $i < $iMax; $i++) {
                if (ECOM == 1) {
                    $users[$i]->usernamelabel = "<a href='viewuser/{$users[$i]->id}'  class='fileinfo'>{$users[$i]->username}</a>";
                } else {
                    $users[$i]->usernamelabel = $users[$i]->username;
                }
            }

            $returnValue = array(
                'recordsReturned' => count($users),
                'totalRecords'    => $totalrecords,
                'startIndex'      => $startIndex,
                'sort'            => $sort,
                'dir'             => $dir,
                'pageSize'        => $results,
                'records'         => $users
            );
        } else {

            $totalrecords = $this->$modelname->find('count', $filter);

            $users = $this->$modelname->find('all', $filter, $sort . ' ' . $dir, $results, $startIndex);

            for ($i = 0, $iMax = count($users); $i < $iMax; $i++) {
                if (ECOM == 1) {
                    $users[$i]->usernamelabel = "<a href='viewuser/{$users[$i]->id}'  class='fileinfo'>{$users[$i]->username}</a>";
                } else {
                    $users[$i]->usernamelabel = $users[$i]->username;
                }
            }

            $returnValue = array(
                'recordsReturned' => count($users),
                'totalRecords'    => $totalrecords,
                'startIndex'      => $startIndex,
                'sort'            => $sort,
                'dir'             => $dir,
                'pageSize'        => $results,
                'records'         => $users
            );

        }

        echo json_encode($returnValue);
    }

    public function viewuser() {
        global $user;

        if (!empty($this->params['id']) && $user->isAdmin()) {
            $u = new user($this->params['id']);
        } elseif (!empty($user->id)) {
            $u = $user;
        } else {
            flash('error', gt('You may not view this user'));
            expHistory::back();
        }
        $address = new address();

        $billings = $address->find('all', 'user_id=' . $u->id . ' AND is_billing = 1');
        $shippings = $address->find('all', 'user_id=' . $u->id . ' AND is_shipping = 1');

        // build out a SQL query that gets all the data we need and is sortable.
        $sql = 'SELECT o.*, b.firstname as firstname, b.billing_cost as total, b.middlename as middlename, b.lastname as lastname, os.title as status, ot.title as order_type ';
        $sql .= 'FROM ' . DB_TABLE_PREFIX . '_orders o, ' . DB_TABLE_PREFIX . '_billingmethods b, ';
        $sql .= DB_TABLE_PREFIX . '_order_status os, ';
        $sql .= DB_TABLE_PREFIX . '_order_type ot ';
        $sql .= 'WHERE o.id = b.orders_id AND o.order_status_id = os.id AND o.order_type_id = ot.id AND o.purchased > 0 AND user_id =' . $u->id;

        $limit = (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10;
        $order = !empty($this->params['order']) ? $this->params['order'] : 'purchased';
        $dir = !empty($this->params['dir']) ? $this->params['dir'] : 'DESC';
        //eDebug($sql, true);
        $orders = new expPaginator(array(
            //'model'=>'order',
            'sql'        => $sql,
            'limit'      => $limit,
            'order'      => $order,
            'dir'        => $dir,
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns'    => array(
                gt('Order #')        => 'invoice_id',
                gt('Total')          => 'total',
                gt('Date Purchased') => 'purchased',
//                gt('Type')           => 'order_type_id',
                gt('Status')         => 'order_status_id',
                gt('Ref')            => 'orig_referrer',
            ),
            'controller' => $this->params['controller'],
            'action'     => $this->params['action'],
        ));

        assign_to_template(array(
            'u'         => $u,
            'billings'  => $billings,
            'shippings' => $shippings,
            'orders'    => $orders,
        ));
    }

    public function userperms() {
        global $user;

        if (!empty($this->params['mod']) && $user->isAdmin()) {
            $loc = expCore::makeLocation($this->params['mod'], isset($this->params['src']) ? $this->params['src'] : null, isset($this->params['int']) ? $this->params['int'] : null);
            $users = array();
            $modclass = expModules::getModuleClassName(($loc->mod));
            $mod = new $modclass();
            $perms = $mod->permissions($loc->int);
            $have_users = 0;
            foreach (user::getAllUsers(false) as $u) {
                $have_users = 1;
                foreach ($perms as $perm => $name) {
//        			$var = 'perms_'.$perm;
                    if (expPermissions::checkUser($u, $perm, $loc, true)) {
                        $u->$perm = 1;
                    } else if (expPermissions::checkUser($u, $perm, $loc)) {
                        $u->$perm = 2;
                    } else {
                        $u->$perm = 0;
                    }
                }
                $users[] = $u;
            }

            $p[gt("User Name")] = 'username';
            $p[gt("First Name")] = 'firstname';
            $p[gt("Last Name")] = 'lastname';
            foreach ($mod->permissions() as $value) {
                //        $p[gt($value)]=$key;
                $p[gt($value)] = 'no-sort';
            }

//            if (SEF_URLS == 1) {
                $page = new expPaginator(array(
                    //'model'=>'user',
//                    'limit'      => (isset($this->params['limit']) ? $this->params['limit'] : 20),
                    'records'    => $users,
                    //'sql'=>$sql,
                    'order'      => (isset($this->params['order']) ? $this->params['order'] : 'username'),
                    'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'ASC'),
                    'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
                    'controller' => $this->params['controller'],
                    'action'     => $this->params['action'],
                    'columns'    => $p,
                ));
//            } else {
//                $page = new expPaginator(array(
//                    //'model'=>'user',
////                    'limit'      => (isset($this->params['limit']) ? $this->params['limit'] : 20),
//                    'records'    => $users,
//                    //'sql'=>$sql,
//                    'order'      => (isset($this->params['order']) ? $this->params['order'] : 'username'),
//                    'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'ASC'),
//                    'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
//                    'controller' => $this->params['module'],
//                    'action'     => $this->params['action'],
//                    'columns'    => $p,
//                ));
//            }

            assign_to_template(array(
                'user_form'  => 1,
                'have_users' => $have_users,
                'users'      => $users,
                'page'       => $page,
                'perms'      => $perms,
                'loc'        => $loc,
//                'title'=>($modclass != 'navigationController' || ($modclass == 'navigationController' && !empty($loc->src))) ? $mod->name().' '.($modclass != 'containermodule' ? gt('module') : '').' ' : gt('Page'),
                'title'      => ($loc->mod != 'navigation' || ($loc->mod == 'navigation' && !empty($loc->src))) ? $mod->name() . ' ' . ($loc->mod != 'container' ? gt('module') : '') . ' ' : gt('Page'),
            ));
        } else {
//            echo SITE_403_HTML;
            notfoundController::handle_not_authorized();
        }
    }

    public function userperms_save() {
        global $user;

        $loc = expCore::makeLocation($this->params['mod'], isset($this->params['src']) ? $this->params['src'] : null, isset($this->params['int']) ? $this->params['int'] : null);
        foreach ($this->params['users'] as $u) {
            expPermissions::revokeAll($u, $loc);
        }
        foreach ($this->params['permdata'] as $k => $user_str) {
            $perms = array_keys($user_str);
            $u = user::getUserById($k);
            for ($i = 0, $iMax = count($perms); $i < $iMax; $i++) {
                expPermissions::grant($u, $perms[$i], $loc);
            }

            if ($k == $user->id) {
                expPermissions::load($user);
            }
        }
        expSession::triggerRefresh();
        expHistory::back();
    }

    public function groupperms() {
        global $user;

        if (!empty($this->params['mod']) && $user->isAdmin()) {
            $loc = expCore::makeLocation($this->params['mod'], isset($this->params['src']) ? $this->params['src'] : null, isset($this->params['int']) ? $this->params['int'] : null);
            $users = array(); // users = groups
            $modclass = expModules::getModuleClassName($loc->mod);
            $mod = new $modclass();
            $perms = $mod->permissions($loc->int);

            foreach (group::getAllGroups() as $g) {
                foreach ($perms as $perm => $name) {
//        			$var = 'perms_'.$perm;
                    if (expPermissions::checkGroup($g, $perm, $loc, true)) {
                        $g->$perm = 1;
                    } else if (expPermissions::checkGroup($g, $perm, $loc)) {
                        $g->$perm = 2;
                    } else {
                        $g->$perm = 0;
                    }
                }
                $users[] = $g;
            }

            $p[gt("Group")] = 'username';
            foreach ($mod->permissions() as $value) {
                //        $p[gt($value)]=$key;
                $p[gt($value)] = 'no-sort';
            }

//            if (SEF_URLS == 1) {
                $page = new expPaginator(array(
                    //'model'=>'user',
//                    'limit'      => (isset($this->params['limit']) ? $this->params['limit'] : 20),
                    'records'    => $users,
                    //'sql'=>$sql,
                    'order'      => (isset($this->params['order']) ? $this->params['order'] : 'name'),
                    'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'ASC'),
                    'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
                    'controller' => $this->params['controller'],
                    'action'     => $this->params['action'],
                    'columns'    => $p,
                ));
//            } else {
//                $page = new expPaginator(array(
//                    //'model'=>'user',
////                    'limit'      => (isset($this->params['limit']) ? $this->params['limit'] : 20),
//                    'records'    => $users,
//                    //'sql'=>$sql,
//                    'order'      => (isset($this->params['order']) ? $this->params['order'] : 'name'),
//                    'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'ASC'),
//                    'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
//                    'controller' => $this->params['module'],
//                    'action'     => $this->params['action'],
//                    'columns'    => $p,
//                ));
//            }

            assign_to_template(array(
                'user_form'  => 0,
                'is_group'   => 1,
                'have_users' => count($users) > 0, // users = groups
                'users'      => $users,
                'page'       => $page,
                'perms'      => $perms,
                'loc'        => $loc,
//                'title'=>($modclass != 'navigationController' || ($modclass == 'navigationController' && !empty($loc->src))) ? $mod->name().' '.($modclass != 'containermodule' ? gt('module') : '').' ' : gt('Page'),
                'title'      => ($loc->mod != 'navigation' || ($loc->mod == 'navigation' && !empty($loc->src))) ? $mod->name() . ' ' . ($loc->mod != 'container' ? gt('module') : '') . ' ' : gt('Page'),
            ));
        } else {
//            echo SITE_403_HTML;
            notfoundController::handle_not_authorized();
        }
    }

    public function groupperms_save() {
        $loc = expCore::makeLocation($this->params['mod'], isset($this->params['src']) ? $this->params['src'] : null, isset($this->params['int']) ? $this->params['int'] : null);
        foreach ($this->params['users'] as $g) {
            expPermissions::revokeAllGroup($g, $loc);
        }
        foreach ($this->params['permdata'] as $k => $group_str) {
            $perms = array_keys($group_str);
            $g = group::getGroupById($k);
            for ($i = 0, $iMax = count($perms); $i < $iMax; $i++) {
                expPermissions::grantGroup($g, $perms[$i], $loc);
            }
        }
        expSession::triggerRefresh();
        expHistory::back();
    }

    public function import() {
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

//            //Setup the mete data (hidden values)
//            $form = new form();
//            $form->meta("controller", "users");
//            $form->meta("action", "import_users_mapper");
//
//            //Register the dropdown menus
//            $form->register("delimiter", gt('Delimiter Character'), new dropdowncontrol(",", $delimiterArray));
//            $form->register("upload", gt('CSV File to Upload'), new uploadcontrol());
//            $form->register("use_header", gt('First Row is a Header'), new checkboxcontrol(0, 0));
//            $form->register("rowstart", gt('User Data begins in Row'), new textcontrol("1", 1, 0, 6));
//            $form->register("submit", "", new buttongroupcontrol(gt('Next'), "", gt('Cancel')));

            assign_to_template(array(
//                "form_html" => $form->tohtml(),
                'delimiters' => $delimiterArray,
            ));
        }
    }

    public function import_users_mapper() {
        //Check to make sure the user filled out the required input.
        //FIXME needs to be the newer fail form
        if (!is_numeric($this->params["rowstart"])) {
            unset($this->params["rowstart"]);
            $this->params['_formError'] = gt('The starting row must be a number.');
            expSession::set("last_POST", $this->params);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit('Redirecting...');
        }

        //Get the temp directory to put the uploaded file
        $directory = "tmp";

        //Get the file save it to the temp directory
        if ($_FILES["upload"]["error"] == UPLOAD_ERR_OK) {
            //	$file = file::update("upload",$directory,null,time()."_".$_FILES['upload']['name']);
            $file = expFile::fileUpload("upload", false, false, time() . "_" . $_FILES['upload']['name'], $directory.'/');
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
        $fh = fopen(BASE . $directory . "/" . $file->filename, "r");
        if (!empty($this->params["use_header"])) $this->params["rowstart"]++;
        for ($x = 0; $x < $this->params["rowstart"]; $x++) {
            $lineInfo = fgetcsv($fh, 2000, $this->params["delimiter"]);
            if ($x == 0 && !empty($this->params["use_header"])) $headerinfo = $lineInfo;
        }

        $colNames = array(
            "none"      => gt('--Disregard this column--'),
            "username"  => gt('Username'),
            "password"  => gt('Password'),
            "firstname" => gt('First Name'),
            "lastname"  => gt('Last Name'),
            "email"     => gt('Email Address')
        );

        //Check to see if the line got split, otherwise throw an error
        if ($lineInfo == null) {
            $this->params['_formError'] = sprintf(gt('This file does not appear to be delimited by "%s". <br />Please specify a different delimiter.<br /><br />'), $this->params["delimiter"]);
            expSession::set("last_POST", $this->params);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit("");
        } else {
            //Setup the meta data (hidden values)
            $form = new form();
            $form->meta("controller", "users");
            $form->meta("action", "import_users_process");
            $form->meta("rowstart", $this->params["rowstart"]);
            $form->meta("use_header", $this->params["use_header"]);
            $form->meta("filename", $directory . "/" . $file->filename);
            $form->meta("delimiter", $this->params["delimiter"]);
            for ($i = 0, $iMax = count($lineInfo); $i < $iMax; $i++) {
                if ($headerinfo != null) {
                    $title = $headerinfo[$i] . ' (' . $lineInfo[$i] .')';
                    if (array_key_exists($headerinfo[$i], $colNames)) {
                        $default = $headerinfo[$i];
                    } else {
                        $default = "none";
                    }
                } else {
                    $title = $lineInfo[$i];
                    $default = "none";
                }
                $form->register("column[$i]", $title, new dropdowncontrol($default, $colNames));
            }
            $form->register("submit", "", new buttongroupcontrol(gt('Next'), "", gt('Cancel')));

            assign_to_template(array(
                "form_html" => $form->tohtml(),
            ));
        }
    }

    public function import_users_process() {
        if (in_array("username", $this->params["column"]) == false) {
            $unameOptions = array(
                "FILN"    => gt('First Initial / Last Name'),
                "FILNNUM" => gt('First Initial / Last Name / Random Number'),
                "EMAIL"   => gt('Email Address'),
                "FNLN"    => gt('First Name / Last Name'));
        } else {
            $unameOptions = array("INFILE" => gt('Username Specified in CSV File'));
        }

        if (in_array("password", $this->params["column"]) == false) {
            $pwordOptions = array(
                "RAND"    => gt('Generate Random Passwords'),
                "DEFPASS" => gt('Use the Default Password Supplied Below'));
        } else {
            $pwordOptions = array("INFILE" => gt('Password Specified in CSV File'));
        }
        if (count($pwordOptions) == 1) {
            $disabled = true;
        } else {
            $disabled = false;
        }

//        $form = new form();
//        $form->meta("controller", "users");
//        $form->meta("action", "import_users_display");
//        $form->meta("column", $this->params["column"]);
//        $form->meta("delimiter", $this->params["delimiter"]);
//        $form->meta("use_header", $this->params["use_header"]);
//        $form->meta("filename", $this->params["filename"]);
//        $form->meta("rowstart", $this->params["rowstart"]);
//
//        $form->register("unameOptions", gt('User Name Generations Options'), new dropdowncontrol("INFILE", $unameOptions));
//        $form->register("pwordOptions", gt('Password Generation Options'), new dropdowncontrol("defpass", $pwordOptions));
//        $form->register("pwordText", gt('Default Password'), new textcontrol("", 10, $disabled));
//        $form->register("update", gt('Update users already in database, instead of creating new user?'), new checkboxcontrol(0, 0));
//        $form->register("submit", "", new buttongroupcontrol(gt('Next'), "", gt('Cancel')));

        assign_to_template(array(
//            "form_html" => $form->tohtml(),
            'uname_options' => $unameOptions,
            'pword_options' => $pwordOptions,
            'pword_disabled' => $disabled,
            'params' => $this->params
        ));
    }

    public function import_users_display() {
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $file = fopen(BASE . $this->params["filename"], "r");
        $userinfo = array();
        $userarray = array();
        $usersdone = array();
        $linenum = 1;

        while (($filedata = fgetcsv($file, 2000, $this->params["delimiter"])) != false) {

            if ($linenum >= $this->params["rowstart"]) {
                $i = 0;

                $userinfo['username'] = "";
                $userinfo['firstname'] = "";
                $userinfo['lastname'] = "";
                $userinfo['is_admin'] = 0;
                $userinfo['is_acting_admin'] = 0;
//                $userinfo['is_locked'] = 0;
                $userinfo['email'] = '';
                $userinfo['changed'] = "";

                foreach ($filedata as $field) {
                    if (!empty($this->params["column"][$i]) && $this->params["column"][$i] != "none") {
                        $colname = $this->params["column"][$i];
                        $userinfo[$colname] = trim($field);
                    } else {
                        unset($this->params['column'][$i]);
                    }
                    $i++;
                }

                switch ($this->params["unameOptions"]) {
                    case "FILN":
                        if (($userinfo['firstname'] != "") && ($userinfo['lastname'] != "")) {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['firstname']{0} . $userinfo['lastname']));
                        } else {
                            $userinfo['username'] = "";
//                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "FILNNUM":
                        if (($userinfo['firstname'] != "") && ($userinfo['lastname'] != "")) {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['firstname']{0} . $userinfo['lastname'] . mt_rand(100, 999)));
                        } else {
                            $userinfo['username'] = "";
//                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "EMAIL":
                        if ($userinfo['email'] != "") {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['email']));
                        } else {
                            $userinfo['username'] = "";
//                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "FNLN":
                        if (($userinfo['firstname'] != "") && ($userinfo['lastname'] != "")) {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['firstname'] . $userinfo['lastname']));
                        } else {
                            $userinfo['username'] = "";
//                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "INFILE":
                        if ($userinfo['username'] != "") {
                            $userinfo['username'] = str_replace(" ", "", $userinfo['username']);
                        } else {
                            $userinfo['username'] = "";
//                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                }

                if ((!isset($userinfo['changed'])) || ($userinfo['changed'] != "skipped")) {
//                    switch ($this->params["pwordOptions"]) {
//                        case "RAND":
//                            $newpass = "";
//                            for ($i = 0; $i < mt_rand(12, 20); $i++) {
//                                $num = mt_rand(48, 122);
//                                if (($num > 97 && $num < 122) || ($num > 65 && $num < 90) || ($num > 48 && $num < 57)) $newpass .= chr($num);
//                                else $i--;
//                            }
//                            $userinfo['clearpassword'] = $newpass;
//                            break;
//                        case "DEFPASS":
//                            $userinfo['clearpassword'] = str_replace(" ", "", trim($this->params["pwordText"]));
//                            break;
//                    }
//
//                    $userinfo['password'] = user::encryptPassword($userinfo['clearpassword']);

                    $suffix = "";
                    while (user::getUserByName($userinfo['username'] . $suffix) != null) { //username already exists
                        if (!empty($this->params["update"])) {
                            if (in_array($userinfo['username'], $usersdone)) {
                                $suffix = '-rand-' . mt_rand(100, 999);
                            } else {
                                $tmp = user::getUserByName($userinfo['username'] . $suffix);
                                $userinfo['id'] = $tmp->id;
                                $userinfo['changed'] = 1;
                                break;
                            }
                        } else {
                            $suffix = '-rand-' . mt_rand(100, 999);
                        }
                    }

                    $userinfo['username'] = $userinfo['username'] . $suffix;
                    $userinfo['linenum'] = $linenum;
                    $userarray[] = $userinfo;
                    $usersdone[] = $userinfo['username'];
                } else {
                    $userinfo['linenum'] = $linenum;
                    $userarray[] = $userinfo;
                }
            }
            $linenum++;
        }
        assign_to_template(array(
            "userarray" => $userarray,
            "params" => $this->params,
        ));
    }

    public function import_users_add() {
        if (!empty($this->params['filename']) && (strpos($this->params['filename'], 'tmp/') === false || strpos($this->params['folder'], '..') !== false)) {
            header('Location: ' . URL_FULL);
            exit();  // attempt to hack the site
        }
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $file = fopen(BASE . $this->params["filename"], "r");
        $userinfo = array();
        $userarray = array();
        $usersdone = array();
        $linenum = 1;

        while (($filedata = fgetcsv($file, 2000, $this->params["delimiter"])) != false) {

            if ($linenum >= $this->params["rowstart"] && in_array($linenum,$this->params['importuser'])) {
                $i = 0;

                $userinfo['username'] = "";
                $userinfo['firstname'] = "";
                $userinfo['lastname'] = "";
                $userinfo['is_admin'] = 0;
                $userinfo['is_acting_admin'] = 0;
//                $userinfo['is_locked'] = 0;
                $userinfo['email'] = '';
                $userinfo['changed'] = "";

                foreach ($filedata as $field) {
                    if ($this->params["column"][$i] != "none") {
                        $colname = $this->params["column"][$i];
                        $userinfo[$colname] = trim($field);
                    }
                    $i++;
                }

                switch ($this->params["unameOptions"]) {
                    case "FILN":
                        if (($userinfo['firstname'] != "") && ($userinfo['lastname'] != "")) {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['firstname']{0} . $userinfo['lastname']));
                        } else {
                            $userinfo['username'] = "";
                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "FILNNUM":
                        if (($userinfo['firstname'] != "") && ($userinfo['lastname'] != "")) {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['firstname']{0} . $userinfo['lastname'] . mt_rand(100, 999)));
                        } else {
                            $userinfo['username'] = "";
                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "EMAIL":
                        if ($userinfo['email'] != "") {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['email']));
                        } else {
                            $userinfo['username'] = "";
                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "FNLN":
                        if (($userinfo['firstname'] != "") && ($userinfo['lastname'] != "")) {
                            $userinfo['username'] = str_replace(" ", "", strtolower($userinfo['firstname'] . $userinfo['lastname']));
                        } else {
                            $userinfo['username'] = "";
                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                    case "INFILE":
                        if ($userinfo['username'] != "") {
                            $userinfo['username'] = str_replace(" ", "", $userinfo['username']);
                        } else {
                            $userinfo['username'] = "";
                            $userinfo['clearpassword'] = "";
                            $userinfo['changed'] = "skipped";
                        }
                        break;
                }

                if ((!isset($userinfo['changed'])) || ($userinfo['changed'] != "skipped")) {
                    switch ($this->params["pwordOptions"]) {
                        case "RAND":
                            $newpass = "";
                            for ($i = 0, $iMax = mt_rand(12, 20); $i < $iMax; $i++) {
                                $num = mt_rand(48, 122);
                                if (($num > 97 && $num < 122) || ($num > 65 && $num < 90) || ($num > 48 && $num < 57)) $newpass .= chr($num);
                                else $i--;
                            }
                            $userinfo['clearpassword'] = $newpass;
                            break;
                        case "DEFPASS":
                            $userinfo['clearpassword'] = str_replace(" ", "", trim($this->params["pwordText"]));
                            break;
                    }

                    $userinfo['password'] = user::encryptPassword($userinfo['clearpassword']);

                    $suffix = "";
                    while (user::getUserByName($userinfo['username'] . $suffix) != null) { //username already exists
                        if (!empty($this->params["update"])) {
                            if (in_array($userinfo['username'], $usersdone)) {  // username exists because we already created it
                                $suffix = mt_rand(100, 999);
                            } else {
                                $tmp = user::getUserByName($userinfo['username'] . $suffix);
                                $userinfo['id'] = $tmp->id;
                                $userinfo['changed'] = 1;
                                break;
                            }
                        } else {
                            $suffix = mt_rand(100, 999);
                        }
                    }

                    $userinfo['username'] = $userinfo['username'] . $suffix;
                    $newuser = new user($userinfo);
                    $newuser->update();
                    $userinfo['linenum'] = $linenum;
                    $userarray[] = $userinfo;
                    $usersdone[] = $userinfo['username'];
                    if (USER_REGISTRATION_SEND_WELCOME && $this->params['sendemail'] && !empty($newuser->email)) {
                        $msg = $newuser->firstname . ", \n\n";
                        $msg .= sprintf(USER_REGISTRATION_WELCOME_MSG, $newuser->firstname, $newuser->lastname, $newuser->username);
                        $msg .= "/n/nYour new password is: ".$userinfo['clearpassword'];
                        $mail = new expMail();
                        $mail->quickSend(array(
                            'text_message' => $msg,
                            'to'           => array(trim($newuser->email) => trim(user::getUserAttribution($newuser->id))),
                            'from'         => array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
                            'subject'      => USER_REGISTRATION_WELCOME_SUBJECT,
                        ));
                    }
                } else {
                    $userinfo['linenum'] = $linenum;
                    $userarray[] = $userinfo;
                }
            }
            $linenum++;
        }
        fclose($file);
        ini_set('auto_detect_line_endings',$line_end);
        assign_to_template(array(
            "userarray" => $userarray,
        ));
        unlink(BASE . $this->params["filename"]);
    }

    public function sync_LDAPUsers() {
        if (USE_LDAP == 1 && function_exists('ldap_connect')) {
            $ldap = new expLDAP();
            $updated = $ldap->syncLDAPUsers();
            $ldap->close();
            flash('message', $updated.' '.gt('LDAP Users Updated'));
        }
    }

}

?>