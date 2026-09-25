<?php   
    if (!defined('DB_COMUN')) { 
        define('TOPBAR',    	'none');  // block, none

        include('variables_cliente.php'); 
        
        // ::: DB
        define('DB_ADMIN',    	DB_PREFIJO.'@admin');
        define('DB_CLIENTE', 	DB_PREFIJO.'@cliente');
        define('DB_COMUN',   	DB_PREFIJO.'@comun');
		define('DB_COMUN_CON',  DB_PREFIJO.'@comunContab');
		define('DB_COMUN_IND',  DB_PREFIJO.'@comunIndicadores');
        define('DB_CONTAB',  	DB_PREFIJO.'contabilidad');
        define('DB_PERSON',     DB_PREFIJO.'@personas');        
        
        define('DB_MULTAS',    'db_multas');

        // :::  Contraseña inicial usario
        define('USER_CADUC',        '+1 year');             //'+1 year': para un año; '+6 month': para 6 meses
        define('USER_PASS_INI',     '123456'); 

        // ::: DB 
        define('DB_INTRANET',	     DB_PREFIJO .  'intranet'); 
		define('DB_PERSONAL_CODIGO', DB_PREFIJO . 'personalCodigo'); 	
		define('DB_PERSONAL_EDUC',	 DB_PREFIJO . 'personalEduc'); 	
		define('DB_PERSONAL_JUNJI',	 DB_PREFIJO . 'personalJunji'); 	
		define('DB_PERSONAL_MUNI',	 DB_PREFIJO . 'personalMuni'); 		 			
        define('DB_PERSONAL_SALUD',	 DB_PREFIJO . 'personalSalud'); 	

        define('DB_SOC_MUNI',	 DB_PREFIJO . 'socMuni'); 	
        define('DB_SOC_SALUD',	 DB_PREFIJO . 'socSalud'); 	

        define('DB_GDOC',            DB_PREFIJO . 'gdoc');
        // define('DB_CERT',            DB_PREFIJO . 'certificadosMuni');

        // APP
        define('APP_CME',           '/appCme/');
        define('APP_COMUN',         '/appComun/');   
        define('APP_CONTAB',        '/appContabilidad/');
        define('APP_EDUC',          '/appEduc/');
        define('APP_HTAS',          '/appHtas/');
        define('APP_INTRANET',      '/appIntranet/');
        define('APP_MUNI',          '/appMuni/');
        define('APP_SALUD',         '/appSalud/');
        define('APP_UTILIDADES',    '/appUtilidades/');        

        // :::  Diseño
        define('ICONO_NAVEGADOR',	'icoDC.png');
        define('LOGO',              'icoDC.png'); 
		define('LOGOH',             'icoDC.png'); 
		define('PATH_ICO',			'/images/'); 
        define('TITULO_NAVEGADOR',  'dCode Desarrollo');

		define('VERSION_SIS' ,      '5.001/2024');	
    }
     
    // :: PAGINACION
    if (!defined('NRO_PAGINAS')){ define('NRO_PAGINAS', '10'); }  // Numero de paginas mostradas en indice inferior
    if (!defined('PAGINACION')) { define('PAGINACION',  '15'); }  // Numero de filas mostradas por pagina grilla
    if (!defined('REGSINPAGINACION')) { define('REGSINPAGINACION',  '120'); }  // Numero maximo de registros leidos sin paginacion
	

    // :: array meses
	$meses = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
    $dias  = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");

    // foreach ($variablesCli as $key => $value) { echo "$key => $value<br>"; }
?>