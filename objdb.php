<?php
  class DB {

    var $link;

    function __construct(){
      $this->conectar();
    }


    public function conectar(){
      $this->link = mysql_connect('[servidor]', '[usuario]', '[contraseña]');
      if (!$this->link) {
          die();
      }
      // make foo the current db
      $db_selected = mysql_select_db('aguascontuagua', $this->link);
      if (!$db_selected) {
          die();
      }
    }

    public function cerrar(){
      mysql_close($this->link);
    }

    public function consultar($query){
      $result = mysql_query($query,$this->link);
      $error = mysql_error($this->link);
      $collResult = array();
      if($error!=""){
        die();
      }else{
        while($row = mysql_fetch_assoc($result)){
          array_push($collResult,$row);
        }
      }
      return $collResult;
    }

    public function ejecutar($query){
      $resultado = mysql_query($query,$this->link);
      $error = mysql_error($this->link);
      if($error!=""){
        file_put_contents("errores.txt","errores:".$error,FILE_APPEND);
      }
      return $resultado;
    }

    public function crearRegistroConversacion($usuario){
      $sql = "insert into REGISTRO(idusuario,hora) VALUES('".$usuario."',now())";
      $this->ejecutar($sql);
    }

    public function conversacionExiste($usuario){
      $sql = "select * from REGISTRO where idusuario like '".$usuario."'";
      $resultados = $this->consultar($sql);
      return count($resultados);
    }

    public function pasosFlujo($idusuario,$idflujo){
      $sql = "select paso FROM USUARIO_FLUJOS WHERE idusuario like '".$idusuario."' AND idflujo='".$idflujo."'";
      file_put_contents("errores.txt","a consultar:".$sql,FILE_APPEND);

      $result = $this->consultar($sql);
      file_put_contents("errores.txt","ya consultò",FILE_APPEND);

      $arrPasos = array();
      if($result){
        foreach($result as $k=>$arr){
          array_push($arrPasos,$arr['paso']);
        }
      }
      return $arrPasos;
    }

    public function registrarPasoFlujo($idusuario,$idflujo,$idpaso){
      $sql = "insert INTO USUARIO_FLUJOS(idusuario,idflujo,paso) VALUES('".$idusuario."','".$idflujo."','".$idpaso."')";
      $this->ejecutar($sql);
    }

    public function procesoActual($destinatario){
      $sql = "select idflujo,paso FROM USUARIO_FLUJOS where idusuario like '".$destinatario."' ORDER BY hora DESC LIMIT 1";
      file_put_contents("errores.txt","hizo sql:".$sql,FILE_APPEND);
      $arrResult = $this->consultar($sql);
      file_put_contents("errores.txt","y si consulto",FILE_APPEND);
      $arrProceso = array();
      if(count($arrResult)>0){
        $arrProceso=$arrResult[0];
      }
      return $arrProceso;
    }

    public function grabarMensaje($idusuario,$idflujo,$idpaso,$arrMensaje){
      $mensaje = "";
      if($arrMensaje){
        $mensaje=@$arrMensaje['text'];
      }
      echo "estructura mensaje a grabar";
      print_r($arrMensaje);
      if(array_key_exists("attachments",$arrMensaje)){
        $attachment = $arrMensaje["attachments"];
        foreach($attachment as $attach){
          if($attach["type"]=="image"){
            $sql="insert into ADJUNTOS(idusuario,idflujo,url,hora,tipo) VALUES('".$idusuario."','".$idflujo."','".$attach['payload']['url']."',now(),'img')";
            $this->ejecutar($sql);
          }
          if($attach["type"]=="location"){
            $coord = $attach['payload']['coordinates']['lat'].",".$attach['payload']['coordinates']['long'];
            $sql="insert into ADJUNTOS(idusuario,idflujo,url,hora,tipo) VALUES('".$idusuario."','".$idflujo."','".$coord."',now(),'coord')";
            $this->ejecutar($sql);
          }
        }
        $mensaje="(attach)";
      }
      $sql = "insert into MENSAJES_USUARIOS(idusuario,idflujo,idpaso,mensaje,hora) VALUES('".$idusuario."','".$idflujo."','".$idpaso."','".$mensaje."',now())";
      file_put_contents("errores.txt",$sql,FILE_APPEND);
      $this->ejecutar($sql);
    }


  }
?>
