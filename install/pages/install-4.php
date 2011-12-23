<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
# Written and Designed by James Hunt
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

<form method="post" action="index.php">
<input type="hidden" name="page" value="install-5" />

<div id="tab1" class="">
  <h2><?php echo gt('Site Configuration'); ?></h2>
  <div class="text-control control  ">
    <label class="label"><?php echo gt('Site/Organization Name'); ?></label>
    <input type="text" name="sc[ORGANIZATION_NAME]" value="Exponent CMS" size="40" class="text ">
  </div>
  <div class="text-control control  ">
    <label class="label"><?php echo gt('Site Title'); ?></label>
    <input type="text" name="sc[SITE_TITLE]" value="Exponent CMS - A Powerful, Flexible, and Intuitive Web Solution." size="40" class="text ">
  </div>
  
  <div class="text-control control ">
    <label class="label"><?php echo gt('Site Header'); ?></label>
    <input type="text" name="sc[SITE_HEADER]" value="Exponent CMS" size="40" class="text ">
  </div>
  
  <div class="text-control control ">
    <label class="label"><?php echo '('.gt('Meta').') '.gt('Keywords'); ?></label>
    <textarea class="textarea" id="sc[SITE_KEYWORDS]" name="sc[SITE_KEYWORDS]" rows="5" cols="38">exponent cms</textarea>
  </div>
  <div class="text-control control ">
    <label class="label"><?php echo '('.gt('Meta').') '.gt('Description'); ?></label>
    <textarea class="textarea" id="sc[SITE_DESCRIPTION]" name="sc[SITE_DESCRIPTION]" rows="5" cols="38">exponent</textarea>
  </div>
  <div class="control checkbox">
    <table border="0" cellpadding="0" cellspacing="0">
      <tbody>
        <tr>
          <td class="input">
            <input type="hidden" name="sc[SEF_URLS]" value="0">
            <input type="checkbox" name="sc[SEF_URLS]" value="1" checked="checked" class="checkbox">
          </td>
          <td>
            <label class="label "><?php echo gt('Enable Search Engine Friendly URLs'); ?></label>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<button class="awesome large green"><?php echo gt('Add Configuration Settings'); ?></button>
</form>
