<?php

  class FlujoComentarios {

    var $idflujo = "FLCOMENTS";

    var $arrFlujo = array(
      'instrucciones'=>array(
        'simple'=>array(
          'mensaje'=>'Todo lo que escriba será tomado como un comentario y será trasladado al personal correspondiente.'
        )
      )
    );

    public function siguienteMensaje($usuario){
      $sigpaso= 'instrucciones';
      $arrmensaje = $this->arrFlujo[$sigpaso];
      $objMensaje=null;
      foreach($arrmensaje as $key=>$params){
        $objMensaje = new ObjMensaje($key);
        $objMensaje->setIdFlujo($this->idflujo);
        $objMensaje->setIdPaso($sigpaso);
        $objMensaje->setDestinatario($usuario);
        $objMensaje->setParams($params);
      }
      return $objMensaje;
    }
  }
?>
