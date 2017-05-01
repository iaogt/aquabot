<?php

  class FlujoMensajes{

    var $idflujo = "FLUJONORMAL";
    var $arrFlujo = array(
      'bienvenida'=>array(
        'menu'=>array(
          'mensaje'=>"Bienvenido al sistema de alertas, ¿que tipo de incidente deseas reportar?"
        )
      ),
      'decision'=>array(
        'decision'=>array(
        'textodecision'=>"¿Desea que guardemos estos mensajes como una queja?",
        'opcionesdecision'=>'[
            {
              "content_type":"text",
              "title":"Si",
              "payload":"si_comentarios_queja"
            },
            {
              "content_type":"text",
              "title":"No",
              "payload":"no_comentarios_queja"
            }
          ]'
        )
      )
    );

    var $db;

    function __construct(){
      $this->db = new DB();
    }

    public function siguienteMensaje($usuario){
      if($this->db->conversacionExiste($usuario)){
        file_put_contents("errores.txt","encontro conversacion",FILE_APPEND);
        $arrPasos = $this->db->pasosFlujo($idusuario,$this->idflujo);
        file_put_contents("errores.txt","pasos:".count($arrPasos),FILE_APPEND);
        $sigpaso="";
        for($i=0;$i<count($arrFlujo);$i++){
          $llaves = array_keys($arrFlujo);
          $paso = $llaves[$i];
          if(!in_array($paso,$arrPasos)){
            file_put_contents("errores.txt","no paso",FILE_APPEND);
            $sigpaso = $paso;
            $i=count($arrFlujo);
          }else{
        file_put_contents("errores.txt","si paso:".$paso,FILE_APPEND);
          }
        }
      }else{
        file_put_contents("errores.txt","a crear registro",FILE_APPEND);
        $this->db->crearRegistroConversacion($usuario);
        $sigpaso = 'bienvenida';
      }
      $objMensaje=null;
      if($sigpaso!=""){
        $arrmensaje = $this->arrFlujo[$sigpaso];
        foreach($arrmensaje as $key=>$params){
          $objMensaje = new ObjMensaje($key);
          $objMensaje->setIdFlujo($this->idflujo);
          $objMensaje->setIdPaso($sigpaso);
          $objMensaje->setDestinatario($usuario);
          $objMensaje->setParams($params);
        }
      }
      return $objMensaje;
    }

    public function procesaMensaje($idusuario,$arrMensaje){
        file_put_contents("errores.txt","a procesar",FILE_APPEND);
      $arrPasos = $this->db->pasosFlujo($idusuario,$this->idflujo);
        file_put_contents("errores.txt","cargo pasos",FILE_APPEND);
      $ultimo=array('paso'=>'bienvenida');
      if(count($arrPasos)>0){
        $ultimo = $arrPasos[count($arrPasos)-1];
      }
      $this->db->grabarMensaje($idusuario,$this->idflujo,$ultimo['paso'],$arrMensaje);
    }

  }
?>
