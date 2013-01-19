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

/**
 * @subpackage Models
 * @package Modules
 */

class recyclebin extends expRecord {
    public $table = 'sectionref';
    //public $validates = '';
    public function moduleOrphans($module) {
        global $db;
        if (empty($module)) {
            $orphans = $db->selectObjects($this->table,'refcount = 0 AND source!=\'\' ORDER BY module');
        } else {
            $orphans = $db->selectObjects($this->table,'refcount = 0 AND source!=\'\' AND module=\''.$module.'\'');
        }
        $loc =null;

        //foreach ($orphans as $orphan) {
        $numrecycled = count($orphans);
        for($i=0; $i<$numrecycled; $i++) {
//            $loc = new stdClass();
//            $loc->mod = $orphans[$i]->module;
//            $loc->src = $orphans[$i]->source;
//            $loc->int = $orphans[$i]->internal;
            $loc = expCore::makeLocation($orphans[$i]->module,$orphans[$i]->source,$orphans[$i]->internal);
            $orphans[$i]->loc = serialize($loc);
            if ($orphans[$i]->module == 'recyclebinController') {
                unset($orphans[$i]);
            } else {
                if (expModules::controllerExists($orphans[$i]->module)) {
                    $orphans[$i]->html = renderAction(array('controller'=>$orphans[$i]->module, 'action'=>'showall','src'=>$orphans[$i]->source,"no_output"=>true));
                } else {
                    echo($module).'...';
                    if (in_array($orphans[$i]->module,expModules::modules_list())) {
                        $mod = new $orphans[$i]->module();
                        ob_start();
                        $mod->show("Default",$loc);
                        $orphans[$i]->html = ob_get_contents();
                        ob_end_clean();
                    } else {
                        echo($orphans[$i]->module . ' ' . gt('no longer available!'));
                    }
                }
            }
        }
        
        return $orphans;
    }
    
}

?>