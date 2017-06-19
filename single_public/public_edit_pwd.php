<?php
// get VID from session

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
    return date("d/m/y", $time);
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Single entry result</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
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
        $sid = $_POST['id'];
        $email = $_POST['email'];
        ?>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:342px;height:124px;" src="../AISF_logo.png" alt="AISF_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center">Modifica password</h2>
            </div>
            <div class="col-md-2"></div>
        </div>
        <div class="small_skip"></div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <form method="POST" action="public_update_pwd.php">
                    <table class='table'>
                        <tr>
                            <td>Email</td>
                            <td>
                                <input type="text" name="email" value="<?php echo $email; ?>" size="30">
                            </td>
                        </tr>
                        <tr>
                            <td>Vecchia password</td>
                            <td>
                                <input type="password" name="old_password" size="30">
                            </td>
                        </tr>
                        <tr>
                            <td>Nuova password</td>
                            <td>
                                <input type="password" name="new_password" id="new_pwd" maxlength="10" size="30">
                            </td>
                        </tr>
                         <tr>
                            <td>Conferma nuova password</td>
                            <td>
                                <input type="password" name="confirm_new_password" id="confirm_new_pwd" maxlength="10" size="30">
                                <p id="loginError" style="display:none;">Password diverse!</p>
                            </td>
                        </tr>
                        
                        <script>
                            $('#confirm_new_pwd').change(function() { 
                                if($(this).val() != $('#new_pwd').val()){
                                    $('#loginError').show();
                                    $('#button').prop('disabled', true);
                                } else {
                                    $('#loginError').hide();
                                    $('#button').prop('disabled', false);
                                }
                            });
                        </script>
                        <!--     <tr>
<td><div class="tiny_skip"></div></td>
<td><div class="tiny_skip"></div></td>
</tr> -->
                        <tr>
                            <td><div class="tiny_skip"></div></td>
                            <td>
                                <div class="tiny_skip"></div>
                                <input type="hidden" name="id" value="<?php echo $sid; ?>">
                                <input id='button' type="submit" value="Invia" class="btn btn-primary btn-lg" style="float: center;"/>
                            </td>
                        </tr>
                    </table>
                </form >
            </div>
            <div class="col-md-4"></div>
        </div>
        <div class="med_skip"></div>
        <div class="med_skip"></div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <form action="singleEntry_public.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $sid;?>"/>
                    <button class="btn btn-default" type="submit" style="float: left;">Torna al tuo record</button>
                </form>
                <a class="btn btn-default" href="single_login.php" >Logout</a>
            </div>
            <div class="col-md-4"></div>
        </div>


        <br>
    </body>
</html>