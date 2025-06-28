<?php
session_start();

// Determinar ID del evento (puede recibirse por POST o GET)
$evento_id = isset($_POST['evento']) ? intval($_POST['evento']) : (isset($_GET['evento']) ? intval($_GET['evento']) : 1);

// Guardar los datos recibidos para repoblar el formulario en caso de error
$_SESSION['form_data'] = $_POST;

// Validar campos requeridos
$required = ['documento', 'nombres', 'genero', 'edad', 'correo', 'firma'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['message'] = 'Todos los campos obligatorios deben ser diligenciados.';
        $_SESSION['message_type'] = 'danger';
        header('Location: form.php?evento=' . $evento_id);
        exit;
    }
}

$documento = trim($_POST['documento']);
$nombres = trim($_POST['nombres']);
$empresa = trim($_POST['empresa'] ?? '');
$cargo = trim($_POST['cargo'] ?? '');
$genero = trim($_POST['genero']);
$edad = trim($_POST['edad']);
$enfoques = isset($_POST['enfoques']) ? implode(', ', $_POST['enfoques']) : '';
$comuna = trim($_POST['comuna'] ?? '');
$organizacion = trim($_POST['organizacion'] ?? '');
$correo = trim($_POST['correo']);
$telefono = trim($_POST['telefono'] ?? '');
$firmaData = $_POST['firma'];

try {
    // Conectar a base de datos MySQL
    $db = new PDO('mysql:host=localhost;dbname=formulario1;charset=utf8mb4', 'formuser', 'formpassword');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Verificar si ya existe un registro para este evento y documento
    $stmt = $db->prepare('SELECT id FROM formulario1 WHERE evento_id = ? AND documento = ?');
    $stmt->execute([$evento_id, $documento]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = 'Usted ya firmo la asistencia para este evento';
        $_SESSION['message_type'] = 'warning';
        header('Location: form.php?evento=' . $evento_id);
        exit;
    }

    // Guardar la firma como archivo PNG
    $firmaData = preg_replace('#^data:image/\w+;base64,#', '', $firmaData);
    $firmaData = str_replace(' ', '+', $firmaData);
    $firmaBinary = base64_decode($firmaData);
    if ($firmaBinary === false) {
        throw new Exception('Firma no válida');
    }

    if (!is_dir('firmas')) {
        mkdir('firmas');
    }
    $firmaArchivo = 'firmas/firma_' . $documento . '_' . $evento_id . '_' . time() . '.png';
    file_put_contents($firmaArchivo, $firmaBinary);

    // Insertar datos en la base de datos
    $insert = $db->prepare('INSERT INTO formulario1 (evento_id, documento, nombres, empresa, cargo, genero, edad, enfoques, comuna, organizacion, correo, telefono, firma_archivo, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())');
    $insert->execute([$evento_id, $documento, $nombres, $empresa, $cargo, $genero, $edad, $enfoques, $comuna, $organizacion, $correo, $telefono, $firmaArchivo]);

    $_SESSION['message'] = 'Registro guardado correctamente';
    $_SESSION['message_type'] = 'success';
    unset($_SESSION['form_data']);
    header('Location: form.php?evento=' . $evento_id);
    exit;
} catch (Exception $e) {
    $_SESSION['message'] = 'Error al procesar el formulario: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
    header('Location: form.php?evento=' . $evento_id);
    exit;
}
