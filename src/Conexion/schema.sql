CREATE TABLE usuario (
id SERIAL PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
contrasena VARCHAR(255) NOT NULL,
creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE profesor (
codigo VARCHAR(20) PRIMARY KEY,
nombres VARCHAR(100) NOT NULL,
apellidos VARCHAR(100) NOT NULL,
genero VARCHAR(10),
usuario_id INTEGER NOT NULL,
creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

CREATE TABLE estudiante (
codigo VARCHAR(20) PRIMARY KEY,
ci VARCHAR(20) NOT NULL UNIQUE, 
nombres VARCHAR(100) NOT NULL,
apellidos VARCHAR(100) NOT NULL,
estado VARCHAR(20) DEFAULT 'activo',
genero VARCHAR(10),
usuario_id INTEGER NOT NULL,
creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);


CREATE TABLE materia (
id SERIAL PRIMARY KEY,
nombre VARCHAR(100) NOT NULL
);


CREATE TABLE grupo (
id SERIAL PRIMARY KEY,
nombre VARCHAR(50) NOT NULL,
capacidad INTEGER DEFAULT 30,
materia_id INTEGER NOT NULL,
profesor_codigo VARCHAR(20) NOT NULL,
FOREIGN KEY (materia_id) REFERENCES materia(id),
FOREIGN KEY (profesor_codigo) REFERENCES profesor(codigo)
);


CREATE TABLE horario (
id SERIAL PRIMARY KEY,
dia VARCHAR(20) NOT NULL,
hora_inicio TIME NOT NULL,
hora_final TIME NOT NULL,
grupo_id INTEGER NOT NULL,
FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE
);


CREATE TABLE clases (
id SERIAL PRIMARY KEY,
dia DATE NOT NULL,
fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
qr VARCHAR(255),
grupo_id INTEGER NOT NULL,
FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE 
);


CREATE TABLE inscribe (
estudiante_codigo VARCHAR(20) NOT NULL,
grupo_id INTEGER NOT NULL,
PRIMARY KEY (estudiante_codigo, grupo_id),
FOREIGN KEY (estudiante_codigo) REFERENCES estudiante(codigo) ON DELETE CASCADE ,
FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE
);


CREATE TABLE asistencia (
id SERIAL PRIMARY KEY,
fecha DATE NOT NULL,
hora_inicio TIME,
hora_fin TIME,
tipo VARCHAR(20) DEFAULT 'presente',
estudiante_codigo VARCHAR(20) NOT NULL,
clases_id INTEGER NOT NULL,
FOREIGN KEY (estudiante_codigo) REFERENCES estudiante(codigo),
FOREIGN KEY (clases_id) REFERENCES clases(id) ON DELETE CASCADE 
);

-- Insertar datos de prueba
INSERT INTO usuario (nombre, contrasena) VALUES 
('admin', '123456'),
('profesor1', 'prof123'),
('estudiante1', 'est123'),
('estudiante2', 'est456'),
('estudiante3', 'est789');

INSERT INTO profesor (codigo, nombres, apellidos, genero, usuario_id) VALUES 
('PROF001', 'Juan Carlos', 'García López', 'M', 2);

INSERT INTO estudiante (codigo, ci, nombres, apellidos, genero, usuario_id) VALUES 
('EST001', '12345678', 'María Elena', 'Pérez Silva', 'F', 3),
('EST002', '87654321', 'Carlos Alberto', 'Mamani Quispe', 'M', 4),
('EST003', '11223344', 'Ana Sofía', 'Rodríguez Torres', 'F', 5);

INSERT INTO materia (nombre) VALUES 
('Matemáticas I'),
('Programación Web'),
('Base de Datos'),
('Física I'),
('Química General');

INSERT INTO grupo (nombre, capacidad, materia_id, profesor_codigo) VALUES 
('MAT-A', 30, 1, 'PROF001'),
('PROG-B', 25, 2, 'PROF001'),
('BD-C', 20, 3, 'PROF001');

INSERT INTO horario (dia, hora_inicio, hora_final, grupo_id) VALUES 
('Lunes', '08:00', '10:00', 1),
('Miércoles', '10:00', '12:00', 1),
('Martes', '14:00', '16:00', 2),
('Jueves', '16:00', '18:00', 2),
('Viernes', '08:00', '10:00', 3);

INSERT INTO clases (dia, qr, grupo_id) VALUES 
('2025-09-15', 'QR123ABC', 1),
('2025-09-15', 'QR456DEF', 2),
('2025-09-15', 'QR789GHI', 3);

INSERT INTO inscribe (estudiante_codigo, grupo_id) VALUES 
('EST001', 1),
('EST001', 2),
('EST002', 1),
('EST002', 3),
('EST003', 2),
('EST003', 3);

INSERT INTO asistencia (fecha, hora_inicio, hora_fin, tipo, estudiante_codigo, clases_id) VALUES 
('2025-09-15', '08:05', '09:55', 'presente', 'EST001', 1),
('2025-09-15', '08:15', '09:55', 'tarde', 'EST002', 1),
('2025-09-15', '14:00', '15:50', 'presente', 'EST001', 2),
('2025-09-15', '08:00', '09:45', 'presente', 'EST003', 3);
