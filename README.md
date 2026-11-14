# TalentHub 👥

**Sistema web de gestión de Recursos Humanos** desarrollado en PHP con arquitectura MVC y MySQL.
Permite administrar empleados, la estructura organizacional, el historial de cargos, las solicitudes de licencia y las evaluaciones de desempeño, con control de acceso según el rol de cada usuario.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL%20%2F%20MariaDB-10.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

---

## 🛠️ Tecnologías

| Capa | Tecnología |
|---|---|
| Backend | PHP 8 (MySQLi con consultas preparadas, sesiones) |
| Base de datos | MySQL / MariaDB (triggers, funciones, procedimientos almacenados y vistas) |
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5.3, Bootstrap Icons |
| Entorno | XAMPP (Apache + MariaDB + phpMyAdmin) |
| Arquitectura | MVC (modelos, vistas, controladores) + Singleton para la conexión |

## ✨ Funcionalidades principales

- **Autenticación segura**: inicio de sesión con contraseñas hasheadas (`password_hash` / `password_verify`), cierre de sesión y verificación de sesión en cada página.
- **Registro en dos pasos**: el empleado valida su DNI (debe estar cargado previamente por RR.HH.) y luego crea su usuario y contraseña. Si el administrador le preasignó un rol, se activa ese registro; si no, se crea con rol `EMPLEADO`.
- **Recuperación de cuenta** por email, con mensaje genérico para no revelar si el email existe.
- **Dashboard** con indicadores: empleados, presentes hoy, de licencia hoy, solicitudes pendientes, evaluaciones registradas y departamentos (con detalle en ventanas modales).
- **Gestión de empleados**: alta, edición, baja, ficha completa (antigüedad, cargo actual, promedio de evaluaciones, direcciones) y vista de **equipos**.
- **Estructura organizacional**: provincias, localidades, departamentos, niveles jerárquicos y cargos.
- **Historial de cargos**: asignación, edición y cierre de cargos, con supervisores asociados.
- **Licencias**: solicitud, consulta, cambio de estado (aprobación/rechazo mediante procedimiento almacenado) y registro de documentos adjuntos (por ejemplo, certificados médicos).
- **Evaluaciones de desempeño**: alta, edición y consulta.
- **Auditoría** de los cambios de estado de las solicitudes de licencia.
- **Administración de usuarios**: activar, desactivar y eliminar cuentas.
- **Catálogos configurables**: tipos de licencia, tipos de documento y estados de solicitud.

## 🔐 Roles del sistema

| Rol | Acceso |
|---|---|
| **ADMINISTRADOR** | Acceso total: estructura organizacional, empleados, equipos, historial, licencias, evaluaciones, usuarios, auditoría y catálogos. |
| **SUPERVISOR** | Empleados, historial de cargos, licencias y evaluaciones **de su equipo**, además de su perfil, sus licencias y sus evaluaciones. |
| **EMPLEADO** | Su perfil, sus licencias (puede solicitarlas) y sus evaluaciones. |

El acceso se controla con `verificarRol([...])` en cada vista. Si un usuario intenta entrar a una sección no permitida, se lo redirige a una página de acceso denegado.

## 🗄️ Base de datos

Base `talenthub2`, normalizada, con lógica de negocio implementada directamente en el motor:

- **19 tablas**: `empleado`, `empleado_direccion`, `usuario`, `rol`, `cargo`, `nivel_jerarquico`, `departamento`, `departamento_localidad`, `localidad`, `provincia`, `historial_cargo`, `supervisor_historial`, `solicitud_licencia`, `tipo_licencia`, `estado_solicitud`, `documento_adjunto`, `tipo_documento`, `evaluacion_desempeno` y `auditoria_estado`.
- **4 vistas**:
  - `v_empleado_detallado`: datos del empleado con su dirección completa.
  - `ve_organigramaactual`: cargo, nivel, departamento y supervisor vigentes.
  - `ve_reportelicencias`: reporte de solicitudes de licencia.
  - `vista_dashboard`: totales para el panel principal.
- **4 triggers**:
  - `trg_validar_cargo_unico_activo`: impide asignar un cargo nuevo si el empleado ya tiene uno activo.
  - `trg_validar_solapamiento_licencia`: impide cargar licencias superpuestas en fechas.
  - `trg_auditoria_nueva_solicitud`: registra en la auditoría cada nueva solicitud.
  - `trg_desactivar_usuario_al_eliminar_empleado`: da de baja el usuario asociado al eliminar un empleado.
- **5 funciones almacenadas**: `fn_antiguedad_anios`, `fn_cargo_actual`, `fn_dias_licencia_usados`, `fn_promedio_evaluacion` y `fn_tiene_licencia_pendiente`.
- **1 procedimiento almacenado**: `sp_cambiar_estado_licencia`, que cambia el estado de una solicitud y deja registro en la auditoría.

El script `database/talenthub2.sql` crea la base completa con datos de prueba ficticios.

## 📁 Estructura del proyecto

```
talenthub2/
├── index.php                 # Dashboard principal
├── acceso_denegado.php
├── auth/                     # Login, logout, registro, recuperación, verificación de sesión y rol
├── config/
│   ├── Conexion.example.php  # Plantilla de conexión (copiar como Conexion.php)
│   └── Conexion.php          # (no versionado) credenciales locales
├── controladores/            # Lógica de cada entidad (Empleado, Cargo, Licencia, etc.)
├── modelos/                  # Acceso a datos con MySQLi y consultas preparadas
├── vistas/                   # Pantallas por módulo
│   ├── auditoria/  cargos/  departamentos/  empleados/
│   ├── estados_solicitud/  evaluaciones/  historial/  licencias/
│   ├── localidades/  niveles/  provincias/
│   └── tipos_documento/  tipos_licencia/  usuarios/
├── includes/                 # header (menú lateral según rol) y footer
└── database/
    └── talenthub2.sql        # Estructura + datos de prueba
```

## 🚀 Instalación con XAMPP

1. **Instalar [XAMPP](https://www.apachefriends.org/)** e iniciar **Apache** y **MySQL** desde el panel de control.

2. **Clonar el repositorio dentro de `htdocs`** con el nombre de carpeta `talenthub2`. Las rutas internas del sistema usan `/talenthub2/`, así que la carpeta tiene que llamarse así:
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/sofiaguerooo6-ship-it/TalentHub.git talenthub2
   ```

3. **Importar la base de datos**:
   - Abrir [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
   - Ir a **Importar**, elegir `database/talenthub2.sql` y ejecutar. El script crea la base `talenthub2` automáticamente.

4. **Configurar la conexión**: copiar el archivo de ejemplo y ajustar los datos si tu MySQL usa otro usuario o contraseña.
   ```bash
   copy config\Conexion.example.php config\Conexion.php
   ```
   Valores por defecto de XAMPP: host `localhost`, usuario `root`, contraseña vacía, base `talenthub2`.

5. **Abrir el sistema** en [http://localhost/talenthub2](http://localhost/talenthub2).

## 👩‍💻 Autora

**Sofía Agüero**
Estudiante de la Licenciatura en Sistemas de Información, Universidad Champagnat.
