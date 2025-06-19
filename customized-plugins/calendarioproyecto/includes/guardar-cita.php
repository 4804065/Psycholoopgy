<?php
$host     = 'localhost';
$dbname   = 'db';
$user     = 'user';
$password = 'password';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexi�n a la base de datos']);
    exit;
}

$id    = isset($_POST['idcita'])    ? $conn->real_escape_string($_POST['idcita'])    : '';
$fecha    = isset($_POST['fecha'])    ? $conn->real_escape_string($_POST['fecha'])    : '';
$hora     = isset($_POST['hora'])     ? $conn->real_escape_string($_POST['hora'])     : '';
$nombre   = isset($_POST['nombre'])   ? $conn->real_escape_string($_POST['nombre'])   : '';
$email    = isset($_POST['email'])    ? $conn->real_escape_string($_POST['email'])    : '';
$telefono = isset($_POST['telefono']) ? $conn->real_escape_string($_POST['telefono']) : '';
$amb = isset($_POST['amb']) ? $conn->real_escape_string($_POST['amb']) : '';

if((empty($fecha) || empty($hora) || empty($nombre) || empty($email) || empty($telefono)) && $amb !='delete') {
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
    exit;
}


if ($amb == 'insert'){
$sql = "INSERT INTO wp_citas (fecha_cita, hora_cita, nombre_cliente, email_cliente, tel_cliente, creado)
        VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('sssss', $fecha, $hora, $nombre, $email, $telefono);
    $result = $stmt->execute();
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar la cita']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la preparaci�n de la consulta']);
}
}


if ($amb == 'update'){
$sql = "UPDATE wp_citas 
        SET fecha_cita = ?, hora_cita = ?, nombre_cliente = ?, email_cliente = ?, tel_cliente = ?
        WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('sssssi', $fecha, $hora, $nombre, $email, $telefono, $id);
    $result = $stmt->execute();
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo actualizar la cita']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la preparaci�n de la consulta']);
}
}

if ($amb == 'delete'){
$sql = "DELETE FROM  wp_citas WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la cita']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la preparaci�n de la consulta']);
}
}

$conn->close();
?>
