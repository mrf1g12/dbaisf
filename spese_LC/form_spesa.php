<?php
require('../access.php');
$ID = $_SESSION['ID'];

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$gmail_pwd = $dbinfo[15];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

function format_date($date){
    $data=explode("/",$date);
    return $data[2]."-".$data[1]."-".$data[0];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Pagamento quota associativa</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <style>
            .tiny_skip{
                margin-top: 10px;
                margin-bottom: 10px}
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
            <div class="col-md-3" style="text-align:center"><img style="width:228px;height:83px;" src="../AISF_logo.png" alt="AISF_logo"></div>
            <div class="col-md-6" align="center">
                <h2>Richiesta finanziamento Comitati Locali</h2>
            </div>
            <div class="col-md-3">
                <!--      <a href='http://www.ai-sf.it/dbaisf/payment_form1_EN.php'>
<img style="width:48px;height:48px;" src="United-Kingdom-icon.png" alt="english">
</a>-->
            </div>
        </div>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <form action="crea_spesa.php" method="POST" enctype="multipart/form-data">
                    <table class='table'>
                        <tr>
                            <td width="30%" align="right"><b>Comitato Locale</b></td>
                            <td>
                                <?php
                                if ($ID=='admin' || $ID=='esecutivo_aisf'){
                                    echo "<select id=\"lc\" name=\"lc\" required=\"required\">
                                                <option disabled selected value> -- seleziona un'opzione -- </option>";
                                    $stringa = "SELECT LC,lc_sigla FROM LC";
                                    $result = $mysqli->query($stringa);
                                    while($row = $result->fetch_assoc()){
                                        echo "<option value=\"". $row['lc_sigla'] . "\">". $row['LC'] . "</option>";
                                    }
                                    echo "</select>";
                                } else{
                                    $stringa = "SELECT lc_sigla FROM LC WHERE LC='".$ID."'";
                                    $result = $mysqli->query($stringa);
                                    $row = $result->fetch_assoc();
                                    echo "<input name=\"showlc\" readonly value=\"".$ID."\"/>
                                          <input name=\"lc\" type=\"hidden\" value=\"".$row['lc_sigla']."\"/>";            
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Nome evento</b></td>
                            <td>
                                <input name="subject" required="required" type="text" size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Descrizione e finalità</b></td>
                            <td>
                                <textarea name="descrizione" required="required" rows="4" cols="40"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Importo</b></td>
                            <td>
                                <input name="importo" required="required" type="text" size="35"/> euro
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Cofinanziato?</b></td>
                            <td>
                                <input type="radio" id="yes_cofi" name="cofinanziato" value="yes"/> Sì<br>
                                <input type="radio" id="no_cofi" name="cofinanziato" value="no" checked/> No
                            </td>
                        </tr>
                        <tr id="cofi" style="display:none;">
                            <td width="30%" align="right"><b>Cofinanziatori</b></td>
                            <td>
                                <textarea name="cofinanziatori" rows="4" cols="40"></textarea>
                            </td>
                        </tr>
                        <script>
                            $('#yes_cofi').on('change', function() {
                                $('#cofi').show();
                            });
                            $('#no_cofi').on('change', function() {
                                $('#cofi').hide();
                            });
                        </script>
                        <tr>
                            <td width="30%" align="right"><b>Beneficiario rimborso</b></td>
                            <td>
                                <input name="beneficiario" required="required" type="text" size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>IBAN</b></td>
                            <td>
                                <input name="iban" required="required" type="text" size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Documenti</b></td>
                            <td>
                                Inserire qui documenti a illustrazione dell'evento e <i>preventivi di spesa</i>.<br><br>
                                <input type="file" name="fileToUpload[]" multiple="multiple"/><br>
                                Si può caricare più di un file.<br>
                                <i>Formato file: pdf</i>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary" type="submit" value="Submit">Invia</button>
                            </td>
                        </tr>
                    </table>
                </form>

            </div>
            <div class="col-md-3"></div>
        </div>


        <br>

    </body>
</html>