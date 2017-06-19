<?php
// get VID from session
require('../access.php');
$ID = $_SESSION['ID'];

$check_id=$_GET['id'];

if ($ID!=$check_id && $ID!='admin' && $ID!='esecutivo_aisf'){
    header('Location: ' . '../badPrivileges.php'); 
    exit();
}

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
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


// function
function status_to_color($s) {
    if ($s == 'pending') {
        return 'warning';
    } elseif ($s == 'approvato') {
        return 'info';
    } elseif ($s == 'respinto') {
        return 'danger';
    } elseif ($s == 'pagato')
    {
        return 'success';
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

function format_date($date){
    $time = strtotime($date);
    return date("d/m/Y", $time);
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Richiesta singola</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>
            .tiny_skip{
                margin-top: 5px;
                margin-bottom: 5px}
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
        </style>
    </head>
    <body>

        <?php
        // ID is sent via GET method
        $rid = trim($_GET['rid']);
        $red_page = $_GET['page'];

        #echo $red_page;

        // select single row, using ID
        $stringa = "SELECT * FROM spese_LC WHERE RID = '".$rid."'";
        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        // should never be more than one result! (because ID must be unique)
        if ($entries != 1) {
            echo "CONFLICTING IDs !!<br>";
        }

        // fetch data
        $row = $result->fetch_array();

        $result->free();
        $mysqli->close();

        ?>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-7"><h4>ID: <font color="green"><?php echo $ID;?></font></h4></div>
            <div class="col-md-3"></div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6" align="center">
                <h3>Dettagli richiesta</h3>
            </div>
            <div class="col-md-1"> 
                <?php echo "<a class=\"btn btn-default\" href=\"" . $red_page . "\" style=\"float: left;\" >Back</a>";?>
            </div>
            <div class="col-md-2"> 
                <a class="btn btn-default" href="../index.php" >Home</a>
                <a class="btn btn-default" href="search_movimenti.php">Lista movimenti</a>
            </div>
        </div>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <table class='table'>
                    <tr>
                        <th>Campo</th>
                        <th>Valore</th>
                    </tr>
                    <tr>
                        <td><div class="tiny_skip"></div></td>
                        <td><div class="tiny_skip"></div></td>
                    </tr>
                    <tr>
                        <td>RID</td>
                        <td><?php echo $row['RID'];?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Comitato Locale</td>
                        <td><b><?php echo $row['LC'];?></b></td>
                    </tr>
                    <tr>
                        <td>Data invio richiesta</td>
                        <td><?php echo format_date($row['data']);?></td>
                    </tr>
                    <tr>
                        <td>Nome evento</td>
                        <td><?php echo $row['oggetto'];?></td>
                    </tr>
                    <tr>
                        <td>Descrizione e finalità</td>
                        <td><?php echo $row['descrizione'];?></td>
                    </tr>
                    <tr>
                        <td>Importo</td>
                        <td><b><?php echo $row['importo'];?> €</b></td>
                    </tr>
                    <tr>
                        <td>Beneficiario</td>
                        <td><?php echo $row['beneficiario'];?></td>
                    </tr>
                    <tr>
                        <td>IBAN</td>
                        <td><?php echo $row['IBAN'];?></td>
                    </tr>
                    <?php
                    if ($row['cofinanziatori']!=''){
                        echo "
                        <tr>
                            <td>Cofinanziatori</td>
                            <td>".$row['cofinanziatori']."</td>
                        </tr>
                        ";
                    }
                    ?>
                    <tr>
                        <?php 
                        $col_sx="<td>Documenti<br><br>";
                        $col_dx="<td><br><br>";
                        $url_list = explode(" ",trim($row['url_doc']));
                        foreach ($url_list as $url){
                            if ($url!=''){
                                $col_sx .= "<a class=\"btn btn-default btn-xs\" href='remove_file.php?url=".$url."&rid=".$rid."'>Rimuovi</a><br>";
                                $col_dx .= "<a href='https://".$url."'a>".$url."</a><br>";
                            }
                        }
                        $col_sx .= "</td>";
                        $col_dx .= "</td>";
                        echo $col_sx;
                        echo $col_dx;
                        ?>
                    </tr>
                    <tr>
                        <td>Caricamento giustificativi</td>
                        <td>
                            <form action="upload_ricevute.php" method='POST' enctype="multipart/form-data">
                                <input type="hidden" name='lc' value='<?php echo $row['lc_sigla'];?>'/>
                                <input type="hidden" name='rid' value='<?php echo $row['RID'];?>'/>
                                <input type="file" name="fileToUpload[]" multiple="multiple"/>
                                Si può caricare più di un file.
                                <i>Formato file: pdf</i><br><br>
                                <button class="btn btn-primary btn-md" type="submit" value="Submit">Carica</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td><div class="tiny_skip"></div></td>
                        <td><div class="tiny_skip"></div></td>
                    </tr>
                    <tr>
                        <?php 
                        $col_sx="<td>Ricevute bonifici";
                        $col_dx="<td>";
                        $url_list = explode(" ",trim($row['url_bonifico']));
                        foreach ($url_list as $url){
                            if ($url!=''){
                                //$col_sx .= "<a class=\"btn btn-default btn-xs\" href='http://www.ai-sf.it/dbaisf/spese_lc/remove_file.php?url=".$url."&rid=".$rid."'>Rimuovi</a><br>";
                                $col_sx .=' ';
                                $col_dx .= "<a href='https://".$url."'a>".$url."</a><br>";
                            }
                        }
                        $col_sx .= "</td>";
                        $col_dx .= "</td>";
                        echo $col_sx;
                        echo $col_dx;
                        ?>
                    </tr>
                    <tr>
                        <td>Stato</td>
                        <?php 
                        $backcolor = status_to_color($row['status']);
                        echo "<td class=\"" . $backcolor . "\">".$row['status']."</td>";
                        ?>
                    </tr>
                    <?php
                    if ($ID=='admin' || $ID=='esecutivo_aisf'){
                        echo "
                        <tr>
                        <form action=\"change_status_richiesta.php\" method=\"GET\">
                            <td>
                                <button class=\"btn btn-default\" type=\"submit\" value=\"Submit\">Modifica status</button>
                            <td>
                            <select id=\"new_status\" name=\"new_status\">
                                    <option disabled selected value> -- seleziona un'opzione -- </option>
                                    <option value='pending'>pending</option>
                                    <option value='approvato'>approvato</option>
                                    <option value='respinto'>respinto</option>
                                    <option value='pagato'>pagato</option>
                                </select>
                            </td>
                            <input type=\"hidden\" name='rid' value='".$row['RID']."'/>
                        </form>
                        </tr>
                        ";
                        if ($row['status'] == 'pagato'){
                            echo "<tr>
                        <td>Caricamento ricevuta bonifico</td>
                        <td>
                            <form action=\"upload_bonifico.php\" method=\"POST\" enctype=\"multipart/form-data\">
                                <input type=\"hidden\" name=\"lc\" value=\"".$row['lc_sigla']."\"/>
                                <input type=\"hidden\" name=\"rid\" value=\"".$row['RID']."\"/>
                                <input type=\"file\" name=\"fileToUpload[]\" multiple=\"multiple\"/>
                                Si può caricare più di un file.
                                <i>Formato file: pdf</i><br><br>
                                <button class=\"btn btn-default btn-sm\" type=\"submit\" value=\"Submit\">Carica</button>
                            </form>
                        </td>";
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class='small_skip'></div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <?php 
                if ($ID=='admin'){
                    echo "
                             <form action=\"make_editable.php\" method=\"GET\" style=\"float: left;\">
                               <input type=\"hidden\" name=\"rid\" value=\"" . $rid . "\"></input>
                               <input type=\"submit\" value=\"Rendi modificabile\" class=\"btn btn-default\"></input>
                            </form>
                    ";
                }
                ?>
            </div>
        </div>


        <br>
    </body>
</html>