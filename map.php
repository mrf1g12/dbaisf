<?php
// get VID from session
#require('access.php');
#$ID = $_SESSION['ID'];

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

function status_to_color($s) {
    if ($s == '-') {
        return 'warning';
    } elseif ($s == 'Pagato') {
        return 'success';
    } elseif ($s == 'Non Pagato') {
        return 'danger';
    }
}

function status_to_btncolor($s) {
    if ($s == '-') {
        return 'btn-warning';
    } elseif ($s == 'Pagato') {
        return 'btn-success';
    } elseif ($s == 'Non Pagato') {
        return 'btn-danger';
    }
}

function appr_to_btncolor($s) {
    if ($s == 'Approvato') {
        return 'btn-info';
    } elseif ($s == 'Non Approvato') {
        return 'btn-warning';
    } else {
        return 'btn-danger';
    }
}

$stringa = "SELECT sq.reg,sq.code AS code,COUNT(*) AS count FROM (SELECT DISTINCT id,dati_cap.regione AS reg,dati_cap.codice_regione AS code FROM ".$table." LEFT JOIN  dati_cap ON ".$table.".cap=dati_cap.cap) AS sq WHERE sq.reg IS NOT NULL GROUP BY sq.reg";
$result = $mysqli->query($stringa);
#echo $stringa;

$arr_reg=array();
while($row = $result->fetch_assoc()){
    #echo "<p>".$row['reg']." ".$row['code']." ".$row['count']."</p>";
    $arr_reg[$row['code']]=$row['count'];
}

/*
$stringa = "SELECT codice_provincia AS code FROM dati_cap";
$result = $mysqli->query($stringa);
$arr_prov=array();
while($row = $result->fetch_assoc()){
    #echo "<p>".$row['reg']." ".$row['code']." ".$row['count']."</p>";
    $arr_prov[$row['code']]=;
}
*/

$stringa = "SELECT sq.prov,sq.code AS code,COUNT(*) AS count FROM (SELECT DISTINCT id,dati_cap.provincia AS prov,dati_cap.codice_provincia AS code FROM ".$table." LEFT JOIN  dati_cap ON ".$table.".cap=dati_cap.cap) AS sq WHERE sq.prov IS NOT NULL GROUP BY sq.prov";
$result = $mysqli->query($stringa);

$arr_prov=array();
while($row = $result->fetch_assoc()){
    #echo "<p>".$row['reg']." ".$row['code']." ".$row['count']."</p>";
    $arr_prov[$row['code']]=$row['count'];
}


#$arr_reg = array('IT-21' => 100,'IT-25'=>80,'IT-55'=>12);
$reg_json = json_encode($arr_reg);
$prov_json = json_encode($arr_prov);

?>


<!DOCTYPE html>
<html>
    <head>
        <title>Distribuzione geografica</title>
        <link rel="stylesheet" href="./jquery-jvectormap-2.0.3/jquery-jvectormap-2.0.3.css" type="text/css" media="screen"/>
        <script src="./jquery-3.1.1.min.js"></script>
        <script src="./jquery-jvectormap-2.0.3/jquery-jvectormap-2.0.3.min.js"></script>
        <script src="./jquery-jvectormap-2.0.3/maps/it_regions_mill.js"></script>
        <script src="./jquery-jvectormap-2.0.3/maps/it_mill.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-alpha2/html2canvas.min.js"></script>-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>
        <!--<script type="text/javascript" src="./html2canvas-master/dist/html2canvas.js"></script>-->
        <!--<script type="text/javascript" src="http://canvg.github.io/canvg/rgbcolor.js"></script> 
<script type="text/javascript" src="http://canvg.github.io/canvg/StackBlur.js"></script>
<script type="text/javascript" src="http://canvg.github.io/canvg/canvg.js"></script>-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <style>
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
        </style>
    </head>
    <body>
        <div class="small_skip"></div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9">
                <h3>Distribuzione geografica dei membri AISF</h3>
            </div>
            <div class="col-md-2">
                <a class="btn btn-default" href="index.php" >Home</a>
                <a class="btn btn-default" href="search.php?query=&sorting=data" >Full list</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9" align="center">
                <h4>Distribuzione per Regione di residenza</h4>
                <!-- <div id="ita-map1" style="width: 1000px; height: 800px;"></div>-->
                
                <div id="wrapper" style="background-color: white">
                    <div id="vmap" style="width: 1000px; height: 800px;"></div>
                </div>
                <!--<button id="export">Export</button>-->
                <script>
                    var regData = <?php echo $reg_json?>;
                    var map_obj = {
                        map: 'it_regions_mill',
                        series: {
                            regions: [{
                                values: regData,
                                scale: ['#C8EEFF', '#0071A4'],
                                normalizeFunction: 'linear'
                            }]
                        },
                        onRegionTipShow: function(e, el, code){
                            el.html(el.html()+': '+regData[code]);
                        }
                    };
                    $('#vmap').vectorMap(map_obj);


                    $(document).ready(function(){

                        $('#export').click(function (e) {

                            e.preventDefault();

                            var useWidth = $('#wrapper').prop('scrollWidth'); //document.getElementById("primary").style.width;
                            var useHeight = $('#wrapper').prop('scrollHeight'); //document.getElementById("primary").style.height;

                            html2canvas(document.getElementById("wrapper"), {width: useWidth, height: useHeight}).then(function (canvas) {
                                //document.body.appendChild(canvas);

                                var imgData = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");  // here is the most important part because if you dont replace you will get a DOM 18 exception.
                                //var imgData = canvas.toDataURL("image/png");  // here is the most important part because if you dont replace you will get a DOM 18 exception.
                                //window.location.href = image; // it will save locally
                                //var doc = new jsPDF('p', 'mm');
                                //doc.addImage(imgData, 'PNG', 10, 10);
                                //doc.save('sample-file.pdf');
                            });


                        });

                    });

                </script>
            </div>
        </div>
        <div class="med_skip"></div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9" align="center">
                <h4>Distribuzione per Provincia di residenza</h4>
                <div id="ita-map2" style="width: 1000px; height: 800px;"></div>
                <script>
                    var provData = <?php echo $prov_json?>;
                    $('#ita-map2').vectorMap({
                        map: 'it_mill',
                        series: {
                            regions: [{
                                values: provData,
                                scale: ['#C8EEFF', '#0071A4'],
                                normalizeFunction: 'polynomial'
                            }]
                        },
                        onRegionTipShow: function(e, el, code){
                            if (provData[code]>0){
                                el.html(el.html()+': '+provData[code]);
                            } else{
                                el.html(el.html()+': 0');
                            }
                        }
                    });
                </script>
            </div>
            <div class="col-md-2"></div>
        </div>
    </body>
</html>