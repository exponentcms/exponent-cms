<?php

class storeCategoryFeeds extends storeCategory {

	public $has_and_belongs_to_many = array('google_product_types');
	
	public function __construct($params=array(), $get_assoc=true, $get_attached=true) {
	
		parent::__construct($params, $get_assoc, $get_attached);
		
	}
	

}

?>