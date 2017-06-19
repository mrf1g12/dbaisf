<?php
function format_data($dat)
{
    $date=explode('-',$dat);
    return $date[2] . "-" . $date[1] . "-" . $date[0];
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Search results</title>
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
                <h2>Pagamento quota associativa 2017</h2>
            </div>
            <div class="col-md-3"></div>
        </div>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <form action="payment.php" method="POST">
                    <table class='table'>
                        <tr>
                            <td width="30%" align="right"><b>Nome</b></td>
                            <td>
                                <input name="nome" required="required" type="text" <?php echo (isset($_GET['nome']) ? "value=".$_GET['nome'] : "placeholder=\"Il tuo nome\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Cognome</b></td>
                            <td>
                                <input name="cognome" required="required" type="text" <?php echo (isset($_GET['cognome']) ? "value=".$_GET['nome'] : "placeholder=\"Il tuo cognome\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Data di nascita</b></td>
                            <td>
                                <input name="dob" required="required" type="date" size="35" value="<?php echo (isset($_GET['data']) ? format_data($_GET['data']) : ""); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Email</b></td>
                            <td>
                                <input name="email" required="required" type="email" <?php echo (isset($_GET['email']) ? "value=".$_GET['email'] : "placeholder=\"La tua email\""); ?> size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Corso di Studi</b></td>
                            <td>
                                <select id="studi" name="studi" required="required">
                                    <option disabled selected value> -- seleziona un'opzione -- </option>
                                    <option value="triennale" <?php echo ($_GET['studi']=='Laurea triennale' ? "selected" : "") ?>>Laurea triennale</option>
                                    <option value="magistrale"<?php echo ($_GET['studi']=='Laurea magistrale' ? "selected" : "") ?>>Laurea magistrale</option>
                                    <option value="phd" <?php echo ($_GET['studi']=='Dottorato di ricerca' ? "selected" : "") ?>>Dottorato di ricerca</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Metodo di pagamento</b></td>
                            <td>
                                <select id="metodo" name="metodo" required="required">
                                    <option disabled selected value="empty"> -- seleziona un'opzione -- </option>
                                    <option value="bonifico">Bonifico bancario</option>
                                    <option value="PayPal">PayPal</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right">
                                <div class="tiny_skip"></div>
                                <b>Quota</b>
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
                                <button class="btn btn-primary" type="submit" value="Submit">Invia</button>
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
                                        $('.quota').html('Bonifico: 5.00');
                                    } else{
                                        $('.quota').html('Bonifico: 10.00€');
                                    }
                                }
                            });
                        </script>
                        <script>
                            $('#studi').change(function() {
                                if($(this).val() === 'triennale' || $(this).val()==='magistrale'){
                                    if($('#metodo').val()=='bonifico'){
                                        $('.quota').html('Bonifico: 5.00€');
                                    } else if($('#metodo').val()=='PayPal'){
                                        $('.quota').html('PayPal: 5.45€');
                                    }
                                } else if($(this).val() === 'phd'){
                                    if($('#metodo').val()=='bonifico'){
                                        $('.quota').html('Bonifico: 10.00€');
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