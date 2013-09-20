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

if (!defined('EXPONENT'))
    exit('');

?>
<h1><?php
echo gt('Pick Your Theme');
?></h1>

<?php

$themes = array();
if (is_readable(BASE . 'themes')) {
    echo '<form method="post" action="index.php">';
    echo '<div class="theme">';
    if (file_exists(BASE . "install/samples/sample.eql")) {
        echo '<b font-size:120%><label class="label">'.gt('Install Sample Content?').' </label></b>';
        echo '<select name="install_sample">';
        echo '    <option value="">'.gt('No Sample Data').'</option>';
        echo '    <option value="sample">'.gt('Sample Site').'</option>';
        $dh = opendir(BASE . 'install/samples');
        while (($file = readdir($dh)) !== false) {
            if (substr($file,-4,4) == '.eql' && $file != 'sample.eql' && $file != 'ecommerce.eql') {  //FIXME we should add to ecommerce.eql for a full sample store
                $filename = substr($file,0,strlen($file)-4);
                echo '    <option value="'.$filename.'">'.ucwords($filename).' '.gt('Site').'</option>';
            }
        }
        echo '</select>';
    }
    echo "</div>";
    $dh = opendir(BASE . 'themes');
    while (($file = readdir($dh)) !== false) {
        if (is_readable(BASE . "themes/$file/class.php")) {
            include_once(BASE . "themes/$file/class.php");
            /**
             * Stores the theme object
             * @var \theme $theme
             * @name $theme
             */
            $theme          = new $file();
            echo '<div class="theme clearfix">';
            echo is_readable(BASE . "themes/$file/preview.jpg") ? "<img src=\"".PATH_RELATIVE."thumb.php?src=themes/$file/preview.jpg&amp;w=100&amp;q=75\" class=\"themepreview\">" : "";
            echo "<h2>".$theme->name()."</h2>";
            echo "<em>".$theme->author()."</em>";
            echo "<p>".$theme->description().'</p>
                  <input type="hidden" name="page" value="install-6">';
            echo '<button class="awesome green small" name="sc[DISPLAY_THEME_REAL]" value="'.$file.'" id="sc[DISPLAY_THEME_REAL]" style="float:right">'. gt('Use') .' '.$theme->name().'</button>';
            echo "</div>";
        }
    }
    echo "</form>";
}
?>