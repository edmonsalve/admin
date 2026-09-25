<?php

class plantilla{
	public $tpl_file;
	public $fd;
	public $template_file;
	public $mihtml;
	public $vars;
	
	function __construct($tpl_file) {
		$this->tpl_file = PATH_TEMPLATES . $tpl_file.'.tpl'; 
	}
	
	public function asigna_variables($vars){
		$this->vars= (empty($this->vars)) ? $vars : $this->vars . $vars;
	}
	
	public function muestra(){ 
		$this->fd = fopen($this->tpl_file, 'r'); 

		if (!$this->fd) {
			sostenedor_error('error al abrir la plantilla ' . $this->tpl_file);
		} else{ 
			$this->template_file = fread($this->fd, filesize($this->tpl_file));
			fclose($this->fd);
			$this->mihtml = $this->template_file;
			$this->mihtml = str_replace ("'", "\'", $this->mihtml);
			$this->mihtml = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->mihtml);
			reset ($this->vars);
			
			foreach($this->vars as $key => $val) { $$key = $val; } 

			eval("\$this->mihtml = '$this->mihtml';");
			reset ($this->vars);
			foreach($this->vars as $key => $val) { unset($$key); } 
	
			$this->mihtml=str_replace ("\'", "'", $this->mihtml);
			echo $this->mihtml;
		}
	}
}

function sostenedor_error($error){
	$miplantilla=new plantilla("error");
	$miplantilla->asigna_variables(array(
		'ERROR' => $error)
	);
	return $miplantilla->muestra();
}
?>
