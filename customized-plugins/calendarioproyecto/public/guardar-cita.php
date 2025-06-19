<?php
$host     = 'localhost';
$dbname   = 'db';
$user     = 'user';
$password = 'password';

header('Content-Type: application/json');

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success"=>false, "error"=>"Error de conexi�n a la base de datos"]);
    exit;
}

$fecha = trim($_POST['fecha'] ?? '');
$hora = trim($_POST['hora'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if (!$fecha || !$hora || !$nombre || !$email || !$telefono) {
    echo json_encode(["success"=>false, "error"=>"Todos los campos son obligatorios"]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success"=>false, "error"=>"Email inv�lido"]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM wp_citas WHERE fecha_cita = ? AND hora_cita = ?");
$stmt->bind_param("ss", $fecha, $hora);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res['total'] > 0) {
    echo json_encode(["success"=>false, "error"=>"La hora seleccionada ya est� ocupada"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO wp_citas (fecha_cita, hora_cita, nombre_cliente, email_cliente, tel_cliente, creado) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("sssss", $fecha, $hora, $nombre, $email, $telefono);
if ($stmt->execute()) {
    echo json_encode(["success"=>true]);
} else {
    echo json_encode(["success"=>false, "error"=>"Error al guardar la cita"]);
}
$conn->close();
exit;
?>
