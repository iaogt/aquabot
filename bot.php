<?php
ob_start();
        file_put_contents("errores.txt","empezasdfo",FILE_APPEND);
include("objmensaje.php");
file_put_contents("errores.txt","1",FILE_APPEND);
include("objdb.php");
file_put_contents("errores.txt","2",FILE_APPEND);
include("flujomensajes.php");
file_put_contents("errores.txt","3",FILE_APPEND);
include("flujocomentarios.php");
file_put_contents("errores.txt","4",FILE_APPEND);
include("flujofugaagua.php");
file_put_contents("errores.txt","5",FILE_APPEND);
include("procesos.php");
        file_put_contents("errores.txt","empezo",FILE_APPEND);
$arrData = json_decode(file_get_contents('php://input'), true);
if($arrData['object']=="page"){
  $entry = $arrData['entry'];
  foreach($entry as $req){
    if($req['messaging']){
      $paquete = $req['messaging'][0];
      $destinatario = $req['messaging'][0]['sender']['id'];
      $mensaje = $req['messaging'][0]['message'];
      if(array_key_exists("delivery",$paquete)){  //Confirmación de entrega

      }else if(array_key_exists("message",$paquete)){
        if(array_key_exists("is_echo",$mensaje)){

        }else {
        file_put_contents("errores.txt","viene normal",FILE_APPEND);
          $objFlujo = Procesos::flujoActual($destinatario);
          if($objFlujo!=null){
            $objFlujo->procesaMensaje($destinatario,$mensaje);
            $objMensaje = $objFlujo->siguienteMensaje($destinatario);
            if($objMensaje!=null){
              $objMensaje->enviar();
            }
          }
        }
      }else if(array_key_exists("postback",$paquete)){
        $opcion = $paquete['postback']['payload'];
        file_put_contents("errores.txt","postback",FILE_APPEND);
        file_put_contents("errores.txt",$opcion,FILE_APPEND);
        $objFlujo  = Procesos::flujo($opcion);
        file_put_contents("errores.txt","obtuvo flujo",FILE_APPEND);
        $objMensaje = $objFlujo->siguienteMensaje($destinatario);
file_put_contents("errores.txt","obtuvo mensaje",FILE_APPEND);
        if($objMensaje!=null){
          $objMensaje->enviar();
        }
file_put_contents("errores.txt","envio",FILE_APPEND);
      }
    }
  }
}
$texto = ob_get_contents();
ob_end_clean();
file_put_contents("errores.txt",$texto,FILE_APPEND);
?>
