<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

$datos = json_decode(file_get_contents("php://input"), true);
$tema = $datos['tema'] ?? 'Álgebra';
$dificultad = $datos['dificultad'] ?? 'Intermedio';


$api_key = 'AQ.Ab8RN6J8BvmnbeX26qCFEJZ_TRRSsyfNpRCTijK98TMmiSPopA'; 
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;
$prompt = "Actúa como un profesor de matemáticas. Genera un problema de $tema nivel $dificultad de selección múltiple. Devuelve ESTRICTAMENTE un formato JSON puro con esta estructura exacta: { \"enunciado\": \"El enunciado del problema matemático\", \"opcion_a\": \"Texto de la alternativa A\", \"opcion_b\": \"Texto de la alternativa B\", \"opcion_c\": \"Texto de la alternativa C\", \"opcion_d\": \"Texto de la alternativa D\", \"respuesta_correcta\": \"A\", \"B\", \"C\" o \"D\" }";

$datosParaGoogle = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ],
  
    "generationConfig" => [
        "responseMimeType" => "application/json"
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosParaGoogle));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$respuesta = curl_exec($ch);

if(curl_errno($ch)){
    echo json_encode([
        "enunciado" => "Error de red en XAMPP: " . curl_error($ch),
        "opcion_a" => "-", "opcion_b" => "-", "opcion_c" => "-", "opcion_d" => "-"
    ]);
    curl_close($ch);
    exit;
}
curl_close($ch);

$datos_google = json_decode($respuesta, true);

if (isset($datos_google['error'])) {
    echo json_encode([
        "enunciado" => "Error de API Google: " . $datos_google['error']['message'],
        "opcion_a" => "-", "opcion_b" => "-", "opcion_c" => "-", "opcion_d" => "-"
    ]);
    exit;
}

$textoIA = $datos_google['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
echo trim($textoIA);
?>