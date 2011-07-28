<?php

/*
 * This file is part of ExponentCMS.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Prints all log messages in real time.
 * 
 * @package Swift
 * @subpackage Transport
 * @author Chris Corbyn
 */
class Swift_Plugins_Loggers_eDebugLogger implements Swift_Plugins_Logger
{

  /**
   * Create a new EchoLogger.
   * 
   * @param boolean $isHtml
   */
  public function __construct()
  {
  }
  
  /**
   * Add a log entry.
   */
  public function add($entry)
  {
      eDebug($entry);
  }
  
  /**
   * Not implemented.
   */
  public function clear()
  {
  }
  
  /**
   * Not implemented.
   */
  public function dump()
  {
  }
  
}
