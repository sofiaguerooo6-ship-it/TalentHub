<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["id_usuario"])) {
    header("Location: /talenthub2/index.php");
    exit;
}

require_once '../config/Conexion.php';

$mensaje = "";
$tipo    = "";   // "exito" | "info"
$enviado = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $enviado = true;

    if ($email == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Ingresá un email válido.";
        $tipo    = "error";
    } else {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare(
            "SELECT u.nombre_usuario
             FROM usuario u
             JOIN empleado e ON u.legajo = e.legajo
             WHERE e.email = ? AND u.estado = 1
             LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $mensaje = "Encontramos tu cuenta. Tu nombre de usuario es: <strong>" .
                       htmlspecialchars($row["nombre_usuario"]) .
                       "</strong>.<br>Para restablecer tu contraseña contactá al administrador del sistema.";
            $tipo = "exito";
        } else {
            // Mensaje genérico para no revelar si el email existe o no
            $mensaje = "Si existe una cuenta asociada a ese email, recibirás las instrucciones en breve.";
            $tipo = "info";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TalentHub — Recuperar contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --verde:      #828C6A;
            --sage:       #A0AB89;
            --lavender:   #F6E5E7;
            --tea:        #EFC0BC;
            --coral:      #E69B97;
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

        .bg-blobs { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.45; animation: drift 10s ease-in-out infinite alternate; }
        .blob-1 { width: 500px; height: 500px; background: var(--tea);    top: -150px; left: -150px; animation-delay: 0s; }
        .blob-2 { width: 380px; height: 380px; background: var(--sage);   bottom: -100px; right: -80px; animation-delay: 3s; }
        .blob-3 { width: 280px; height: 280px; background: var(--coral);  top: 40%; left: 30%; animation-delay: 6s; }
        .blob-4 { width: 200px; height: 200px; background: var(--verde);  bottom: 10%; left: 10%; animation-delay: 2s; }
        @keyframes drift { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(30px,20px) scale(1.08); } }

        .card-wrap {
            position: relative;
            z-index: 1;
            display: flex;
            width: 880px;
            min-height: 480px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(106,116,87,0.28);
        }

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
            content: ''; position: absolute;
            width: 320px; height: 320px; border-radius: 50%;
            background: rgba(255,255,255,0.06); bottom: -120px; right: -100px;
        }
        .panel-left::after {
            content: ''; position: absolute;
            width: 180px; height: 180px; border-radius: 50%;
            background: rgba(255,255,255,0.05); top: -50px; left: -50px;
        }

        .brand-wrap { position: relative; z-index: 1; }
        .brand-name { font-size: 2.1rem; font-weight: 900; letter-spacing: 4px; color: white; }
        .brand-name span { color: var(--tea); }
        .brand-line { width: 36px; height: 3px; background: rgba(255,255,255,0.4); border-radius: 2px; margin: 11px 0; }
        .brand-desc { color: rgba(255,255,255,0.65); font-size: 0.83rem; line-height: 1.55; }

        .left-body { position: relative; z-index: 1; }
        .left-icon {
            width: 64px; height: 64px; border-radius: 18px;
            background: rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; color: white; margin-bottom: 18px;
        }
        .left-title { color: white; font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; }
        .left-text  { color: rgba(255,255,255,0.6); font-size: 0.82rem; line-height: 1.6; }

        .panel-footer { position: relative; z-index: 1; color: rgba(255,255,255,0.28); font-size: 0.70rem; }

        .panel-right {
            width: 58%;
            background: white;
            padding: 52px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .page-title { font-size: 1.65rem; font-weight: 800; color: #2a2a1e; margin-bottom: 4px; }
        .page-sub   { color: #a09888; font-size: 0.88rem; margin-bottom: 30px; }

        .fgroup { margin-bottom: 20px; }
        .fgroup label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #5a5040; margin-bottom: 7px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--tea); font-size: 1rem; }
        .finput {
            width: 100%; padding: 12px 14px 12px 40px;
            border: 2px solid var(--lavender); border-radius: 10px;
            font-size: 0.9rem; color: #2a2a1e; background: #fdf8f8;
            transition: all 0.2s; outline: none;
        }
        .finput:focus { border-color: var(--verde); background: white; box-shadow: 0 0 0 4px rgba(130,140,106,0.12); }

        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, var(--verde-dark), var(--verde));
            color: white; border: none; border-radius: 10px;
            font-size: 0.92rem; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; cursor: pointer; transition: all 0.2s; margin-top: 4px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(106,116,87,0.35); }

        .msg-box {
            border-radius: 12px; padding: 14px 18px;
            font-size: 0.88rem; margin-bottom: 22px;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .msg-exito { background: #edf3e4; border: 1px solid var(--sage); color: var(--verde-dark); }
        .msg-info  { background: #f0f4fd; border: 1px solid #b8c9f0; color: #2d4a8a; }
        .msg-error { background: #fdf0ef; border: 1px solid var(--coral); color: #7a2e2b; }
        .msg-box i { font-size: 1.1rem; margin-top: 1px; flex-shrink: 0; }

        .back-link { text-align: center; margin-top: 22px; font-size: 0.85rem; color: #9a8878; }
        .back-link a { color: var(--verde); font-weight: 700; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }

        @media (max-width: 680px) {
            .card-wrap { flex-direction: column; width: 95%; }
            .panel-left  { width: 100%; padding: 28px 24px; min-height: auto; }
            .panel-right { width: 100%; padding: 30px 24px; }
            .left-body { display: none; }
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

<div class="card-wrap">

    <!-- Panel izquierdo -->
    <div class="panel-left">
        <div class="brand-wrap">
            <div class="brand-name">TALENT<span>HUB</span></div>
            <div class="brand-line"></div>
            <div class="brand-desc">Sistema de Gestión de<br>Recursos Humanos</div>
        </div>

        <div class="left-body">
            <div class="left-icon"><i class="bi bi-key"></i></div>
            <div class="left-title">Recuperación de acceso</div>
            <div class="left-text">
                Ingresá el email asociado a tu cuenta.<br><br>
                Si encontramos tu usuario, te mostraremos el nombre de usuario con el que podés ingresar.
                Para resetear tu contraseña, contactá al administrador del sistema.
            </div>
        </div>

        <div class="panel-footer">TalentHub &copy; 2026 &mdash; Todos los derechos reservados</div>
    </div>

    <!-- Panel derecho -->
    <div class="panel-right">
        <div class="page-title">Recuperar acceso</div>
        <div class="page-sub">Ingresá tu email registrado en el sistema</div>

        <?php if ($enviado && $mensaje != "") { ?>
        <div class="msg-box msg-<?php echo $tipo; ?>">
            <?php if ($tipo == "exito") { ?>
                <i class="bi bi-check-circle-fill"></i>
            <?php } elseif ($tipo == "info") { ?>
                <i class="bi bi-info-circle-fill"></i>
            <?php } else { ?>
                <i class="bi bi-exclamation-circle-fill"></i>
            <?php } ?>
            <div><?php echo $mensaje; ?></div>
        </div>
        <?php } ?>

        <?php if (!$enviado || $tipo == "error") { ?>
        <form method="post">
            <div class="fgroup">
                <label>Email</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" class="finput"
                           placeholder="tu@email.com"
                           value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                           required autofocus>
                </div>
            </div>
            <button type="submit" class="btn-submit">Buscar mi cuenta</button>
        </form>
        <?php } ?>

        <div class="back-link">
            <a href="login.php">← Volver al inicio de sesión</a>
        </div>
    </div>

</div>

</body>
</html>
