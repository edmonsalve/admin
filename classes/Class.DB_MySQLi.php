<?php

/**
 * @author     Edmundo Monsalve
 * @copyright  2010
 * @filesource class-DB_MySQLi.php
 **/

class DB_MySQLi {
    // Variables de Conexion
    var $BaseDatos;
    var $Servidor;
    var $Usuario;
    var $Clave;
    
    // identidicadores de Consulta y Conexion
    var $IdConexion = 0;
    var $IdConsulta = 0;
    
    // Control de Errores
    var $ErrNro = 0;
    var $ErrTxt = '';
    
    // Método Constructor: Cada vez que creemos una variable de esta clase, se ejecutará esta función 
    function DB_MySQLi($bd = "", $host = "", $user = "", $pass = "") {
        $this->BaseDatos = $bd;
        $this->Servidor  = $host;
        $this->Usuario   = $user;
        $this->Clave     = $pass;
    }
    
    
    // Conexión a la base de datos
    function conectar($bd, $host, $user, $pass){
        if ($bd != "")   $this->BaseDatos = $bd;
        if ($host != "") $this->Servidor  = $host;
        if ($user != "") $this->Usuario   = $user;
        if ($pass != "") $this->Clave     = $pass; 
        
        // Conectamos al servidor
       
        $this->IdConexion = @mysqli_connect($this->Servidor, $this->Usuario, $this->Clave);
        
        if (!$this->IdConexion) {  
            $this->Error = "Ha fallado la conexión.";
            return 0;
        }

        //seleccionamos la base de datos
        if (!@mysqli_select_db($this->IdConexion,$this->BaseDatos)) {
            $this->Error = "Imposible abrir ".$this->BaseDatos ;
            return 0;
        }
        
        
        // Si hemos tenido éxito conectando devuelve el identificador de la conexión, sino devuelve 0
        return $this->IdConexion;
    }

 
    // Ejecutar un consulta a la base de datos
    function consulta($sql = ""){
        if (trim($sql) == "") {
            $this->ErrTxt = "No ha especificado una consulta SQL";
            return 0;
        }  
        
        //ejecutamos la consulta
        $this->IdConsulta = mysqli_query($this->IdConexion, $sql);
        if (!$this->IdConsulta) {
            $this->ErrNro = mysqli_errno($this->IdConexion);
            $this->ErrTxt = mysqli_error($this->IdConexion);
            return 0;
        }
        
        // Si hemos tenido éxito en la consulta devuelve el identificador de la conexión, sino devuelve 0 
        return $this->IdConsulta;
    }


    //Devuelve el número de campos de una consulta
    function numcampos() {
        return mysqli_num_fields($this->IdConsulta);
    }

 

    // Devuelve el número de registros de una consulta 
    function numregistros(){
        return mysqli_num_rows($this->IdConsulta);
    }

 
    // Devuelve el nombre de un campo de una consulta 
    /*
    function nombrecampo($numcampo) {
        return mysql_field_name($this->IdConsulta, $numcampo);
    }
    */
    
    // Devuelve el ID del ultimo Ingreso
    function ultimoId() {
		return mysqli_insert_id($this->IdConexion); 
	}
    
    // Muestra los datos de una consulta 
    function verconsulta() {
        echo "<table border=1>\n";
        
        // mostramos los nombres de los campos
        for ($i = 0; $i < $this->numcampos(); $i++){
            echo "<td><b>".$this->nombrecampo($i)."</b></td>\n";
        }
        
        echo "</tr>\n";

        // mostrarmos los registros
        while ($row = mysqli_fetch_row($this->IdConsulta)) {
            echo "<tr> \n";
            for ($i = 0; $i < $this->numcampos(); $i++){
                echo "<td>".$row[$i]."</td>\n";
            }
            echo "</tr>\n";
        }
    }

}

?>
