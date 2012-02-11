<?php
header("Content-type: text/javascript");

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright 2006 Maxim Mueller
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

//Initialize exponent Framework
require_once('exponent.php');

?>

// exponent Javascript Support Systems

EXPONENT = {};

// map certian php CONSTANTS to JS vars

EXPONENT.LANG = "<?php echo LANG; ?>";
EXPONENT.PATH_RELATIVE = "<?php echo PATH_RELATIVE; ?>";
EXPONENT.URL_FULL = "<?php echo URL_FULL; ?>";
EXPONENT.BASE = "<?php echo BASE; ?>";
EXPONENT.THEME_RELATIVE = "<?php echo THEME_RELATIVE; ?>";
EXPONENT.ICON_RELATIVE = "<?php echo ICON_RELATIVE; ?>";
EXPONENT.JS_FULL = '<?php echo JS_FULL; ?>';
EXPONENT.YUI3_VERSION = '<?php echo YUI3_VERSION; ?>';
EXPONENT.YUI3_PATH = '<?php echo YUI3_PATH; ?>';
EXPONENT.YUI3_URL = '<?php echo YUI3_URL; ?>';
EXPONENT.YUI2_VERSION = '<?php echo YUI2_VERSION; ?>';
EXPONENT.YUI2_PATH = '<?php echo YUI2_PATH; ?>';
EXPONENT.YUI2_URL = '<?php echo YUI2_URL; ?>';

EXPONENT.YUI3_CONFIG = {
    combine:<?php echo (MINIFY==1)?1:0; ?>,
    // root:         EXPONENT.YUI3_PATH.substr(1),
    // base:         EXPONENT.YUI3_PATH,
    comboBase:    EXPONENT.PATH_RELATIVE+'external/minify/min/index.php?b='+EXPONENT.PATH_RELATIVE.substr(1)+'external/yui&f=',
    filter: {
        'searchExp': "&3",
        'replaceStr': ",3"
    },
    //combine: false,
    //filter:   "debug",
    // onFailure: function (error) {
    //   console.debug(error);  
    // },
    groups: {
        yui2: {
            combine:false,
            base: EXPONENT.YUI2_PATH,
            root: EXPONENT.YUI2_VERSION+'/',
            comboBase:EXPONENT.PATH_RELATIVE+'external/minify/min/index.php?b='+EXPONENT.PATH_RELATIVE.substr(1)+'external/yui/2in3/dist&f=',
            patterns:  {
                "yui2-": {
                    configFn: function (me) {
                        if(/-skin|reset|fonts|grids|base/.test(me.name)) {
                            //return me;
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

//gt = function(str) {
//    var langStr = <?php echo json_encode($cur_lang); ?>;
//    return langStr[str];
//};

// if you feel like short-handing
eXp = EXPONENT;

//console.log(gt('Add Configuration Settings'));

//console.debug(EXPONENT.YUI3_CONFIG);