<?php
session_start();
require_once 'conexion.php'; 
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE username = :username AND estado = 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['user'] = $user['nombre'];
            $_SESSION['role'] = $user['rol'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Credenciales incorrectas.';
        }
    } catch (PDOException $e) {
        $error = "Error de conexión.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso POS Premium | FercitoDev</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-formal: #1e293b; /* Color oscuro profesional */
            --bg-light: #f8fafc;     /* Fondo gris ultra claro */
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --accent-glow: radial-gradient(circle, rgba(255,255,255,0.7) 0%, rgba(248,250,252,0) 70%); /* Resalte suave */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { height: 100vh; display: flex; background-color: #fff; overflow: hidden; }

        /* LADO IZQUIERDO: INFO PANEL REDISEÑADO */
        .info-panel {
            flex: 1.2;
            background-color: var(--bg-light);
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Logo arriba, info/imagen centro, copyright abajo */
            padding: 5rem;
            border-right: 1px solid var(--border);
            position: relative;
        }

        /* RESALTE MODERNO PARA EL LOGO */
        .logo-resaltado-wrapper {
            position: absolute;
            top: 5rem;
            left: 5rem;
            width: fit-content;
            height: fit-content;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        /* Contenedor con brillo suave */
        .logo-resaltado-wrapper::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: var(--accent-glow);
            border-radius: 50%;
            z-index: -1;
            transform: translate(-50px, -50px);
        }

        .brand-logo-img {
            height: 60px; /* ¡MUCHO MÁS GRANDE! (Ajusta si necesitas) */
            width: auto;
            display: block;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.06)); /* Sombra suave para profundidad */
            transition: all 0.3s ease;
        }

        /* INFO CONTENT CENTRADO */
        .info-content-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-grow: 1;
            margin-top: 8rem; /* Espacio para el logo */
        }

        .hero-title {
            font-size: 3rem;
            color: var(--text-main);
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .hero-title br { display: block; content: ""; margin-top: 10px; } /* Ajuste interlineado manual */

        .hero-desc {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 450px;
            line-height: 1.6;
            margin-bottom: 3rem; /* Espacio antes de la imagen */
        }

        /* NUEVA IMAGEN DE INVENTARIO */
        .info-image-container {
            width: 100%;
            max-width: 500px;
            display: flex;
            justify-content: center;
        }

        .info-image-src {
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 12px; /* Toque suave */
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.04)); /* Profundidad */
        }

        /* COPYRIGHT ABAJO */
        .copyright-container {
            padding-top: 2rem;
        }

        .copyright-text {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* LADO DERECHO: LOGIN PANEL */
        .login-panel { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem; }
        .form-container { width: 100%; max-width: 400px; }
        .login-title { margin-bottom: 2rem; }
        .login-title h2 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .login-title p { color: var(--text-muted); font-size: 0.95rem; }

        /* DEMO INFO */
        .demo-info { background-color: #f1f5f9; border-radius: 12px; padding: 1.25rem; margin-bottom: 2rem; border: 1px solid var(--border); }
        .demo-info span { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
        .demo-info p { font-size: 0.9rem; color: var(--text-main); }
        .demo-info strong { color: var(--primary-formal); background: #fff; padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border); font-family: monospace; }

        /* FORMULARIO */
        .input-group { margin-bottom: 1.5rem; }
        .input-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .input-group input { width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border); background-color: var(--bg-light); font-size: 0.95rem; transition: all 0.2s; }
        .input-group input:focus { outline: none; border-color: var(--primary-formal); background-color: #fff; box-shadow: 0 0 0 4px rgba(30, 41, 59, 0.05); }
        .btn-login { width: 100%; padding: 14px; background-color: var(--primary-formal); color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: opacity 0.2s; margin-top: 1rem; }
        .btn-login:hover { opacity: 0.9; }
        .error-box { background-color: #fef2f2; color: #b91c1c; padding: 12px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1.5rem; border: 1px solid #fee2e2; display: <?php echo $error ? 'block' : 'none'; ?>; }
        .footer-link { text-align: center; margin-top: 2.5rem; font-size: 0.85rem; color: var(--text-muted); }
        .footer-link a { color: var(--primary-formal); text-decoration: none; font-weight: 600; }

        /* RESPONSIVO MÓVIL */
        @media (max-width: 1024px) {
            .info-panel { display: none; }
            body { background-color: var(--bg-light); justify-content: center; align-items: center; overflow: auto; padding: 2rem;}
            .login-panel { background: #fff; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); max-height: fit-content; padding: 4rem 2rem;}
        }
    </style>
</head>
<body>

    <div class="info-panel">
        
        <div class="logo-resaltado-wrapper">
            <img src="img/fercitodev.png" alt="POS FercitoDev Logo" class="brand-logo-img">
        </div>
        
        <div class="info-content-container">
            <h1 class="hero-title">Gestión integral para<br>negocios modernos.</h1>
            <p class="hero-desc">Optimiza tus ventas, monitorea inventarios en tiempo real y toma decisiones basadas en datos con nuestra plataforma POS de alto rendimiento.</p>
            
            <div class="info-image-container">
                <img src="img/inventario.png" alt="Previsualización Inventario POS" class="info-image-src">
            </div>
        </div>
        
        <div class="copyright-container">
            <p class="copyright-text">© 2026 FercitoDev. Todos los derechos reservados.</p>
        </div>
    </div>

    <div class="login-panel">
        <div class="form-container">
            <div class="login-title">
                <h2>Iniciar sesión</h2>
                <p>Ingresa tus datos para gestionar tu negocio.</p>
            </div>
            <div class="error-box"><?php echo $error; ?></div>
            <div class="demo-info">
                <span>Acceso de Prueba</span>
                <p>Usuario: <strong>vistaprevia</strong> | Clave: <strong>vistaprevia</strong></p>
            </div>
            <form method="POST">
                <div class="input-group">
                    <label>Usuario</label>
                    <input type="text" name="username" placeholder="Tu nombre de usuario" required>
                </div>
                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">Acceder al Sistema</button>
            </form>
            <div class="footer-link">
                ¿Necesitas soporte técnico? <br>
                <a href="https://fercitodev.com" target="_blank">Contactar a FercitoDev</a>
            </div>
        </div>
    </div>

</body>
</html>