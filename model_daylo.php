<?php
class ModelDaylo {
    private $pdo;

    
    public function __construct($host, $db, $user, $pass, $port = 3306) {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }
        
/*
    public function __construct($host, $db, $user, $pass) {
        $this->mysqli = new mysqli($host, $user, $pass, $db);

        if ($this->mysqli->connect_error) {
            die("Error de conexión: " . $this->mysqli->connect_error);
        }

        // Establece el charset a utf8mb4
        if (!$this->mysqli->set_charset("utf8mb4")) {
            die("Error al establecer el conjunto de caracteres: " . $this->mysqli->error);
        }
    }
*/
    public function obtenerTiposFormacion($idioma='es') {
        $sql = "SELECT 
                    tf.id, 
                    tf.clave, 
                    tf.icono,
                    tf.imagen_cabecera,
                    tf.estado,
                    tft.titulo, 
                    tft.subtitulo, 
                    tft.descripcion_html, 
                    tft.pie_html,
                    tft.descripcion_larga,
                    tft.estado
                FROM tipo_formacion tf
                INNER JOIN tipo_formacion_traduccion tft
                    ON tf.id = tft.id_tipo_formacion
                WHERE tft.idioma = :idioma
                AND tf.estado = 'A'
                AND tft.estado = 'A'
                ORDER BY tf.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idioma', $idioma);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerFormaciones($idioma = 'es') {
        $sql = "SELECT 
                     f.id, 
                     f.tipo_formacion_id, 
                     f.slug, 
                     f.imagen, 
                     f.fecha_inicio, 
                     f.duracion, 
                     f.horarios, 
                     f.dias_cursado, 
                     f.carga_horaria, 
                     f.recurso_pdf, 
                     f.recurso_imagen, 
                     f.destacado, 
                     f.imagen_cabecera, 
                     f.estado,
                     ft.titulo, 
                     ft.descripcion, 
                     ft.boton
                FROM formacion f
                LEFT JOIN formacion_trad ft ON f.id = ft.formacion_id AND ft.idioma_id = ?
                WHERE f.estado = 'A'
                ORDER BY f.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idioma]);

        return $stmt->fetchAll();
    }     public function obtenerFormacionesPorTipo($tipo_id, $idioma = 'es') { //cambiarlo similo imagen
        $sql = "SELECT 
                    f.id, 
                    f.slug, 
                    f.imagen, 
                    f.fecha_inicio, 
                    f.duracion,
                    f.horarios,
                    f.dias_cursado,
                    f.carga_horaria, 
                    f.recurso_pdf, 
                    f.recurso_imagen,
                    f.destacado,
                    COALESCE(ft.titulo, f.slug, CONCAT('Curso ID ', f.id)) as titulo, 
                    COALESCE(ft.descripcion, 'Formación profesional certificada') as descripcion, 
                    COALESCE(ft.boton, 'Ver más') as boton,
                    f.estado as estado
                FROM formacion f
                LEFT JOIN formacion_trad ft ON f.id = ft.formacion_id AND ft.idioma_id = ?
                WHERE f.tipo_formacion_id = ?
                ORDER BY f.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idioma, $tipo_id]);
        return $stmt->fetchAll();
      
    }

    public function obtenerFormacionesTraduccion($idioma='es') {
        $sql = "SELECT 
                    id, 
                    formacion_id, 
                    idioma_id, 
                    titulo, 
                    descripcion, 
                    boton, 
                    estado
                FROM formacion_trad
                WHERE idioma_id = :idioma
                ORDER BY id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idioma', $idioma);
        $stmt->execute();

        return $stmt->fetchAll();
    }

