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
		 * @return array
		 */
		public static function getArray($path, $class_prefix = 'fa-'){

			if( ! file_exists($path) )
				return false;//if path is incorect or file does not exist, stop.

			$css = file_get_contents($path);
			$pattern = '/\.('. $class_prefix .'(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';

			preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);
			
			$icons = array();
			foreach ($matches as $match) {
				$icons[$match[1]] = $match[2];
			}
			return $icons;

		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sort array by key name
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @return array
		 */
		public function sortByName($array){
			
			if( ! is_array($array) )
				return false;//Do not proceed if is not array

			ksort( $array );
			return $array;

		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Get only HTML class key(class) => value(class), no unicode. 'fa-calendar' => 'fa-calendar',
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @return array
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
		 * @return array
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
		 * @return array
		 */
		public function readableName($array, $class_prefix = 'fa-'){
			
			if( ! is_array($array) )
				return false;//Do not proceed if is not array

			$temp = array();
			foreach ($array as $class => $unicode) {
				$temp[$class] = ucfirst( str_ireplace(array($class_prefix, '-'), array('', ' '), $class) );
			}
			return $temp;

		}

		/**
		 * Readable class name with glyph as prefix. Ex: fa-video-camera => '<i class="fa fa-video-camera">Video Camera</a>'
		 *
		 * @param array $array font awesome array. Create it using `getArray` method
		 * @param string $class_prefix change this if the class names does not start with `fa-`
		 * @return array
		 */
		public function nameGlyph($array, $class_prefix = 'fa-'){

			if( ! is_array($array) )
				return false;//Do not proceed if is not array

			$temp = array();
			foreach ($array as $class => $unicode) {
				$temp[$class] = '&#x' . substr($unicode, 1) . '; ' . ucfirst(str_ireplace(array($class_prefix, '-'), array('', ' '), $class));
			}
			return $temp;

		}

	}//class
}//class_exists