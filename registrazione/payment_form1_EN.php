<?php
// get VID from session
#require('access.php');
#$ID = $_SESSION['ID'];

#date_default_timezone_set('Australia/Melbourne');

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
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

$name = $_POST['nome'];
$surname = $_POST['cognome'];
$birthday = $_POST['data_nascita'];
$birthplace = $_POST['luogo_nascita'];
$cf = $_POST['cf'];
$addr1 = $_POST['indirizzo1'];
$city = $_POST['citta'];
$prov = $_POST['prov'];
$cap = $_POST['cap'];
$state = $_POST['stato'];
$address = $addr1 . " " . $city . " " . $prov . " " . $cap . " " . $state;
$email = $_POST['email'];
$uni1 = $_POST['uni'];
if ($uni1=='altro'){
    $university = $_POST['altra_uni'];
} elseif ($uni1=='estero'){
    $university = "ESTERO: " . $_POST['estero_uni'];
} else 
    $university = $_POST['uni'];
if ($_POST['studi']=='triennale'){
    $study='Laurea triennale';
} elseif ($_POST['studi']=='magistrale'){
    $study='Laurea magistrale';
} elseif ($_POST['studi']=='phd'){
    $study='Dottorato di Ricerca';
}
$data = $date = date('Y-m-d');
//$data = $mysqli->real_escape_string("2017-11-10");
$stato2016 = '-';
$stato2017 = 'Non Pagato';
$appr = 'Non Approvato';

