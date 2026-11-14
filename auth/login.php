<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["id_usuario"])) {
    header("Location: /talenthub2/index.php");
    exit;
}

require_once '../config/Conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST["nombre_usuario"]);
    $contrasena     = $_POST["contrasena"];

    if ($nombre_usuario == "" || $contrasena == "") {
        $error = "Completá todos los campos.";
    } else {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare(
            "SELECT u.id_usuario, u.nombre_usuario, u.contrasena,
                    u.estado, r.nombre_rol, e.nombre, e.apellido
             FROM usuario u
             JOIN rol r ON u.id_rol = r.id_rol
             LEFT JOIN empleado e ON u.legajo = e.legajo
             WHERE u.nombre_usuario = ?"
        );
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            $error = "Usuario o contraseña incorrectos.";
        } else {
            $usuario = $resultado->fetch_assoc();
            if ($usuario["estado"] == 0) {
                $error = "Tu cuenta está desactivada. Contactá al administrador.";
            } elseif (!password_verify($contrasena, $usuario["contrasena"])) {
                $error = "Usuario o contraseña incorrectos.";
            } else {
                $_SESSION["id_usuario"]     = $usuario["id_usuario"];
                $_SESSION["nombre_usuario"] = $usuario["nombre_usuario"];
                $_SESSION["nombre"]         = $usuario["nombre"];
                $_SESSION["apellido"]       = $usuario["apellido"];
                $_SESSION["rol"]            = $usuario["nombre_rol"];
                header("Location: /talenthub2/index.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TalentHub — Ingresar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --verde:    #828C6A;
            --sage:     #A0AB89;
            --lavender: #F6E5E7;
            --tea:      #EFC0BC;
            --coral:    #E69B97;
            --verde-dark: #6B7457;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
            background: var(--lavender);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* FONDO ORGÁNICO */
        .bg-blobs {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.45;
            animation: drift 10s ease-in-out infinite alternate;
        }
        .blob-1 { width: 500px; height: 500px; background: var(--tea);    top: -150px; left: -150px; animation-delay: 0s; }
        .blob-2 { width: 380px; height: 380px; background: var(--sage);   bottom: -100px; right: -80px; animation-delay: 3s; }
        .blob-3 { width: 280px; height: 280px; background: var(--coral);  top: 40%; left: 30%; animation-delay: 6s; }
        .blob-4 { width: 200px; height: 200px; background: var(--verde);  bottom: 10%; left: 10%; animation-delay: 2s; }

        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 20px) scale(1.08); }
        }

        /* CARD PRINCIPAL */
        .login-card {
            position: relative;
            z-index: 1;
            display: flex;
            width: 880px;
            min-height: 540px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(106,116,87,0.28);
        }

        /* PANEL IZQUIERDO */
        .panel-left {
            width: 42%;
            background: linear-gradient(160deg, var(--verde-dark) 0%, var(--verde) 45%, var(--sage) 100%);
            padding: 48px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .panel-left::before {
            content: '';
            position: absolute;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -120px; right: -100px;
        }
        .panel-left::after {
            content: '';
            position: absolute;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -50px; left: -50px;
        }

        .brand-wrap { position: relative; z-index: 1; }
        .brand-name {
            font-size: 2.1rem;
            font-weight: 900;
            letter-spacing: 4px;
            color: white;
        }
        .brand-name span { color: var(--tea); }
        .brand-line {
            width: 36px; height: 3px;
            background: rgba(255,255,255,0.4);
            border-radius: 2px;
            margin: 11px 0;
        }
        .brand-desc {
            color: rgba(255,255,255,0.65);
            font-size: 0.83rem;
            line-height: 1.55;
        }

        .features { position: relative; z-index: 1; }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            color: rgba(255,255,255,0.82);
            font-size: 0.84rem;
        }
        .feature-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,0.13);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .panel-footer {
            position: relative; z-index: 1;
            color: rgba(255,255,255,0.28);
            font-size: 0.70rem;
        }

        /* PANEL DERECHO */
        .panel-right {
            width: 58%;
            background: white;
            padding: 52px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #2a2a1e;
            margin-bottom: 4px;
        }
        .login-sub {
            color: #a09888;
            font-size: 0.88rem;
            margin-bottom: 34px;
        }

        .fgroup { margin-bottom: 20px; }
        .fgroup label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #5a5040;
            margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            color: var(--tea);
            font-size: 1rem;
        }
        .finput {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 2px solid var(--lavender);
            border-radius: 10px;
            font-size: 0.9rem;
            color: #2a2a1e;
            background: #fdf8f8;
            transition: all 0.2s;
            outline: none;
        }
        .finput:focus {
            border-color: var(--verde);
            background: white;
            box-shadow: 0 0 0 4px rgba(130,140,106,0.12);
            outline: none;
        }
        .pass-wrap .finput:focus { box-shadow: none; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--verde-dark), var(--verde));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(106,116,87,0.35);
        }
        .btn-login:active { transform: translateY(0); }

        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 22px 0;
            color: #d0c4c4;
            font-size: 0.78rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #ede5e5;
        }

        .register-link {
            text-align: center;
            font-size: 0.85rem;
            color: #9a8878;
        }
        .register-link a {
            color: var(--verde);
            font-weight: 700;
            text-decoration: none;
        }
        .register-link a:hover { text-decoration: underline; }

        .alert-error {
            background: #fdf0ef;
            border: 1px solid var(--coral);
            color: #7a2e2b;
            border-radius: 10px;
            padding: 11px 15px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pass-wrap { position: relative; display: flex; align-items: stretch; }
        .pass-wrap .pass-left-icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: var(--tea); font-size: 1rem; pointer-events: none; z-index: 1;
        }
        .pass-wrap .finput { border-radius: 10px 0 0 10px; border-right: none; flex: 1; }
        .toggle-pass {
            display: flex; align-items: center; justify-content: center;
            width: 46px; flex-shrink: 0;
            background: #fdf8f8;
            border: 2px solid var(--lavender); border-left: none;
            border-radius: 0 10px 10px 0;
            color: var(--verde-dark); cursor: pointer;
            font-size: 1.05rem; transition: all 0.2s;
        }
        .toggle-pass:hover { background: var(--lavender); color: var(--verde-dark); }
        .pass-wrap:focus-within .finput,
        .pass-wrap:focus-within .toggle-pass {
            border-color: var(--verde);
        }
        .pass-wrap:focus-within { box-shadow: 0 0 0 4px rgba(130,140,106,0.12); border-radius: 10px; }

        .forgot-link {
            display: block; text-align: right;
            font-size: 0.78rem; color: var(--verde);
            text-decoration: none; margin-top: 6px;
            font-weight: 600;
        }
        .forgot-link:hover { text-decoration: underline; }

        @media (max-width: 680px) {
            .login-card { flex-direction: column; width: 95%; }
            .panel-left  { width: 100%; padding: 30px 24px; min-height: auto; }
            .panel-right { width: 100%; padding: 30px 24px; }
            .features { display: none; }
        }
    </style>
