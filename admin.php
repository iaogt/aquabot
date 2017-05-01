<?php
  $link = mysql_connect('[servidor]', '[usuario]', '[contraseña]');
  if (!$link) {
      file_put_contents("errores.txt",'Could not connect: ' . mysql_error(),FILE_APPEND);
  }
  // make foo the current db
  $db_selected = mysql_select_db('aguascontuagua', $link);
  if (!$db_selected) {
      file_put_contents("errores.txt",'Can\'t use database : ' . mysql_error(),FILE_APPEND);
  }
  $sql = "select * FROM ADJUNTOS where tipo like 'coord' ORDER BY hora DESC";
  $result = mysql_query($sql,$link);
  $error = mysql_error($link);
  $marcadores = array();
  if($error!=""){
    file_put_contents("errores.txt","error en consulta:".$sql,FILE_APPEND);
    die();
  }else{
    while($row = mysql_fetch_assoc($result)){
      $marcadores[$row['idflujo']]=$row;
    }
  }
  $sql = "select * FROM ADJUNTOS where tipo like 'img' ORDER BY hora DESC";
  $result = mysql_query($sql,$link);
  $error = mysql_error($link);
  $imagenes = array();
  if($error!=""){
    file_put_contents("errores.txt","error en consulta:".$sql,FILE_APPEND);
    die();
  }else{
    while($row = mysql_fetch_assoc($result)){
      $imagenes[$row['idflujo']]=$row;
    }
  }
?>
<html>
<head>
  <title>Herramienta de monitoreo</title>
  <link href="/monitoreo/themes/prototipo/css/bootstrap/bootstrap.min.css" rel="stylesheet">

  <link href="/monitoreo/themes/prototipo/css/font-awesome.css" rel="stylesheet">
  <link href="/monitoreo/themes/prototipo/css/inspinia/animate.css" rel="stylesheet">
  <link href="/monitoreo/themes/prototipo/css/inspinia/styleinspinia.css" rel="stylesheet">
    <script src="/monitoreo/themes/prototipo/js/jquery.min.js"></script>
    <script src="/monitoreo/themes/prototipo/js/bootstrap/bootstrap.min.js"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>
</head>
<body>
 <div id="wrapper">

    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> <span>
                            <h2>Aguascontuagua</h2>
                             </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Admin</strong>
                             </span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="profile.html">Profile</a></li>
                            <li><a href="contacts.html">Contacts</a></li>
                            <li><a href="mailbox.html">Mailbox</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?r=site/logout">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        IN+
                    </div>
                </li>
                <li>

                    <a href="#"><i class="fa fa-edit"></i> <span class="nav-label">Mapa</span><span class="fa arrow"></span></a>
                </li>

            </ul>

        </div>
    </nav>

        <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
        <nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="search_results.html">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                </div>
            </form>
        </div>
            <ul class="nav navbar-top-links navbar-right">



                <li>
                    <a href="index.php?r=site/logout">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
            </ul>

        </nav>
        </div>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>Incidentes de Agua</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index.html">Incidentes</a>
                        </li>
                        <li class="active">
                            <strong>Agua  </strong>
                        </li>
                    </ol>
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div id="mapid" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="pull-right">

            </div>
            <div>
                WebYMovil &copy; 2017
            </div>
        </div>

        </div>
        </div>

    <script src="/monitoreo/themes/prototipo/js/inspinia/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/monitoreo/themes/prototipo/js/inspinia/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/monitoreo/themes/prototipo/js/inspinia/inspinia.js"></script>
    <script src="/monitoreo/themes/prototipo/js/inspinia/plugins/pace/pace.min.js"></script>
<script type="text/javascript">
var mymap = L.map('mapid').setView([14.634915, -90.506882], 12);
L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
    '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="http://mapbox.com">Mapbox</a>',
  id: 'mapbox.streets'
}).addTo(mymap);
<?php
  foreach($marcadores as $m){
    $idflujo = $m['idflujo'];
    $titulo="";
    if($idflujo=="FLFUGAAGUA"){
      $titulo="Fuga de Agua";
    }
?>
var marker = L.marker([<?php echo $m['url']?>]).addTo(mymap);
marker.bindPopup('<p><?php echo $titulo;?></p><br/><img width="300px" src="<?php echo $imagenes[$idflujo]['url']?>"/>').openPopup();
<?php
  }
?>

</script>
</body>
</html>
