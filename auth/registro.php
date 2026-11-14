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
$mensaje = "";
$paso = 1;
$empleado = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexion = Conexion::conectar();

    if (isset($_POST["dni"])) {
        // PASO 1 — verificar DNI
        $dni = trim($_POST["dni"]);
        $stmt = $conexion->prepare(
            "SELECT e.legajo, e.nombre, e.apellido, e.email
             FROM empleado e
             LEFT JOIN usuario u ON e.legajo = u.legajo
             WHERE e.dni = ? AND (u.legajo IS NULL OR u.estado = 0)"
        );
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            $error = "No se encontró ningún empleado con ese DNI o ya tiene una cuenta creada.";
        } else {
            $empleado = $resultado->fetch_assoc();
            $paso = 2;
        }

    } elseif (isset($_POST["legajo"])) {
        // PASO 2 — crear cuenta
        $legajo         = $_POST["legajo"];
        $nombre_usuario = trim($_POST["nombre_usuario"]);
        $contrasena     = $_POST["contrasena"];
        $confirmar      = $_POST["confirmar"];

        if (empty($nombre_usuario) || strlen($nombre_usuario) < 4) {
            $error = "El nombre de usuario debe tener al menos 4 caracteres.";
            $paso = 2;
            $stmt = $conexion->prepare("SELECT legajo, nombre, apellido, email FROM empleado WHERE legajo = ?");
            $stmt->bind_param("i", $legajo);
            $stmt->execute();
            $empleado = $stmt->get_result()->fetch_assoc();
        } elseif (empty($contrasena) || strlen($contrasena) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres.";
            $paso = 2;
            $stmt = $conexion->prepare("SELECT legajo, nombre, apellido, email FROM empleado WHERE legajo = ?");
            $stmt->bind_param("i", $legajo);
            $stmt->execute();
            $empleado = $stmt->get_result()->fetch_assoc();
        } elseif ($contrasena !== $confirmar) {
            $error = "Las contraseñas no coinciden.";
            $paso = 2;
            $stmt = $conexion->prepare("SELECT legajo, nombre, apellido, email FROM empleado WHERE legajo = ?");
            $stmt->bind_param("i", $legajo);
            $stmt->execute();
            $empleado = $stmt->get_result()->fetch_assoc();
        } else {
            // Verificar que el nombre de usuario no exista
            $stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE nombre_usuario = ?");
            $stmt->bind_param("s", $nombre_usuario);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = "Ese nombre de usuario ya está en uso. Elegí otro.";
                $paso = 2;
                $stmt2 = $conexion->prepare("SELECT legajo, nombre, apellido, email FROM empleado WHERE legajo = ?");
                $stmt2->bind_param("i", $legajo);
                $stmt2->execute();
                $empleado = $stmt2->get_result()->fetch_assoc();
            } else {
                $hash       = password_hash($contrasena, PASSWORD_DEFAULT);
                $fecha_alta = date("Y-m-d");

                // Verificar si hay un registro pendiente (rol pre-asignado por el admin)
                $stPend = $conexion->prepare(
                    "SELECT id_usuario FROM usuario WHERE legajo = ? AND estado = 0 LIMIT 1"
                );
                $stPend->bind_param("i", $legajo);
                $stPend->execute();
                $pendiente = $stPend->get_result()->fetch_assoc();

                if ($pendiente) {
                    // Activar el registro pendiente con el username y password elegidos
                    $stmt = $conexion->prepare(
                        "UPDATE usuario SET nombre_usuario = ?, contrasena = ?, estado = 1,
                         fecha_alta = ? WHERE id_usuario = ?"
                    );
                    $stmt->bind_param("sssi", $nombre_usuario, $hash, $fecha_alta, $pendiente["id_usuario"]);
                } else {
                    // Sin pre-asignación: crear con rol EMPLEADO por defecto
                    $stRol = $conexion->prepare("SELECT id_rol FROM rol WHERE nombre_rol = 'EMPLEADO'");
                    $stRol->execute();
                    $id_rol = $stRol->get_result()->fetch_assoc()["id_rol"];

                    $stmt = $conexion->prepare(
                        "INSERT INTO usuario (legajo, id_rol, nombre_usuario, contrasena, estado, fecha_alta)
                         VALUES (?, ?, ?, ?, 1, ?)"
                    );
                    $stmt->bind_param("iisss", $legajo, $id_rol, $nombre_usuario, $hash, $fecha_alta);
                }

                if ($stmt->execute()) {
                    $mensaje = "Cuenta creada correctamente. Ya podés iniciar sesión.";
                    $paso = 3;
                } else {
                    $error = "Error al crear la cuenta. Intentá de nuevo.";
                    $paso = 2;
                }
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
    <title>TalentHub - Crear cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --verde: #828C6A; --sage: #A0AB89;
            --lavender: #F6E5E7; --tea: #EFC0BC;
            --coral: #E69B97; --verde-dark: #6B7457;
        }
        body {
            margin: 0; min-height: 100vh; display: flex;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: var(--lavender);
        }
        .lado-izquierdo {
            width: 45%;
            background: linear-gradient(160deg, var(--verde-dark) 0%, var(--verde) 50%, var(--sage) 100%);
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            padding: 60px; color: white;
            position: relative; overflow: hidden;
        }
        .lado-izquierdo::before {
            content: ''; position: absolute;
            width: 280px; height: 280px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -80px; right: -80px;
        }
        .lado-izquierdo h1 {
            font-size: 2.6rem; font-weight: 900;
            letter-spacing: 4px; margin-bottom: 10px;
            position: relative; z-index: 1;
        }
        .lado-izquierdo h1 span { color: var(--tea); }
        .lado-izquierdo p {
            font-size: 1rem; opacity: 0.80;
            max-width: 280px; text-align: center;
            line-height: 1.6; position: relative; z-index: 1;
        }
        .separador {
            width: 40px; height: 3px;
            background: rgba(255,255,255,0.4);
            border-radius: 2px; margin: 16px auto;
            position: relative; z-index: 1;
        }
        .lado-derecho {
            width: 55%; display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            padding: 60px 50px; background: white;
        }
        .registro-box { width: 100%; max-width: 400px; }
        .registro-box h2 {
            font-size: 1.55rem; font-weight: 800;
            color: var(--text-dark, #3D3D2E); margin-bottom: 6px;
        }
        .subtitulo { color: #a09888; font-size: 0.88rem; margin-bottom: 24px; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #5a5040; }
        .form-control {
            border-radius: 8px; border-color: var(--lavender);
            padding: 10px 14px; font-size: 0.9rem; background: #fdf8f8;
        }
        .form-control:focus {
            border-color: var(--verde);
            box-shadow: 0 0 0 3px rgba(130,140,106,0.15);
            background: white;
        }
        .btn-registro {
            background: linear-gradient(135deg, var(--verde-dark), var(--verde));
            border: none; border-radius: 9px;
            padding: 12px; font-size: 0.92rem; font-weight: 700;
            width: 100%; color: white; margin-top: 8px;
            cursor: pointer; transition: all 0.2s;
            letter-spacing: 0.5px;
        }
        .btn-registro:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(106,116,87,0.3);
        }
        .link-login { font-size: 0.85rem; color: #9a8878; text-align: center; margin-top: 20px; }
        .link-login a { color: var(--verde); text-decoration: none; font-weight: 700; }
        .link-login a:hover { text-decoration: underline; }
        .empleado-card {
            background: #f0f5e8; border-radius: 10px;
            padding: 14px 18px; margin-bottom: 20px;
            font-size: 0.9rem; color: var(--verde-dark);
            border-left: 4px solid var(--sage);
        }
        .paso-indicator { display: flex; gap: 8px; margin-bottom: 24px; }
        .paso-dot {
            width: 10px; height: 10px;
            border-radius: 50%; background: var(--tea);
        }
        .paso-dot.activo { background: var(--verde); }
        .copyright { margin-top: 28px; font-size: 0.72rem; color: #b0a8a0; text-align: center; }
        .alert-success {
            background: #e8edd8; border-color: var(--sage); color: var(--verde-dark);
        }
        .alert-danger {
            background: #fce9e8; border-color: var(--coral); color: #7a2e2b;
        }
        .pass-wrap { display: flex; align-items: stretch; }
        .pass-wrap .form-control { border-radius: 8px 0 0 8px; border-right: none; flex: 1; }
        .toggle-pass {
            display: flex; align-items: center; justify-content: center;
            width: 44px; flex-shrink: 0;
            background: #fdf8f8;
            border: 1px solid var(--lavender); border-left: none;
            border-radius: 0 8px 8px 0;
            color: var(--verde-dark); cursor: pointer; font-size: 1.05rem;
            transition: background 0.2s;
        }
        .toggle-pass:hover { background: var(--lavender); }
        .pass-wrap:focus-within .form-control,
        .pass-wrap:focus-within .toggle-pass {
            border-color: var(--verde);
        }
        .form-control::placeholder { color: #c0b0a8; font-style: italic; }
        @media (max-width: 640px) {
            .lado-izquierdo { display: none; }
            .lado-derecho { width: 100%; padding: 40px 24px; }
        }
    </style>
</head>
<body>

<div class="lado-izquierdo">
    <h1>TALENT<span>HUB</span></h1>
    <div class="separador"></div>
    <p>Creá tu cuenta con tu DNI de empleado y accedé al sistema.</p>
</div>

<div class="lado-derecho">
    <div class="registro-box">

        <?php if ($paso == 3) { ?>
            <h2>Cuenta creada</h2>
            <p class="subtitulo">Tu cuenta fue creada correctamente.</p>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $mensaje; ?>
            </div>
            <a href="login.php" class="btn-registro text-center d-block text-decoration-none mt-3">
                Ir al inicio de sesión
            </a>

        <?php } elseif ($paso == 2 && $empleado) { ?>
            <h2>Crear cuenta</h2>
            <p class="subtitulo">Completá tus datos de acceso</p>

            <div class="paso-indicator">
                <div class="paso-dot activo"></div>
                <div class="paso-dot activo"></div>
            </div>

            <div class="empleado-card">
                <strong><?php echo $empleado["nombre"] . " " . $empleado["apellido"]; ?></strong><br>
                <small><?php echo $empleado["email"]; ?></small>
            </div>

            <?php if ($error != "") { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form method="post">
                <input type="hidden" name="legajo" value="<?php echo $empleado["legajo"]; ?>">
                <div class="mb-3">
                    <label class="form-label">Nombre de usuario</label>
                    <input type="text" name="nombre_usuario" class="form-control"
                           placeholder="Mínimo 4 caracteres" minlength="4" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <div class="pass-wrap">
                        <input type="password" id="reg_pass1" name="contrasena" class="form-control"
                               placeholder="Mínimo 6 caracteres" minlength="6" required>
                        <button type="button" class="toggle-pass" onclick="toggleVer('reg_pass1','ico1')" tabindex="-1">
                            <i class="bi bi-eye" id="ico1"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <div class="pass-wrap">
                        <input type="password" id="reg_pass2" name="confirmar" class="form-control"
                               placeholder="Repetí la contraseña" required>
                        <button type="button" class="toggle-pass" onclick="toggleVer('reg_pass2','ico2')" tabindex="-1">
                            <i class="bi bi-eye" id="ico2"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-registro">Crear cuenta</button>
            </form>

        <?php } else { ?>
            <h2>Crear cuenta</h2>
            <p class="subtitulo">Ingresá tu DNI para verificar que sos empleado</p>

            <div class="paso-indicator">
                <div class="paso-dot activo"></div>
                <div class="paso-dot"></div>
            </div>

            <?php if ($error != "") { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-control"
                           placeholder="Ingresá tu DNI sin puntos" maxlength="8" required autofocus>
                </div>
                <button type="submit" class="btn-registro">Verificar DNI</button>
            </form>
        <?php } ?>

        <p class="link-login">
            ¿Ya tenés cuenta? <a href="login.php">Iniciá sesión</a>
        </p>

        <p class="copyright">TalentHub &copy; 2026 — Sistema de RRHH</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleVer(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>