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
capacidad_maxima INTEGER DEFAULT 100,
capacidad_actual INTEGER DEFAULT 0,
materia_id INTEGER NOT NULL,
profesor_codigo VARCHAR(20) NOT NULL,
FOREIGN KEY (materia_id) REFERENCES materia(id),
FOREIGN KEY (profesor_codigo) REFERENCES profesor(codigo)
);




CREATE TABLE clases (
id SERIAL PRIMARY KEY,
dia DATE NOT NULL,
hora_inicio TIME,
hora_fin TIME,
codigo VARCHAR(255),
grupo_id INTEGER NOT NULL,
FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE 
);


CREATE TABLE inscribe (
estudiante_codigo VARCHAR(20) NOT NULL,
grupo_id INTEGER NOT NULL,
fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (estudiante_codigo, grupo_id),
FOREIGN KEY (estudiante_codigo) REFERENCES estudiante(codigo) ON DELETE CASCADE ,
FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE
);


CREATE TABLE asistencia (
id SERIAL PRIMARY KEY,
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

INSERT INTO grupo (nombre, capacidad_maxima, capacidad_actual, materia_id, profesor_codigo) VALUES 
('MAT-A', 30, 0, 1, 'PROF001'),
('PROG-B', 25, 0, 2, 'PROF001'),
('BD-C', 20, 0, 3, 'PROF001');


INSERT INTO clases (dia, codigo, grupo_id, hora_inicio, hora_fin) VALUES 
('2025-09-15', '123ABC', 1, '08:00', '10:00'),
('2025-09-15', '456DEF', 2, '10:00', '12:00'),
('2025-09-15', '789GHI', 3, '08:00', '10:00'  );

INSERT INTO inscribe (estudiante_codigo, grupo_id, fecha_inscripcion) VALUES 
('EST001', 1, CURRENT_TIMESTAMP),
('EST001', 2, CURRENT_TIMESTAMP),
('EST002', 1, CURRENT_TIMESTAMP),
('EST002', 3, CURRENT_TIMESTAMP),
('EST003', 2, CURRENT_TIMESTAMP),
('EST003', 3, CURRENT_TIMESTAMP);

INSERT INTO asistencia (tipo, estudiante_codigo, clases_id) VALUES 
( 'presente', 'EST001', 1),
( 'tarde', 'EST002', 1),
( 'presente', 'EST001', 2),
( 'presente', 'EST003', 3);
