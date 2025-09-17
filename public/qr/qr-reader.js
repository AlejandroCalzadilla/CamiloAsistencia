// Función para leer QR desde imagen usando jsQR
function leerQRDesdeImagen(file, callback) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    img.onload = function() {
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            callback(code.data, null);
        } else {
            callback(null, 'No se pudo leer el código QR de la imagen');
        }
    };
    
    img.onerror = function() {
        callback(null, 'Error al cargar la imagen');
    };
    
    const reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Función para procesar QR y registrar asistencia
function procesarQRConJavaScript(claseId, input) {
    const file = input.files[0];
    if (!file) {
        alert('Por favor selecciona una imagen');
        return;
    }
    
    const uploadBtn = document.querySelector(`button[onclick*="procesarQRConJavaScript(${claseId}"]`);
    const originalText = uploadBtn.textContent;
    uploadBtn.textContent = 'Procesando...';
    uploadBtn.disabled = true;
    
    leerQRDesdeImagen(file, function(qrData, error) {
        if (error) {
            alert('Error: ' + error);
            uploadBtn.textContent = originalText;
            uploadBtn.disabled = false;
            return;
        }
        
        // Mostrar QR leído
        console.log('QR leído:', qrData);
        
        // Registrar asistencia con AJAX
        const formData = new FormData();
        formData.append('clase_id', claseId);
        formData.append('qr_texto', qrData);
        formData.append('procesarQR', '1');
        
        fetch('procesar-qr-simple.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('¡Asistencia registrada correctamente!');
                location.reload(); // Recargar para ver cambios
            } else {
                alert('Error: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        })
        .finally(() => {
            uploadBtn.textContent = originalText;
            uploadBtn.disabled = false;
        });
    });
}

// Función para previsualizar imagen
function previsualizarImagen(input, claseId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview_' + claseId);
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'preview_' + claseId;
                preview.className = 'preview-image';
                preview.style.maxWidth = '200px';
                preview.style.maxHeight = '200px';
                preview.style.margin = '10px 0';
                preview.style.border = '1px solid #ddd';
                preview.style.borderRadius = '4px';
                input.parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Función para registrar asistencia con texto QR
function registrarAsistencia(claseId, qrTexto) {
    if (!qrTexto.trim()) {
        alert('Por favor ingresa el código QR');
        return;
    }
    
    const formData = new FormData();
    formData.append('clase_id', claseId);
    formData.append('qr_texto', qrTexto);
    formData.append('procesarQR', '1');
    
    fetch('procesar-qr-simple.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('¡Asistencia registrada correctamente!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}
