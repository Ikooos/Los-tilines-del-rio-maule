<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$datos = json_decode(file_get_contents("php://input"), true);
$enunciado = $datos['enunciado'] ?? '';
$respuesta_usuario = $datos['respuesta_usuario'] ?? '';
$respuesta_correcta = $datos['respuesta_correcta'] ?? '';

$es_correcto = ($respuesta_usuario === $respuesta_correcta);


$api_key = 'AQ.Ab8RN6J8BvmnbeX26qCFEJZ_TRRSsyfNpRCTijK98TMmiSPopA'; 
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;
$prompt = "Un alumno resolvió este problema: '$enunciado'. Él eligió la alternativa $respuesta_usuario. La correcta era la $respuesta_correcta. Escribe un feedback de máximo 2 líneas. Si está correcto, felicítalo como un profe chileno amigable. Si se equivocó, anímalo y dile brevemente por qué la correcta era $respuesta_correcta. Responde solo con el texto del mensaje, nada más.";

$datosParaGoogle = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosParaGoogle));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$respuesta = curl_exec($ch);
curl_close($ch);

$datos_google = json_decode($respuesta, true);


if (isset($datos_google['error'])) {
    $mensajeIA = "Error de API Google: " . $datos_google['error']['message'];
} else {
    $mensajeIA = $datos_google['candidates'][0]['content']['parts'][0]['text'] ?? 'Hubo un error evaluando, pero ¡sigue practicando!';
}

echo json_encode([
    "correcto" => $es_correcto,
    "mensaje" => trim($mensajeIA)
]);
?>