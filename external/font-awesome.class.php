<?php
/*
 * SMK Font Awesome
 *
 * Get font awesome class names in an array or json format.
 *
 * -------------------------------------------------------------------------------------
 * @Author: Smartik
 * @Author URI: http://smartik.ws/
 * @Copyright: (c) 2014 Smartik. All rights reserved
 * @License: MIT
 *
 * Modified for Exponent CMS to provide additional functionality
 * -------------------------------------------------------------------------------------
 *
 *
 */
if( ! class_exists('Smk_FontAwesome') ){
	class Smk_FontAwesome{

		/**
		 * Font Awesome
		 *
		 * @param string $path font awesome css file path
		 * @param string $class_prefix change this if the class names does not start with `fa-`
		 * @return array|boolean
		 */
		public function getArray($path, $class_prefix = 'fa-'){

			if( ! file_exists($path) )
				return false;//if path is incorrect or file does not exist, stop.

			$file = file_get_contents($path);
            $fileinfo = pathinfo($path);

            $icons = array();
			switch ($fileinfo['extension']) {
                case 'css':
                    $pattern = '/\.('. $class_prefix .'(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';

                    preg_match_all($pattern, $file, $matches, PREG_SET_ORDER);

                    foreach ($matches as $match) {
                        $icons[$match[1]] = $match[2];
                    }
                    break;
                case 'json':
                    $matches = json_decode($file);
                    foreach ($matches as $name=>$match) {
                        if (bs5() && USE_BOOTSTRAP_ICONS) {
                            $icons[$class_prefix . $name] = "\\" . $match;
                        } elseif (bs4() || bs5()) {
                            foreach ($match->styles as $style) {
                                $icons['fa' . $style[0] . ' ' . $class_prefix . $name] = "\\" . $match->unicode;
    //                                break;  // same icon only different font weight
                            }
                        } else {
                            $icons[$name] = "\\" . $match->unicode;
                        }
                    }
                    break;
                case 'yml':
                    require_once(BASE . 'external/spyc-0.6.3/Spyc.php');
                    $matches = spyc_load($file);
                    foreach ($matches as $name=>$match) {
                        if (bs4() || (bs5() && !USE_BOOTSTRAP_ICONS)) {
                            foreach ($match['styles'] as $style) {
                                $icons['fa' . $style[0] . ' ' . $name] = "\\" . $match['unicode'];
                            }
                        } else {
                            $icons[$name] = "\\" . $match['unicode'];
                        }
                    }
                    break;
                default:
                    return false;

            }

			return $icons;
		}

		//------------------------------------//--------------------------------------//

        /**
         * Sort without class prefix, but take it into account
         *
         * @param $a
         * @param $b
         * @return int
         */
        private function cmp($a, $b)
        {
            $a1 = substr($a, 6);
            $a2 = substr($a, 0, 3);
            $b1 = substr($b, 6);
            $b2 = substr($b, 0, 3);
            if ($a1 == $b1) {
                if ($a2 == $b2)
                    return 0;
                return ($a2 < $b2) ? -1 : 1;
            }
            return ($a1 < $b1) ? -1 : 1;
        }

		/**
		 * Sort array by key name
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @return array|boolean
		 */
		public function sortByName($array){

			if( ! is_array($array) )
				return false;//Do not proceed if is not array

            if (bs4() || (bs5() && !USE_BOOTSTRAP_ICONS)) {
                uksort($array, array($this, "cmp"));
            } else {
                ksort( $array );
            }
			return $array;

		}

		//------------------------------------//--------------------------------------//

		/**
		 * Get only HTML class key(class) => value(class), no unicode. 'fa-calendar' => 'fa-calendar',
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @return array|boolean
		 */
		public function onlyClass($array){

			if( ! is_array($array) )
				return false;//Do not proceed if is not array

			$temp = array();
			foreach ($array as $class => $unicode) {
				$temp[$class] = $class;
			}
			return $temp;

		}

		//------------------------------------//--------------------------------------//

		/**
		 * Get only the unicode key, no HTML class. '\f073' => '\f073',
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @return array|boolean
		 */
		public function onlyUnicode($array){

			if( ! is_array($array) )
				return false;//Do not proceed if is not array

			$temp = array();
			foreach ($array as $class => $unicode) {
				$temp[$unicode] = $unicode;
			}
			return $temp;

		}

		//------------------------------------//--------------------------------------//

		/**
		 * Readable class name. Ex: fa-video-camera => Video Camera
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @param string $class_prefix change this if the class names does not start with `fa-`
		 * @return array|boolean
		 */
		public function readableName($array, $class_prefix = 'fa-'){

			if( ! is_array($array) )
				return false;//Do not proceed if is not array

			$temp = array();
			foreach ($array as $class => $unicode) {
			    if (bs5() && USE_BOOTSTRAP_ICONS) {
                    $temp[$class] = ucwords( str_ireplace(array('bi-' , '-'), array('', ' '), $class) );
                } elseif (bs4()  || (bs5() && !USE_BOOTSTRAP_ICONS)) {
                    $temp[$class] = ucwords( str_ireplace(array('fas fa-', 'far fa-', 'fab fa-' , '-'), array('', '', '', ' '), $class) );
                } else {
                    $temp[$class] = ucwords( str_ireplace(array($class_prefix, '-'), array('', ' '), $class) );
                }
			}
			return $temp;

		}

		/**
		 * Readable class name with glyph as prefix. Ex: fa-video => '&#xf03d; Video'
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @param string $class_prefix change this if the class names does not start with `fa-`
		 * @return array|boolean
		 */
		public function nameGlyph($array, $class_prefix = 'fa-'){

			if( ! is_array($array) )
				return false;//Do not proceed if is not array

			$temp = array();
			foreach ($array as $class => $unicode) {
                if (bs4() || (bs5() && !USE_BOOTSTRAP_ICONS)) {
                    $temp[$class] = '&#x' . substr($unicode, 1) . '; ' . ucwords(str_ireplace(array('fas fa-', 'far fa-', 'fab fa-' , '-'), array('', '', '', ' '), $class));
                } else {
                    $temp[$class] = '&#x' . substr($unicode, 1) . '; ' . ucwords(str_ireplace(array($class_prefix, '-'), array('', ' '), $class));
                }
			}
			return $temp;

		}

	}//class
}//class_exists