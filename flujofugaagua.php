<?php

  class FlujoFugaAgua {

    var $idflujo = "FLFUGAAGUA";

    var $arrFlujo = array(
      'instrucciones'=>array(
        'ubicacion'=>array(
          'mensaje'=>'Necesitamos información del lugar para saber donde está sucediendo, haz click para compartir ubicación o describe como llegar al lugar'
        )
      ),
      'fotografia'=>array(
        'simple'=>array(
          'mensaje'=>'Por favor envía una fotografía del lugar o la señal de fuga'
        )
      ),
      'final'=>array(
        'simple'=>array(
          'mensaje'=>'Gracias por la información, las autoridades revisarán el caso tan pronto sea posible'
        )
      )
    );

    public function siguienteMensaje($usuario){
      $db = new DB();
      file_put_contents("errores.txt","ininicio",FILE_APPEND);
      $arrPasos = $db->pasosFlujo($usuario,$this->idflujo);
      file_put_contents("errores.txt","sisiguie",FILE_APPEND);

      $sigpaso="instrucciones";
      $encontrados = 0;
      for($i=0;$i<count($this->arrFlujo);$i++){
        $llaves = array_keys($this->arrFlujo);
        $paso = $llaves[$i];
        if(!in_array($paso,$arrPasos)){
          file_put_contents("errores.txt","no paso",FILE_APPEND);
          $sigpaso = $paso;
          $i=count($this->arrFlujo);
        }else{
          $encontrados=$encontrados+1;
          file_put_contents("errores.txt","si paso:".$paso,FILE_APPEND);
        }
      }
      $objMensaje=null;
      if($encontrados==count($this->arrFlujo)){

      }else{
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
      $db = new DB();
      $arrPasos = $db->pasosFlujo($idusuario,$this->idflujo);
      echo "array de pasos";
      print_r($arrPasos);
      $ultimo=array('paso'=>'instrucciones');
      if(count($arrPasos)>0){
        $ultimo = $arrPasos[count($arrPasos)-1];
      }
      $db->grabarMensaje($idusuario,$this->idflujo,$ultimo,$arrMensaje);
    }
  }
?>
