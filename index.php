<?php
session_start();
require_once 'conexion.php'; // Conectamos a la BD
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Buscamos al usuario en la BD
        $stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE username = :username AND estado = 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verificamos si existe el usuario y si la contraseña coincide con el hash
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['user'] = $user['nombre'];
            $_SESSION['role'] = $user['rol'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Credenciales incorrectas. Por favor, verifica tus datos.';
        }
    } catch (PDOException $e) {
        $error = "Error de base de datos: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema POS - FercitoDev</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent: #0ea5e9;
            --accent-hover: #0284c7;
            --error-color: #ef4444;
            --demo-bg: #e0f2fe;
            --demo-border: #bae6fd;
            --demo-text: #0369a1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: var(--card-bg);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header i {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 10px;
            display: inline-block;
        }

        .login-header h1 {
            font-size: 1.5rem;
            color: var(--text-main);
            font-weight: 800;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        /* Caja de Información de la Demo */
        .demo-alert {
            background-color: var(--demo-bg);
            border: 1px solid var(--demo-border);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .demo-alert i {
            color: var(--accent);
            font-size: 1.2rem;
            margin-top: 2px;
        }

        .demo-alert-content h4 {
            color: var(--demo-text);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .demo-alert-content p {
            color: var(--demo-text);
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .demo-alert-content strong {
            background: rgba(255,255,255,0.6);
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-main);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .form-icon {
            position: absolute;
            bottom: 12px;
            left: 15px;
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: var(--accent-hover);
        }

        .error-message {
            color: var(--error-color);
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 20px;
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .footer-text a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <i class="ri-store-2-line"></i>
            <h1>Sistema POS</h1>
            <p>Gestión de Punto de Venta</p>
        </div>

        <div class="error-message">
            <i class="ri-error-warning-line"></i> <?php echo $error; ?>
        </div>

        <div class="demo-alert">
            <i class="ri-information-fill"></i>
            <div class="demo-alert-content">
                <h4>¿Quieres ver una demostración?</h4>
                <p>Ingresa como espectador usando las siguientes credenciales de prueba:<br>
                Usuario: <strong>vistaprevia</strong><br>
                Contraseña: <strong>vistaprevia</strong></p>
            </div>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Usuario</label>
                <i class="ri-user-3-line form-icon"></i>
                <input type="text" id="username" name="username" class="form-control" placeholder="Ingresa tu usuario" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <i class="ri-lock-password-line form-icon"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="Ingresa tu contraseña" required>
            </div>

            <button type="submit" class="btn-login">
                Ingresar al Sistema <i class="ri-login-circle-line"></i>
            </button>
        </form>

        <div class="footer-text">
            ¿Necesitas tu propio sistema? <br>
            <a href="https://fercitodev.com" target="_blank">Contacta a FercitoDev.com</a>
        </div>
    </div>

</body>
</html>