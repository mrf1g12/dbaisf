<?php
// get VID from session
#require('access.php');
#$ID = $_SESSION['ID'];


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

$uni_arr = array("BARI","BOLOGNA","CAGLIARI","CALABRIA","CAMERINO","CATANIA","FERRARA","FIRENZE","GENOVA","INSUBRIA","L'AQUILA","MESSINA","MILANO","BICOCCA","BRESCIA","MODENA e REGGIO","NAPOLI","CASERTA","PADOVA","PALERMO","PARMA","PAVIA","PERUGIA","PISA","ROMA La Sapienza","ROMA Tor Vergata","ROMA Tre","SALERNO","SALENTO","SIENA","TORINO","TRENTO","TRIESTE");

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

function format_data($dat)
{
    $date=explode('-',$dat);
    return $date[2] . "-" . $date[1] . "-" . $date[0];
}

function format_data2($dat)
{
    $date=explode('-',$dat);
    return $date[2] . "/" . $date[1] . "/" . $date[0];
}


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Iscrizione AISF</title>
        <link rel="stylesheet" href="css/bootstrap-min.css">
        <link rel="stylesheet" href="css/bootstrap-formhelpers-min.css" media="screen">
        <link rel="stylesheet" href="css/bootstrapValidator-min.css"/>
        <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" />
        <link rel="stylesheet" href="css/bootstrap-side-notes.css" />
        <style type="text/css">
            .col-centered {
                display:inline-block;
                float:none;
                text-align:left;
                margin-right:-4px;
            }
            .row-centered {
                margin-left: 9px;
                margin-right: 9px;
            } 
        </style>
        <style>
            .blank_row
            {
                height: 10px !important; /* Overwrite any previous rules */
                background-color: #FFFFFF;
            }
            .blank_space
            {
                height: 100px !important; /* Overwrite any previous rules */
                background-color: #FFFFFF;
            }
            .tiny_skip{
                margin-top: 10px;
                margin-bottom: 10px}
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
            .divider{
                width:5px;
                height:auto;
                display:inline-block;
            }
            .btn-space {
                margin-right: 5px;
            }
        </style>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="js/bootstrap-min.js"></script>
        <script src="js/bootstrap-formhelpers-min.js"></script>
        <script type="text/javascript" src="js/bootstrapValidator-min.js"></script>
        <script src="//oss.maxcdn.com/momentjs/2.8.2/moment.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#form').bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        nome: {
                            message: 'Nome non valido',
                            validators: {
                                notEmpty: {
                                    message: 'Il nome è necessario e non può essere vuoto'
                                },
                                stringLength: {
                                    min: 2,
                                    max: 30,
                                    message: 'Il nome deve essere più lungo di 1 carattere.'
                                },
                            }
                        },
                        cognome: {
                            message: 'Cognome non valido',
                            validators: {
                                notEmpty: {
                                    message: 'Il cognome è necessario e non può essere vuoto'
                                },
                                stringLength: {
                                    min: 2,
                                    max: 30,
                                    message: 'Il cognome deve essere più lungo di 1 carattere.'
                                },
                            }
                        },
                        luogo_nascita: {
                            validators: {
                                notEmpty: {
                                    message: 'Il luogo di nascita non può essere vuoto'
                                }
                            }
                        },
                        data_nascita: {
                            validators: {
                                date: {
                                    message: 'La data di nascita non è valida.',
                                    format: 'DD/MM/YYYY'
                                },
                                callback: {
                                    message: 'Data fuori range.',
                                    callback: function(value, validator) {
                                        var m = new moment(value, 'DD/MM/YYYY', true);
                                        if (!m.isValid()) {
                                            return false;
                                        }
                                        return m.isAfter('01/01/1900') && m.isBefore('01/01/2000');
                                    }
                                }
                            }
                        },
                        cf: {
                            message: 'Codice fiscale non valido',
                            validators: {
                                notEmpty: {
                                    message: 'Il Codice fiscale è necessario e non può essere vuoto'
                                },
                                stringLength: {
                                    min: 16,
                                    max: 16,
                                    message: 'Il Codice fiscale deve essere di 16 caratteri.'
                                },
                            }
                        },
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'L\'email non può essere vuota.'
                                },
                                emailAddress: {
                                    message: 'L\'email inserita non è valida.'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 65,
                                    message: 'L\'indirizzo email deve essere più lungo di 6 caratteri.'
                                }
                            }
                        },
                    }
                });
            });
        </script>
    </head>
    <?php 
    $sid = $_POST['id'];

    //echo "cane: ".$sid;

    // select single row, using ID

    $stringa = "SELECT * FROM " . $table . " WHERE id = '".$sid."'";
    $result = $mysqli->query($stringa);
    $entries = $result->num_rows;
    $row = $result->fetch_array();


    if (in_array($row['uni'],$uni_arr)){
        $uni = $row['uni'];
    } else {
        $uni_exploded = explode(" ", $row['uni']);
        //print_r($uni_exploded);
        if ($uni_exploded[0]=='ESTERO:') {
            $uni = 'estero';
            $uni_estero = trim(str_replace('ESTERO:','',$row['uni']));
        } else {
            $uni = 'altro';
        }
    }

    $addr = explode(" ", $row['indirizzo']);
    $num_elm = count($addr);
    $stato = $addr[$num_elm-1];
    $cap = $addr[$num_elm-2];
    $prov = $addr[$num_elm-3];
    $citta = $addr[$num_elm-4];
    for ($i=0; $i<$num_elm-4; $i++){
        $indirizzo .= $addr[$i]. " "; 
    }
    $indirizzo;

    ?>
    <body>
        <form action="https://www.ai-sf.it/dbaisf/public_edit_record.php" method="POST" id="form" class="form-horizontal">
            <input type="hidden" name="id" value="<?php echo $sid ?>"/>
            <div class="row">
                <div class="col-md-3" style="text-align:center">
                    <div class="small_skip">
                        <a  href="https://www.icps2017.it/"><img style="width:228px;height:83px;" src="../AISF_logo.png" alt="AISF_logo"></a>
                        <!-- <a  href="https://stripe.com/"><img style="width:114;height:41px;" src="stripe_logo.png" alt="stripe_logo"></a>-->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="small_skip"></div>
                    <div class="row" align="center">
                        <h2>Modifica la tua iscrizione</h2>
                    </div>
                    <noscript>
                        <div class="bs-callout bs-callout-danger">
                            <h4>JavaScript is not enabled!</h4>
                            <p>This payment form requires your browser to have JavaScript enabled. Please activate JavaScript and reload this page. Check <a href="https://enable-javascript.com" target="_blank">enable-javascript.com</a> for more informations.</p>
                        </div>
                    </noscript>
                    <div class="small_skip"></div>

                    <fieldset>

                        <!-- Form Name -->
                        <legend>Dati anagrafici</legend>

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Nome</label>
                            <div class="col-sm-6">
                                <input type="text/css" name="nome" class="address form-control" value="<?php echo $row['nome']?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Cognome</label>
                            <div class="col-sm-6">
                                <input type="text" name="cognome"  class="address form-control" value="<?php echo $row['cognome']?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Luogo di nascita</label>
                            <div class="col-sm-6">
                                <input type="text" name="luogo_nascita"class="city form-control" value="<?php echo $row['luogo_nascita']?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Data di nascita</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="data_nascita" value="<?php echo format_data2($row['data_nascita'])?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Codice Fiscale</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="cf" value="<?php echo $row['CF']?>"/>
                            </div>
                        </div>

                        <div class="blank_row"></div>



                        <legend>Residenza</legend>


                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">via/corso/piazza e numero</label>
                            <div class="col-sm-6">
                                <input type="text" name="indirizzo" value="<?php echo trim($indirizzo)?>" class="form-control">
                            </div>
                            <label class="col-sm-4 control-label" for="textinput">CAP</label>
                            <div class="col-sm-6">
                                <input type="text" name="cap" value="<?php echo $cap?>" class="form-control">
                            </div>
                            <label class="col-sm-4 control-label" for="textinput">Città</label>
                            <div class="col-sm-6">
                                <input type="text" name="citta" value="<?php echo $citta?>" class="form-control">
                            </div>
                            <label class="col-sm-4 control-label" for="textinput">Provincia</label>
                            <div class="col-sm-6"> 
                                <div class="state bfh-selectbox bfh-states" data-state="<?php echo trim($prov)?>" data-country="stato" data-name="prov" data-filter="true" placeholder="Provincia"></div>
                            </div>
                            <label class="col-sm-4 control-label" for="textinput">Stato</label>
                            <div class="col-sm-6"> 
                                <div class="country bfh-selectbox bfh-countries" data-name="stato" id="stato" data-flags="true" data-filter="true" data-country="<?php echo $stato?>"> </div>
                            </div>
                        </div>

                        <div class="blank_row"></div>



                        <legend>Dati accademici</legend>


                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Email</label>
                            <div class="col-sm-6">
                                <input type="text" name="email" maxlength="65" value="<?php echo $row['email']?>" class="email form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4" align="right">Università</label>
                            <div class="col-sm-6">
                                <div class="bfh-selectbox" id="uni" data-name="uni" name="uny" data-filter="true" data-value="<?php echo $uni?>">
                                    <div data-value="BARI">Università di BARI "Aldo Moro"</div>
                                    <div data-value="BOLOGNA">Università di BOLOGNA</div>
                                    <div data-value="CAGLIARI">Università di CAGLIARI</div>
                                    <div data-value="CALABRIA">Università della CALABRIA</div>
                                    <div data-value="CAMERINO">Università di CAMERINO</div>
                                    <div data-value="CATANIA">Università di CATANIA</div>
                                    <div data-value="FERRARA">Università di FERRARA</div>
                                    <div data-value="FIRENZE">Università di FIRENZE</div>
                                    <div data-value="GENOVA">Università di GENOVA</div>
                                    <div data-value="INSUBRIA">Università INSUBRIA Varese-Como</div>
                                    <div data-value="L'AQUILA">Università de L'AQUILA</div>
                                    <div data-value="MESSINA">Università di MESSINA</div>
                                    <div data-value="MILANO">Università di MILANO</div>
                                    <div data-value="BICOCCA">Università di MILANO - BICOCCA</div>
                                    <div data-value="BRESCIA">Università Cattolica del Sacro Cuore (Brescia)</div>
                                    <div data-value="MODENA e REGGIO">Università di MODENA e REGGIO EMILIA</div>
                                    <div data-value="NAPOLI">Università di NAPOLI "Federico II"</div>
                                    <div data-value="CASERTA">Seconda Università di NAPOLI (Caserta)</div>
                                    <div data-value="PADOVA">Università di PADOVA</div>
                                    <div data-value="PALERMO">Università di PALERMO</div>
                                    <div data-value="PARMA">Università di PARMA</div>
                                    <div data-value="PAVIA">Università di PAVIA</div>
                                    <div data-value="PERUGIA">Università di PERUGIA</div>
                                    <div data-value="PISA">Università di PISA</div>
                                    <div data-value="ROMA La Sapienza">Università di ROMA "La Sapienza"</div>
                                    <div data-value="ROMA Tor Vergata">Università di ROMA "Tor Vergata"</div>
                                    <div data-value="ROMA Tre">Università di ROMA TRE</div>
                                    <div data-value="SALERNO">Università di SALERNO</div>
                                    <div data-value="SALENTO">Università del SALENTO</div>
                                    <div data-value="SIENA">Università di SIENA</div>
                                    <div data-value="TORINO">Università di TORINO</div>
                                    <div data-value="TRENTO">Università di TRENTO</div>
                                    <div data-value="TRIESTE">Università di TRIESTE</div>
                                    <div data-value="altro">Altro</div>
                                    <div data-value="estero">Estero</div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group" id="altro" style="display:none;">
                            <label class="col-sm-4" align="right">Altra Università</label>
                            <div class="col-sm-6">
                                <input name="altra_uni" type="text" size="35" placeholder="Altra università" class="form-control" value="<?php echo $row['uni'];?>"/>
                            </div>
                        </div>
                        <div class="form-group" id="estero" style="display:none;">
                            <label class="col-sm-4" align="right">Università estera</label>
                            <div class="col-sm-6">
                                <input name="estero_uni" type="text" size="35" placeholder="Università estara" class="form-control" value="<?php echo $uni_estero;?>"/>
                            </div>
                        </div>
                        <script>
                            $('#uni').on('change.bfhselectbox', function() {
                                if($(this).val() === 'altro') {
                                    $('#altro').show();
                                    $('#estero').hide();
                                } else if ($(this).val() === 'estero'){
                                    $('#altro').hide();
                                    $('#estero').show();
                                } else {
                                    $('#altro').hide();
                                    $('#estero').hide();
                                }
                            });
                            $(document).ready(function() {
                                if($('#uni').val() === 'altro') {
                                    $('#altro').show();
                                    $('#estero').hide();
                                } else if ($('#uni').val() === 'estero'){
                                    $('#altro').hide();
                                    $('#estero').show();
                                } else {
                                    $('#altro').hide();
                                    $('#estero').hide();
                                }
                            });
                        </script>

                        <div class="form-group">
                            <label class="col-sm-4" align="right">Corso di studi</label>
                            <div class="col-sm-6">
                                <div class="bfh-selectbox" data-name="studi" name="study" id="studi" data-filter="true" data-value="<?php echo $row['studi']?>">
                                    <div data-value="XX"> -- seleziona un'opzione -- </div>
                                    <div data-value="Laurea triennale">Laurea triennale</div>
                                    <div data-value="Laurea magistrale">Laurea magistrale</div>
                                    <div data-value="Dottorato di Ricerca">Dottorato di Ricerca</div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="small_skip"></div>

                    <!-- Submit -->
                    <div class="control-group">
                        <div class="controls">
                            <center>
                                <button class="btn btn-primary btn-lg" type="submit" onclick="return confirm('Vuoi veramente modificare questo record?')" style="float: center;">Invia</button>
                            </center>
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-4" style="text-align:left">
<div class="small_skip"></div>
<a  href="https://www.icps2017.it/"><img style="width:135px;height:202px;" src="LOGO.jpg" alt="ICPS_logo"></a>
</div>-->
            </div>
        </form>
        <div class="med_skip"></div>
        <div class="med_skip"></div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <form action="https://www.ai-sf.it/dbaisf/single_login.php" method="GET">
                    <button class="btn btn-info" type="submit" style="float: left;">Logout</button>
                </form>
                <form action="https://www.ai-sf.it/dbaisf/singleEntry_public.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $sid;?>"/>
                    <button class="btn btn-success" type="submit" style="float: left;">Torna al tuo record</button>
                </form>
            </div>
            <div class="col-md-3"></div>
        </div>
        <div class="blank_space"></div>
    </body>
</html>
