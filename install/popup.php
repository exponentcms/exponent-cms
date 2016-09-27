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

include_once('../exponent.php');

?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo gt('Exponent CMS : Install Wizard Help'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo LANG_CHARSET; ?>"/>
    <meta name="Generator" content="Exponent Content Management System - <?php echo expVersion::getVersion(true); ?>"/>
    <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>external/normalize/normalize.css"/>
    <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/forms.css"/>
    <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/button.css"/>
    <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/tables.css"/>
    <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/common.css"/>
    <link rel="stylesheet" title="exponent" href="style.css"/>
</head>
<body>
<div class="popup_content_area">
    <?php
    $page = (isset($_REQUEST['page']) ? expString::sanitize($_REQUEST['page']) : '');
    if (!empty($page) && (strpos($page, '..') !== false || strpos($page, '/') !== false)) {
        header('Location: ../index.php');
        exit();  // attempt to hack the site
    }
    if (is_readable('popups/' . $page . '.php')) {
        include('popups/' . $page . '.php');
    }
    ?>
</div>
</body>
</html>