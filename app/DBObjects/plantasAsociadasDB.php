<?php

require_once './../models/conexion.php';

class PlantasAsociadasDB {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

 /**
   * Relacionar un usuario con una planta
    * 
    * @param int $idPlanta El ID de la planta
    * @param int $idUsuario El ID del usuario
    * @param string $proveedor El nombre del proveedor
    * @return array en caso de éxito o false en caso de error
    */
    public function getPlantasAsociadasAlUsuario($idUsuario) {
        try {
            $conexion = new Conexion();
            $conn = $conexion->getConexion();
    
            $query = "SELECT * FROM plantas_asociadas WHERE usuario_id = ?;";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
    
            // Vincula el parámetro 'i' para enteros
            $stmt->bind_param('i', $idUsuario);
    
            // Ejecuta la consulta
            if (!$stmt->execute()) {
                throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
                return false;
            }
    
            // Recoge los resultados de la consulta
            $result = $stmt->get_result();
            $plantas = [];
            while ($row = $result->fetch_assoc()) {
                $plantas[] = $row;
            }
    
            // Cierra la consulta y la conexión
            $stmt->close();
            $conn->close();
    
            // Devuelve el array de plantas asociadas
            return $plantas;
        } catch (Exception $e) {
            error_log("Error al relacionar usuario y planta: " . $e->getMessage());
            return false;
        }
    }
    
}

?>