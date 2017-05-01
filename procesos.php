<?php
  class Procesos {
    public static function flujo($nomflujo){
      $objFlujo=null;
      switch($nomflujo){
        case "FLUJONORMAL":{
          $objFlujo = new FlujoMensajes();
          break;
        }
        case "FLCOMENTS":{
          $objFlujo = new FlujoComentarios();
          break;
        }
        case "FLFUGAAGUA":{
          $objFlujo = new FlujoFugaAgua();
          break;
        }
        case "fuga_de_agua":{
          $objFlujo = new FlujoFugaAgua();
          break;
        }
        case "si_comentarios_queja":{
          $objFlujo =  new FlujoComentarios();
          break;
        }
        case "no_comentarios_queja":{
          $objFlujo = new FlujoMensajes();
          break;
        }
      }
      return $objFlujo;
    }

    public static function flujoActual($idusuario){
      $objDB = new DB();
      file_put_contents("errores.txt","instancio DB",FILE_APPEND);
      $actual = $objDB->procesoActual($idusuario);
      $objProceso = null;
      if($actual!=null && is_array($actual) && (count($actual)>0)){
        file_put_contents("errores.txt","reinicia:".$actual['idflujo'],FILE_APPEND);
        $objProceso = self::flujo($actual['idflujo']);
      }else{
        file_put_contents("errores.txt","toca normalito",FILE_APPEND);
        $objProceso = self::flujo("FLUJONORMAL");
      }
      return $objProceso;
    }

  }
?>
