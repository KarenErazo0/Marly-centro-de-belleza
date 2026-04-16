CREATE DATABASE IF NOT EXISTS marly_centro_belleza;
USE marly_centro_belleza;

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo_electronico VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL
);

CREATE TABLE personal (
    id_personal INT AUTO_INCREMENT PRIMARY KEY,
    nombre_personal VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    datos_contacto VARCHAR(150) NOT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'
);

CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    duracion_minutos INT NOT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'
);

CREATE TABLE citas (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_personal INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado_cita ENUM('pendiente','confirmada','atendida','cancelada','inasistencia') NOT NULL DEFAULT 'pendiente',
    fecha_creacion DATETIME NOT NULL,
    fecha_actualizacion DATETIME NOT NULL,
    CONSTRAINT fk_citas_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE,
    CONSTRAINT fk_citas_personal FOREIGN KEY (id_personal) REFERENCES personal(id_personal)
);

CREATE TABLE cita_servicio (
    id_cita INT NOT NULL,
    id_servicio INT NOT NULL,
    PRIMARY KEY (id_cita, id_servicio),
    CONSTRAINT fk_cita_servicio_cita FOREIGN KEY (id_cita) REFERENCES citas(id_cita) ON DELETE CASCADE,
    CONSTRAINT fk_cita_servicio_servicio FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio)
);
