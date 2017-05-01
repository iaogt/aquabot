<?php
class ObjMensaje {

  var $plantillas = array(
    'simple'=>'{
                  "recipient":{
                      "id":"{destinatario}"
                  },
                  "message":{
                      "text":"{mensaje}"
                  }
              }',
    'menu'=>'{
        "recipient":{
          "id":"{destinatario}"
          },
        "message":{
          "attachment":{
            "type":"template",
            "payload":{
              "template_type":"button",
              "text":"Bienvenido al sistema de alertas.  ¿Desea reportar un incidente?",
              "buttons":[
                {
                  "type":"postback",
                  "title":"Fuga de agua",
                  "payload":"FLFUGAAGUA"
                  },
                  {
                    "type":"postback",
                    "title":"Falta de servicio",
                    "payload":"falta_servicio"
                    }
                ]
              }
            }
          }
      }',
      'decision'=>'{
        "recipient":{
          "id":"{destinatario}"
        },
        "message":{
          "text":"{textodecision}",
          "quick_replies":{opcionesdecision}
        }
      }',
      'ubicacion'=>'{
        "recipient":{
          "id":"{destinatario}"
          },
        "message":{
          "text":"{mensaje}",
          "quick_replies":[
            {
              "content_type":"location"
              }
            ]
          }
        }'
  );

  var $plantilla="";

  var $destinatario = "";
  var $params=array();

  var $idflujo;
  var $idpaso;

  function __construct($tipo){
    $this->plantilla = $this->plantillas[$tipo];
  }

  public function setIdPaso($id){
    $this->idpaso = $id;
  }

  public function setIdFlujo($id){
    $this->idflujo = $id;
  }

  public function setPlantilla($txt){
    $this->plantilla=$txt;
  }

  public function setDestinatario($txtDest){
    $this->destinatario = $txtDest;
  }

  public function setParams($arrParams){
    $this->params = $arrParams;
  }

  private function componer($txt,$arrVals){
    $newtxt=$txt;
    foreach($arrVals as $placeholder=>$val){
      $newtxt = str_replace("{".$placeholder."}",$val,$newtxt);
    }
    $newtxt = str_replace("{destinatario}",$this->destinatario,$newtxt);
    return $newtxt;
  }

  public function getJsonData(){
    return $this->componer($this->plantilla,$this->params);
  }

  public function enviar(){
    $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAFI0jV3LZCgBAKJSJZCdGZCZB2QSx1Afcipipd3TTnYmihT9sZC5wl0hKF1tf1WE9Hdp2STZBDyRWLsXhshZCunZA5ENkY9XdG4xl39w73MGtDtPjPRLRQfntQsiK1O1kjPp4FFx1wGWUjZB9ZCO8bxMHZBuTy6pAs6iQxvh1IuxZALZCwZDZD';
    $ch = curl_init($url);
    $jsonData = $this->getJsonData();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
file_put_contents("errores.txt","mensaje a enviar".$jsonData,FILE_APPEND);
    $result = curl_exec($ch);
file_put_contents("errores.txt","va a registrar paso",FILE_APPEND);
    $objDB = new DB();
file_put_contents("errores.txt","instancio",FILE_APPEND);
    $objDB->registrarPasoFlujo($this->destinatario,$this->idflujo,$this->idpaso);
file_put_contents("errores.txt","respuesta:".$result,FILE_APPEND);
  }

}
?>
