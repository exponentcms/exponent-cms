<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * Smarty {module_style} function plugin
 *
 * Type:     function<br>
 * Name:     module_style<br>
 * Purpose:  convert module config style settings to class names
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @return string
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_module_style($params,&$smarty) {
	if (empty($params['style']))
		return '';

	$class = '';
	foreach ($params['style'] as $type=>$style) {
		if (!empty($class))
			$class .= ' ';
		switch ($type) {
			case 'border':
				switch ($style) {
					case 'top':
						$class .= 'top-border';
						break;
					case 'bottom':
						$class .= 'bottom-border';
						break;
					case 'topbottom':
						$class .= 'top-border bottom-border';
						break;
                    case 'left':
                        $class .= 'left-border';
                        break;
                    case 'right':
                        $class .= 'right-border';
                        break;
                    case 'leftright':
                        $class .= 'left-border right-border';
                        break;
					case 'box':
						$class .= 'box-border';
						break;
				}
				break;
			case 'background':
				switch ($style) {
					case 'well':
						if (bs4() || bs5()) {
                            $class .= 'card card-body';
                        } else {
                            $class .= 'well';
                        }
						break;
					case 'light':
						$class .= 'light-background';
						break;
					case 'medium':
						$class .= 'medium-background';
						break;
					case 'dark':
						$class .= 'dark-background';
						break;
				}
				break;
			case 'styled':
                if (bs4() || bs5()) {
                    $class .= 'card card-body bg-faded';
                } else {
                    $class .= 'well';
                }
                break;
				break;
			case 'hiddenxs':
				if (bs4() || bs5())
                    $class .= 'd-none d-sm-block';
                elseif (bs3())
					$class .= 'hidden-xs';
				elseif (bs2())
					$class .= 'hidden-phone';
				break;
			case 'hiddensm':
				if (bs4() || bs5())
                    $class .= 'd-none d-md-block';
                elseif (bs3())
					$class .= 'hidden-sm';
				elseif (bs2())
					$class .= 'hidden-phone';
				break;
			case 'hiddenmd':
                if (bs4() || bs5())
                    $class .= 'd-none d-lg-block';
                elseif (bs3())
					$class .= 'hidden-md';
				elseif (bs2())
					$class .= 'hidden-tablet';
				break;
			case 'hiddenlg':
                if (bs4() || bs5())
                    $class .= 'd-none d-xl-block';
                elseif (bs3())
					$class .= 'hidden-lg';
				elseif (bs2())
					$class .= 'hidden-desktop';
				break;
			case 'visiblexs':
                if (bs4() || bs5())
                    $class .= 'd-block d-sm-none';
                elseif (bs3())
					$class .= 'visible-xs-block';
				elseif (bs2())
					$class .= 'visible-phone';
				break;
			case 'visiblesm':
                if (bs4() || bs5())
                    $class .= 'd-block d-md-none';
                elseif (bs3())
					$class .= 'visible-sm-block';
				elseif (bs2())
					$class .= 'visible-phone';
				break;
			case 'visiblemd':
                if (bs4() || bs5())
                    $class .= 'd-block d-lg-none';
                elseif (bs3())
					$class .= 'visible-md-block';
				elseif (bs2())
					$class .= 'visible-tablet';
				break;
			case 'visiblelg':
                if (bs4() || bs5())
                    $class .= 'd-block d-xl-none';
                elseif (bs3())
					$class .= 'visible-lg-block';
				elseif (bs2())
					$class .= 'visible-desktop';
				break;
		}
	}
	return $class;
}

?>