-- AGPR V2
-- Productos, empleados, puestos y privilegios.
-- Se puede ejecutar más de una vez.

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'cliente',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(40) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    descripcion VARCHAR(180) NOT NULL,
    detalle TEXT NOT NULL,
    caracteristicas TEXT NOT NULL,
    etiqueta VARCHAR(30) NULL,
    stock INT NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id),
    CONSTRAINT chk_productos_precio CHECK (precio > 0),
    CONSTRAINT chk_productos_stock CHECK (stock >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS privilegios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(60) NOT NULL UNIQUE,
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    grupo VARCHAR(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS puestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS puesto_privilegio (
    puesto_id INT NOT NULL,
    privilegio_id INT NOT NULL,
    PRIMARY KEY (puesto_id, privilegio_id),
    CONSTRAINT fk_puesto_privilegio_puesto FOREIGN KEY (puesto_id) REFERENCES puestos (id) ON DELETE CASCADE,
    CONSTRAINT fk_puesto_privilegio_privilegio FOREIGN KEY (privilegio_id) REFERENCES privilegios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    puesto_id INT NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_empleados_puesto FOREIGN KEY (puesto_id) REFERENCES puestos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS empleado_privilegio (
    empleado_id INT NOT NULL,
    privilegio_id INT NOT NULL,
    modo ENUM('conceder', 'revocar') NOT NULL,
    PRIMARY KEY (empleado_id, privilegio_id),
    CONSTRAINT fk_empleado_privilegio_empleado FOREIGN KEY (empleado_id) REFERENCES empleados (id) ON DELETE CASCADE,
    CONSTRAINT fk_empleado_privilegio_privilegio FOREIGN KEY (privilegio_id) REFERENCES privilegios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categorias (clave, nombre)
SELECT 'cascos', 'Cascos' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE clave = 'cascos');

INSERT INTO categorias (clave, nombre)
SELECT 'guantes', 'Guantes' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE clave = 'guantes');

INSERT INTO categorias (clave, nombre)
SELECT 'soportes', 'Soportes' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE clave = 'soportes');

INSERT INTO categorias (clave, nombre)
SELECT 'intercomunicadores', 'Intercomunicadores' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE clave = 'intercomunicadores');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'productos.ver', 'Ver productos', 'Consultar el catálogo interno', 'Productos' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'productos.ver');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'productos.crear', 'Crear productos', 'Dar de alta accesorios', 'Productos' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'productos.crear');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'productos.editar', 'Editar productos', 'Modificar precio, stock y ficha', 'Productos' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'productos.editar');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'productos.eliminar', 'Eliminar productos', 'Quitar accesorios del catálogo', 'Productos' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'productos.eliminar');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'empleados.ver', 'Ver empleados', 'Consultar la plantilla', 'Empleados' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'empleados.ver');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'empleados.crear', 'Crear empleados', 'Registrar nuevas cuentas de personal', 'Empleados' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'empleados.crear');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'empleados.editar', 'Editar empleados', 'Cambiar datos, puesto y estado', 'Empleados' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'empleados.editar');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'empleados.eliminar', 'Eliminar empleados', 'Dar de baja una cuenta de personal', 'Empleados' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'empleados.eliminar');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'empleados.privilegios', 'Privilegios de empleados', 'Conceder o revocar permisos individuales', 'Empleados' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'empleados.privilegios');

INSERT INTO privilegios (clave, nombre, descripcion, grupo)
SELECT 'puestos.editar', 'Editar puestos', 'Definir los privilegios base de cada puesto', 'Puestos' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM privilegios WHERE clave = 'puestos.editar');

INSERT INTO puestos (nombre, descripcion)
SELECT 'Administrador', 'Control total de la tienda y de la plantilla' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM puestos WHERE nombre = 'Administrador');

INSERT INTO puestos (nombre, descripcion)
SELECT 'Gerente', 'Catálogo completo y gestión de privilegios, sin eliminar empleados' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM puestos WHERE nombre = 'Gerente');

INSERT INTO puestos (nombre, descripcion)
SELECT 'Vendedor', 'Consulta el catálogo para orientar a los clientes' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM puestos WHERE nombre = 'Vendedor');

INSERT INTO puestos (nombre, descripcion)
SELECT 'Almacenista', 'Consulta y actualiza existencias y fichas' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM puestos WHERE nombre = 'Almacenista');

INSERT IGNORE INTO puesto_privilegio (puesto_id, privilegio_id)
SELECT p.id, pr.id
FROM puestos p
JOIN privilegios pr
WHERE p.nombre = 'Administrador';

INSERT IGNORE INTO puesto_privilegio (puesto_id, privilegio_id)
SELECT p.id, pr.id
FROM puestos p
JOIN privilegios pr
WHERE p.nombre = 'Gerente'
  AND pr.clave IN (
    'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
    'empleados.ver', 'empleados.editar', 'empleados.privilegios'
  );

INSERT IGNORE INTO puesto_privilegio (puesto_id, privilegio_id)
SELECT p.id, pr.id
FROM puestos p
JOIN privilegios pr
WHERE p.nombre = 'Vendedor'
  AND pr.clave = 'productos.ver';

INSERT IGNORE INTO puesto_privilegio (puesto_id, privilegio_id)
SELECT p.id, pr.id
FROM puestos p
JOIN privilegios pr
WHERE p.nombre = 'Almacenista'
  AND pr.clave IN ('productos.ver', 'productos.editar');
