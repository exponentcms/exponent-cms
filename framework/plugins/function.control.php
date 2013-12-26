<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * Smarty {control} function plugin
 *
 * Type:     function<br>
 * Name:     control<br>
 * Purpose:  create a form control
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_control($params, &$smarty) {
    global $db, $user;

    if ((isset($params['type']) && isset($params['name'])) || $params['type'] == 'buttongroup' || $params['type'] == 'antispam' || $params['type'] == 'tags') {
//    || $params['type'] == 'captcha' || $params['type'] == 'recaptcha' || $params['type'] == 'antispam') {

        // if a label wasn't passed in then we need to set one.
        //if (empty($params['label'])) $params['label'] = $params['name'];
        $showdate=true;
        if (isset($params['showdate']) && $params['showdate'] == false) {
            $showdate = false;
        }
        $showtime=true;
        if (isset($params['showtime']) && $params['showtime'] == false) {
            $showtime = false;
        }

        //Figure out which type of control to use. Also, some controls need some special setup.  We handle that here.
        switch ($params['type']) {
            case "popupdatetimecontrol":
            case "popupdatetime":
                if (empty($params['value'])) $params['value'] = time();
                $disabletext = isset($params['disable_text']) ? $params['disable_text'] : '';
                $control = new popupdatetimecontrol($params['value'], $disabletext, $showtime);
                break;
            case "yuidatetimecontrol":
            case "yuidatetime":
                if (empty($params['value'])) $params['checked'] = true;
                if (empty($params['value'])) $params['value'] = time();
                $edittext = isset($params['edit_text']) ? $params['edit_text'] : 'Change Date/Time';
                $control = new yuidatetimecontrol($params['value'], $edittext, $showdate, $showtime);
                break;
            case "yuicalendarcontrol":
            case "yuicalendar":
                if (empty($params['value'])) $params['value'] = time();
                $disabletext = isset($params['disable_text']) ? $params['disable_text'] : 'Change Date/Time';
                $control = new yuicalendarcontrol($params['value'], $disabletext, $showtime);
                break;
            case "datetimecontrol":
            case "datetime":
                if (empty($params['value'])) $params['value'] = time();
                $control  = new datetimecontrol($params['value'], $showdate, $showtime);
                break;
            case "calendarcontrol":
            case "calendar":
                $control = new calendarcontrol();
                break;
            case "monthyear":
                $control = new monthyearcontrol($params['month'], $params['year']);
                break;
            case "buttongroup":
                $submit     = isset($params['submit']) ? $params['submit'] : null;
                $reset      = isset($params['reset']) ? $params['reset'] : null;
                $cancel     = isset($params['cancel']) ? $params['cancel'] : null;
                $returntype = isset($params['returntype']) ? $params['returntype'] : null;
                $control    = new buttongroupcontrol($submit, $reset, $cancel, null, $returntype);
                break;
            case "uploader":
                $control = new uploadcontrol();
                if (!empty($params['accept'])) $control->accept = $params['accept'];
                break;
            case "files":
                if (!empty($params['olduploader'])) {
                    $control = new uploadcontrol();
                } else {
                    $subtype        = isset($params['subtype']) ? $params['subtype'] : null;
                    $control        = new filemanagercontrol($subtype);
                    $control->limit = isset($params['limit']) ? $params['limit'] : 10;
                    if (!empty($params['value'])) $control->value = $params['value'];
                }
                if (!empty($params['accept'])) $control->accept = $params['accept'];
                break;
            case "filedisplay-types":
                $control                = new dropdowncontrol();
                $control->include_blank = gt('-- This module does not use files --');
                $control->items         = get_filedisplay_views();
                break;
            case "dropdown":
                $control                = new dropdowncontrol(!empty($params['default'])?$params['default']:null);
                if (!empty($params['default'])) $control->default = $params['default'];
                $control->type          = "select";
                $control->include_blank = isset($params['includeblank']) ? $params['includeblank'] : false;
                $control->multiple      = isset($params['multiple']) ? true : false;
                if (isset($params['from']) && isset($params['to'])) {
                    for ($i = $params['from']; $i <= $params['to']; $i++) {
                        $control->items[$i] = isset($params['zeropad']) ? sprintf("%02d", $i) : $i;
                    }
                } elseif (isset($params['frommodel']) || (isset($params['items']) && isset($params['key']))) {
                    $key     = isset($params['key']) ? $params['key'] : 'id';
                    $display = isset($params['display']) ? $params['display'] : 'title';
                    $order   = isset($params['orderby']) ? $params['orderby'] : $display;
                    $dir     = isset($params['dir']) ? $params['dir'] : 'ASC';
                    if (isset($params['frommodel'])) {
                        $model           = new $params['frommodel'];
                        $where           = empty($params['where']) ? null : $params['where'];
                        $params['items'] = $db->selectObjects($model->tablename, $where, $order . ' ' . $dir);
                    }
                    foreach ($params['items'] as $item) {
                        $control->items[$item->$key] = $item->$display;
                    }
                    $noitems = gt("-- No items found --");
                    if (count($control->items) < 1) $control->items = array(0=> $noitems);
                } else {
                    if (is_array($params['items'])) {
                        $control->items = $params['items'];
                        if (!empty($params['values'])) {
                            $control->items = array_combine($params['values'], $control->items);
                        }
                    } elseif (is_string($params['items'])) {
                        $delimiter = isset($params['delimiter']) ? $params['delimiter'] : ',';
                        $items     = explode($delimiter, $params['items']);
                        if (!empty($params['values'])) {
                            $values = is_array($params['values']) ? $params['values'] : explode($delimiter, $params['values']);
                        } else {
                            $values = $items;
                        }
                        $control->items = array_combine($values, $items);
                    } else {
                        $control->items = array();
                    }
                }
                break;
            case "checkbox":
                $value              = isset($params['value']) ? $params['value'] : null;
                $control            = new checkboxcontrol($value);
                $control->postfalse = isset($params['postfalse']) ? 1 : 0;
                $control->newschool = true;
                $control->value     = isset($params['value']) ? $params['value'] : 1;
                $control->jsHooks   = isset($params['hooks']) ? $params['hooks'] : null;
                break;
            case "radiogroup":
                $control = new radiogroupcontrol();
                // differentiate it from the old school forms
                $control->newschool = true;
                if (!empty($params['default'])) $control->default = $params['default'];
                $control->cols      = $params['columns'];

                // get the items to use as the radio button labels
                $items = is_array($params['items']) ? $params['items'] : explode(',', $params['items']);
                // check if we have a list of values.  if not we can assume they are passed in via the items
                // array as the keys.
                if (isset($params['values'])) {
                    $values         = is_array($params['values']) ? $params['values'] : explode(',', $params['values']);
                    $control->items = array_combine($values, $items);
                } else {
                    $control->items = $items; //array_combine($items, $items);
                }
                if (!empty($params['item_descriptions'])) $control->item_descriptions = $params['item_descriptions'];
                break;
            case "radio":
                $control            = new radiocontrol();
                if (!empty($params['value'])) $control->value = $params['value'];
                $control->newschool = true;
                break;
            case "textarea":
                $control = new texteditorcontrol();
                if (isset($params['module'])) $control->module = $params['module'];
                if (isset($params['rows'])) $control->rows = $params['rows'];
                if (isset($params['cols'])) $control->cols = $params['cols'];
                //if (isset($params['toolbar'])) $control->toolbar = $params['toolbar'];
                break;
            case "editor":
            case "html":
                if (empty($params['editor'])) {
                    $editor = SITE_WYSIWYG_EDITOR;
                } else {
                    $editor = $params['editor'];
                }
                if ($editor == "ckeditor") {
                    $control = new ckeditorcontrol();
                    $control->toolbar  = !isset($params['toolbar']) ? '' : $params['toolbar'];
                    $control->lazyload = empty($params['lazyload']) ? 0 : 1;
                    $control->plugin = empty($params['plugin']) ? '' : $params['plugin'];
                    $control->additionalConfig = empty($params['additionalConfig']) ? '' : $params['additionalConfig'];
                } elseif ($editor == "tinymce") {
                    $control = new tinymcecontrol();
                    $control->toolbar  = !isset($params['toolbar']) ? '' : $params['toolbar'];
                    $control->lazyload = empty($params['lazyload']) ? 0 : 1;
                    $control->plugin = empty($params['plugin']) ? '' : $params['plugin'];
                    $control->additionalConfig = empty($params['additionalConfig']) ? '' : $params['additionalConfig'];
                } else {
                    $control = new htmleditorcontrol();
                    if (isset($params['module'])) $control->module = $params['module'];
                    if (isset($params['rows'])) $control->rows = $params['rows'];
                    if (isset($params['cols'])) $control->cols = $params['cols'];
                    $control->height = !empty($params['height']) ? $params['height'] : "600px";
                    if (isset($params['toolbar'])) $control->toolbar = $params['toolbar'];
                }
                break;
            case "listbuilder":
//                $default = isset($params['default']) ? $params['default'] : array();
                $default = isset($params['values']) ? $params['values'] : array();
                $source  = isset($params['source']) ? $params['source'] : null;
                $control = new listbuildercontrol($default, $source);
			    $control->process  = isset($params['process']) ? $params['process'] : null;
                break;
//            case "list":
//                $control = new listcontrol();
//                break;
            case "user":
                $control                = new dropdowncontrol();
                $control->include_blank = isset($params['includeblank']) ? $params['includeblank'] : false;
                $control->items         = $db->selectDropdown('user', 'username');
                break;
            case "color":
            case "colorpicker":
                $control = new colorcontrol();
                if (!empty($params['value'])) $control->value = $params['value'];
                if (!empty($params['hide'])) $control->hide = $params['hide'];
                break;
            case "state":
                //old use:  if (empty($params['all_us_territories'])) {
                /*$regions = $db->select
                    $not_states = array(3,6,7,8,9,10,11,17,20,30,46,50,52,60);
                } else {
                    $not_states = array();
                }*/

                //if(!empty($params['exclude'])) $not_states = array_merge($not_states,explode(',',$params['exclude']));

                if ($db->tableExists('geo_region')) {
                    $c = $db->selectObject('geo_country', 'is_default=1');
                    if (empty($c->id)) $country = 223;
                    else $country = $c->id;

                    $control = new dropdowncontrol();

                    if (isset($params['multiple'])) {
                        $control->multiple  = true;
                        $control->items[-1] = 'ALL United States';
                    }
                    /*if (isset($params['add_other'])) {
                        $control->items[-2] = '-- Specify State Below --';
                    }*/
                    $states = $db->selectObjects('geo_region', 'country_id=' . $country . ' AND active=1 ORDER BY rank, name ASC');
                    foreach ($states as $state) {
                        // only show the US states unless the theme says to show all us territories
                        //if (!in_array($state->id, $not_states)) {
                        $control->items[$state->id] = isset($params['abbv']) ? $state->code : $state->name;
                        //}
                    }
                    //if(!count($states)) $control->items[-2] = '-- Specify State Below --';
                    if (isset($params['add_other'])) $control->items[-2] = '-- Specify State Below --';
                    else $control->include_blank = isset($params['includeblank']) ? $params['includeblank'] : false;

                    // sanitize the default value. can accept as id, code abbrv or full name,
                    if (!empty($params['value']) && !is_numeric($params['value']) && !is_array($params['value'])) {
                        $params['value'] = $db->selectValue('geo_region', 'id', 'name="' . $params['value'] . '" OR code="' . $params['value'] . '"');
                    }
                } else {
                    echo "NO TABLE";
                    exit();
                }
                break;
            case "country":
                //old - pre address configuration
                //if(!empty($params['exclude'])) $not_countries = explode(',',$params['exclude']);
                //else $not_countries = array();

                if ($db->tableExists('geo_country')) {
                    $control                = new dropdowncontrol();
                    $control->include_blank = isset($params['includeblank']) ? $params['includeblank'] : false;
                    if (isset($params['multiple'])) {
                        $control->multiple = true;
                        //$control->items[-1] = 'ALL United States';
                    }

                    if ($params['show_all']) $countries = $db->selectObjects('geo_country', null, 'name ASC');
                    else $countries = $db->selectObjects('geo_country', 'active=1', 'name ASC');

                    foreach ($countries as $country) {
                        //if (!in_array($country->id, $not_countries)) {
                        $control->items[$country->id] = isset($params['abbv']) ? $country->iso_code_3letter : $country->name;
                        //}
                    }

                    // sanitize the default value. can accept as id, code abbrv or full name,
                    if (!empty($params['value']) && !is_numeric($params['value']) && !is_array($params['value'])) {
                        $params['value'] = $db->selectValue('geo_country', 'id', 'name="' . $params['value'] . '" OR code="' . $params['value'] . '"');
                    }
                } else {
                    echo "NO TABLE";
                    exit();
                }
                break;
            case "tagtree":
                $control = new tagtreecontrol($params);
                break;
            case "tags":
                $collections = isset($params['collections']) ? $params['collections'] : array();  //FIXME we don't really use this
                $subtype     = isset($params['subtype']) ? $params['subtype'] : null;   //FIXME we don't really use this
                $control     = new tagpickercontrol($collections, $subtype);
                break;
            case "antispam":
                //eDebug(ANTI_SPAM_CONTROL, true);
                if (SITE_USE_ANTI_SPAM && ANTI_SPAM_CONTROL == 'recaptcha') {
                    // make sure we have the proper config.
                    if (!defined('RECAPTCHA_PUB_KEY') || RECAPTCHA_PUB_KEY == '') {
                        echo '<h2 style="color:red">' . gt('reCaptcha configuration is missing the public key.') . '</h2>';
                        return;
                    }
                    if ($user->isLoggedIn() && ANTI_SPAM_USERS_SKIP == 1) {
                        // skip it for logged on users based on config
                    } else {
                        // include the library and show the form control
                        require_once(BASE . 'external/recaptchalib.php');
                        echo recaptcha_get_html(RECAPTCHA_PUB_KEY);
                        echo '<p>' . gt('Fill out the above security question to submit your form.') . '</p>';
                    }
                    return;
                } elseif (ANTI_SPAM_CONTROL == 0) {
                    return;
                }
                break;
            case "autocomplete":
                $control              = new autocompletecontrol();
                $control->schema      = "'" . str_replace(",", "','", $params['schema']) . "'";
                $control->value   = isset($params['value']) ? $params['value'] : null;
                $control->controller  = empty($params['controller']) ? "search" : $params['controller'];
                $control->action      = empty($params['action']) ? "autocomplete" : $params['action'];
                $control->searchmodel = empty($params['searchmodel']) ? "text" : $params['searchmodel'];
                $control->searchoncol = empty($params['searchoncol']) ? "title" : $params['searchoncol'];
                $control->jsinject    = empty($params['jsinject']) ? "" : $params['jsinject'];
                break;
//            case "massmail":
//                $control = new massmailcontrol();
//                if (!empty($params['var'])) $control->type = 1;
//                if (!empty($params['default'])) $control->default = $params['default'];
//                break;
//            case "contact":
//                $control = new contactcontrol();
//                if (!empty($params['var'])) $control->type = 1;
//                if (!empty($params['default'])) $control->default = $params['default'];
//                break;
            case "quantity":
//                $value   = isset($params['value']) ? $params['value'] : null;
//                $min     = isset($params['min']) ? $params['min'] : 0;
//                $max     = isset($params['max']) ? $params['max'] : 99999;
//                $control = new quantitycontrol($value, $min, $max);
//                break;
                $params['type'] = 'number';
            case "text":
            case "search":
            case "email":
            case "url":
            case "tel":
            case "telephone":
            case "number":
            case "range":
            default:
                $control       = new genericcontrol($params['type']);
                $control->size = !empty($params['size']) ? $params['size'] : "40";
                $control->placeholder = !empty($params['placeholder']) ? $params['placeholder'] : "";
                $control->pattern = !empty($params['pattern']) ? $params['pattern'] : "";
                $control->prepend = !empty($params['prepend']) ? $params['prepend'] : "";
                break;
        }

        //eDebug($smarty->getTemplateVars('formError'));
        //Add the optional params in specified
        if (isset($params['class'])) $control->class = $params['class'];
        if (isset($params['required'])) $control->required = true;

        // Let see if this control should be checked
        if (isset($params['checked'])) {
            // if we have a control group the values will probably be coming in an array
            if (is_array($params['checked'])) {
                // check if its in the array
                if (in_array($params['value'], $params['checked'])) {
                    $control->checked = true;
                } elseif (is_object(current($params['checked']))) {
                    foreach ($params['checked'] as $obj) {
                        if ($obj->id == $params['value']) $control->checked = true;
                    }
                }
            } elseif ($params['value'] == $params['checked']) {
                $control->checked = true;
            } elseif (is_bool($params['checked'])) {
                $control->checked = $params['checked'];
            } elseif ($params['checked'] == 1) {
                $control->checked = 1;
            }
        }

        if (expSession::is_set('last_POST')) {
            $post        = expSession::get('last_POST');
            $post_errors = expSession::get('last_post_errors');
            // flag this field as having errors if it failed validation
            if (is_array($post_errors) && in_array($params['name'], $post_errors)) {
                $control->class .= ' field-error';
            }

            if ($params['type'] == 'checkbox') {
                $realname         = str_replace('[]', '', $params['name']);
                $control->default = $params['value'];
                if (!empty($post[$realname])) {
                    if (is_array($post[$realname])) {
                        if (in_array($params['value'], $post[$realname])) $control->checked = true;
                    } else {
                        $control->checked = true;
                    }
                }
            } elseif (isset($params['multiple'])) {
                $realname = str_replace('[]', '', $params['name']);
                if (!empty($post[$realname])) $control->default = $post[$realname];
            } else {
                if (!empty($post[$params['name']])) $control->default = $post[$params['name']];
            }
        } elseif (isset($params['value'])) {
            // if this field is filtered than lets go ahead and format the data before we stick it in the field.
            if (!empty($params['filter']) && $params['filter'] == 'money') {
                $params['value'] = expCore::getCurrencySymbol() . number_format($params['value'], 2, '.', ',');
            } elseif (!empty($params['filter']) && $params['filter'] == 'integer') {
                $params['value'] = number_format($params['value'], 0, '.', ',');
            }
            $control->default = $params['value'];
        }

        //if (isset($params['value'])) $control->default = $params['value'];
        if (isset($params['caption'])) $control->caption = $params['caption'];
        if (isset($params['description'])) $control->description = $params['description'];
        if (isset($params['size'])) $control->size = $params['size'];
        if (isset($params['nowrap'])) $control->nowrap = "nowrap";
        if (isset($params['flip'])) $control->flip = $params['flip'];
        if (isset($params['disabled']) && $params['disabled'] != false) $control->disabled = true;
        if (isset($params['maxlength'])) $control->maxlength = $params['maxlength'];
        if (isset($params['tabindex'])) $control->tabindex = $params['tabindex'];
        if (isset($params['accesskey'])) $control->accesskey = $params['accesskey'];
        if (isset($params['filter'])) $control->filter = $params['filter'];
        if (isset($params['onclick'])) $control->onclick = $params['onclick'];
        if (isset($params['onchange'])) $control->onchange = $params['onchange'];
        if (isset($params['readonly']) && $params['readonly'] != false) $control->readonly = true;
        if (isset($params['ajaxaction'])) $control->ajaxaction = $params['ajaxaction'];
        if (isset($params['loadjsfile'])) $control->loadjsfile = $params['loadjsfile'];
        if (isset($params['default_date'])) $control->default_date = $params['default_date'];
        if (isset($params['default_hour'])) $control->default_hour = $params['default_hour'];
        if (isset($params['default_min'])) $control->default_min = $params['default_min'];
        if (isset($params['default_ampm'])) $control->default_ampm = $params['default_ampm'];
        if (isset($params['min'])) $control->min = $params['min'];
        if (isset($params['max'])) $control->max = $params['max'];
        if (isset($params['step'])) $control->step = $params['step'];

        $params['name'] = !empty($params['name']) ? $params['name'] : '';
        $control->name  = $params['name'];
//        $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
        //$newid = str_replace($badvals, "", $params['name']);
        $params['id'] = createValidId(!empty($params['id']) ? $params['id'] : '');
        $control->id  = createValidId(isset($params['id']) && $params['id'] != "" ? $params['id'] : "");
        //echo $control->id;
        if (empty($control->id)) $control->id = $params['name'];
        if (empty($control->name)) $control->name = $params['id'];

        /*$labelclass = isset($params['labelclass']) ? ' '.$params['labelclass'] : '';
        
        //container for the control set, including labelSpan and input
        if($params['type']!='hidden') echo '<label id="'.$control->id.'Control" class="control">'; 


        //Write out the label for this control if the user specified a label and there is no label position or position is set to left
        if ( (isset($params['label'])) && (!isset($params['labelpos']) || $params['labelpos'] == 'left') ) {
            echo '<span class="label'.$labelclass.'">'.$params['label'].'</span>';
        }
        */

        // attempt to translate the label
        if (!empty($params['label'])) {
            $params['label'] = gt($params['label']);
        } else {
            $params['label'] = null;
        }
        //write out the control itself...and then we're done. 
        if (isset($params['model'])) {
            echo $control->toHTML($params['label'], $params['model'] . '[' . $params['name'] . ']');
        } else {
            echo $control->toHTML($params['label'], $params['name']);
        }
        /*
        //Write out the label for this control if the user specified a label and position is set to right
        if (isset($params['label']) && $params['labelpos'] == 'right') {
            echo '<span class="label'.$labelclass.'">'.$params['label'].'</span>';
        }
        
        //close the control container div
        if($params['type']!='hidden'){ echo '</label>'; }
        */
    } else {
        echo gt("Both the 'type' and 'name' parameters are required for the control plugin to function");
    }
}

?>
