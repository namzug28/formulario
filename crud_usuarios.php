<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "formulario1";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["crear"])) {
        $documento = $_POST["documento"];
        $nombre = $_POST["nombre"];
        $empresa = $_POST["empresa"];
        $cargo = $_POST["cargo"];
        $genero = $_POST["genero"];
        $edad = $_POST["edad"];
        $enfoques = $_POST["enfoques"];
        $comuna = $_POST["comuna"];
        $organizacion = $_POST["organizacion"];
        $correo = $_POST["correo"];
        $telefono = $_POST["telefono"];
        $firma = $_POST["firma"];
        $campos_obligatorios = ['documento', 'nombre', 'genero', 'edad', 'correo', 'firma'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($_POST[$campo])) {
                die("El campo '$campo' es obligatorio.");
            }
        }

        $sql = "INSERT INTO usuarios (documento, nombre, empresa, cargo, genero, edad, enfoques, comuna, organizacion, correo, telefono, firma) 
                VALUES ('$documento', '$nombre', '$empresa', '$cargo', '$genero', '$edad', '$enfoques', '$comuna', '$organizacion', '$correo', '$telefono', '$firma')";
// Guardar la firma como archivo PNG
    $firma = preg_replace('#^data:image/\w+;base64,#', '', $firma);
    $firma = str_replace(' ', '+', $firma);
    $firmaBinary = base64_decode($firma);
    if ($firmaBinary === false) {
        throw new Exception('Firma no válida');
    }

    if (!is_dir('firmas')) {
        mkdir('firmas');
    }
    $firmaArchivo = 'firmas/firma_' . $documento . '_' . $evento_id . '_' . time() . '.png';
    file_put_contents($firmaArchivo, $firmaBinary);
        if ($conn->query($sql) === TRUE) {
            echo "Nuevo registro creado con éxito";
            
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST["actualizar"])) {
        $id = $_POST["id"];
        $documento = $_POST["documento"];
        $nombre = $_POST["nombre"];
        $empresa = $_POST["empresa"];
        $cargo = $_POST["cargo"];
        $genero = $_POST["genero"];
        $edad = $_POST["edad"];
        $enfoques = $_POST["enfoques"];
        $comuna = $_POST["comuna"];
        $organizacion = $_POST["organizacion"];
        $correo = $_POST["correo"];
        $telefono = $_POST["telefono"];
        $firma = $_POST["firma"];

        $sql = "UPDATE usuarios SET documento='$documento', nombre='$nombre', empresa='$empresa', cargo='$cargo', genero='$genero', edad='$edad', enfoques='$enfoques', comuna='$comuna', organizacion='$organizacion', correo='$correo', telefono='$telefono', firma='$firma' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo "Registro actualizado con éxito";
        } else {
            echo "Error actualizando registro: " . $conn->error;
        }
    }
} elseif (isset($_GET["borrar"])) {
    $id = $_GET["borrar"];
    $sql = "DELETE FROM usuarios WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Registro borrado con éxito";
    } else {
        echo "Error borrando registro: " . $conn->error;
    }
}

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Usuarios Extendido</title>
    <style>
        canvas#signatureCanvas {
            width: 300px;
            height: 150px;
            border: 1px solid black;
            display: block;
        }
    </style>
</head>
<body>
    <h1>Crear Nuevo Usuario</h1>
    <form id="registroForm" method="post" action="crud_usuarios.php">
        Documento: <input type="text" name="documento" required><br><br>
        Nombre: <input type="text" name="nombre" required><br><br>
        Empresa: <input type="text" name="empresa"><br><br>
        Cargo: <input type="text" name="cargo"><br><br>
        Género: <input type="text" name="genero" required><br><br>
        Edad: <input type="number" name="edad" required><br><br>
        Enfoques: <input type="text" name="enfoques"><br><br>
        Comuna: <input type="text" name="comuna"><br><br>
        Organización: <input type="text" name="organizacion"><br><br>
        Correo: <input type="email" name="correo" required><br><br>
        Teléfono: <input type="text" name="telefono"><br><br>
        
        <label>Firma:</label><br>
        <canvas id="signatureCanvas" style="border: 1px solid black;"></canvas>
        <input type="hidden" name="firma" id="firmaInput" required>
        <button type="button" onclick="clearSignature()">Limpiar Firma</button><br><br>
    
        <input type="submit" name="crear" value="Crear">
</form>

    <h2>Lista de Usuarios</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Documento</th>
            <th>Nombre</th>
            <th>Empresa</th>
            <th>Cargo</th>
            <th>Género</th>
            <th>Edad</th>
            <th>Enfoques</th>
            <th>Comuna</th>
            <th>Organización</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Firma</th>
            <th>Acciones</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $dato) {
                    echo "<td>" . htmlspecialchars($dato) . "</td>";
                }
                echo "<td><a href='crud_usuarios.php?borrar=" . $row["id"] . "'>Borrar</a> | <a href='crud_usuarios.php?editar=" . $row["id"] . "'>Editar</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='14'>0 resultados</td></tr>";
        }
        ?>
    </table>
    <!-- Librería SignaturePad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <!-- Tu script personalizado -->
    <script src="firma.js" defer></script>
</body>
</html>

<?php $conn->close(); ?>