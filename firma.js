// JS/firma.js

document.addEventListener("DOMContentLoaded", function () {
  // --- Mueve toda la lógica aquí dentro ---
  const canvas = document.getElementById("signatureCanvas");
  if (!canvas) {
    console.error(
      "¡Error! No se encontró el elemento canvas con id 'signatureCanvas'"
    );
    return; // Detiene la ejecución si no encuentra el canvas
  }
  const signaturePad = new SignaturePad(canvas);

  const firmaInput = document.getElementById("firmaInput");
  if (!firmaInput) {
    console.error("¡Error! No se encontró el input oculto con id 'firmaInput'");
    return; // Detiene la ejecución si no encuentra el input
  }

  const form = document.getElementById("registroForm");
  if (!form) {
    console.error("¡Error! No se encontró el formulario con id 'registroForm'");
    return; // Detiene la ejecución si no encuentra el form
  }

  // Función para ajustar el canvas
  function resizeCanvas() {
    const container = canvas.parentElement;
    const ratio = Math.max(window.devicePixelRatio || 1, 1);

    // Guardar el contenido antes de redimensionar si ya hay algo dibujado
    let data = null;
    if (!signaturePad.isEmpty()) {
      data = signaturePad.toDataURL();
    }

    canvas.width = container.offsetWidth * ratio;
    // Asegurar una altura mínima si el contenedor no tiene altura definida por defecto
    canvas.height = (container.offsetHeight || 150) * ratio;
    canvas.getContext("2d").scale(ratio, ratio);

    // Restaurar el contenido después de redimensionar
    if (data) {
      signaturePad.fromDataURL(data);
    } else {
      signaturePad.clear(); // Asegura que esté limpio si no había nada
    }
  }

  // Eventos de redimensionamiento
  window.addEventListener("resize", resizeCanvas);
  resizeCanvas(); // Llama una vez al inicio

  // Validación y asignación de firma al enviar
  form.addEventListener("submit", function (e) {
    console.log("Submit event capturado"); // Para depuración
    if (signaturePad.isEmpty()) {
      alert("Por favor realice su firma");
      console.log("Firma vacía, envío prevenido."); // Para depuración
      e.preventDefault(); // Detiene el envío del formulario
    } else {
      const dataURL = signaturePad.toDataURL(); // 'image/png' es el default
      console.log(
        "Firma NO vacía. Data URL generada:",
        dataURL.substring(0, 50) + "..."
      ); // Muestra el inicio para depurar
      firmaInput.value = dataURL;
      console.log(
        "Valor asignado a firmaInput:",
        firmaInput.value.substring(0, 50) + "..."
      ); // Verifica la asignación
    }
  });

  // Limpiar firma (asumiendo que tienes un botón con onclick="clearSignature()")
  window.clearSignature = function () {
    // Hace la función globalmente accesible para el onclick
    console.log("Limpiando firma"); // Para depuración
    signaturePad.clear();
  };

  // Inicialización táctil (ya estaba bien)
  canvas.addEventListener(
    "touchstart",
    (e) => {
      e.preventDefault();
    },
    {
      passive: false,
    }
  );
  // --- Fin de la lógica movida ---
}); // Fin del DOMContentLoaded
