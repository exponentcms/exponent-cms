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

function smarty_function_ddrerank($params,&$smarty) {
    global $db;
	$loc = $smarty->_tpl_vars['__loc'];
	
    $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
    $uniqueid = str_replace($badvals, "", $loc->src).$params['id'];
    $controller = !empty($params['controller']) ? $params['controller'] : $loc->mod;
    
    if ($params['sql']) {
        $sql = explode("LIMIT",$params['sql']);
        $params['items'] = $db->selectObjectsBySQL($sql[0]);
    } else {
        if ($params['items'][0]->id) {
            $model = empty($params['model']) ? $params['items'][0]->classname : $params['model'] ;
            $obj = new $model();
            $params['items'] = $obj->find('all',"location_data='".serialize($loc)."'","rank");
        } else {
            $params['items'] = array();
        }
    }
    
    if (count($params['items'])>=2) {
        expCSS::pushToHead(array(
		    "corecss"=>"rerankpanel,panels",
		    )
		);
        
        $sortfield = empty($params['sortfield']) ? 'title' : $params['sortfield']; //what was this even for?
   
        echo '<a id="rerank'.$uniqueid.'" class="reranklink" href="#">Order '.$params['label'].'</a>';

        $html = '
        <div id="panel'.$uniqueid.'" class="exp-panel exp-panel-rerank hide">
            <div class="hd">Order '.$params['label'].'</div>
            <div class="bd">
            <form method="post" action="'.URL_FULL.'">
            <input type="hidden" name="model" value="'.$model.'" />
            <input type="hidden" name="controller" value="'.$controller.'" />
            <input type="hidden" name="lastpage" value="'.curPageURL().'" />
            <input type="hidden" name="src" value="'.$loc->src.'" />';
            if (!empty($params['items'])) {
                // we may need to pass through an ID for some reason, like a category ID for products
                $html .= ($params['id']) ? '<input type="hidden" name="id" value="'.$params['id'].'" />' : '';
                $html .= '<input type="hidden" name="action" value="manage_ranks" />
                <ul id="listToOrder'.$uniqueid.'" style="height:350px;overflow-y:auto;">
                ';
                $odd = "even";
                foreach ($params['items'] as $item) {
                    $html .= '
                    <li class="'.$odd.'">
                    <input type="hidden" name="rerank[]" value="'.$item->id.'" />
                    <div class="fpdrag"></div>';
        			//Do we include the picture? It depends on if there is one set.
                    $html .= ($item->expFile[0]->id) ? '<img class="filepic" src="'.URL_FULL.'thumb.php?id='.$item->expFile[0]->id.'&w=24&h=24&zc=1">' : '';
                    $html .= '<div class="filename">'.substr($item->$sortfield, 0, 40).'</div>
                    </li>';
                    $odd = $odd == "even" ? "odd" : "even";
                }
                $html .='</ul>
                    <input type="submit" value="Submit">
                    </form>
                    </div>
                </div>
                ';
            } else {
                $html .='<strong>Nothing to re-rank</strong>
            
                    </div>
                </div>
                ';
            }
            
    
        echo $html;
    
        $script = "
        YUI(EXPONENT.YUI3_CONFIG).use('dd-constrain','dd-proxy','dd-drop','dd-scroll', function(Y) {

        var ropanel".$uniqueid." = new YAHOO.widget.Panel('panel".$uniqueid."', { width:'400px',y:100,zindex:50,visible:false, constraintoviewport:true, fixedcenter:1 } );
    	ropanel".$uniqueid.".render(document.body);
    	YAHOO.util.Dom.removeClass('panel".$uniqueid."', 'hide');
    
            var rrlink = Y.one('#rerank".$uniqueid."');
            if (!Y.Lang.isNull(rrlink)) {
                rrlink.on('click',function(e){
                    e.halt();
                    ropanel".$uniqueid.".show();
                });
            
            
                ropanel".$uniqueid.". showEvent.subscribe(
                function() {
                    //Listen for all drop:over events
                    //Y.DD.DDM._debugShim = true;

                    Y.DD.DDM.on('drop:over', function(e) {
                        //Get a reference to out drag and drop nodes
                        var drag = e.drag.get('node'),
                            drop = e.drop.get('node');

                        //Are we dropping on a li node?
                        if (drop.get('tagName').toLowerCase() === 'li') {
                            //Are we not going up?
                            if (!goingUp) {
                                drop = drop.get('nextSibling');
                            }
                            //Add the node to this list
                            e.drop.get('node').get('parentNode').insertBefore(drag, drop);
                            //Resize this nodes shim, so we can drop on it later.
                            e.drop.sizeShim();
                        }
                    });
                    //Listen for all drag:drag events
                    Y.DD.DDM.on('drag:drag', function(e) {
                        //Get the last y point
                        var y = e.target.lastXY[1];
                        //is it greater than the lastY var?
                        if (y < lastY) {
                            //We are going up
                            goingUp = true;
                        } else {
                            //We are going down..
                            goingUp = false;
                        }
                        //Cache for next check
                        lastY = y;
                        Y.DD.DDM.syncActiveShims(true);
                    });
                    //Listen for all drag:start events
                    Y.DD.DDM.on('drag:start', function(e) {
                        //Get our drag object
                        var drag = e.target;
                        //Set some styles here
                        drag.get('node').setStyle('opacity', '.25');
                        drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
                        drag.get('dragNode').setStyles({
                            opacity: '.5',
                            borderColor: drag.get('node').getStyle('borderColor'),
                            backgroundColor: drag.get('node').getStyle('backgroundColor')
                        });
                    });
                    //Listen for a drag:end events
                    Y.DD.DDM.on('drag:end', function(e) {
                        var drag = e.target;
                        //Put out styles back
                        drag.get('node').setStyles({
                            visibility: '',
                            opacity: '1'
                        });
                    });
                    //Listen for all drag:drophit events
                    Y.DD.DDM.on('drag:drophit', function(e) {
                        var drop = e.drop.get('node'),
                            drag = e.drag.get('node');

                        //if we are not on an li, we must have been dropped on a ul
                        if (drop.get('tagName').toLowerCase() !== 'li') {
                            if (!drop.contains(drag)) {
                                drop.appendChild(drag);
                            }
                        }
                    });

                    //Static Vars
                    var goingUp = false, lastY = 0;
                    // the list
                    var ul = '#listToOrder".$uniqueid."';

                    //Get the list of li's in the lists and make them draggable
                    var lis = Y.Node.all('#listToOrder".$uniqueid." li');
                    lis.each(function(v, k) {
                        var dd = new Y.DD.Drag({
                            node: v,
                            target: {
                                padding: '0 0 0 20'
                            }
                        }).plug(Y.Plugin.DDProxy, {
                            moveOnEnd: false
                        }).plug(Y.Plugin.DDConstrained, {
                            constrain2node: ul,
                            stickY:true
                        }).plug(Y.Plugin.DDNodeScroll, {
                            node: ul
                        }).addHandle('.fpdrag');
                    });

                    //Create simple targets for the 2 lists..
                    var tar = new Y.DD.Drop({
                        node: ul
                    });        
                
                });
            
            }
        });
        
        ";
        
        expJavascript::pushToFoot(array(
            "unique"=>$uniqueid,
            "yui2mods"=>"container",
            "yui3mods"=>"yes",
            "content"=>$script,
         ));
        
    }
}

?>

