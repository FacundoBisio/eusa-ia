<?php
header('Content-Type: application/json');

// Evitar acceso directo
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

// Función helper para limpiar datos
function clean($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Recibir datos
$name = clean($_POST['name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$startup = clean($_POST['startup_name'] ?? '');
$stage = clean($_POST['stage'] ?? '');
$raise = clean($_POST['target_raise'] ?? '');
$blocker = clean($_POST['blocker'] ?? '');

// Validación simple
if (empty($name) || empty($email) || empty($startup)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Please fill required fields"]);
    exit;
}

// Configuración del correo
$to = "sebastian@eusa-partners.com"; // CAMBIA ESTO POR TU EMAIL REAL SI HACE FALTA
$subject = "New Founder Application: $startup";

$message = "New Founder Application:\n\n";
$message .= "Name: $name\n";
$message .= "Email: $email\n";
$message .= "Startup: $startup\n";
$message .= "Stage: $stage\n";
$message .= "Target Raise: $raise\n";
$message .= "Blocker: $blocker\n";

$headers = "From: EUSA Website <noreply@eusa-partners.com>\r\n";
$headers .= "Reply-To: $email\r\n";

// Enviar
if (mail($to, $subject, $message, $headers)) {
    echo json_encode(["status" => "ok"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server mail error"]);
}
?>