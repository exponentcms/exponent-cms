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

if (!defined('EXPONENT')) {
    exit('');
}

?>
<h1>
    <?php echo gt('Create Administrator'); ?>
</h1>

<span style="color: red; font-weight: bold; padding-top: 8px;" id="errorMessage">
    <?php echo isset($_REQUEST['errusername']) == 'true' ? gt('You must supply a valid username.') : ''; ?>
    <?php echo isset($_REQUEST['errpassword']) == 'true' ? gt('Your passwords do not match.') : ''; ?>
    <?php echo isset($_REQUEST['errpwusername']) == 'true' ? gt('Your password cannot be equal to the username.') : ''; ?>
    <?php echo isset($_REQUEST['errpwstrength']) == 'true' ? gt('Your password is not strong enough.') : ''; ?>
    <?php echo isset($_REQUEST['erremail']) == 'true' ? gt('Your email address is invalid.') : ''; ?>
</span>

<form role="form" method="post" onsubmit="return validateForm(this);">
    <input type="hidden" name="page" value="install-7"/>
    <div class="form_section">
        <div class="control">
            <span class="label"><?php echo gt('Username'); ?>: </span>
            <input class="text form-control" type="text" name="username" value="<?php echo gt('admin'); ?>" required=1/>
            <div class="control_help">
                <?php echo gt('The username of your administrator account.  You should change this to something other than the default of \'admin\'.'); ?>
            </div>
        </div>
        <div class="control">
            <span class="label"><?php echo gt('Password'); ?>: </span>
            <input class="text form-control" type="password" name="password" id="password" value="" required=1/>
            <div class="control_help">
                <?php echo gt('The password of your administrator account.'); ?>
            </div>
        </div>
        <div class="control">
            <span class="label"><?php echo gt('Password Again'); ?>: </span>
            <input class="text form-control" type="password" name="password2" value="" required=1/>
            <div class="control_help">
                <?php echo gt('Type your password again.'); ?>
            </div>
        </div>
        <div class="control">
            <span class="label"><?php echo gt('First Name'); ?>: </span>
            <input class="text form-control" type="text" name="firstname" value="<?php echo gt('System'); ?>"/>
        </div>
        <div class="control">
            <span class="label"><?php echo gt('Last Name'); ?>: </span>
            <input class="text form-control" type="text" name="lastname" value="<?php echo gt('Administrator'); ?>"/>
        </div>
        <div class="control">
            <span class="label"><?php echo gt('Email Address'); ?>: </span>
            <input class="text form-control" type="text" name="email" value="" required=1/>
        </div>
    </div>
    <button class="awesome large green"><?php echo gt('Create Administrator'); ?></button>
</form>
<script type="text/javascript" src="<?php echo JQUERY2_SCRIPT; ?>"></script>
<script type="text/javascript" src="<?php echo PATH_RELATIVE; ?>external/jquery/addons/js/strength-meter.js"></script>
<!--<script type="text/javascript" src="--><?php //echo PATH_RELATIVE; ?><!--external/jquery/addons/js/locales/strength-meter---><?php //echo LOCALE; ?><!--.js"></script>-->
<script>
    function strcasecmp(f_string1, f_string2) {
        //  discuss at: http://phpjs.org/functions/strcasecmp/
        // original by: Martijn Wieringa
        // bugfixed by: Onno Marsman
        //   example 1: strcasecmp('Hello', 'hello');
        //   returns 1: 0

        var string1 = (f_string1 + '')
            .toLowerCase();
        var string2 = (f_string2 + '')
            .toLowerCase();

        if (string1 > string2) {
            return 1;
        } else if (string1 == string2) {
            return 0;
        }

        return -1;
    }

    function validateForm(f) {
        emailfilter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        pw = f.password.value;
        if (f.username.value == "") {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('You must specify a valid username.'); ?>";
            return false;
        } else if (strcasecmp(f.username.value, f.password.value) == 0) {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('Your password cannot be equal to the username.'); ?>";
            return false;
        } else if (pw != f.password2.value) {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('Your passwords do not match.'); ?>";
            return false;
        } else if (pw.length < <?php echo MIN_PWD_LEN; ?>) {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('Passwords must be at least') . ' ' . MIN_PWD_LEN . ' ' . gt('characters long'); ?>";
            return false;
        } else if ((pw.match(/([A-Z])/g) || []).length < <?php echo MIN_UPPER; ?>) {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('Passwords must have at least') . ' ' . MIN_UPPER . ' ' . gt('upper case letter(s)'); ?>";
            return false;
        } else if ((pw.match(/([0-9])/g) || []).length < <?php echo MIN_DIGITS; ?>) {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('Passwords must have at least') . ' ' . MIN_DIGITS . ' ' . gt('digit(s)'); ?>";
            return false;
        } else if ((pw.match(/([!,%,&,@,#,$,^,*,?,_,~])/g) || []).length < <?php echo MIN_SYMBOL; ?>) {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('Passwords must have at least') . ' ' . MIN_SYMBOL . ' ' . gt('symbol(s)'); ?>";
            return false;
        } else if (!emailfilter.test(f.email.value)) {
            document.getElementById("errorMessage").innerHTML = "<?php echo gt('Your email address is invalid.'); ?>";
            return false;
        } else {
            f.submit();
            return true;
        }
    }

    $("#password").strength({
        toggleMask: false,
        mainTemplate: '<div class="kv-strength-container">{input}<div class="kv-meter-container">{meter}</div></div>',
        rules: {
            minLength: <?php echo MIN_PWD_LEN; ?>,
        },
    });
    $("head").append("<link href=\"<?php echo PATH_RELATIVE; ?>external/jquery/addons/css/strength-meter.css\" rel=\"stylesheet\" type=\"text/css\" />");
    $("head").append("<style type=\"text/css\"> .kv-scorebar-border { margin: 0; margin-top: 3px; } </style>");
</script>
