<?php
// api/send-investor.php
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
$name = strip_tags(field("name"));
$email = filter_var(field("email"), FILTER_SANITIZE_EMAIL);
$firm_name = strip_tags(field("firm_name"));
$thesis = strip_tags(field("thesis"));
$geography = strip_tags(field("geography"));
$ticket_size = strip_tags(field("ticket_size"));

// 3. Validación básica
if ($name === "" || $email === "") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// 4. Configuración del email
$to = "sebastian@eusa-partners.com"; 
$subject = "New Investor Access Request: $name";

// 5. Cuerpo del correo
$body = "New Investor Network Request from the EUSA website:\n\n"
      . "Name: $name\n"
      . "Email: $email\n"
      . "Firm/Angel Name: $firm_name\n"
      . "Investment Thesis: $thesis\n"
      . "Geography: $geography\n"
      . "Ticket Size: $ticket_size\n";

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