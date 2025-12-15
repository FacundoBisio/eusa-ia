<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

function clean($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$name = clean($_POST['name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$firm = clean($_POST['firm_name'] ?? '');
$thesis = clean($_POST['thesis'] ?? '');
$geo = clean($_POST['geography'] ?? '');
$ticket = clean($_POST['ticket_size'] ?? '');

if (empty($name) || empty($email)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$to = "sebastian@eusa-partners.com"; // CAMBIA ESTO
$subject = "New Investor Access Request: $name";

$message = "New Investor Network Request:\n\n";
$message .= "Name: $name\n";
$message .= "Email: $email\n";
$message .= "Firm/Angel: $firm\n";
$message .= "Thesis: $thesis\n";
$message .= "Geography: $geo\n";
$message .= "Ticket Size: $ticket\n";

$headers = "From: EUSA Website <noreply@eusa-partners.com>\r\n";
$headers .= "Reply-To: $email\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo json_encode(["status" => "ok"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server mail error"]);
}
?>