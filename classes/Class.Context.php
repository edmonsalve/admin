<?php

   /* @author     Edmundo Monsalve
	* @copyright  2011
	* @filesource ClassContext.php
	**/
 
class Context {
	var $Dictionary;
	
	
	function Init() {
		$this->Dictionary = array();
	}
	
	function GetDefinition($Code) {
		if (array_key_exists($Code, $this->Dictionary)) {
			return $this->Dictionary[$Code];
		} else {
			return $Code.' -> No encontrado ';
		}
	}
	
}
?>
