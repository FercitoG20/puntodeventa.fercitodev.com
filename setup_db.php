<?php
require_once 'conexion.php';

try {
    // 1. Crear la tabla de usuarios
    $sqlTabla = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        rol ENUM('admin', 'cajero', 'visor') NOT NULL DEFAULT 'cajero',
        estado TINYINT(1) DEFAULT 1,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlTabla);
    echo "✅ Tabla 'usuarios' creada o verificada correctamente.<br>";

    // 2. Insertar usuarios (Usamos password_hash para máxima seguridad)
    $stmt = $pdo->prepare("INSERT IGNORE INTO usuarios (nombre, username, password, rol) VALUES (?, ?, ?, ?)");

    // Usuario Demo (Para los clientes de tu web)
    $passDemo = password_hash('vistaprevia', PASSWORD_DEFAULT);
    $stmt->execute(['Usuario Demo', 'vistaprevia', $passDemo, 'visor']);

    // Usuario Administrador (Para ti)
    $passAdmin = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt->execute(['Fernando Admin', 'admin', $passAdmin, 'admin']);

    echo "✅ Usuarios insertados correctamente (si no existían).<br>";
    echo "<br><strong>¡Todo listo!</strong> Ve a phpMyAdmin para comprobarlo y luego intenta iniciar sesión.";

} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
?>