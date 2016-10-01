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

/**
 * Smarty plugin
 *
 * @package    Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {ddrerank} function plugin
 *
 * Type:     function<br>
 * Name:     ddrerank<br>
 * Purpose:  display item re-ranking popup
 *
 * @param         $params
 * @param \Smarty $smarty
 */
if (!function_exists('smarty_function_ddrerank')) {
    function smarty_function_ddrerank($params, &$smarty) {
        global $db;

        $loc = $smarty->getTemplateVars('__loc');
        $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
        $params_id = !empty($params['id']) ? $params['id'] : '';
        $uniqueid = str_replace($badvals, "", $loc->src) . str_replace($badvals, "", $params_id);
        $controller = !empty($params['controller']) ? $params['controller'] : $loc->mod;

        if (!empty($params['sql'])) {
            $sql = explode("LIMIT", $params['sql']);
            $params['items'] = $db->selectObjectsBySQL($sql[0]);
        } elseif (!empty($params['items'][0]->id)) {
            $model = empty($params['model']) ? $params['items'][0]->classname : $params['model'];
            $only = !empty($params['only']) ? ' AND ' . $params['only'] : '';
            $obj = new $model();
            if ($params['model'] == 'expCat') {
                if (empty($params['module'])) {
                    $locsql = '1';
                } else {
                    $locsql = "module='" . $params['module'] . "'";
                }
//        } elseif (isset($obj->location_data)) {
        } elseif (property_exists($obj, 'location_data')) {
            $locsql = "location_data='" . serialize($loc) . "'";
        } else {
            $locsql = '1';
        }
//            $params['items'] = $obj->find('all',"location_data='".serialize($loc)."'".$only,"rank");
            $params['items'] = $obj->find('all', $locsql . $only, "rank"); // we MUST re-pull since we only received one page of $items
            $params['items'] = expSorter::sort(array('array' => $params['items'], 'sortby' => 'rank', 'order' => 'ASC'));
        } elseif (!empty($params['module'])) {
            $model = empty($params['model']) ? $params['module'] : $params['model'];
            $where = !empty($params['where']) ? $params['where'] : 1;
            $only = !empty($params['only']) ? ' AND ' . $params['only'] : '';
            $params['items'] = $db->selectObjects($model, $where . $only, "rank");
            // we need a good uniqueid since we get both internal and external calls from the same container template
//            $uniqueloc = $smarty->getTemplateVars('container');  //FIXME we don't seem to get a container var
//            if (!empty($uniqueloc->external)) {
//            if (!empty($params['uniqueid'])) {
////                $uniqueloc2 = expUnserialize($uniqueloc->external);
//                $uniqueloc2 = expUnserialize($params['uniqueid']);
//                $uniqueid = str_replace($badvals, "", $uniqueloc2->src) . $params['id'];
//            }
        } else {
            $params['items'] = array();
        }

        if (count($params['items']) >= 2) {
            $sortfield = empty($params['sortfield']) ? 'title' : $params['sortfield']; // this is the field to display in list

            // attempt to translate the label
            if (!empty($params['label'])) {
                $params['label'] = gt($params['label']);
            }
            $btn_size = expTheme::buttonStyle();
            $icon_size = expTheme::iconSize();
            if ($model != 'container') {  // make a button
                echo '<a id="rerank', $uniqueid, '" class="',$btn_size,'" data-toggle="modal" data-target="#panel', $uniqueid, '" href="#"><i class="fa fa-exchange fa-rotate-90 ',$icon_size,'"></i> ', gt("Order"), ' ', $params['label'], '</a>';
            } else {  // make a menu item
                echo '<a id="rerank', $uniqueid, '" class="" data-toggle="modal" data-target="#panel', $uniqueid, '" href="#"><i class="fa fa-exchange fa-rotate-90 fa-fw"></i> ', gt("Order"), ' ', $params['label'], '</a>';
            }

            $html = '
        <div id="panel' . $uniqueid . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="rerank' . $uniqueid . '" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
            <form role="form" method="post" action="' . PATH_RELATIVE . '">
              <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">' . gt('Close') . '</span></button>
                <h4 class="modal-title" id="myModalLabel' . $uniqueid . '">' . gt('Set Order of') . ' ' . $params['label'] . '</h4>
              </div>
              <div class="modal-body">
            <input type="hidden" name="model" value="' . $model . '" />
            <input type="hidden" name="controller" value="' . $controller . '" />
            <input type="hidden" name="lastpage" value="' . curPageURL() . '" />
            <input type="hidden" name="src" value="' . $loc->src . '" />';
            if (!empty($params['items'])) {
                // we may need to pass through an ID for some reason, like a category ID for products
                $html .= !empty($params['id']) ? '<input type="hidden" name="id" value="' . $params['id'] . '" />' : '';
                $html .= '<input type="hidden" name="action" value="manage_ranks" />
                <ul id="listToOrder' . $uniqueid . '" class="scrollable" style="' . ((count($params['items']) < 12) ? "" : "height:350px") . ';overflow-y:auto;">
                ';
                $stringlen = 40;
                foreach ($params['items'] as $item) {
                if (!empty($params['module']) || $params['model'] == 'expDefinableField') {  // we want to embellish the title used
                        if ($params['module'] == 'formbuilder_control' || $params['module'] == 'forms_control' || $params['model'] == 'expDefinableField') {
                            $control = expUnserialize($item->data);
                            $ctrl = new $control();
                            $name = $ctrl->name();
                            $item->$sortfield = (!empty($item->$sortfield) ? substr($item->$sortfield, 0, $stringlen) : gt('Untitled')) . ' (' . $name . ')';
                            $stringlen = 65;
                        } elseif ($params['module'] == 'container') {
                            $mod = expUnserialize($item->internal);
                            $item->$sortfield = (!empty($item->$sortfield) ? substr($item->$sortfield, 0, $stringlen) : gt('Untitled')) . ' (' . ucfirst(expModules::getModuleBaseName($mod->mod)) . ')';
                            $stringlen = 65;
                        }
                    }
                    $html .= '
                    <li class="">
                    <input type="hidden" name="rerank[]" value="' . $item->id . '" />
                    <div class="fpdrag"></div>';
                    //Do we include the picture? It depends on if there is one set.
                    $html .= (!empty($item->expFile[0]->id) && !empty($item->expFile[0]->is_image)) ? '<img class="filepic" src="' . PATH_RELATIVE . 'thumb.php?id=' . $item->expFile[0]->id . '&w=16&h=16&zc=1" alt="item'.$item->id.'">' : '';
                    $html .= '<span class="title">' . (!empty($item->$sortfield) ? substr($item->$sortfield, 0, $stringlen) : gt('Untitled')) . '</span>
                    </li>';
                }
                $html .= '</ul></div>
                    <div class="modal-footer">';
//                    <a href="#" class="btn btn-default '.$btn_size.'" name=alpha' . $uniqueid . ' id=alpha' . $uniqueid . ' style="float:left;"><i class="fa fa-sort '.$icon_size.'"></i> ' . gt('Sort List Alphabetically') . '</a>
                $html .= '
                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o '.$icon_size.'"></i> ' . gt('Save') . '</button>
                    </div>
                  </form>
                </div>
                </div>
                </div>
                ';
            } else {
                $html .= '<strong>' . gt('Nothing to re-rank') . '</strong>
                    </div>
                </div>
                ';
            }
            echo $html;

//            $script = "
//                $(document).ready(function(){
//                  $('#listToOrder" . $uniqueid . "').sortable();
//            ";
//            if ($model == 'container') {  // must move modal off of menu to display
//                $script .= "$('#panel" . $uniqueid . "').appendTo('body');";
//            }
//            $script .="
//                });
//            ";
            $script = "
                $(document).ready(function(){
                  new Sortable(document.getElementById('listToOrder" . $uniqueid . "'));
            ";
            if ($model == 'container') {  // must move modal off of menu to display
                $script .= "$('#panel" . $uniqueid . "').appendTo('body');";
            }
            $script .="
                });
            ";

            if (!expTheme::inPreview()) {
                expJavascript::pushToFoot(array(
                    "unique"    => $uniqueid,
                    "jquery"    => 'Sortable',
                    "bootstrap" => 'modal,transition',
                    "content"   => $script,
                ));
            }
        }
    }
}

?>
