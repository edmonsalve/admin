<?php 
    $usuario_LabsMobile = "emonsalve@dcode.cl"; // USER_LABS_MOBIL;  
    $token_LabsMobile   = "9bYKot1kNRIZCd9ZHSVSe4RvNBoSwGLu"; // TOKEN_LABS_MOBIL;
    $nroTelefono        = "56" . $SMStelefono; // $SMSprefijio 

    // Enviar SMS usando LabsMobile
    $auth_basic = base64_encode("$usuario_LabsMobile:$token_LabsMobile");
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.labsmobile.com/json/send",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => '{
        "message":"' . $SMSmensaje . '",
        "tpoa":"Municipalidad de Petorca",
        "recipient":
          [
            {
              "msisdn":"' . $nroTelefono . '"
            }
          ]
      }',
      CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".$auth_basic,
        "Cache-Control: no-cache",
        "Content-Type: application/json"
      ),
    ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
   echo "cURL Error #:" . $err;
  } else {
   echo $response;
  }
?>