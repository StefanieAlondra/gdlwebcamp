<?php
// if($conn->ping()){
// echo "conectado";
// } else {
//   echo "no conectdo ";
// }

//  echo "<pre>";
// var_dump($_POST);
//  echo "</pre>";
include_once 'funciones/funciones.php';
$usuario = $_POST['usuario'];
$nombre = $_POST['nombre'];
$password = $_POST['password'];
$id_registro = $_POST['id_registro'];
if($_POST['registro'] == 'nuevo'){
    //die(json_encode($_POST));
    $opciones = array(
        'cost' => 12
    );
    $password_hashed = password_hash($password, PASSWORD_BCRYPT, $opciones);
    //echo $password_hashed;
    try{
        include_once 'funciones/funciones.php';
        $stmt = $conn->prepare("INSERT INTO admin (usuario, nombre, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $nombre, $password_hashed);
        $stmt->execute();
        $id_registro = $stmt->insert_id;
        if($id_registro > 0){
            $respuesta = array(
                'respuesta' => 'exito',
                'id_admin' =>  $id_registro
            );
            
        } else {
            $respuesta = array(
                'respuesta' => 'error'
            );

        }
        $stmt->close();
        $conn->close();
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
    die(json_encode($respuesta));


}
if($_POST['registro'] == 'actualizar'){
  
    try{
        if(empty($_POST['password'])){
            $stmt = $conn->prepare('UPDATE admin SET usuario = ?, nombre = ?, editado = NOW() WHERE id_admin = ? ');
            $stmt->bind_param("ssi", $usuario, $nombre, $id_registro);
        } else{
            $opciones = array(
                'cost' => 12
            );
            $hash_password = password_hash($password, PASSWORD_BCRYPT, $opciones);
            $stmt = $conn->prepare('UPDATE admin SET usuario = ?, nombre = ?, password = ?, editado = NOW()  WHERE id_admin = ? ');
            $stmt->bind_param("sssi", $usuario, $nombre, $hash_password, $id_registro);
        }
        
        $stmt->execute();
        if($stmt->affected_rows){
            $respuesta = array(
                'respuesta' => 'exito',
                'id_actualizado' => $stmt->insert_id

        );
        }else{
            $respuesta = array(
                'respuesta' => 'error'
            );
        }
        $stmt->close();
        $conn->close();
    }catch(Exception $e){
        $respuesta = array(
            'respuesta' => $e->getMessage()
        );
    }
    die(json_encode($respuesta));
}

if($_POST['registro'] == 'eliminar'){
   //die(json_encode($_POST));
   $id_borrar = $_POST['id'];
   try{
    $stmt = $conn->prepare('DELETE FROM admin WHERE id_admin = ?');
    $stmt->bind_param('i', $id_borrar);
    $stmt->execute();
    if($stmt->affected_rows){
        $respuesta =array(
            'respuesta'=> 'exito',
            'id_eliminado'=> $id_borrar
        );
    }else {
        $respuesta = array(
            'respuesta' => 'error!'
        );
    }

   }catch(Exception $e){
       $respuesta = array(
            'respuesta' => $e->getMessage()
       );
   }
   die(json_encode($respuesta));
}


if(isset($_POST['login-admin'])){
    //die(json_encode($_POST));
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    try{
        include_once 'funciones/funciones.php';
        $stmt = $conn->prepare("SELECT * FROM admin WHERE usuario = ?;");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->bind_result($id_admin, $usuario_admin, $nombre_admin, $password_admin, $editado);
        if($stmt->affected_rows){
            $existe = $stmt->fetch();
           if($existe){
                if(password_verify($password, $password_admin )){
                    session_start();
                    $_SESSION['usuario'] = $usuario_admin;
                    $_SESSION['nombre'] = $nombre_admin;
                    $respuesta = array(
                        'respuesta' => 'exitoso',
                        'usuario' => $nombre_admin
                    );
                }else{
                    $respuesta = array(
                        'respuesta' => 'error'
                    );
                 }
           }else{
                $respuesta = array(
                    'respuesta' => 'error'
                );
            }
        }
        $stmt->close();
        $conn->close();
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
    die(json_encode($respuesta));
}
?>
