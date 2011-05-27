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

if (!defined('EXPONENT'))
    exit('');


?>
<h1><?php
echo gt('Pick Your Theme');
?></h1>

<?php

$themes = array();
if (is_readable(BASE . 'themes')) {
    $dh = opendir(BASE . 'themes');
    while (($file = readdir($dh)) !== false) {
        if (is_readable(BASE . "themes/$file/class.php")) {
            include_once(BASE . "themes/$file/class.php");
            $theme          = new $file();
            
            echo '<div class="theme clearfix">
            <form method="post" action="index.php">
            ';
            echo is_readable(BASE . "themes/$file/preview.jpg") ? "<img src=\"".URL_FULL."thumb.php?src=themes/$file/preview.jpg&amp;w=100&amp;q=75\" class=\"themepreview\">" : "";
            echo "<h2>".$theme->name()."</h2>";
            echo "<em>".$theme->author()."</em>";
            echo "<p>".$theme->description().'<br /><br />
                <input type="hidden" name="page" value="install-6">
                <input type="hidden" name="sc[DISPLAY_THEME_REAL]" value="'.$file.'" id="sc[DISPLAY_THEME_REAL]">
                <button class="awesome green small">'. gt('Use') .' '.$theme->name().'</button>
            
            </p>';
            echo "</form></div>";

            // if (is_file(BASE . "themes/$file/default_content.eql")) {
            //     $t->author      = $theme->author();
            // }
        }
    }
}
?>