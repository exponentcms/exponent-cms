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
?>
// Exponent Javascript Support Systems
EXPONENT = {};

// map certain php CONSTANTS to JS vars
EXPONENT.LANG = "<?php echo LANG; ?>";
EXPONENT.PATH_RELATIVE = "<?php echo PATH_RELATIVE; ?>";
EXPONENT.URL_FULL = "<?php echo URL_FULL; ?>";
EXPONENT.BASE = "<?php echo BASE; ?>";
EXPONENT.THEME_RELATIVE = "<?php echo THEME_RELATIVE; ?>";
EXPONENT.ICON_RELATIVE = "<?php echo ICON_RELATIVE; ?>";
EXPONENT.MIMEICON_RELATIVE = "<?php echo MIMEICON_RELATIVE; ?>";
EXPONENT.JS_URL = '<?php echo JS_URL; ?>';
EXPONENT.JS_RELATIVE = '<?php echo JS_RELATIVE; ?>';
EXPONENT.JQUERY_RELATIVE = '<?php echo JQUERY_RELATIVE; ?>';
EXPONENT.JQUERY_PATH = '<?php echo JQUERY_PATH; ?>';
EXPONENT.JQUERY_URL = '<?php echo JQUERY_URL; ?>';
EXPONENT.YUI3_VERSION = '<?php echo YUI3_VERSION; ?>';
EXPONENT.YUI3_RELATIVE = '<?php echo YUI3_RELATIVE; ?>';
EXPONENT.YUI3_URL = '<?php echo YUI3_URL; ?>';
EXPONENT.YUI2_VERSION = '<?php echo YUI2_VERSION; ?>';
EXPONENT.YUI2_RELATIVE = '<?php echo YUI2_RELATIVE; ?>';
EXPONENT.YUI2_URL = '<?php echo YUI2_URL; ?>';

// Exponent YUI Configuration
EXPONENT.YUI3_CONFIG = {
    combine:<?php echo (MINIFY==1&&MINIFY_YUI3==1)?1:0; ?>,
    comboBase:    EXPONENT.PATH_RELATIVE+'external/minify/min/index.php?b='+EXPONENT.PATH_RELATIVE.substr(1)+'external/yui&f=',
    filter: {
        'searchExp': "&([2-3])",
        'replaceStr': ",$1"
    },
    modules: {},
    groups: {
        yui2: {
            combine:<?php echo (MINIFY==1&&MINIFY_YUI2==1)?1:0; ?>,
            base: EXPONENT.YUI2_RELATIVE,
            root: EXPONENT.YUI2_VERSION+'/build/',
            comboBase:EXPONENT.PATH_RELATIVE+'external/minify/min/index.php?b='+EXPONENT.PATH_RELATIVE.substr(1)+'external/yui/2in3/dist&f=',
            patterns:  {
                "yui2-": {
                    configFn: function (me) {
                        if(/-skin|reset|fonts|grids|base/.test(me.name)) {
                            me.type = "css";
                            me.path = me.path.replace(/\.js/, ".css");
                            me.path = me.path.replace(/\/yui2-skin/, "/assets/skins/sam/yui2-skin");
                        }   
                    }   
                }   
            }
        }
    }
};

// if you feel like short-handing
eXp = EXPONENT;