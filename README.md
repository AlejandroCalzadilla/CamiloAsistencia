# 🎓 Sistema de Asistencia Académica

Sistema web completo para gestión de asistencias universitarias desarrollado en PHP con PostgreSQL y arquitectura MVC.

## ✨ Características

- 🔐 **Sistema de autenticación** con roles (Admin, Profesor, Estudiante)
- 👥 **Gestión de usuarios** y perfiles
- 📚 **Administración de materias** y grupos
- 👨‍🏫 **Registro de profesores** y estudiantes
- 📅 **Creación y gestión de clases**
- ✅ **Control de asistencias** con códigos únicos
- 📊 **Reportes de asistencia** detallados
- 📤 **Carga masiva** de estudiantes desde Excel/CSV
- 📱 **Diseño responsive** y moderno

## 🏗️ Estructura del Proyecto

```
asistencia_web/
├── public/                 # Archivos públicos (punto de entrada)
│   ├── index.php          # Login y página principal
│   ├── grupo.php          # Gestión de grupos
│   ├── clase.php          # Gestión de clases
│   ├── estudiante.php     # Gestión de estudiantes
│   ├── profesores.php     # Gestión de profesores
│   └── test-conexion.php  # Test de conexión DB
├── src/
│   ├── Conexion/          # Configuración de base de datos
│   │   ├── Conexion.php   # Clase singleton de conexión
│   │   ├── Config.php     # Configuración desde .env
│   │   └── schema.sql     # Estructura y datos iniciales
│   ├── Controller/        # Controladores MVC
│   ├── Model/            # Modelos y lógica de negocio
│   ├── View/             # Vistas y renderizado HTML
│   └── interfaces/       # Interfaces y contratos
├── temp/                 # Archivos temporales y ejemplos
└── vendor/              # Dependencias de Composer
```

## 🚀 Instalación y Configuración

### 1. Requisitos Previos

- PHP 8.0 o superior
- PostgreSQL 12 o superior
- Composer (opcional)
- Git

### 2. Clonar el Repositorio

```bash
git clone <tu-repositorio>
cd asistencia_web
```

### 3. Configurar Base de Datos

#### Opción A: PostgreSQL Local
```bash
# Instalar PostgreSQL (Ubuntu/Debian)
sudo apt install postgresql postgresql-contrib

# Crear usuario y base de datos
sudo -u postgres createuser --interactive
sudo -u postgres createdb asistenciadb
```

#### Opción B: PostgreSQL con Docker
```bash
docker run --name postgres-local \
  -e POSTGRES_DB=asistenciadb \
  -e POSTGRES_USER=postgres \
  -e POSTGRES_PASSWORD=ale12345678 \
  -p 5432:5432 -d postgres:15
```

### 4. Configurar Variables de Entorno

Crea un archivo `.env` en la raíz del proyecto:

```env
# Configuración de Base de Datos PostgreSQL
DATABASE_URL=postgresql://postgres:ale12345678@localhost:5432/asistenciadb

# Configuración individual (alternativa)
DB_HOST=localhost
DB_PORT=5432
DB_NAME=asistenciadb
DB_USER=postgres
DB_PASSWORD=ale12345678

# Sin pooler para base local
POOL_MODE=none

# Configuración de la aplicación
APP_ENV=development
APP_DEBUG=true
APP_TIMEZONE=America/La_Paz
```

### 5. Inicializar la Base de Datos

1. **Ejecutar el servidor**:
   ```bash
   php -S localhost:8000 -t public
   ```

2. **Abrir test de conexión**: http://localhost:8000/test-conexion.php

3. **Ejecutar script SQL** para crear tablas y datos iniciales

## 🔧 Test de Conexión

El archivo `test-conexion.php` permite:

- ✅ **Verificar conexión** a PostgreSQL
- 📋 **Listar tablas** existentes y conteo de registros
- 🚀 **Ejecutar script SQL** para crear estructura inicial
- 🔄 **Recrear tablas** cuando sea necesario
- 👤 **Verificar usuarios** de prueba

### Datos de Prueba Incluidos

Al ejecutar el script SQL se crean:
- **Usuario Admin**: `admin` / `123456`
- **Profesores de ejemplo** con usuarios asignados
- **Estudiantes de ejemplo**
- **Materias básicas** (Matemáticas, Programación, etc.)

## 🎯 Uso del Sistema

### Roles y Permisos

#### 👑 **Administrador**
- Gestión completa de usuarios, profesores y estudiantes
- Creación y administración de materias
- Asignación de grupos y capacidades
- Carga masiva de estudiantes desde Excel/CSV

#### 👨‍🏫 **Profesor**
- Visualización de grupos asignados
- Creación y gestión de clases
- Generación de códigos de asistencia
- Control y reportes de asistencias

#### 👨‍🎓 **Estudiante**
- Visualización de grupos inscritos
- Registro de asistencia con códigos
- Consulta de historial de asistencias

### Carga Masiva de Estudiantes

El sistema permite cargar múltiples estudiantes desde archivos Excel o CSV:

#### Formatos Soportados
```csv
# Con encabezado
codigo
EST001
EST002
EST003

# Sin encabezado
EST001
EST002
EST003

# Con información adicional
codigo,nombre,apellido,ci
EST001,María,Pérez,12345678
EST002,Carlos,Mamani,87654321
```

#### Archivos de Ejemplo
Los archivos de ejemplo están en la carpeta `temp/`:
- `estudiantes_ejemplo.csv` - Con encabezado
- `estudiantes_simple.csv` - Solo códigos
- `estudiantes_completo.csv` - Con información completa

## 🎨 Diseño y UI

- **Paleta minimalista**: Blanco, negro y grises
- **Diseño responsive** que funciona en móviles
- **Interfaz consistente** en todas las vistas
- **Navegación intuitiva** con breadcrumbs
- **Feedback visual** claro para acciones del usuario

## 🔒 Seguridad

- **Autenticación por sesiones** PHP
- **Validación de roles** en cada vista
- **Sanitización de inputs** en formularios
- **Prepared statements** para prevenir SQL injection
- **Validación server-side** de todos los datos

## 🚀 Comandos Útiles

```bash
# Iniciar servidor de desarrollo
php -S localhost:8000 -t public

# Ver logs en tiempo real
tail -f /var/log/apache2/error.log

# Backup de base de datos
pg_dump asistenciadb > backup.sql

# Restaurar backup
psql asistenciadb < backup.sql

# Verificar conexión PostgreSQL
psql -h localhost -U postgres -d asistenciadb -c "SELECT version();"
```

## 🐛 Solución de Problemas

### Error de Conexión a PostgreSQL
```bash
# Verificar que PostgreSQL esté ejecutándose
sudo systemctl status postgresql

# Reiniciar PostgreSQL
sudo systemctl restart postgresql

# Verificar puerto 5432
netstat -ln | grep 5432
```

### Problemas de Permisos
```bash
# Dar permisos de escritura a temp/
chmod 755 temp/
```

### Error "Class not found"
```bash
# Verificar estructura de archivos
# Verificar que todos los require_once apunten correctamente
```

## 📝 Notas de Desarrollo

- **Arquitectura MVC** estricta
- **Patrón Singleton** para conexión DB
- **Separation of concerns** entre controladores y modelos
- **Validación robusta** en backend
- **Logging detallado** para debugging
- **Código documentado** y mantenible

## 🤝 Contribuir

1. Fork del proyecto
2. Crear branch para feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit de cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto es de uso académico y educativo.

---

**Desarrollado con ❤️ para la gestión académica moderna**