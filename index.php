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
    file_put_contents("mensaje.txt", $pregunta);
}
