<?php
function preguntaChatgpt($system, $pregunta, $telefonoCliente,$listaCategorias){
    require "config.php";
    $config = new Config();
    $apiKey = $config->apiKeyChatgpt;
    $telefono = str_replace("521", "52", $telefonoCliente);
    //API KEY DE CHATGPT
    global $conn;
    // Consulta SQL para obtener las conversaciones anteriores
    $query = "SELECT mensaje_recibido, mensaje_enviado 
    FROM kimai2_registro 
    WHERE telefono_wa = ? 
    AND fecha_hora > DATE_SUB(NOW(), INTERVAL 2 HOUR)
    -- AND cita_creada = 0
    ORDER BY fecha_hora ASC";
    $stmtMensajes = $conn->prepare($query);
    // Vincula el parámetro "telefono" a la consulta SQL
    $stmtMensajes->bind_param('s', $telefono);
    // Ejecuta la consulta
    $stmtMensajes->execute();
    // Obtiene el resultado
    $result = $stmtMensajes->get_result();
    // Construye el array de mensajes anteriores
    $messages = array();
    while ($row = $result->fetch_assoc()) {
        $messages[] = array("role" => "user", "content" => $row['mensaje_recibido']);
        $messages[] = array("role" => "assistant", "content" => $row['mensaje_enviado']);
    }
    // Agrega el mensaje del sistema y la pregunta actual del usuario
    array_unshift($messages, array("role" => "system", "content" => $system));
    $messages[] = array("role" => "user", "content" => $pregunta);
    //INICIAMOS LA CONSULTA DE CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$apiKey,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 
    json_encode(array(
        "model" => "gpt-3.5-turbo",
        "messages" => $messages,
        "temperature" => 0
    )));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    // Obtiene información de los encabezados de la respuesta
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    curl_close($ch);
    // Decodificar el JSON
    $data = json_decode($header, true);
    // Obtener el valor de 'total_tokens'
    $totalTokens = $data['usage']['total_tokens'];
    curl_close($ch);
    $decoded_json = json_decode($response, false);
    //RETORNAMOS LA RESPUESTA QUE EXTRAEMOS DEL JSON
    $respuesta=$decoded_json->choices[0]->message->content;
    $respuesta = str_replace('"', '-', $respuesta);
    $respuesta = str_replace('\"', '-', $respuesta);
    $respuesta = str_replace('\\"', '-', $respuesta);
    $respuesta=stripcslashes($respuesta);
    if (strpos($respuesta, '||') !== false) {
        $respuestaDividida = explode('||', $respuesta);
        // Ahora, cada elemento del array $respuestaDividida contiene una parte de la respuesta
        $cliente = trim($respuestaDividida[1]);
        $cliente=$cliente." ".$telefono;
        $edad = trim($respuestaDividida[2]);
        $especialidad = trim($respuestaDividida[3]);
        $especialidad=quitar_acentos($especialidad);
        $listaCategoriasArray = explode(",", $listaCategorias);
        // quitar acentos
        foreach($listaCategoriasArray as $categoria){
            if(strpos($especialidad, $categoria) !== false){
                $especialidad = $categoria;
                break;
            }
        }
        //Si $especialidad esta vacio poner "Diagnostico"
        if($especialidad==""){
            $especialidad="Medico general";
        }
        $sintomas = trim($respuestaDividida[4]);
        require_once('crearCita.php');
        //$fechaCita=creaCita($cliente,$telefono, $edad, $especialidad, $sintomas);
        //actualizarCitaCreada($telefono);
        global $textoCita;
        //a $fechaCita darle formato dia mes año hora y minuto
        
        $fechaCita = strtotime($fechaCita);  // Convierte la fecha en un timestamp de Unix
        setlocale(LC_TIME, 'es_ES.UTF-8');
        $fechaFormateada = strftime("%A %d de %B del %Y a las %H:%M", $fechaCita);
        $fechaFormateada=translateDayInText($fechaFormateada);
        $respuesta = $textoCita." ".$fechaFormateada.", en el transcurso del dia un experto en '".$especialidad."' se pondrá en contacto con usted para confirmar la cita y darle seguimiento.";
        //$respuesta
    }

    //obtener la fecha y hora en string
    $fecha = date("Y-m-d H:i:s");
    file_put_contents('responseChatgpt.txt', $fecha."-".$response."\n".$respuesta);
    return $respuesta;
           
}
function quitar_acentos($cadena){
    $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby';
    $cadena = utf8_decode($cadena);
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    return utf8_encode($cadena);
}
function translateDayInText($text) {
    // Definir la matriz de traducción
    $translationArray = array(
        "Monday" => "Lunes",
        "Tuesday" => "Martes",
        "Wednesday" => "Miércoles",
        "Thursday" => "Jueves",
        "Friday" => "Viernes",
        "Saturday" => "Sábado",
        "Sunday" => "Domingo"
    );
    
    // Dividir el texto en palabras
    $words = explode(" ", $text);

    // Verificar cada palabra
    foreach ($words as $key => $word) {
        // Si la palabra es un día de la semana en inglés, traducirlo
        if (isset($translationArray[$word])) {
            $words[$key] = $translationArray[$word];
        }
    }
    
    // Unir las palabras de nuevo en un texto
    $text = implode(" ", $words);
    
    return $text;
}