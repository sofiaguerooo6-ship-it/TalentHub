<?php
// Archivo de EJEMPLO. Copialo como config/Conexion.php y completá tus datos de MySQL.
// config/Conexion.php está en .gitignore para no subir credenciales al repositorio.
//
// Patrón Singleton: garantiza una sola conexión MySQLi por request HTTP.
// Todos los modelos usan Conexion::conectar() y reciben la misma instancia.
// IMPORTANTE: por eso cada $stmt debe cerrarse con $stmt->close() antes de la siguiente
// query — de lo contrario MySQLi lanza "Commands out of sync".
class Conexion {
    private static $instancia = null;

    private const DB_HOST = "localhost";
    private const DB_USER = "root";
    private const DB_PASS = "";
    private const DB_NAME = "talenthub2";

    public static function conectar() {
        if (self::$instancia === null) {
            $conexion = new mysqli(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_NAME);
            if ($conexion->connect_error) {
                die("Error de conexión: " . $conexion->connect_error);
            }
            $conexion->set_charset("utf8mb4");
            self::$instancia = $conexion;
        }
        return self::$instancia;
    }
}
?>
