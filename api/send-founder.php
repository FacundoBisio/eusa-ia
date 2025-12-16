<?php
// api/send-founder.php
// Iniciar buffer de salida para evitar que warnings/echo ensucien el JSON
ob_start();

// --- CONFIGURACIÓN DE LIMPIEZA ---
error_reporting(0);
ini_set('display_errors', 0);

// Headers cruciales para evitar errores de CORS y procesar bien la respuesta
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// 1. Verificación de método
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

// Función helper
function field($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : "";
}

// 2. Capturamos los campos
$name         = strip_tags(field("name"));
$email        = filter_var(field("email"), FILTER_SANITIZE_EMAIL);
$startup_name = strip_tags(field("startup_name"));
$stage        = strip_tags(field("stage"));
$target_raise = strip_tags(field("target_raise"));
$blocker      = strip_tags(field("blocker"));

// 3. Validación básica
if ($name === "" || $email === "" || $startup_name === "") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// 4. Configuración del email
$to      = "sebastian@eusa-partners.com"; 
$subject = "New Founder Application: $startup_name";

// 5. Cuerpo del correo
$body = "New application from the EUSA website (FOUNDER):\n\n"
      . "Name: $name\n"
      . "Email: $email\n"
      . "Startup Name: $startup_name\n"
      . "Stage: $stage\n"
      . "Target Raise: $target_raise\n"
      . "Biggest Blocker: $blocker\n";

$headers  = "From: EUSA Website <no-reply@eusa-partners.com>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// 6. Enviar
$sent = mail($to, $subject, $body, $headers);

// Limpiar buffer para asegurar que solo salga el JSON
ob_end_clean();

if ($sent) {
    echo json_encode(["status" => "ok"]);
} else {
    // Si falla el mail, enviamos 500 pero con JSON válido
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Mail not sent"]);
}
exit;
?>