// 👇 NUEVO MÉTODO: CREAR FORMACION (CRUD: Create)
    public function crearFormacion($tipo_formacion_id, $slug, $imagen, $fecha_inicio, $duracion, $horarios, $dias_cursado, $carga_horaria, $recurso_pdf, $recurso_imagen, $destacado, $imagen_cabecera, $estado) {
        $sql = "INSERT INTO formacion (
                    tipo_formacion_id, slug, imagen, fecha_inicio, duracion, horarios, dias_cursado, 
                    carga_horaria, recurso_pdf, recurso_imagen, destacado, imagen_cabecera, estado)
                VALUES (
                    :tipo_formacion_id, :slug, :imagen, :fecha_inicio, :duracion, :horarios, :dias_cursado,
                    :carga_horaria, :recurso_pdf, :recurso_imagen, :destacado, :imagen_cabecera, :estado)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':tipo_formacion_id' => $tipo_formacion_id,
            ':slug' => $slug,
            ':imagen' => $imagen,
            ':fecha_inicio' => $fecha_inicio,
            ':duracion' => $duracion,
            ':horarios' => $horarios,
            ':dias_cursado' => $dias_cursado,
            ':carga_horaria' => $carga_horaria,
            ':recurso_pdf' => $recurso_pdf,
            ':recurso_imagen' => $recurso_imagen,
            ':destacado' => $destacado,
            ':imagen_cabecera' => $imagen_cabecera,
            ':estado' => $estado
        ]);
        return $this->pdo->lastInsertId();
    }

     // 👇 ACTUALIZAR FORMACION (UPDATE)
    public function actualizarFormacion($id, $slug, $imagen, $fecha_inicio, $duracion, $horarios, $dias_cursado, $carga_horaria, $recurso_pdf, $recurso_imagen, $destacado, $imagen_cabecera, $estado) {
        $sql = "UPDATE formacion SET 
                    slug = :slug, 
                    imagen = :imagen, 
                    fecha_inicio = :fecha_inicio, 
                    duracion = :duracion, 
                    horarios = :horarios, 
                    dias_cursado = :dias_cursado, 
                    carga_horaria = :carga_horaria, 
                    recurso_pdf = :recurso_pdf, 
                    recurso_imagen = :recurso_imagen, 
                    destacado = :destacado, 
                    imagen_cabecera = :imagen_cabecera, 
                    estado = :estado 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':slug' => $slug,
            ':imagen' => $imagen,
            ':fecha_inicio' => $fecha_inicio,
            ':duracion' => $duracion,
            ':horarios' => $horarios,
            ':dias_cursado' => $dias_cursado,
            ':carga_horaria' => $carga_horaria,
            ':recurso_pdf' => $recurso_pdf,
            ':recurso_imagen' => $recurso_imagen,
            ':destacado' => $destacado,
            ':imagen_cabecera' => $imagen_cabecera,
            ':estado' => $estado
        ]);
    }


       // 👇 ELIMINAR FORMACION (DELETE)
    public function eliminarFormacion($id) {
        $sql = "DELETE FROM formacion WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }



   


    /**
     * Obtiene los detalles de una formación específica según el slug y el idioma.
     *
     * Une las tablas 'formacion' y 'formacion_trad' para devolver los datos completos
     * y traducidos de la formación.
     *
     * @param string $slug Identificador único de la formación.
     * @param string $idioma Código de idioma (por defecto 'es').
     * @return array|null Datos de la formación como array asociativo, o null si no existe.
     */
    public function obtenerDetalleFormacion($slug, $idioma = 'es') {
        $sql = "SELECT f.*, ft.titulo, ft.descripcion, ft.boton
                FROM formacion f
                JOIN formacion_trad ft ON f.id = ft.formacion_id
                WHERE f.slug = ? AND ft.idioma_id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$slug, $idioma]);
        $formacion = $stmt->fetch();

        if (!$formacion) {
            return null;
        }

        return $formacion;
    }
    public function obtenerTipoFormacionPorIdYIdioma($id, $idioma = 'es') {
        $sql = "
            SELECT 
                    tf.id, 
                    tf.clave, 
                    tf.icono, 
                    tf.imagen_cabecera,
                    tft.titulo, 
                    tft.subtitulo, 
                    tft.descripcion_html, 
                    tft.pie_html,
                    tft.descripcion_larga
            FROM tipo_formacion tf
            INNER JOIN tipo_formacion_traduccion tft
                ON tf.id = tft.id_tipo_formacion
            WHERE tf.id = :id AND tft.idioma = :idioma
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':idioma', $idioma);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>



















