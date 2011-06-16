<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Phillip Ball
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

class recyclebin extends expRecord {
    public $table = 'locationref';
    //public $validates = '';
    public function moduleOrphans($module) {
        global $db;
        $orphans = $db->selectObjects($this->table,'refcount = 0 AND source!=\'\' AND module=\''.$module.'\'');
        
        //foreach ($orphans as $orphan) {
        for($i=0; $i<count($orphans); $i++) {
            if (controllerExists($module)) {
                $orphans[$i]->html = renderAction(array('controller'=>$module, 'action'=>'showall','src'=>$orphans[$i]->source,"no_output"=>true));
            } else {
                $mod = new $module();
                ob_start();
	                $mod->show("Default",$loc);
	                $orphans[$i]->html =ob_get_contents();
	            ob_end_clean();
            }
        }
        
        return $orphans;
    }
    
}

?>
