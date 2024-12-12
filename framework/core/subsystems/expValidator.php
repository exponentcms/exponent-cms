<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

/** @define "BASE" "../../.." */
/**
 * This is the class expValidator
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expValidator {
	/**
     * Checks if object field contains a value (not empty)
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function presence_of($field, $object, $opts) {
        $value = trim($object->$field);
        if (empty($value)) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is a required field";
        } else {
            return true;
        }
    }

	/**
     * Checks if object field contains an numeric value
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function numericality_of($field, $object, $opts) {
        if (is_numeric($object->$field)) {
            return true;
        } else {
            return ucwords($field).' must be a valid number.';
        }
    }

	/**
     * Checks if object field contains an alphanumeric value
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function alphanumericality_of($field, $object, $opts) {
        $reg = "#[^a-z0-9\s-_)(/]#i";
        $count = preg_match($reg, $object->$field, $matches);

        if ($count > 0){
            return ucwords($field).' contains invalid characters.';
        } else {
            return true;
        }
    }

	/**
     * Checks if object field value is between two limits
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function value_between($field, $object, $opts) {
        // make sure the value is numeric
        if (!is_numeric($object->$field)) return ucwords($field).' must be a valid number.';
        if ($object->$field < $opts['range']['from'] || $object->$field > $opts['range']['to']) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." must be a value between ".$opts['range']['to']." and ".$opts['range']['from'] ;
        } else {
            return true;
        }
    }

	/**
     * Checks if object field is set (boolean true)
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function acceptance_of($field, $object, $opts) {
        if (empty($object->$field)) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." must be accepted.";
        } else {
            return true;
        }
    }

	/**
	 * @param $field
	 * @param $object
	 * @param $opts
	 */
	public static function confirmation_of($field, $object, $opts) {
        //STUB::TODO
    }

	/**
	 * @param $field
	 * @param $object
	 * @param $opts
	 */
	public static function format_of($field, $object, $opts) {
        //STUB::TODO
    }

	/**
     * Checks if object field is minimum length
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function length_of($field, $object, $opts) {
        if (strlen($object->$field) < $opts['length']) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." must be longer than ".$opts['length']." characters long.";
        } else {
            return true;
        }
    }

	/**
     * Check for uniqueness of object field amongst all items in table
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function uniqueness_of($field, $object, $opts) {
        global $db;

        $sql = $field."='".$object->$field."'";
        if (!empty($object->id)) $sql .= ' AND id != '.$object->id;
        if (array_key_exists('grouping_sql', $opts)) $sql .= $opts['grouping_sql'];  // allow grouping sql parameter to be passed
        $ret = $db->countObjects($object->tablename, $sql);
        if ($ret > 0) {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field).' "'.$object->$field.'" is already in use.';
        } else {
            return true;
        }
    }

	/**
     * Checks if object files contains a valid zipcode
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function is_valid_zipcode($field, $object, $opts) {
        $match = array();
        if($object->country != 223) return true; //if non-us, than we won't validate the zip

		$pattern = "/^\d{5}([\-]\d{4})?$/";
		if (!preg_match($pattern, $object->$field, $match, PREG_OFFSET_CAPTURE)) {
			return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is not a valid US zip code.";
		} else {
			return true;
		}
    }

	/**
     * Checks if object field contains a valid phone number
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function is_valid_phonenumber($field, $object, $opts) {
        $match = array();
		$pattern = "/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/";
		if (!preg_match($pattern, $object->$field, $match, PREG_OFFSET_CAPTURE)) {
			return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is not a valid phone number.";
		} else {
			return true;
		}
    }

    /**
     * Checks if object field contains a valid state abbreviation
     *
     * @param $field
     * @param $object
     * @param $opts
     *
     * @return bool|string
     */
    public static function is_valid_state($field, $object, $opts) {
        if(($object->state == -2 && !empty($object->non_us_state)) || $object->country != 223 || $object->state > 0) {
            return true; //supplied a non-us state/province, so we're OK
        } else {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." is not a valid US state abbreviation.";
        }
    }

	/**
     * Checks if object field contains a valude email address
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function is_valid_email($field, $object, $opts) {
        $email = $object->$field;
        // First, we check that there's one @ symbol, and that the lengths are right
//        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {

        //
        if(self::isValidEmail($email))
        {
            return true;
        }
        else
        {
            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
        }
        //  old code below
//        if (!preg_match("^[^@]{1,64}@[^@]{1,255}$", $email)) {
//            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
//            return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
//        }
//        // Split it into sections to make life easier
//        $email_array = explode("@", $email);
//        $local_array = explode(".", $email_array[0]);
//        for ($i = 0; $i < count($local_array); $i++) {
//            if (!preg_match("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
//                return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
//            }
//        }
//        if (!preg_match("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
//            $domain_array = explode(".", $email_array[1]);
//            if (count($domain_array) < 2) {
//                return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
//            }
//            for ($i = 0; $i < count($domain_array); $i++) {
//                if (!preg_match("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
//                    return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field)." does not appear to be a valid email address";
//                }
//            }
//        }
//        return true;
    }

	/**
     * Validate an email address.
     * Provide email address (raw input)
     * Returns true if the email address has the email
     * address format and the domain exists.
     *
	 * @param $email
	 * @return bool
	 */
    public static function isValidEmail($email)
    {
       $isValid = true;
       $atIndex = strrpos($email, "@");
       if (is_bool($atIndex) && !$atIndex)
       {
          $isValid = false;
       }
       else
       {
          $domain = substr($email, $atIndex+1);
          $local = substr($email, 0, $atIndex);
          $localLen = strlen($local);
          $domainLen = strlen($domain);
          if ($localLen < 1 || $localLen > 64)
          {
             // local part length exceeded
             $isValid = false;
          }
          else if ($domainLen < 1 || $domainLen > 255)
          {
             // domain part length exceeded
             $isValid = false;
          }
          else if ($local[0] === '.' || $local[$localLen-1] === '.')
          {
             // local part starts or ends with '.'
             $isValid = false;
          }
          else if (preg_match('/\\.\\./', $local))
          {
             // local part has two consecutive dots
             $isValid = false;
          }
          else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
          {
             // character not valid in domain part
             $isValid = false;
          }
          else if (preg_match('/\\.\\./', $domain))
          {
             // domain part has two consecutive dots
             $isValid = false;
          }
          else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
          {
             // character not valid in local part unless
             // local part is quoted
             if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
             {
                $isValid = false;
             }
          }
          /*if ($isValid && !(checkdnsrr($domain,"MX") ||  checkdnsrr($domain,"A")))
          {
             // domain not found in DNS
             $isValid = false;
          }*/
       }
       return $isValid;
    }

	/**
     * Checks if object field contains a valid sef name
     *
	 * @param $field
	 * @param $object
	 * @param $opts
	 * @return mixed
	 */
	public static function is_valid_sef_name($field, $object, $opts) {
		$match = array();
		$pattern = "/([^0-9a-z-_\+\.])/i";
		if (empty($object->$field) || preg_match($pattern, $object->$field, $match, PREG_OFFSET_CAPTURE)) {
			return array_key_exists('message', $opts) ? $opts['message'] : ucwords($field) . " '" . $object->$field . "' is not a valid sef url.";
		} else {
			return true;
		}
    }

	/**
     * Checks validity of recaptcha antispam entry
     *
	 * @param $params
	 * @param string $msg
	 * @return bool
	 */
	public static function check_antispam($params, $msg="") {
		global $user;

		if (SITE_USE_ANTI_SPAM == 0 || ($user->isLoggedIn() && ANTI_SPAM_USERS_SKIP == 1)) {
			return true;
		}
        $msg = empty($msg) ? gt('Anti-spam verification failed.  Please try again.') : $msg;
        switch (ANTI_SPAM_CONTROL) {
            case 'recaptcha':
            case 'recaptcha_v2':
                if (empty($params["g-recaptcha-response"])) {
                    self::failAndReturnToForm($msg . '-' . gt('No Response'), $params);  // there was no response
                }

                if (!defined('RECAPTCHA_PRIVATE_KEY')) {
                    self::failAndReturnToForm(gt('reCAPTCHA is not properly configured. Please contact an administrator.'), $params);
                }

                if (version_compare(PHP_VERSION, '8.0.0', 'lt')) {
                    require_once(BASE . 'external/ReCaptcha/autoload.php');  // v1.2.4
                } else {
                    require_once(BASE . 'external/ReCaptcha/src/autoload.php'); // v1.3.0
                }
                $reCaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_PRIVATE_KEY);

                $resp = $reCaptcha->verify(
                    $params["g-recaptcha-response"],
                    $_SERVER["REMOTE_ADDR"]
                );

                if ($resp->isSuccess()) {
                    return true;
                } else {
                    //Compatibility with old school form module - prb
                    if (!isset($params['manual_redirect'])) {
                        self::failAndReturnToForm($msg, $params);
                    } else {
                        return false;
                    };
                }
                break;
//            case 'recaptcha_v3':
            case 0:
                return true;
//                break;
        }
        return false;
    }

	/**
     * All purpose validation method
     *
	 * @param $vars
	 * @param $post
	 * @return bool
	 */
	public static function validate($vars, $post) {
        if (!is_array($vars)) return false;

        $post['_formError'] = array();
        foreach($vars as $validate_type=>$param) {
            switch($validate_type) {
//                case 'captcha':
//                case 'capcha':
//                    $captcha_real = expSession::get('captcha_string');
//                    if (SITE_USE_ANTI_SPAM && strtoupper($post[$param]) != $captcha_real) {
//                            unset($post[$param]);
//                            $post['_formError'][] = gt('Captcha Verification Failed');
//                    }
//                break;
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
        }
        return true;
    }

	/**
     * Routine to note error and return to the filled out form with messages
     *
	 * @param string|array $msg
	 * @param null $post
	 */
	public static function failAndReturnToForm($msg='', $post=null) {
        if (!is_array($msg))
            $msg = array($msg);
        flash('error', $msg);
        if (!empty($post))
            expSession::set('last_POST',$post);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

	/**
     * Routine to mark which form field had the error
     *
	 * @param $field
	 */
	public static function setErrorField($field) {
        $errors = expSession::get('last_post_errors');
        if (!in_array($field, $errors))
            $errors[] = $field;
        expSession::set('last_post_errors', $errors);
    }

	/**
     * Routine to  error and return to the filled out form with flash message
     *
	 * @param string $queue
	 * @param $msg
	 * @param null $post
	 */
	public static function flashAndReturnToForm($queue='message', $msg='', $post=null) {
        if (!is_array($msg))
            $msg = array($msg);
        flash($queue, $msg);
        if (!empty($post))
            expSession::set('last_POST',$post);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

	/**
     * Wrapper function to validate email address
     *
	 * @param $email
	 * @return bool
	 */
	public static function validate_email_address($email) {
		return self::isValidEmail($email);

		// old code
//        // First, we check that there's one @ symbol, and that the lengths are right
//        if (!preg_match("^[^@]{1,64}@[^@]{1,255}$", $email)) {
//            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
//            return false;
//        }
//        // Split it into sections to make life easier
//        $email_array = explode("@", $email);
//        $local_array = explode(".", $email_array[0]);
//        for ($i = 0; $i < count($local_array); $i++) {
//            if (!preg_match("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
//                return false;
//            }
//        }
//        if (!preg_match("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
//            $domain_array = explode(".", $email_array[1]);
//            if (count($domain_array) < 2) {
//                return false; // Not enough parts to domain
//            }
//            for ($i = 0; $i < count($domain_array); $i++) {
//                if (!preg_match("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
//                    return false;
//                }
//            }
//        }
//        return true;
    }

	/**
     * Determines if upload was successfully inserted into 'file' table
     *
	 * @param $file
	 * @return mixed
     * @deprecated
	 */
	public static function uploadSuccessful($file) {
        global $db;

        if (is_object($file)) {
            return $db->insertObject($file,'file');
        } else {
            $post = expString::sanitize($_POST);
            $post['_formError'] = $file;
            flash('error',$file);
            expSession::set('last_POST',$post);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    /**
     * Method to determine password strength
     *   e.g., not correct type/number of characters
     * @param $password
     *
     * @return string   error if any
     */
    public static function checkPasswordStrength($password) {
		// Return blank string on success, error message on failure.
		// The error message should let the user know why their password is wrong.
//		if (strcasecmp($username, $password) == 0) {
//			return gt('Password cannot be equal to the username.');
//		}

		# Check password minimum length
        if (strlen($password) < MIN_PWD_LEN) {
            return gt('Passwords must be at least') . ' ' . MIN_PWD_LEN . ' ' . gt('characters long');
        }

        # Check password for minimum number of lower case characters
//        if (preg_match_all("#[a-z]#, $password, $matches) < MIN_LOWER) {
//            return gt('Passwords must have at least') . ' ' . MIN_LOWER . ' ' . gt('lower case letter(s)');
//        }

        # Check password for minimum number of upper case characters
        if (preg_match_all("#[A-Z]#", $password, $matches) < MIN_UPPER) {
            return gt('Passwords must have at least') . ' ' . MIN_UPPER . ' ' . gt('upper case letter(s)');
        }

        # Check password for minimum number of numeric characters
        if (preg_match_all('#[0-9]#', $password, $matches) < MIN_DIGITS) {
            return gt('Passwords must have at least') . ' ' . MIN_DIGITS . ' ' . gt('digit(s)');
        }

        # Check password for minimum number of symbols
        if (preg_match_all("#\W+#", $password, $matches) < MIN_SYMBOL) {
            return gt('Passwords must have at least') . ' ' . MIN_SYMBOL . ' ' . gt('symbol(s)');
        }

		return ''; // otherwise, no errors
	}

    /**
     * Generate a random secure password
     *
     * @param string $len
     * @param string $cap
     * @param string $num
     * @param string $sym
     *
     * @return string
     */
    public static function generatePassword($len = MIN_PWD_LEN, $cap = MIN_UPPER, $num = MIN_DIGITS, $sym = MIN_SYMBOL)
    {
        // get count of all required minimum special chars
        $count = (int)$cap + (int)$num + (int)$sym;

        // sanitize inputs; should be self-explanatory
        if (!is_numeric($len) || !is_numeric($cap) || !is_numeric($num) || !is_numeric($sym)) {
            trigger_error('Argument(s) not an integer', E_USER_WARNING);
            return false;
        } elseif ($len < MIN_PWD_LEN || $len > 20 || $cap < MIN_UPPER || $num < MIN_DIGITS || $sym < MIN_SYMBOL) {
            trigger_error('Argument(s) out of range', E_USER_WARNING);
            return false;
        } elseif ($cap > $len) {
            trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
            return false;
        } elseif ($num > $len) {
            trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
            return false;
        } elseif ($sym > $len) {
            trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
            return false;
        } elseif ($count > $len) {
            trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
            return false;
        }

        // all inputs clean, proceed to build password

        // change these strings if you want to include or exclude possible password characters
        $chars = "abcdefghijklmnopqrstuvwxyz";
        $caps = strtoupper($chars);
        $nums = "0123456789";
        $syms = "!@#$%^&*()-+?";

        // build the base password of all lower-case letters
        $out = '';
        for ($i = 0; $i < $len; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // create arrays if special character(s) required
        if ($count) {
            // split base password to array; create special chars array
            $tmp1 = str_split($out);
            $tmp2 = array();

            // add required special character(s) to second array
            for ($i = 0; $i < $cap; $i++) {
                $tmp2[] = $caps[random_int(0, strlen($caps) - 1)];
            }
            for ($i = 0; $i < $num; $i++) {
                $tmp2[] = $nums[random_int(0, strlen($nums) - 1)];
            }
            for ($i = 0; $i < $sym; $i++) {
                $tmp2[] = $syms[random_int(0, strlen($syms) - 1)];
            }

            // hack off a chunk of the base password array that's as big as the special chars array
            $tmp1 = array_slice($tmp1, 0, $len - $count);
            // merge special character(s) array with base password array
            $tmp1 = array_merge($tmp1, $tmp2);
            // mix the characters up
            shuffle($tmp1);
            // convert to string for output
            $out = implode('', $tmp1);
        }

        return $out;
    }

    /**
     * Routine to check that username is valid (longer than 3 characters)
     *
     * @param $username
     *
     * @return string
     */
    public static function checkUsername($username) {
		// Return blank string on success, error message on failure.
		// The error message should let the user know why their username is wrong.
		if (strlen($username) < 3) {
			return gt('Your username must be at least 3 characters.');
		}
		//echo "<xmp>";
		//print_r(preg_match("/^[a-zA-Z0-9]/",$username));
		//echo "</xmp>";
		//exit;

		//if (!preg_match("/[a-zA-Z0-9]/",$username)){
		//	return gt('Your username contains illegal characters.');
		//}
		return ""; // otherwise, no errors
	}
}

?>