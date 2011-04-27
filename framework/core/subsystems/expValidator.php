<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file thats holds the expValidator class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expValidator
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */

class expValidator {
    public static function presence_of($field, $object, $opts) {
        $value = trim($object->$field);
        if (empty($value)) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is a required field";
        } else {
            return true;
        }
    }

    public static function numericality_of($field, $object, $opts) {
        if (is_numeric($object->$field)) {
            return true;
        } else { 
            return ucwords($field).' must be a valid number.';
        }
    }

    public static function alphanumericality_of($field, $object, $opts) {
        $reg = "#[^a-z0-9\s-_)(/]#i";
        $count = preg_match($reg, $object->$field, $matches);
        
        if ($count > 0){
            return ucwords($field).' contains invalid characters.';
        } else { 
            return true;
        }
    }
    
    public static function value_between($field, $object, $opts) {
        // make sure the value is numeric
        if (!is_numeric($object->$field)) return ucwords($field).' must be a valid number.';
        if ($object->$field < $opts['range']['from'] || $object->$field > $opts['range']['to']) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." must be a value between ".$opts['range']['to']." and ".$opts['range']['from'] ;
        } else {
            return true;
        }
    }
    
    public static function acceptance_of($field, $object, $opts) {
        if (empty($object->$field)) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." must be accepted.";
        } else {
            return true;
        }
    }

    public static function confirmation_of($field, $object, $opts) {
        //STUB::TODO        
    }

    public static function format_of($field, $object, $opts) {
        //STUB::TODO    
    }

    public static function length_of($field, $object, $opts) {
        if (strlen($object->$field) < $opts['length']) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." must be longer than ".$opts['length']." characters long.";
        } else {
            return true;
        } 
    }
    
    public static function uniqueness_of($field, $object, $opts) {
        global $db;
        $sql = "`".$field."`='".$object->$field."'";
        if (!empty($object->id)) $sql .= ' AND id != '.$object->id;
        $ret = $db->countObjects($object->tablename, $sql);
        if ($ret > 0) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field).' "'.$object->$field.'" is already in use.';
        } else {
            return true;
        }
    }
    
    public static function is_valid_zipcode($field, $object, $opts) {
        $match = array();
		$pattern = "/^\d{5}([\-]\d{4})?$/";
		if (!preg_match($pattern, $object->$field, $match, PREG_OFFSET_CAPTURE)) {
			return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is not a valid US zip code.";
		} else {
			return true;
		}
    }
    
    public static function is_valid_phonenumber($field, $object, $opts) {
        $match = array();
		$pattern = "/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/";
		if (!preg_match($pattern, $object->$field, $match, PREG_OFFSET_CAPTURE)) {
			return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is not a valid US zip code.";
		} else {
			return true;
		}
    }
    
    public static function is_valid_email($field, $object, $opts) {
        $email = $object->$field;
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
                return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
            }
        }
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                    return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
                }
            }
        }
        return true;
    }
    
    public static function is_valid_sef_name($field, $object, $opts) {
		$match = array();
		$pattern = "/([^0-9a-z-_\+\.])/i";
		if (empty($object->$field) || preg_match($pattern, $object->$field, $match, PREG_OFFSET_CAPTURE)) {
			return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is not a valid sef url.";   
		} else {
			return true;
		}
    }
    
    public static function check_antispam($params, $msg="") {
        $msg = empty($msg) ? 'Anti-spam verification failed.' : $msg;
        switch (ANTI_SPAM_CONTROL) {
            case 'recaptcha':
                if (empty($params["recaptcha_response_field"])) {
                    self::failAndReturnToForm($msg, $params);
                } 
                
                if (!defined('RECAPTCHA_PRIVATE_KEY')) {
                    self::failAndReturnToForm('reCAPTCHA is not properly configured. Please contact an administrator.', $params);
                }
                
                require_once(BASE.'external/recaptchalib.php');
                
                $resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,$_SERVER["REMOTE_ADDR"],$params["recaptcha_challenge_field"],$params["recaptcha_response_field"]);

                if ($resp->is_valid) {
                    return true;
                } else {
                    //Compatibility with old school form module - prb
                    if (!isset($params['manual_redirect'])) {
                        self::failAndReturnToForm($msg, $params);
                    }else{
                        return false;
                    };
                }
            break;
            case 0:
                return true;
            break;
        }
    }

    public static function validate($vars, $post) {
        if (!is_array($vars)) return false;

        $post['_formError'] = array();
        foreach($vars as $validate_type=>$param) {
            switch($validate_type) {
                case 'captcha':
                case 'capcha':
                    $captcha_real = exponent_sessions_get('captcha_string');
                    if (SITE_USE_CAPTCHA && strtoupper($post[$param]) != $captcha_real) {
                            unset($post[$param]);
                            $post['_formError'][] = exponent_lang_getText('Captcha Verification Failed');
                    }
                break;
                case 'presence_of':
                    if (empty($post[$param])) $post['_formError'][] = $param.' is a required field.';
                break;
                case 'valid_email':
                    if (empty($post[$param])) {
                        $post['_formError'][] = $param.' is a required field.';
                    } elseif (!self::validate_email_address($post[$param])) {
                        $post['_formError'][] = $post[$param].' does not appear to be a valid email address.';
                    }
                break;
            }
        }
        
        if (count($post['_formError']) > 0) {
            self::failAndReturnToForm($post['_formError'], $post);
        } else { 
            return true;
        }
    }

    public static function failAndReturnToForm($msg='', $post=null) {
        if (!is_array($msg)) $msg = array($msg);
        flash('error', $msg);
        if (!empty($post)) expSession::set('last_POST',$post);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public static function setErrorField($field) {
        $errors = expSession::get('last_post_errors');
        if (!in_array($field, $errors)) $errors[] = $field;
        expSession::set('last_post_errors', $errors);
    }
    
    public static function flashAndReturnToForm($queue='message', $msg, $post=null) {
        if (!is_array($msg)) $msg = array($msg);
        flash($queue, $msg);
        if (!empty($post)) exponent_sessions_set('last_POST',$post);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    public static function validate_email_address($email) {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
                return false;
            }
        }
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }       
    
    public static function uploadSuccessful($file) {
        global $db;
        if (is_object($file)) {
            return $db->insertObject($file,'file');
        } else {
            $post = $_POST;
            $post['_formError'] = $file;
            flash('error',$file);
            exponent_sessions_set('last_POST',$post);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}
?>