</head>
<body>

<div class="bg-blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="blob blob-4"></div>
</div>

<div class="login-card">

    <!-- Panel izquierdo -->
    <div class="panel-left">
        <div class="brand-wrap">
            <div class="brand-name">TALENT<span>HUB</span></div>
            <div class="brand-line"></div>
            <div class="brand-desc">Sistema de Gestión de<br>Recursos Humanos</div>
        </div>

        <div class="features">
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                <span>Gestión de empleados y organigramas</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-calendar2-check"></i></div>
                <span>Control de licencias y ausencias</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <span>Evaluaciones de desempeño</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
                <span>Acceso seguro por roles</span>
            </div>
        </div>

        <div class="panel-footer">TalentHub &copy; 2026 &mdash; Todos los derechos reservados</div>
    </div>

    <!-- Panel derecho -->
    <div class="panel-right">
        <div class="login-title">Bienvenido</div>
        <div class="login-sub">Ingresá tus credenciales para acceder al sistema</div>

        <?php if ($error != "") { ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="fgroup">
                <label>Usuario</label>
                <div class="input-wrap">
                    <i class="bi bi-person"></i>
                    <input type="text" name="nombre_usuario" class="finput"
                           placeholder="Ingresá tu usuario" required autofocus>
                </div>
            </div>

            <div class="fgroup">
                <label>Contraseña</label>
                <div class="pass-wrap">
                    <i class="bi bi-lock pass-left-icon"></i>
                    <input type="password" id="inputPass" name="contrasena"
                           class="finput"
                           placeholder="Ingresá tu contraseña" required>
                    <button type="button" id="togglePass" class="toggle-pass" tabindex="-1"
                            aria-label="Mostrar contraseña">
                        <i class="bi bi-eye" id="togglePassIcon"></i>
                    </button>
                </div>
                <a href="recuperar.php" class="forgot-link">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-login">Ingresar al sistema</button>
        </form>

        <div class="divider">o</div>

        <div class="register-link">
            ¿Primera vez? <a href="registro.php">Creá tu cuenta con tu DNI</a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePass').addEventListener('click', function () {
    var input = document.getElementById('inputPass');
    var icon  = document.getElementById('togglePassIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
        this.setAttribute('aria-label', 'Ocultar contraseña');
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
        this.setAttribute('aria-label', 'Mostrar contraseña');
    }
});
</script>
</body>
</html>
