<?php
// get VID from session
require('access.php');
$ID = $_SESSION['ID'];

if ($ID!='admin'){
    header('Location: ' . 'badPrivileges.php'); 
    exit();
}

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


// function
function status_to_color($s) {
    if ($s == '-') {
        return 'warning';
    } elseif ($s == 'Pagato') {
        return 'success';
    } elseif ($s == 'Non Pagato') {
        return 'danger';
    } else {
        return 'info';
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
        <title>Single entry result</title>
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
        $sid = $_GET['id'];
        $red_page = $_GET['page'];

        #echo $red_page;

        // select single row, using ID
        $stringa = "SELECT * FROM " . $table . " WHERE id = '".$sid."'";
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
            <div class="col-md-4"></div>
            <div class="col-md-4">
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
                        <td>ID</td>
                        <td><?php echo $row['id'];?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Cognome</td>
                        <td><b><?php echo $row['cognome'];?></b></td>
                    </tr>
                    <tr>
                        <td>Nome</td>
                        <td><?php echo $row['nome'];?></td>
                    </tr>
                    <tr>
                        <td>Luogo e data di nascita</td>
                        <td><?php echo $row['luogo_nascita'] . ", " . format_date($row['data_nascita']);?></td>
                    </tr>
                    <tr>
                        <td>Codice Fiscale</td>
                        <td><?php echo $row['CF'];?></td>
                    </tr>
                    <tr>
                        <td>Indirizzo di residenza</td>
                        <td><?php echo $row['indirizzo'];?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?php echo $row['email'];?></td>
                    </tr>
                    <tr>
                        <td>Università</td>
                        <td><?php echo $row['uni'];?></td>
                    </tr>
                    <tr>
                        <td>Corso di Studi</td>
                        <td><?php echo $row['studi'];?></td>
                    </tr>
                    <tr>
                        <td>Data di iscrizione</td>
                        <td><?php echo format_date($row['data']);?></td>
                    </tr>
                    <tr>
                        <td>Metodo di pagamento</td>
                        <td><?php echo $row['metodo'];?></td>
                    </tr>
                    <tr>
                        <td><div class="med_skip"></div></td>
                        <td><div class="med_skip"></div></td>
                    </tr>
                     <tr>
                        <td>Quota anno 2017</td>
                        <td><?php echo $row['q2017'];?></td>
                    </tr>
                    <tr>
                        <td>Quota anno 2017/2018</td>
                        <?php 
                        if ($row['appr']!='Onorario'){
                            $backcolor = status_to_btncolor($row['q2017_2018']);
                            $thisPage = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                            echo "<td>
                                <form action=\"pagato.php\" method=\"GET\">
                                    <input type=\"submit\" value=\"". $row['q2017_2018'] ."\" class=\"btn btn-default; " . $backcolor . "\" style=\"width:100%\">
                                    </input>
                                    <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                                    <input type=\"hidden\" name=\"statusquo\" value=\"" . $row['q2017_2018'] . "\"></input>
                                    <input type=\"hidden\" name=\"page\" value=\"".$thisPage."\"></input>
                                </form>
                            </td>";
                        } else {
                            echo "<td>
                                    <input type=\"submit\" value=\"Membro onorario\" class=\"btn btn-info; disabled=\"disabled\" style=\"width:100%\">
                                    </input>
                                </td>"; 
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>Approvazione</td>
                        <?php 
                        if ($row['appr']!='Onorario'){    
                            $backcolor = appr_to_btncolor($row['appr']);
                            $thisPage = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                            echo "<td>
                                <form action=\"appr.php\" method=\"GET\">
                                    <input type=\"submit\" value=\"". $row['appr'] ."\" class=\"btn btn-default; " . $backcolor . "\" style=\"width:100%\">
                                    </input>
                                    <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                                    <input type=\"hidden\" name=\"statusquo\" value=\"" . $row['appr'] . "\"></input>
                                    <input type=\"hidden\" name=\"page\" value=\"".$thisPage."\"></input>
                                </form>
                            </td>";
                        } else {
                            echo "<td>
                <input type=\"submit\" value=\"Membro onorario\" class=\"btn btn-info; disabled=\"disabled\" style=\"width:100%\">
                </input>
             </td>";
                        }
                        ?>
                    </tr>
                    <!--
<tr>
<td>
<form action="editStatus.php" method="GET">
<select name="status">
<option value="waiting"   <?php echo ($row['STATUS']=='waiting' ? 'disabled' : ''); ?> >Waiting list</option>
<option value="accepted"  <?php echo ($row['STATUS']=='accepted' ? 'disabled' : ''); ?> >Accepted</option>
<option value="rejected"  <?php echo ($row['STATUS']=='rejected' ? 'disabled' : ''); ?> >Rejected</option>
<option value="withdrawn" <?php echo ($row['STATUS']=='withdrawn' ? 'disabled' : ''); ?> >Withdrawn</option>
</select>
<input type="hidden" name="ID" value=<?php echo '"' . $row['ID'] . '"' ?> />
<input type="submit" value="Change status" />
</form>
</td>
<td></td>
</tr>


<button type=\"submit\" class=\"btn btn-default\"><span class=\"glyphicon glyphicon-remove\"></span></button>
-->
                </table>
            </div>
            <div class="col-md-1"> 
                <?php echo "<a class=\"btn btn-default\" href=\"" . $red_page . "\" style=\"float: left;\" >Back</a>";?>
            </div>
            <div class="col-md-2"> 
                <a class="btn btn-default" href="index.php" >Home</a>
                <a class="btn btn-default" href="search.php?query=&cfilter=all&cfilter2=all&sorting=data">Full list</a>
            </div>

        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10"> 
                <?php
                echo "<td>
                             <form action=\"delete.php\" method=\"GET\" style=\"float: left;\">
                               <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                               <input type=\"hidden\" name=\"reason\" value=\"plain\"></input>
                               <input type=\"hidden\" name=\"page\" value=\"" . $red_page . "\"></input>
                               <input type=\"submit\" value=\"Cancella\" class=\"btn btn-default\" onclick=\"return confirm('Vuoi veramente cancellare questo record?')\"></input>
                            </form>
                            </td>";
                if ($row['q2017']=='Non Pagato'){
                    echo "<td>
                             <form action=\"delete.php\" method=\"GET\" style=\"float: left;\">
                               <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                               <input type=\"hidden\" name=\"reason\" value=\"not_paid\"></input>
                               <input type=\"hidden\" name=\"page\" value=\"" . $red_page . "\"></input>
                               <input type=\"submit\" value=\"Elimina per pagamento\" class=\"btn btn-default\" onclick=\"return confirm('Vuoi veramente cancellare questo record?')\"></input>
                            </form>
                            </td>";
                }
                echo "<td>
                             <form action=\"entry_edit.php\" method=\"GET\" style=\"float: left;\">
                               <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                               <input type=\"submit\" value=\"Modifica\" class=\"btn btn-default\"></input>
                            </form>
                            </td>";
                ?>
            </div>
        </div>


        <br>
    </body>
</html>