<?php
error_reporting(E_ALL);
 ini_set("display_errors", 1);
$diccionario = array(
    'subtitle'=>array('agregar'=>'Crear un nuevo usuario',
                      'buscar'=>'Buscar usuario',
                      'borrar'=>'Eliminar un usuario',
                      'modificar'=>'Modificar usuario'
                     ),
    'links_menu'=>array(
        'VIEW_SET_USER'=>'usuarios/controller.php?act=agregar',
        'VIEW_GET_USER'=>'usuarios/controller.php?act=buscar',
        'VIEW_EDIT_USER'=>'usuarios/controller.php?act=modificar',
        'VIEW_DELETE_USER'=>'usuarios/controller.php?act=borrar'
    ),
    'form_actions'=>array(
        'SET'=>'../'.'usuarios/controller.php?act=set',
        'GET'=>'../'.'usuarios/controller.php',
        'DELETE'=>'../'.'usuarios/controller.php?act=delete',
        'EDIT'=>'../'.'usuarios/controller.php?act=edit'
    )
);

function get_template($form='get') {
    $file = '../site_media/html/user_'.$form.'.html';
    $template = file_get_contents($file);
    return $template;
}

function render_dinamic_data($html, $data) {
    foreach ($data as $clave=>$valor) {
        $html = str_replace('{'.$clave.'}', $valor, $html);
    }
    return $html;
}

function retornar_vista($vista, $data=array()) {
    global $diccionario;
    $html = get_template('template');
    $html = str_replace('{subtitulo}', $diccionario['subtitle'][$vista], $html);
    $html = str_replace('{formulario}', get_template($vista), $html);
    $html = render_dinamic_data($html, $diccionario['form_actions']);
    $html = render_dinamic_data($html, $diccionario['links_menu']);
    $html = render_dinamic_data($html, $data);


    // render {mensaje}
    if(array_key_exists('NOMBRE', $data)&&
       array_key_exists('APELLIDO', $data)&&       
       $vista=='modificar') {

        $mensaje = 'Editar usuario '.$data['NOMBRE'].' '.$data['APELLIDO'];
    } else {
        if(array_key_exists('mensaje', $data)) {
            $mensaje = $data['mensaje'];
        } else {
            $mensaje = 'Datos del usuario:';
               $mensaje = 'Editar usuario '.$data['NOMBRE'].' '.$data['APELLIDO'];
        }
    }
    $html = str_replace('{mensaje}', $mensaje, $html);

    print $html;
}
?>
