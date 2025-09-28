<?php

class AsignacionModel
{

    private $estudiante_codigo;
    private $grupo_id;
    private $fecha_asignacion;
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }



    public function crear($estudiante_codigo, $grupo_id)
    {

        if ($this->existeAsignacion($estudiante_codigo, $grupo_id)) {
            return [
                'success' => false,
                'message' => 'El estudiante ya está asignado en este grupo'
            ];
        }
        if (!$this->verificarCapacidad($grupo_id)) {
            return [
                'success' => false,
                'message' => 'El grupo ha alcanzado su capacidad máxima'
            ];
        }

        $sql = "INSERT INTO asignacion (estudiante_codigo, grupo_id, fecha_asignacion) 
                    VALUES (?, ?, CURRENT_TIMESTAMP)";
        $this->db->query($sql, [$estudiante_codigo, $grupo_id]);
        $this->actualizarCapacidadGrupo($grupo_id);
        return [
            'success' => true,
            'message' => 'Asignación realizada exitosamente'
        ];
    }

    // Eliminar una asignación
    public function eliminar($estudiante_codigo, $grupo_id)
    {
        if (!$this->existeAsignacion($estudiante_codigo, $grupo_id)) {
            return [
                'success' => false,
                'message' => 'La asignación no existe'
            ];
        }
        $sql = "DELETE FROM asignacion WHERE estudiante_codigo = ? AND grupo_id = ?";
        $filasAfectadas = $this->db->delete($sql, [$estudiante_codigo, $grupo_id]);
        if ($filasAfectadas > 0) {
            $this->actualizarCapacidadGrupo($grupo_id);
            return [
                'success' => true,
                'message' => 'Asignación eliminada exitosamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo eliminar la asignación'
            ];
        }
    }

    // Obtener todas las asignaciones
    public function mostrar()
    {
        try {
            $sql = "SELECT 
                        a.estudiante_codigo,
                        a.grupo_id,
                        a.fecha_asignacion,
                        e.nombres as estudiante_nombres,
                        e.apellidos as estudiante_apellidos,
                        e.ci as estudiante_ci,
                        g.nombre as grupo_nombre,
                        m.nombre as materia_nombre,
                        p.nombres as profesor_nombres,
                        p.apellidos as profesor_apellidos
                    FROM asignacion a
                    INNER JOIN estudiante e ON a.estudiante_codigo = e.codigo
                    INNER JOIN grupo g ON a.grupo_id = g.id
                    INNER JOIN materia m ON g.materia_id = m.id
                    INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                    ORDER BY a.fecha_asignacion DESC";

            return $this->db->fetchAll($sql);

        } catch (Exception $e) {
            error_log("Error en AsignacionModel::obtenerTodas: " . $e->getMessage());
            return [];
        }
    }


    // Verificar si existe una asignación
    private function existeAsignacion($estudiante_codigo, $grupo_id)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM asignacion
                    WHERE estudiante_codigo = ? AND grupo_id = ?";
            $resultado = $this->db->fetch($sql, [$estudiante_codigo, $grupo_id]);
            return $resultado['total'] > 0;

        } catch (Exception $e) {
            error_log("Error en AsignacionModel::existeAsignacion: " . $e->getMessage());
            return false;
        }
    }


    private function verificarCapacidad($grupo_id)
    {
        try {
            $sql = "SELECT 
                        g.capacidad_maxima,
                        COUNT(a.estudiante_codigo) as asignados_actuales
                    FROM grupo g
                    LEFT JOIN asignacion a ON g.id = a.grupo_id
                    WHERE g.id = ?
                    GROUP BY g.id, g.capacidad_maxima";

            $resultado = $this->db->fetch($sql, [$grupo_id]);

            if ($resultado) {
                return $resultado['asignados_actuales'] < $resultado['capacidad_maxima'];
            }

            return false;

        } catch (Exception $e) {
            error_log("Error en AsignacionModel::verificarCapacidad: " . $e->getMessage());
            return false;
        }
    }

    // Procesar carga masiva completa (método principal)
    public function procesarCargaMasiva($archivo, $grupo_id)
    {
        try {
            // Validar que se haya subido un archivo
            if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
                return [
                    'success' => false,
                    'message' => 'Error al cargar el archivo. Por favor, selecciona un archivo válido.'
                ];
            }

            $nombreArchivo = $archivo['name'];
            $rutaTemporal = $archivo['tmp_name'];
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            // Verificar extensión del archivo
            $extensionesPermitidas = ['xlsx', 'xls', 'csv'];
            if (!in_array($extension, $extensionesPermitidas)) {
                return [
                    'success' => false,
                    'message' => 'Formato de archivo no válido. Use .xlsx, .xls o .csv'
                ];
            }

            // Procesar archivo según extensión
            $codigosEstudiantes = [];
            if ($extension === 'csv') {
                $codigosEstudiantes = $this->procesarArchivoCSV($rutaTemporal);
            } else {
                $codigosEstudiantes = $this->procesarArchivoExcel($rutaTemporal);
            }

            // DEBUG: Mostrar códigos encontrados
            error_log("DEBUG - Códigos encontrados en archivo: " . json_encode($codigosEstudiantes));

            if (empty($codigosEstudiantes)) {
                return [
                    'success' => false,
                    'message' => 'No se encontraron códigos de estudiantes válidos en el archivo.'
                ];
            }

            // Crear asignaciones masivas
            $resultado = $this->crearMasivo($codigosEstudiantes, $grupo_id);

            // Generar mensaje personalizado para la carga masiva
            if ($resultado['success']) {
                $mensaje = "Carga masiva completada: {$resultado['exitosos']} asignaciones creadas";
                if ($resultado['errores'] > 0) {
                    $mensaje .= ", {$resultado['errores']} errores";
                }
                return [
                    'success' => true,
                    'message' => $mensaje,
                    'exitosos' => $resultado['exitosos'],
                    'errores' => $resultado['errores']
                ];
            } else {
                return $resultado;
            }

        } catch (Exception $e) {
            error_log("Error en procesarCargaMasiva: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno al procesar el archivo: ' . $e->getMessage()
            ];
        }
    }

    // Crear asignaciones masivas
    public function crearMasivo($codigosEstudiantes, $grupo_id)
    {
        try {
            $exitosos = 0;
            $errores = 0;
            $mensajesError = [];

            // Verificar capacidad del grupo antes de empezar
            if (!$this->verificarCapacidad($grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'El grupo ha alcanzado su capacidad máxima'
                ];
            }

            // DEBUG: Información inicial
            error_log("DEBUG - Iniciando creación masiva para grupo $grupo_id con códigos: " . json_encode($codigosEstudiantes));

            foreach ($codigosEstudiantes as $estudiante_codigo) {
                // Verificar que el estudiante existe
                if (!$this->existeEstudiante($estudiante_codigo)) {
                    $errores++;
                    $mensajesError[] = "Estudiante con código '$estudiante_codigo' no existe";
                    continue;
                }

                // Verificar que no esté ya asignado
                if ($this->existeAsignacion($estudiante_codigo, $grupo_id)) {
                    $errores++;
                    $mensajesError[] = "Estudiante '$estudiante_codigo' ya está asignado";
                    continue;
                }

                // Verificar capacidad para cada asignación
                if (!$this->verificarCapacidad($grupo_id)) {
                    $errores++;
                    $mensajesError[] = "Capacidad máxima alcanzada en estudiante '$estudiante_codigo'";
                    break; // Parar si se alcanza la capacidad
                }

                // Crear la asignación
                $sql = "INSERT INTO asignacion (estudiante_codigo, grupo_id, fecha_asignacion) 
                        VALUES (?, ?, CURRENT_TIMESTAMP)";
                
                try {
                    $filasAfectadas = $this->db->query($sql, [$estudiante_codigo, $grupo_id]);
                    error_log("DEBUG - Resultado query para $estudiante_codigo: " . json_encode($filasAfectadas));
                    
                    // Verificar si la asignación se creó correctamente
                    if ($this->existeAsignacion($estudiante_codigo, $grupo_id)) {
                        $exitosos++;
                        error_log("DEBUG - Asignación exitosa para $estudiante_codigo");
                    } else {
                        $errores++;
                        $mensajesError[] = "Error al crear asignación para '$estudiante_codigo'";
                        error_log("DEBUG - Error: asignación no se creó para $estudiante_codigo");
                    }
                } catch (Exception $e) {
                    $errores++;
                    $mensajesError[] = "Error SQL para '$estudiante_codigo': " . $e->getMessage();
                    error_log("DEBUG - Error SQL para $estudiante_codigo: " . $e->getMessage());
                }
            }

            // Actualizar capacidad del grupo una sola vez al final
            if ($exitosos > 0) {
                $this->actualizarCapacidadGrupo($grupo_id);
            }

            // DEBUG: Resumen final
            error_log("DEBUG - Resumen final - Exitosos: $exitosos, Errores: $errores");
            error_log("DEBUG - Mensajes de error: " . json_encode($mensajesError));

            // Preparar respuesta
            $response = [
                'success' => $exitosos > 0,
                'exitosos' => $exitosos,
                'errores' => $errores,
                'mensajes_error' => $mensajesError
            ];

            if ($exitosos > 0 && $errores == 0) {
                $response['message'] = "Todas las asignaciones se crearon exitosamente ($exitosos)";
            } elseif ($exitosos > 0 && $errores > 0) {
                $response['message'] = "Asignación parcial: $exitosos exitosos, $errores errores";
            } else {
                $response['success'] = false;
                $response['message'] = "No se pudo crear ninguna asignación. Revisa los códigos de estudiantes.";
            }

            error_log("DEBUG - Respuesta final: " . json_encode($response));
            return $response;

        } catch (Exception $e) {
            error_log("Error en AsignacionModel::crearMasivo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno al procesar asignaciones masivas',
                'exitosos' => 0,
                'errores' => count($codigosEstudiantes)
            ];
        }
    }

    // Procesar archivo CSV
    private function procesarArchivoCSV($rutaArchivo)
    {
        $codigos = [];
        error_log("DEBUG - Procesando archivo CSV: $rutaArchivo");

        if (($handle = fopen($rutaArchivo, "r")) !== FALSE) {
            $primeraFila = true;
            $columnaCodigoIndex = 0;
            $numeroFila = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $numeroFila++;
                error_log("DEBUG - Procesando fila $numeroFila: " . json_encode($data));
                
                if ($primeraFila) {
                    // Buscar la columna correcta
                    foreach ($data as $index => $header) {
                        $header = strtolower(trim($header));
                        if (in_array($header, ['codigo', 'estudiante_codigo', 'code', 'student_code'])) {
                            $columnaCodigoIndex = $index;
                            error_log("DEBUG - Columna de código encontrada en índice: $index");
                            break;
                        }
                    }
                    $primeraFila = false;

                    // Si el primer valor parece un código, no saltar la primera fila
                    if ($this->esCodigoValido($data[0])) {
                        $codigo = trim($data[$columnaCodigoIndex]);
                        if ($this->esCodigoValido($codigo)) {
                            $codigos[] = $codigo;
                            error_log("DEBUG - Código agregado desde primera fila: $codigo");
                        }
                    }
                    continue;
                }

                if (isset($data[$columnaCodigoIndex])) {
                    $codigo = trim($data[$columnaCodigoIndex]);
                    if ($this->esCodigoValido($codigo)) {
                        $codigos[] = $codigo;
                        error_log("DEBUG - Código agregado: $codigo");
                    } else {
                        error_log("DEBUG - Código inválido: $codigo");
                    }
                }
            }
            fclose($handle);
        } else {
            error_log("DEBUG - No se pudo abrir el archivo CSV");
        }

        $codigosUnicos = array_unique($codigos);
        error_log("DEBUG - Códigos únicos encontrados: " . json_encode($codigosUnicos));
        return $codigosUnicos;
    }

    // Procesar archivo Excel
    private function procesarArchivoExcel($rutaArchivo)
    {
        $codigos = [];
        // Intentar leer como CSV (muchos archivos Excel se pueden leer así)
        $contenido = file_get_contents($rutaArchivo);
        $lineas = explode("\n", $contenido);

        foreach ($lineas as $index => $linea) {
            $valores = str_getcsv($linea, "\t"); // Usar tab como separador
            if (empty($valores)) {
                $valores = str_getcsv($linea, ","); // Intentar con coma
            }

            if (!empty($valores[0])) {
                $codigo = trim($valores[0]);
                if ($this->esCodigoValido($codigo)) {
                    $codigos[] = $codigo;
                }
            }
        }

        return array_unique($codigos);
    }

    // Validar formato de código de estudiante
    private function esCodigoValido($codigo)
    {
        $codigoOriginal = $codigo;
        $codigo = trim($codigo);

        // No debe estar vacío
        if (empty($codigo)) {
            error_log("DEBUG - Código inválido (vacío): '$codigoOriginal'");
            return false;
        }

        // No debe ser un encabezado común
        $encabezadosExcluir = ['codigo', 'estudiante_codigo', 'code', 'student_code', 'id', 'nombre', 'name'];
        if (in_array(strtolower($codigo), $encabezadosExcluir)) {
            error_log("DEBUG - Código inválido (es encabezado): '$codigo'");
            return false;
        }

        // Debe tener una longitud razonable (ajusta según tu formato)
        if (strlen($codigo) < 3 || strlen($codigo) > 20) {
            error_log("DEBUG - Código inválido (longitud " . strlen($codigo) . "): '$codigo'");
            return false;
        }

        error_log("DEBUG - Código válido: '$codigo'");
        return true;
    }

    // Verificar si un estudiante existe
    private function existeEstudiante($estudiante_codigo)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM estudiante WHERE codigo = ?";
            $resultado = $this->db->fetch($sql, [$estudiante_codigo]);
            return $resultado['total'] > 0;
        } catch (Exception $e) {
            error_log("Error en AsignacionModel::existeEstudiante: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar la capacidad actual del grupo
    private function actualizarCapacidadGrupo($grupo_id)
    {
        $sql = "UPDATE grupo 
                    SET capacidad_actual = (
                        SELECT COUNT(*) FROM asignacion WHERE grupo_id = ?
                    ) 
                    WHERE id = ?";
        $this->db->update($sql, [$grupo_id, $grupo_id]);
    }
}