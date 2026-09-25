<?php 		
    $db_map = [
        'C' => S_DATABASE_COMUN,
        'D' => S_DATABASE_COMUN_X,
        'F' => S_DATABASE_FINANZAS_X
    ];

    $S_TDS_VERSION = S_TDS_VERSION;

    $db_key = $DB_Sifim ?? 'C'; 
    $S_DATABASE = $db_map[$db_key] ?? S_DATABASE_COMUN;

    // $tdsVersion = S_SERVER === "192.168.4.11" ? "7.0" : "7.4"; 
    $dsn = "dblib:host=".S_SERVER.":".S_PORT.";dbname=$S_DATABASE;charset=UTF-8;version=$S_TDS_VERSION";

	try {
		$pdo = new PDO($dsn, S_USER, S_PASSWORD, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		]);
	} catch (PDOException $e) { 
		echo $e->getMessage(); 
	}
?>