if (!empty($name) and !empty($surname) and !empty($birthday)){

    $stringa = "SELECT * FROM " . $table . " WHERE nome = '" . $name . "' AND cognome='" . $surname . "' AND data_nascita='" . $birthday."'";
    $result1 = $mysqli->query($stringa);
    #echo $stringa;

    #header ("location: www.google.it?num=" . $result1->num_rows);

    if ($result1->num_rows > 0) {
        echo "
        <script>
            var x=window.alert(\"I tuoi dati sono già presenti nel database AISF. Puoi procedere con il pagamento della quota associativa 2017.\")
        </script>";
    } else {
        //  if (strpos($from_url, 'edit') == false) { //If $from_url does NOT contain "edit", then add record
        $stringa = "INSERT IGNORE INTO " . $table . " (id, nome, cognome, luogo_nascita, data_nascita, CF, indirizzo, cap, email, uni, studi, data, q2016, q2017, appr) VALUES (UUID_SHORT(), '" . $name . "', '" . $surname . "', '" . $birthplace . "', '" . $birthday . "', '" . $cf . "', '" . $address . "', '" .  $cap . "', '" .  $email . "', '" . $university . "', '" . $study . "', '" . $data . "', '" . $stato2016 . "', '" . $stato2017 . "', '" . $appr . "')";
        $result = $mysqli->query($stringa);
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Payment form</title>
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
            <div class="col-md-3" style="text-align:center"><img style="width:228px;height:83px;" src="AISF_logo.png" alt="AISF_logo"></div>
            <div class="col-md-6" align="center">
                <h2>2017 Membership fee payment</h2>
            </div>
            <div class="col-md-3">
                <a href='http://www.ai-sf.it/dbaisf/payment_form1.php'>
                    <img style="width:48px;height:48px;" src="Italy-icon.png" alt="italian">
                </a>
            </div>
        </div>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <form action="payment.php" method="POST">
                    <table class='table'>
                        <tr>
                            <td width="30%" align="right"><b>Name</b></td>
                            <td>
                                <input name="nome" required="required" type="text" <?php echo (isset($name) ? "value='".$name."'" : "placeholder=\"Your name\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Surname</b></td>
                            <td>
                                <input name="cognome" required="required" type="text" <?php echo (isset($surname) ? "value='".$surname."'" : " placeholder=\"Your surname\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Date of birth</b></td>
                            <td>
                                <input name="dob" required="required" type="date" size="35" value="<?php echo (isset($birthday) ? $birthday : ""); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Email</b></td>
                            <td>
                                <input name="email" required="required" type="email" <?php echo (isset($email) ? "value=".$email : "placeholder=\"Your email\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Currently enrolled in</b></td>
                            <td>
                                <select id="studi" name="studi" required="required">
                                    <option disabled selected value> -- select an option -- </option>
                                    <option value="triennale" <?php if($study=='triennale'){echo "selected";} ?>>Bachelor</option>
                                    <option value="magistrale"<?php if($study=='magistrale'){echo "selected";} ?>>Master</option>
                                    <option value="phd" <?php if($study=='phd'){echo "selected";} ?>>Ph.D.</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Payment method</b></td>
                            <td>
                                <select id="metodo" name="metodo" required="required">
                                    <option disabled selected value="empty"> -- select an option -- </option>
                                    <option value="bonifico">Bank transfer</option>
                                    <option value="PayPal">PayPal</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right">
                                <div class="tiny_skip"></div>
                                <b>Fee</b>
                                <div class="tiny_skip"></div>
                            </td>
                            <td>
                                <div class="tiny_skip"></div>
                                <span class="quota"></span>
                                <div class="tiny_skip"></div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary" type="submit" value="Submit">Submit</button>
                            </td>
                        </tr>
                        <script>
                            $('#metodo').on('change', function() {
                                if($(this).val() === 'PayPal') {
                                    if($('#studi').val()=='triennale' || $('#studi').val()=='magistrale'){
                                        $('.quota').html('PayPal: 5.45€');
                                    } else{
                                        $('.quota').html('PayPal: 10.54€');
                                    }
                                } else {
                                    if($('#studi').val()=='triennale' || $('#studi').val()=='magistrale'){
                                        $('.quota').html('Bank transfer: 5.00€');
                                    } else{
                                        $('.quota').html('Bank transfer: 10.00€');
                                    }
                                }
                            });
                        </script>
                        <script>
                            $('#studi').change(function() {
                                if($(this).val() === 'triennale' || $(this).val()==='magistrale'){
                                    if($('#metodo').val()=='bonifico'){
                                        $('.quota').html('Bank transfer: 5.00€');
                                    } else if($('#metodo').val()=='PayPal'){
                                        $('.quota').html('PayPal: 5.45€');
                                    }
                                } else if($(this).val() === 'phd'){
                                    if($('#metodo').val()=='bonifico'){
                                        $('.quota').html('Bank transfer: 10.00€');
                                    } else if($('#metodo').val()=='PayPal'){
                                        $('.quota').html('PayPal: 10.54€');
                                    }
                                }
                            });
                        </script>
                        <!--  <script>
#$('#metodo').on('change', function() {
#    if($(this).val() === 'PayPal') {
#        if($('#studi').val()=='triennale' || $('#studi').val()=='magistrale'){
#            $('.quota').html('PayPal: 5.45€');
#        } else{
#            $('.quota').html('PayPal: 10.54€');
#        }
#    } else {
#        if($('#studi').val()=='triennale' || $('#studi').val()=='magistrale'){
#            $('.quota').html('Bonifico: 5.00');
#        } else{
#            $('.quota').html('Bonifico: 10.00€');
#        }
#    }
#});
#$('#studi').on('change', function() {
#    if($(this).val() === 'triennale' || $(this).val()==='magistrale'){
#        if ($('#metodo')=='PayPal'){
#            $('.quota').html('PayPal: 5.45€');
#        } else if ($('#metodo')=='bonifico'){
#            $('.quota').html('Bonifico: 5.00€');
#        }
#    } else if($(this).val()==='phd'){ 
#        if ($('#metodo')=='PayPal'){
#            $('.quota').html('PayPal: 10.54');
#        } else if ($('#metodo')=='bonifico'){
#            $('.quota').html('Bonifico: 10.00€');
#        }
#    }
#});
</script>-->
                    </table>
                </form>

            </div>
            <div class="col-md-3"></div>
        </div>


        <br>

    </body>
</html>