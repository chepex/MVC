<?php


error_reporting(E_ALL);
 ini_set("display_errors", 1);
require_once('constants.php');
require_once('model.php');
require_once('view.php');

function handler() {
    $event = 'buscar';
    $uri = $_GET['act'];
    $peticiones = array('set', 'get', 'delete', 'edit',
                        'agregar', 'buscar', 'borrar', 
                        'modificar');
    foreach ($peticiones as $peticion) {
        
        if( $uri ==$peticion)  {
            $event = $peticion;
        }
    }


    $user_data = helper_user_data();
    $usuario = set_obj();

    switch ($event) {
        case 'set':
            $usuario->set($user_data);
            $data = array('mensaje'=>$usuario->mensaje);
            retornar_vista('agregar', $data);
            break;
        case 'get':        
            $usuario->get($user_data);
            $data = array(
                'NOMBRE'=>$usuario->NOMBRE,
                'APELLIDO'=>$usuario->APELLIDO,
                'EMAIL'=>$usuario->EMAIL
            );            
            retornar_vista('buscar', $data);
            break;
        case 'delete':
            $usuario->delete($user_data['EMAIL']);
            $data = array('mensaje'=>$usuario->mensaje);
            retornar_vista('borrar', $data);
            break;
        case 'edit':
            $usuario->edit($user_data);
            $data = array('mensaje'=>$usuario->mensaje);
            retornar_vista('modificar', $data);
            break;
        default:
            retornar_vista($event);
    }
}


function set_obj() {
    $obj = new Usuario();
    return $obj;
}

function helper_user_data() {
    $user_data = array();
    if($_POST) {
        if(array_key_exists('NOMBRE', $_POST)) { 
            $user_data['NOMBRE'] = $_POST['NOMBRE']; 
        }
        if(array_key_exists('APELLIDO', $_POST)) { 
            $user_data['APELLIDO'] = $_POST['APELLIDO']; 
        }
        if(array_key_exists('EMAIL', $_POST)) { 
            $user_data['EMAIL'] = $_POST['EMAIL']; 
        }
        if(array_key_exists('CLAVE', $_POST)) { 
            $user_data['CLAVE'] = $_POST['CLAVE']; 
        }
    } else if($_GET) {
        if(array_key_exists('EMAIL', $_GET)) {
            $user_data = $_GET['EMAIL'];
        }
    }
    print_r( $user_data);
    return $user_data;
}


handler();
?>
