<?php
// Iniciar la sesión ANTES de cualquier salida HTML
session_start();

// ID del evento (puede pasarse como parametro en la URL)
$evento_id = $_GET['evento'] ?? 1;

// Recuperar mensajes y datos de la sesión (si existen)
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
$form_data = $_SESSION['form_data'] ?? []; // Datos para repoblar en caso de error

// Limpiar las variables de sesión para que no se muestren de nuevo en recargas
unset($_SESSION['message']);
unset($_SESSION['message_type']);
unset($_SESSION['form_data']);

// Lista de enfoques para generar checkboxes (lógica de presentación)
$enfoquesDisponibles = [
    'Afrodescendiente', 'Indígena', 'ROM', 'LGTBI',
    'Situación de calle', 'Campesino', 'Discapacidad', 'Población Víctima'
];
$enfoquesSeleccionados = $form_data['enfoques'] ?? []; // Usar datos de sesión si hay error

// Lista de edades (lógica de presentación)
$edades = ["0-5", "6-17", "18-28", "29-54", "55+"];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Formulario de Registro</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> mt-3" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form id="registroForm" method="post" action="procesar_formulario.php">
            <input type="hidden" name="evento" value="<?php echo htmlspecialchars($evento_id); ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="doc" class="form-label">Número de documento*</label>
                    <input type="text" id="doc" class="form-control" name="documento" required value="<?php echo isset($form_data['documento']) ? htmlspecialchars($form_data['documento']) : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="nombres" class="form-label">Nombres y apellidos*</label>
                    <input type="text" id="nombres" class="form-control" name="nombres" required value="<?php echo isset($form_data['nombres']) ? htmlspecialchars($form_data['nombres']) : ''; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="empresa" class="form-label">Empresa/Entidad/Dependencia</label>
                    <input type="text" id="empresa" class="form-control" name="empresa" value="<?php echo isset($form_data['empresa']) ? htmlspecialchars($form_data['empresa']) : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="cargo" class="form-label">Cargo/Empleo/Calidad que actúa</label>
                    <input type="text" id="cargo" class="form-control" name="cargo" value="<?php echo isset($form_data['cargo']) ? htmlspecialchars($form_data['cargo']) : ''; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sexo*</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="genero" id="genHombre" value="Hombre" required <?php echo (isset($form_data['genero']) && $form_data['genero'] === 'Hombre') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genHombre">Hombre</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="genero" id="genMujer" value="Mujer" required <?php echo (isset($form_data['genero']) && $form_data['genero'] === 'Mujer') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genMujer">Mujer</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="genero" id="genMujer" value="Mujer" required <?php echo (isset($form_data['genero']) && $form_data['genero'] === 'Mujer') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genMujer">No responde</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="edad" class="form-label">Rango de edad*</label>
                    <select class="form-select" id="edad" name="edad" required>
                        <option value="">Seleccione...</option>
                        <?php
                        foreach ($edades as $rango) {
                            $selected = (isset($form_data['edad']) && $form_data['edad'] === $rango) ? 'selected' : '';
                            echo "<option value=\"$rango\" $selected>$rango" . ($rango === "55+" ? " o más años" : " años") . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Enfoque diferencial (seleccione todos que apliquen)</label>
                <div class="row">
                    <?php
                    foreach ($enfoquesDisponibles as $enfoque) {
                        $idEnfoque = 'enfoque_' . strtolower(str_replace(' ', '_', $enfoque));
                        // Usar $enfoquesSeleccionados que viene de $form_data
                        $checked = in_array($enfoque, $enfoquesSeleccionados) ? 'checked' : '';
                        echo '<div class="col-md-4 col-sm-6 form-check">
                                <input class="form-check-input" type="checkbox" name="enfoques[]" value="' . htmlspecialchars($enfoque) . '" id="' . $idEnfoque . '" ' . $checked . '>
                                <label class="form-check-label" for="' . $idEnfoque . '">' . htmlspecialchars($enfoque) . '</label>
                              </div>';
                    }
                    ?>
                </div>
            </div>

            <div class="row mb-3">
                 <div class="col-md-4">
                    <label for="comuna" class="form-label">Comuna donde reside</label>
                    <input type="text" id="comuna" class="form-control" name="comuna" value="<?php echo isset($form_data['comuna']) ? htmlspecialchars($form_data['comuna']) : ''; ?>">
                </div>
                 <div class="col-md-4">
                    <label for="organizacion" class="form-label">Organización/Grupo (si aplica)</label>
                    <input type="text" id="organizacion" class="form-control" name="organizacion" value="<?php echo isset($form_data['organizacion']) ? htmlspecialchars($form_data['organizacion']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <label for="correo" class="form-label">Correo electrónico*</label>
                    <input type="email" id="correo" class="form-control" name="correo" required value="<?php echo isset($form_data['correo']) ? htmlspecialchars($form_data['correo']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <label for="telefono" class="form-label">Teléfono/Celular</label>
                    <input type="tel" id="telefono" class="form-control" name="telefono" value="<?php echo isset($form_data['telefono']) ? htmlspecialchars($form_data['telefono']) : ''; ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="signatureCanvas">Firma*</label>
                <div class="signature-pad">
                    <canvas id="signatureCanvas" style="width: 100%; height: 100%; touch-action: none;"></canvas>
                </div>
                <button type="button" class="btn btn-secondary clear-button mt-2" onclick="clearSignature()">Limpiar Firma</button>
                <input type="hidden" name="firma" id="firmaInput">
            </div>

            <button type="submit" class="btn btn-send">Enviar Formulario</button>
        </form>
    </div>
    <div class='logo-container'>
        <img src="imgs/escudo-medellin-full-color.png" alt="Logo Alcaldía de Medellín" class="logo">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="firma.js"></script>
</body>
</html>
