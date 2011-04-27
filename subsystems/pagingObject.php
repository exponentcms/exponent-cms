<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Exponent is written and Designed by James Hunt
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
# Author: Jacob Mesu
#
##################################################

class PagingObject {
  var $pageUpperLimit = 3;
  var $pageLowerLimit = 3;
  var $pageCurrent = 0;
  var $pageSize = 25;
  
  var $items;
  var $itemCount;
  
  // Constructor
  function PagingObject($PageCurrent, $ItemCount, $PageSize=25)
  {
    $this->pageCurrent = $PageCurrent;
    $this->itemCount = $ItemCount;
    $this->pageSize = $PageSize; 
  }
  
    
  function GotoNextPage() 
  {
    // TODO: Extend functions with item array 
    if(($this->pageCurrent +1) > $this->ItemCount ) 
    {
      trigger_error("Unable to go to the next page, current page is already the last page.", E_USER_WARNING);
    } 
    else 
    {
      $this->pageCurrent++;      
    }      
  }
  
  function GotoPreviousPage()
  {
    // TODO: Extend functions with item array 
    if(($this->pageCurrent -1) < 1) 
    {
      trigger_error("Unable to go to the previous page, current page is already the first page.", E_USER_WARNING);
    } 
    else 
    {
      $this->pageCurrent--;      
    }    
  }
  
  function GotoPage()
  {  
  }
 
  function GetPageCount()
  {
    return ceil( $this->itemCount / $this->pageSize );
  }
  
  function GetOffSet()
  {  
    return "".($this->pageSize * ($this->pageCurrent-1)) .",". $this->pageSize;
  }
  
  function GetCurrentPage()
  {
    return $this->pageCurrent;
  }
  
  function GetUpperLimit()
  {
  	return $this->pageCurrent + 10;  
  }
  
  function GetLowerLimit()
  {
    return (($this->pageCurrent-10) < 1) ? 1 : ($this->pageCurrent-10);
  }    
}

?>
