<?php
$host     = 'localhost';
$dbname   = 'db';
$user     = 'user';
$password = 'password';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexi�n a la base de datos"]);
    exit;
}

if (!isset($_POST['fecha'])) {
    echo json_encode([]);
    exit;
}

$fecha = $_POST['fecha'];

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT hora_cita FROM wp_citas WHERE fecha_cita = ?");
$stmt->bind_param("s", $fecha);
$stmt->execute();
$result = $stmt->get_result();

$horas = [];
while ($row = $result->fetch_assoc()) {
    $horas[] = substr($row['hora_cita'],0,5);
}

header('Content-Type: application/json');
echo json_encode($horas);
exit;
?>
