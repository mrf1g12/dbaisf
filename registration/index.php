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
        </style>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="js/bootstrap-min.js"></script>
        <script src="js/bootstrap-formhelpers-min.js"></script>
        <script type="text/javascript" src="js/bootstrapValidator-min.js"></script>
        <script src="//oss.maxcdn.com/momentjs/2.8.2/moment.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#payment-form').bootstrapValidator({
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
                        uny: {
                            validators: {
                                notEmpty: {
                                    message: 'Seleziona un\'Università.'
                                },
                                callback: {
                                    message: 'Seleziona un\'Università.',
                                    callback: function(validator) {
                                        var value = $('#uni').val();
                                        //document.write(value);
                                        if (value === "XX") {
                                            return false;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        study: {
                            validators: {
                                callback: {
                                    message: 'Seleziona un Corso di studi.',
                                    callback: function(validator,$field) {
                                        var value = $('#studi').val();
                                        value += "XX";
                                        //document.write(value);
                                        if (value == "XX"){
                                            //document.write(value);
                                            /*return {
                                                valid: false,
                                                message: 'Seleziona un Corso di studi.'
                                            }; */
                                            return false;
                                        }
                                        else if (value === "XXXX") {
                                            //alert(value);
                                            /*return {
                                                valid: false,
                                                message: 'Seleziona un Corso di studi.'
                                            };*/
                                            return false;
                                        } else{
                                            return true;
                                        }
                                    }
                                }
                            }
                        },
                        /*                             uni: {
                            validators: {
                                notEmpty: {
                                    message: 'L\'Università non può essere vuota.'
                                },
                                callback: {
                                    message: 'Seleziona un\'Università.',
                                    callback: function(value, validator) {
                                        if (value === "XX") {
                                            return false;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                       studi: {
                            validators: {
                                notEmpty: {
                                    message: 'Il Corso di studi non può essere vuoto.'
                                },
                                callback: {
                                    message: 'Seleziona il Corso di studi.',
                                    callback: function(value, validator) {
                                        if (value === "XX") {
                                            return false;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },*/
                    }
                });
            });
        </script>
    </head>
    <body>
        <form action="payment_form1.php" method="POST" id="payment-form" class="form-horizontal">
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
                        <h2>AISF - Modulo di iscrizione</h2>
                    </div>
                    <noscript>
                        <div class="bs-callout bs-callout-danger">
                            <h4>JavaScript is not enabled!</h4>
                            <p>This payment form requires your browser to have JavaScript enabled. Please activate JavaScript and reload this page. Check <a href="http://enable-javascript.com" target="_blank">enable-javascript.com</a> for more informations.</p>
                        </div>
                    </noscript>
                    <div class="small_skip"></div>

                    <fieldset>

                        <!-- Form Name -->
                        <legend>Dati anagrafici</legend>

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Nome</label>
                            <div class="col-sm-6">
                                <input type="text/css" name="nome" class="address form-control" placeholder="Il tuo nome"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Cognome</label>
                            <div class="col-sm-6">
                                <input type="text" name="cognome"  class="address form-control" placeholder="Il tuo cognome"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Luogo di nascita</label>
                            <div class="col-sm-6">
                                <input type="text" name="luogo_nascita"class="city form-control" placeholder="Comune italiano o Stato estero">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Data di nascita</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="data_nascita" placeholder="DD/MM/YYYY" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Codice Fiscale</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="cf" placeholder="XHJGDS87H20Z100A"/>
                            </div>
                        </div>

                        <div class="blank_row"></div>



                        <legend>Residenza</legend>


                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Indirizzo</label>
                            <div class="col-sm-6">
                                <input type="text" name="indirizzo1" placeholder="Via/corso/piazza" class="address form-control">
                                <input type="text" name="cap" placeholder="CAP" class="form-control">
                                <input type="text" name="citta" placeholder="Città" class="form-control">
                            </div>
                            <label class="col-sm-4 control-label" for="textinput">Provincia</label>
                            <div class="col-sm-6"> 
                                <div class="state bfh-selectbox bfh-states" data-country="stato" data-name="prov" data-filter="true" placeholder="Provincia"></div>
                            </div>
                            <label class="col-sm-4 control-label" for="textinput">Stato</label>
                            <div class="col-sm-6"> 
                                <div class="country bfh-selectbox bfh-countries" data-name="stato" id="stato" data-flags="true" data-filter="true" data-country="IT"> </div>
                            </div>
                        </div>

                        <div class="blank_row"></div>



                        <legend>Dati accademici</legend>


                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="textinput">Email</label>
                            <div class="col-sm-6">
                                <input type="text" name="email" maxlength="65" placeholder="Email" class="email form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4" align="right">Università</label>
                            <div class="col-sm-6">
                                <div class="bfh-selectbox" id="uni" data-name="uni" name="uny" data-filter="true">
                                    <div data-value="XX"> -- seleziona un'opzione -- </div>
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
                                    <div data-value="SALENTO">Università del SALENTO</div>
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


                            <!--     <select id="uni" name="uni" required="required">
