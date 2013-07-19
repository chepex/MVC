<?php
# Importar modelo de abstracción de base de datos
require_once('../core/db_abstract_model.php');


class Usuario extends DBAbstractModel {

    ############################### PROPIEDADES ################################
    public $NOMBRE;
    public $APELLIDO;
    public $EMAIL;
    private $CLAVE;
    protected $ID;


    ################################# MÉTODOS ##################################
    # Traer datos de un usuario
    public function get($user_email='') {
        if($user_email != '') {
            $this->query = "
                SELECT      ID, NOMBRE, APELLIDO, EMAIL, CLAVE
                FROM        usuariox
                WHERE       EMAIL = '$user_email'
            ";
            $this->get_results_from_query();
        }

        if(count($this->rows) == 1) {
            foreach ($this->rows[0] as $propiedad=>$valor) {
                $this->$propiedad = $valor;
            }
            $this->mensaje = 'Usuario encontrado';
        } else {
            $this->mensaje = 'Usuario no encontrado';
        }
    }

    # Crear un nuevo usuario
    public function set($user_data=array()) {
        if(array_key_exists('EMAIL', $user_data)) {
            $this->get($user_data['EMAIL']);
            if($user_data['EMAIL'] != $this->EMAIL) {
                foreach ($user_data as $campo=>$valor) {
                    $$campo = $valor;
                }
                $this->query = "
                        INSERT INTO     usuariox
                        (NOMBRE, APELLIDO, EMAIL, CLAVE)
                        VALUES
                        ('$NOMBRE', '$APELLIDO', '$EMAIL', '$CLAVE')
                ";
                $this->execute_single_query();
                $this->mensaje = 'Usuario agregado exitosamente';
            } else {
                $this->mensaje = 'El usuario ya existe';
            }
        } else {
            $this->mensaje = 'No se ha agregado al usuario';
        }
    }

    # Modificar un usuario
    public function edit($user_data=array()) {
        foreach ($user_data as $campo=>$valor) {
            $$campo = $valor;
        }
        $this->query = "
                UPDATE      usuariox
                SET         NOMBRE='$NOMBRE',
                            APELLIDO='$APELLIDO'
                WHERE       EMAIL = '$EMAIL'
        ";
        $this->execute_single_query();
        $this->mensaje = 'Usuario modificado';
    }

    # Eliminar un usuario
    public function delete($user_email='') {
        $this->query = "
                DELETE FROM     usuariox
                WHERE           EMAIL = '$user_email'
        ";
        $this->execute_single_query();
        $this->mensaje = 'Usuario eliminado';
    }

    # Método constructor
    function __construct() {
        $this->db_name = 'book_example';
    }

    # Método destructor del objeto
    function __destruct() {
        unset($this);
    }
}
?>
