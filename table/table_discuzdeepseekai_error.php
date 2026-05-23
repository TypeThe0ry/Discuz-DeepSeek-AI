<?php
if(!defined('IN_DISCUZ')){
exit('Acccess Denied');
} 
class table_discuzdeepseekai_error extends discuz_table{
	public function __construct(){
		$this->_table = 'plugin_discuzdeepseekai_err';
		$this->_pk    = 'id';
		parent::__construct();
	}

}
?>