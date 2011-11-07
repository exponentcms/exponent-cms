<?php

/**
 * Smarty {assign_adv} function plugin
 *
 * Type:     function<br>
 * Name:     assign_adv<br>
 * Purpose:  Advanced assign variable to template
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_assign_adv($params, &$smarty)
{
    extract($params);

    if (empty($var)) {
        $smarty->trigger_error("assign_adv: missing 'var' parameter");
        return;
    }

    if (!in_array('value', array_keys($params))) {
        $smarty->trigger_error("assign_adv: missing 'value' parameter");
        return;
    }
    if (preg_match('/^\s*array\s*\(\s*(.*)\s*\)\s*$/s',$value,$match)){
        eval('$value=array('.str_replace("\n", "", $match[1]).');');
    }
    else if (preg_match('/^\s*range\s*\(\s*(.*)\s*\)\s*$/s',$value,$match)){
        eval('$value=range('.str_replace("\n", "", $match[1]).');');
    }

    $smarty->assign($var, $value);
}
?>