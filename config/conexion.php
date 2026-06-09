<?php
session_start();
class Conectar {
    protected $dbh;
    protected function Conexion() {
        if ($this->dbh instanceof PDO) {
            return $this->dbh;
        }

        try {
            // Cambiar los valores según tu configuración de PostgreSQL   192.168.0.200  masterd_asft
            $host = "172.16.5.2";
            $dbname = "compras";
            $usuario = "postgres";
            $contrasena = "masterd_asft";

            $conectar = $this->dbh = new PDO("pgsql:host=$host;port=5432;dbname=$dbname;connect_timeout=10", $usuario, $contrasena);
            return $conectar;
        } catch (PDOException $e) {
            if (isset($_GET['op'])) {
                error_log("Error BD: " . $e->getMessage());
                http_response_code(500);
                if (!headers_sent()) {
                    header('Content-Type: application/json; charset=utf-8');
                }
                echo json_encode([
                    "status" => "error",
                    "message" => "Error de conexion a la base de datos."
                ]);
                die();
            }

            print "¡Error BD!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function set_names() {
        return $this->dbh->query("SET NAMES 'utf8'");
    }
    public function getConexion() {
        return $this->Conexion(); // Llama al método protegido
    }
    public static function ruta() {
        return "http://localhost/compras/";
        //return "http://181.204.219.154:3396/compras/";
    }
}
