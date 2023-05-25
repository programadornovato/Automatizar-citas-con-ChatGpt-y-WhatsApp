<?php
/*
 * VERIFICACION DEL WEBHOOK
*/
//TOQUEN QUE QUERRAMOS PONER 
$token = 'HolaNovato';
//RETO QUE RECIBIREMOS DE FACEBOOK
$palabraReto = $_GET['hub_challenge'];
//TOQUEN DE VERIFICACION QUE RECIBIREMOS DE FACEBOOK
$tokenVerificacion = $_GET['hub_verify_token'];
//SI EL TOKEN QUE GENERAMOS ES EL MISMO QUE NOS ENVIA FACEBOOK RETORNAMOS EL RETO PARA VALIDAR QUE SOMOS NOSOTROS
if ($token === $tokenVerificacion) {
    echo $palabraReto;
    exit;
}
$textoCita="He creado su cita para el dia";
/*
 * RECEPCION DE MENSAJES
 */
//LEEMOS LOS DATOS ENVIADOS POR WHATSAPP
$respuesta = file_get_contents("php://input");
//CONVERTIMOS EL JSON EN ARRAY DE PHP
$respuesta = json_decode($respuesta, true);
//EXTRAEMOS EL MENSAJE DEL ARRAY
$mensaje = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
//EXTRAEMOS EL TELEFONO DEL ARRAY
$telefonoCliente = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['from'];
//EXTRAEMOS EL ID DE WHATSAPP DEL ARRAY
$id = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['id'];
//EXTRAEMOS EL TIEMPO DE WHATSAPP DEL ARRAY
$timestamp = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];
//SI HAY UN MENSAJE
if ($mensaje != null) {
    $pregunta = $mensaje;
    require_once("conexion.php");
    // Prepara la consulta SQL
    $queryCompania = 'SELECT company, address, contact FROM kimai2_invoice_templates WHERE id = ?';
    $stmtCompania = $conn->prepare($queryCompania);
    // Vincula el parámetro "id" a la consulta SQL
    $id_inv = 1;
    $stmtCompania->bind_param('i', $id_inv);
    // Ejecuta la consulta
    $stmtCompania->execute();
    // Obtiene el resultado
    $resultCompania = $stmtCompania->get_result();
    // Obtiene los datos del registro
    $rowCompania = $resultCompania->fetch_assoc();
    // Prepara la consulta SQL
    // Crea la consulta SQL
    $queryCategoria = "SELECT alias FROM kimai2_users WHERE enabled = 1 AND id != 1";
    // Prepara la consulta
    $stmtCategoria = $conn->prepare($queryCategoria);
    // Ejecuta la consulta
    $stmtCategoria->execute();
    // Obtiene el resultado
    $resultCategoria = $stmtCategoria->get_result();
    // Crea un array para guardar los alias
    $listaCategorias = "";
    // Recorre los resultados y añade cada alias al array
    while ($row = $resultCategoria->fetch_assoc()) {
        $listaCategorias = $listaCategorias.$row['alias'].",";
    }
    $listaCategorias = rtrim($listaCategorias, ",");
    $system="Hola, soy un asesor de Información de la clínica ".$rowCompania['company'].
    ", no proporciono citas pero genero reportes para que posteriormente un experto agende una cita, no doy recomendaciones para curar, recomiendo solo mi clínica y pido un dato a la vez.".
    " Mis tareas incluyen recibir información, escribir reportes y no hay necesidad de que el paciente lo sepa. Aquí está el procedimiento que sigo: ".
    "1. Primero, pido el nombre. ".
    "2. Luego, pido la edad. ".
    "3. Después, pregunto qué sintomas tiene y en base a las respuestas defino en cuál de estas especialidades cae: ".
    $listaCategorias.".".
    "Si no cae en ninguna especialidad colocarlo en Medico general.".
    "Una vez recolectada la información sobre los síntomas, no programo ninguna cita. En cambio, escribo un reporte con este formato: ".
    "||paciente||etapa_edad||especialidad||sintomas|| (reemplaza el contenido entre || por la información recolectada).".
    //"||cliente||datos_auto||categoria_falla||descripcion_falla|| (reemplaza el contenido entre || por la información recolectada).".
    "Es vital que siempre que detecte sintomas, escriba el reporte en el formato mencionado para que un experto agende una cita. ".
    "Si el paciente me pide una cita le debo escribir el reporte con el formato mencionado. ".
    "Importante: No invento ni añado ninguna información que no se me haya proporcionado. Todo mi trabajo se basa en los datos que recibo.";
    "Solo si me lo piden doy informacion de la empresa, Nombre de la empresa: " . $rowCompania['company'] . ", Ubicación: " . $rowCompania['address'] . " " . $rowCompania['contact'] . ". ";

    require_once "chatgpt.php";
    $respuesta = preguntaChatgpt($system, $pregunta, $telefonoCliente,$listaCategorias);
    //ESCRIBIMOS LA RESPUESTA
    file_put_contents("respuesta.txt", $respuesta);
    require_once "whatsapp.php";
    //ENVIAMOS LA RESPUESTA VIA WHATSAPP
    enviar($mensaje, $respuesta, $id, $timestamp, $telefonoCliente);

}
