<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TalentHub</title>
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
            --text-dark:  #3D3D2E;
            --sidebar-w:  230px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 0.92rem;
            background: var(--lavender);
            min-height: 100vh;
        }

        /* ─── LAYOUT ─── */
        .page-wrapper { display: flex; min-height: 100vh; }

        /* ─── SIDEBAR IZQUIERDO ─── */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--verde);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            z-index: 100;
            box-shadow: 4px 0 24px rgba(0,0,0,0.13);
        }

        /* BRAND */
        .sidebar-brand {
            padding: 24px 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.13);
            text-decoration: none;
            display: block;
        }
        .brand-logo { font-size: 1.45rem; font-weight: 900; letter-spacing: 3px; color: white; }
        .brand-logo span { color: var(--tea); }
        .brand-sub { font-size: 0.60rem; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 1.5px; margin-top: 3px; }

        /* NAV — solo esta sección hace scroll */
        .sidebar-nav { flex: 1; padding: 10px 0; overflow-y: auto; min-height: 0; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
        .nav-section {
            font-size: 0.59rem; text-transform: uppercase; letter-spacing: 2px;
            color: rgba(255,255,255,0.35); padding: 14px 18px 5px; font-weight: 700;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 18px; color: rgba(255,255,255,0.72); text-decoration: none;
            font-size: 0.83rem; font-weight: 500; transition: all 0.18s;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,0.10); color: white; border-left-color: var(--tea);
        }
        .sidebar-link i { font-size: 0.95rem; width: 18px; text-align: center; flex-shrink: 0; }

        /* USER BLOCK — siempre visible al fondo */
        .sidebar-user {
            padding: 14px 18px 16px;
            border-top: 1px solid rgba(255,255,255,0.13);
            background: rgba(0,0,0,0.15);
            flex-shrink: 0;
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--coral); color: white; font-weight: 700;
            font-size: 0.85rem; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }
        .user-name { font-size: 0.82rem; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role-badge {
            display: inline-block; font-size: 0.59rem;
            background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);
            padding: 1px 7px; border-radius: 20px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            margin-top: 12px; padding: 9px 14px;
            background: rgba(230, 155, 151, 0.2);
            border: 1px solid rgba(230, 155, 151, 0.4);
            border-radius: 8px; color: #f2c4c2;
            text-decoration: none; font-size: 0.82rem;
            font-weight: 600; transition: all 0.2s;
            width: 100%; justify-content: center;
        }
        .btn-logout:hover {
            background: var(--coral); color: white;
            border-color: var(--coral);
        }

        /* ─── CONTENIDO PRINCIPAL ─── */
        .main-content { flex: 1; min-width: 0; padding: 32px 36px; }

        /* ─── COMPONENTES ─── */
        h2 { color: var(--text-dark); font-weight: 700; font-size: 1.35rem; }
        h4 { color: var(--text-dark); font-weight: 600; font-size: 1.05rem; }

        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 22px; padding-bottom: 12px; border-bottom: 2px solid var(--tea);
        }

        .card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(130,140,106,0.10); background: white; }

        /* TABLAS */
        .table thead th {
            background-color: var(--verde); color: white; font-weight: 600;
            font-size: 0.80rem; text-transform: uppercase; letter-spacing: 0.5px;
            border: none; padding: 12px 16px;
        }
        .table tbody td { padding: 10px 16px; vertical-align: middle; border-color: #f0e8e8; font-size: 0.88rem; }
        .table tbody tr:hover { background-color: #fdf5f5; }

        /* BOTONES */
        .btn-primary { background-color: var(--verde); border-color: var(--verde); font-size: 0.88rem; border-radius: 8px; padding: 7px 16px; color: white; }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--verde-dark); border-color: var(--verde-dark); color: white; }
        .btn-secondary { background-color: var(--sage); border-color: var(--sage); color: white; font-size: 0.88rem; border-radius: 8px; padding: 7px 16px; }
        .btn-secondary:hover { background-color: var(--verde); border-color: var(--verde); color: white; }
        .btn-warning { font-size: 0.80rem; border-radius: 6px; padding: 4px 10px; }
        .btn-danger  { font-size: 0.80rem; border-radius: 6px; padding: 4px 10px; }
        .btn-info { font-size: 0.80rem; border-radius: 6px; padding: 4px 10px; color: white; background-color: var(--sage); border-color: var(--sage); }
        .btn-info:hover { background-color: var(--verde); border-color: var(--verde); color: white; }
        .btn-success { font-size: 0.80rem; border-radius: 6px; padding: 4px 10px; }

        /* FORMULARIOS */
        .form-label { font-weight: 600; color: var(--text-dark); font-size: 0.87rem; }
        .form-control, .form-select { border-radius: 8px; border-color: var(--tea); font-size: 0.9rem; }
        .form-control:focus, .form-select:focus { border-color: var(--verde); box-shadow: 0 0 0 3px rgba(130,140,106,0.18); }
        .form-control::placeholder { color: #c0b0a8; font-style: italic; font-size: 0.86rem; }
        .form-control::-webkit-input-placeholder { color: #c0b0a8; font-style: italic; }
        .form-control:-ms-input-placeholder { color: #c0b0a8; font-style: italic; }

        /* ALERTAS */
        .alert { border-radius: 10px; font-size: 0.9rem; }
        .alert-success { background: #e8edd8; border-color: var(--sage); color: var(--verde-dark); }
        .alert-danger  { background: #fce9e8; border-color: var(--coral); color: #7a2e2b; }

        .badge { font-size: 0.76rem; padding: 4px 10px; border-radius: 20px; }
        .badge.bg-primary   { background-color: var(--verde) !important; }
        .badge.bg-secondary { background-color: var(--sage)  !important; }
        .badge.bg-info      { background-color: var(--sage)  !important; }

        /* MOBILE */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 99; }
        .sidebar-toggle {
            display: none; position: fixed; bottom: 20px; left: 20px; z-index: 200;
            background: var(--verde); color: white; border: none; border-radius: 50%;
            width: 50px; height: 50px; font-size: 1.3rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2); cursor: pointer;
            align-items: center; justify-content: center;
        }
        @media (max-width: 768px) {
            .sidebar-toggle { display: flex; }
            .sidebar { position: fixed; left: -100%; top: 0; height: 100vh; transition: left 0.3s ease; }
            .sidebar.open { left: 0; }
            .sidebar-overlay.open { display: block; }
            .main-content { padding: 20px 16px; }
        }
    </style>
</head>
<body>

<div class="page-wrapper">

<!-- ─── SIDEBAR IZQUIERDO ─── -->
<?php if (isset($_SESSION["rol"])) { ?>
<aside class="sidebar">

    <a class="sidebar-brand" href="/talenthub2/index.php">
        <div class="brand-logo">TALENT<span>HUB</span></div>
        <div class="brand-sub">Recursos Humanos</div>
    </a>

    <nav class="sidebar-nav">
        <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>

        <div class="nav-section">Organización</div>
        <a class="sidebar-link" href="/talenthub2/vistas/provincias/listar.php"><i class="bi bi-geo-alt"></i> Provincias</a>
        <a class="sidebar-link" href="/talenthub2/vistas/localidades/listar.php"><i class="bi bi-map"></i> Localidades</a>
        <a class="sidebar-link" href="/talenthub2/vistas/niveles/listar.php"><i class="bi bi-layers"></i> Niveles</a>
        <a class="sidebar-link" href="/talenthub2/vistas/departamentos/listar.php"><i class="bi bi-building"></i> Departamentos</a>
        <a class="sidebar-link" href="/talenthub2/vistas/cargos/listar.php"><i class="bi bi-briefcase"></i> Cargos</a>

        <div class="nav-section">Personal</div>
        <a class="sidebar-link" href="/talenthub2/vistas/empleados/listar.php"><i class="bi bi-people"></i> Empleados</a>
        <a class="sidebar-link" href="/talenthub2/vistas/empleados/equipos.php"><i class="bi bi-diagram-3"></i> Equipos</a>
        <a class="sidebar-link" href="/talenthub2/vistas/historial/listar.php"><i class="bi bi-clock-history"></i> Historial</a>
        <a class="sidebar-link" href="/talenthub2/vistas/licencias/listar.php"><i class="bi bi-calendar3"></i> Licencias</a>
        <a class="sidebar-link" href="/talenthub2/vistas/evaluaciones/listar.php"><i class="bi bi-bar-chart"></i> Evaluaciones</a>

        <div class="nav-section">Sistema</div>
        <a class="sidebar-link" href="/talenthub2/vistas/usuarios/listar.php"><i class="bi bi-person-gear"></i> Usuarios</a>
        <a class="sidebar-link" href="/talenthub2/vistas/auditoria/listar.php"><i class="bi bi-journal-text"></i> Auditoría</a>

        <div class="nav-section">Configuración</div>
        <a class="sidebar-link" href="/talenthub2/vistas/tipos_licencia/listar.php"><i class="bi bi-card-list"></i> Tipos de licencia</a>
        <a class="sidebar-link" href="/talenthub2/vistas/tipos_documento/listar.php"><i class="bi bi-file-earmark-text"></i> Tipos de documento</a>
        <a class="sidebar-link" href="/talenthub2/vistas/estados_solicitud/listar.php"><i class="bi bi-toggle-on"></i> Estados de solicitud</a>

        <?php } elseif ($_SESSION["rol"] == "SUPERVISOR") { ?>

        <div class="nav-section">Mi Equipo</div>
        <a class="sidebar-link" href="/talenthub2/vistas/empleados/listar.php"><i class="bi bi-people"></i> Empleados</a>
        <a class="sidebar-link" href="/talenthub2/vistas/historial/listar.php"><i class="bi bi-clock-history"></i> Historial</a>
        <a class="sidebar-link" href="/talenthub2/vistas/licencias/listar.php"><i class="bi bi-calendar3"></i> Licencias del equipo</a>
        <a class="sidebar-link" href="/talenthub2/vistas/evaluaciones/listar.php"><i class="bi bi-bar-chart"></i> Evaluaciones del equipo</a>

        <div class="nav-section">Mi Espacio</div>
        <a class="sidebar-link" href="/talenthub2/vistas/empleados/mi_perfil.php"><i class="bi bi-person-circle"></i> Mi Perfil</a>
        <a class="sidebar-link" href="/talenthub2/vistas/licencias/listar.php?personal=1"><i class="bi bi-calendar3"></i> Mis Licencias</a>
        <a class="sidebar-link" href="/talenthub2/vistas/evaluaciones/listar.php?personal=1"><i class="bi bi-bar-chart"></i> Mis Evaluaciones</a>

        <?php } elseif ($_SESSION["rol"] == "EMPLEADO") { ?>

        <div class="nav-section">Mi Espacio</div>
        <a class="sidebar-link" href="/talenthub2/vistas/empleados/mi_perfil.php"><i class="bi bi-person-circle"></i> Mi Perfil</a>
        <a class="sidebar-link" href="/talenthub2/vistas/licencias/listar.php"><i class="bi bi-calendar3"></i> Mis Licencias</a>
        <a class="sidebar-link" href="/talenthub2/vistas/evaluaciones/listar.php"><i class="bi bi-bar-chart"></i> Mis Evaluaciones</a>

        <?php } ?>
    </nav>

    <?php if (isset($_SESSION["id_usuario"])) { ?>
    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-2 mb-1">
            <div class="user-avatar">
                <?php
                $ini1 = strtoupper(mb_substr($_SESSION["nombre"] ?? $_SESSION["nombre_usuario"] ?? '?', 0, 1));
                $ini2 = strtoupper(mb_substr($_SESSION["apellido"] ?? '', 0, 1));
                echo $ini1 . $ini2;
                ?>
            </div>
            <div style="min-width:0; flex:1;">
                <div class="user-name">
                    <?php
                    $nombre_display = trim(($_SESSION["nombre"] ?? '') . ' ' . ($_SESSION["apellido"] ?? ''));
                    echo htmlspecialchars($nombre_display ?: $_SESSION["nombre_usuario"]);
                    ?>
                </div>
                <div class="user-role-badge"><?php echo htmlspecialchars($_SESSION["rol"]); ?></div>
            </div>
        </div>
        <a class="btn-logout" href="/talenthub2/auth/logout.php">
            <i class="bi bi-box-arrow-left"></i> Cerrar sesión
        </a>
    </div>
    <?php } ?>

</aside>
<?php } ?>

<!-- ─── CONTENIDO PRINCIPAL ─── -->
<div class="main-content">