<option disabled selected value> -- seleziona un'opzione -- </option>
<option value="BARI">Università degli Studi di BARI "Aldo Moro"</option>
<option value="BOLOGNA">Università degli Studi di BOLOGNA</option>
<option value="CAGLIARI">Università degli Studi di CAGLIARI</option>
<option value="CALABRIA">Università della CALABRIA</option>
<option value="CAMERINO">Università degli Studi di CAMERINO</option>
<option value="CATANIA">Università degli Studi di CATANIA</option>
<option value="FERRARA">Università degli Studi di FERRARA</option>
<option value="FIRENZE">Università degli Studi di FIRENZE</option>
<option value="GENOVA">Università degli Studi di GENOVA</option>
<option value="INSUBRIA">Università degli Studi INSUBRIA Varese-Como</option>
<option value="L'AQUILA">Università degli Studi de L'AQUILA</option>
<option value="SALENTO">Università degli Studi del SALENTO</option>
<option value="MESSINA">Università degli Studi di MESSINA</option>
<option value="MILANO">Università degli Studi di MILANO</option>
<option value="BICOCCA">Università degli Studi di MILANO - BICOCCA</option>
<option value="BRESCIA">Università Cattolica del Sacro Cuore (Brescia)</option>
<option value="MODENA e REGGIO">Università degli Studi di MODENA e REGGIO EMILIA</option>
<option value="NAPOLI">Università degli Studi di NAPOLI "Federico II"</option>
<option value="CASERTA">Seconda Università degli Studi di NAPOLI (Caserta)</option>
<option value="PADOVA">Università degli Studi di PADOVA</option>
<option value="PALERMO">Università degli Studi di PALERMO</option>
<option value="PARMA">Università degli Studi di PARMA</option>
<option value="PAVIA">Università degli Studi di PAVIA</option>
<option value="PERUGIA">Università degli Studi di PERUGIA</option>
<option value="PISA">Università degli Studi di PISA</option>
<option value="ROMA La Sapienza">Università degli Studi di ROMA "La Sapienza"</option>
<option value="ROMA Tor Vergata">Università degli Studi di ROMA "Tor Vergata"</option>
<option value="ROMA Tre">Università degli Studi di ROMA TRE</option>
<option value="SALERNO">Università degli Studi di SALERNO</option>
<option value="SIENA">Università degli Studi di SIENA</option>
<option value="TORINO">Università degli Studi di TORINO</option>
<option value="TRENTO">Università degli Studi di TRENTO</option>
<option value="TRIESTE">Università degli Studi di TRIESTE</option>
<option value="altro">Altro</option>
<option value="estero">Estero</option>
</select>-->

                        </div>


                        <div class="form-group" id="altro" style="display:none;">
                            <label class="col-sm-4" align="right">Altra Università</label>
                            <div class="col-sm-6">
                                <input name="altra_uni" type="text" size="35" placeholder="Altra università" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group" id="estero" style="display:none;">
                            <label class="col-sm-4" align="right">Università estera</label>
                            <div class="col-sm-6">
                                <input name="estero_uni" type="text" size="35" placeholder="Università estara" class="form-control"/>
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
                        </script>

                        <div class="form-group">
                            <label class="col-sm-4" align="right">Corso di studi</label>
                            <div class="col-sm-6">
                                <div class="bfh-selectbox" data-name="studi" name="study" id="studi" data-filter="true">
                                    <div data-value="XX"> -- seleziona un'opzione -- </div>
                                    <div data-value="triennale">Laurea triennale</div>
                                    <div data-value="magistrale">Laurea magistrale</div>
                                    <div data-value="phd">Dottorato di Ricerca</div>
                                </div>
                            </div>
                        </div>

                        <legend>Informativa sulla privacy</legend>
                        <div class="form-group">
                            <div class="col-sm-4" align="right"><input type="radio" name="imgsel" checked></div>
                            <div class="col-sm-6">
                                Acconsento al trattamento dei dati personali secondo l'<a href='http://ai-sf.it/informativa_privacy1.pdf'>informativa</a> sulla privacy ai sensi dell’art. 13 D.Lgs. 196/2003.
                            </div>
                        </div>
                        <hr>


                    </fieldset>

                    <div class="small_skip"></div>

                    <!-- Submit -->
                    <div class="control-group">
                        <div class="controls">
                            <center>
                                <button class="btn btn-primary btn-lg" type="submit">Invia</button>
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
        <div class="blank_space"></div>
    </body>
</html>
