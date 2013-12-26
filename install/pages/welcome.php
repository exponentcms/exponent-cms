<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

?>
<h1><?php echo gt('Welcome to Exponent CMS'); ?></h1>
<p>
    <?php echo gt('The Exponent Development Team would like to thank you for downloading and installing the Exponent Content Management System.') .
        gt('We fervently hope that you will enjoy the power, flexibility, and ease-of-use that Exponent has to offer.') ?>
</p>

<h1 id="subtitle"><?php echo gt('Please select a language'); ?></h1>
<p>
    <?php echo gt('This will set the default Language for the installation process as well as your new Exponent website.'); ?>
</p>

<?php
if (!defined('LANGUAGE')) {
    if (empty($_POST['sc']['LANGUAGE'])) $_POST['sc']['LANGUAGE'] = 'English - US';
    define('LANGUAGE', $_POST['sc']['LANGUAGE']);
}
?>

<form method="post" action="index.php">
    <!--	 send us to the next page -->
    <input type="hidden" name="page" value="install-1"/>

    <div class="control">
        <select name="lang" onchange="Refresh(this.value)">
            <?PHP foreach (expLang::langList() as $currid => $currlang) { ?>
                <option
                    value="<?PHP echo $currid ?>"<?php if ($currid == LANGUAGE) echo " selected"; ?>><?PHP echo $currlang ?></option>
            <?PHP } ?>
        </select>
    </div>
    <br/>
    <button class="awesome large green"><?php echo gt('Begin Installation in Selected Language'); ?></button>
</form>

<?php
// build core css files from .less
expCSS::updateCoreCss();

// profiles
$profiles = expSettings::profiles();
if (!empty($profiles)) {
    $profile = array('' => '(select a profile to abort install)');
    $profiles = array_merge($profile, $profiles);
    ?>
    <br><br>
    <h3>
        <?php echo gt('We\'ve located stored configuration profiles which could be used to restore the system'); ?>
    </h3>
    <div class="control">
        <label class="label">
            <?php echo gt('Select a configuration profile to restore'); ?>
        </label>
        <select id="profiles" onchange="changeProfile(this.value)">
            <?PHP foreach ($profiles as $currid => $currprof) { ?>
                <option
                    value="<?PHP echo $currid ?>"<?php if ($currid == '') echo " selected"; ?>><?PHP echo $currprof ?></option>
            <?PHP } ?>
        </select>
    </div>
<?php
}
?>

<script type="text/javascript">
    function Refresh(id) {
        location.href = "index.php?lang=" + id + "&page='welcome'"
    }

    function changeProfile(val) {
        if (confirm('<?php echo gt('Are you sure you want to load a new profile?'); ?>')) {
            window.location = '<?php echo URL_FULL; ?>' + "install/index.php?profile=" + val;
        } else {
            document.getElementById("profiles").value = '';
        }
    }
</script>