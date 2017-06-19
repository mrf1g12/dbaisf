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

function format_data($dat)
{
    $date=explode('-',$dat);
    return $date[2] . "-" . $date[1] . "-" . $date[0];
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>AISF - Modulo di Iscrizione</title>
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
                <h2>Registration form</h2>
            </div>
            <div class="col-md-3">
                <a href='http://www.ai-sf.it/dbaisf/registration_form.php'>
                    <img style="width:48px;height:48px;" src="Italy-icon.png" alt="italian">
                </a>
            </div>
        </div>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <form action="payment_form1_EN.php" method="POST">
                    <table class='table'>
                        <tr>
                            <td width="30%" align="right"><b>Name</b></td>
                            <td>
                                <input name="nome" required="required" type="text" placeholder="Your name" size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Surname</b></td>
                            <td>
                                <input name="cognome" required="required" type="text" placeholder="Your surname" size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Place of birth</b></td>
                            <td>
                                <input name="luogo_nascita" required="required" type="text" placeholder="Italian city or foreign country" size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Date of birth</b></td>
                            <td>
                                <input name="data_nascita" required="required" type="date" size="35" max="2000-01-01"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Italian <i>Codice Fiscale</i></b></td>
                            <td>
                                <input name="cf" required="required" type="text" size="35" value="N/A"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Residence address</b></td>
                            <td>
                                <input name="indirizzo1" required="required" type="text" size="35" placeholder="Street"/><br>
                                <input name="cap" required="required" type="text" size="13" placeholder="PostCode"/>
                                <input name="citta" required="required" type="text" size="20" placeholder="City"/><br>
                                <input name="prov" required="required" type="text" size="13" placeholder="Province"/>
                                <!--<input name="stato" required="required" type="text" size="20" placeholder="Stato"/>-->
                                <select name="drpPaesi" id="drpPaesi">
                                    <option value="AF">Afghanistan </option>
                                    <option value="AL">Albania </option>
                                    <option value="DZ">Algeria </option>
                                    <option value="AD">Andorra </option>
                                    <option value="AO">Angola </option>
                                    <option value="AI">Anguilla </option>
                                    <option value="AQ">Antartide </option>
                                    <option value="AG">Antigua e Barbuda </option>
                                    <option value="AN">Antille Olandesi </option>
                                    <option value="SA">Arabia Saudita </option>
                                    <option value="AR">Argentina </option>
                                    <option value="AM">Armenia </option>
                                    <option value="AW">Aruba </option>
                                    <option value="AU">Australia </option>
                                    <option value="AT">Austria </option>
                                    <option value="AZ">Azerbaigian </option>
                                    <option value="BS">Bahamas </option>
                                    <option value="BH">Bahrein </option>
                                    <option value="BD">Bangladesh </option>
                                    <option value="BB">Barbados </option>
                                    <option value="BE">Belgio </option>
                                    <option value="BZ">Belize </option>
                                    <option value="BJ">Benin </option>
                                    <option value="BM">Bermuda </option>
                                    <option value="BT">Bhutan </option>
                                    <option value="BY">Bielorussia </option>
                                    <option value="BO">Bolivia </option>
                                    <option value="BA">Bosnia-Erzegovina </option>
                                    <option value="BW">Botswana </option>
                                    <option value="BR">Brasile </option>
                                    <option value="BN">Brunei </option>
                                    <option value="BG">Bulgaria </option>
                                    <option value="BF">Burkina Faso </option>
                                    <option value="BI">Burundi </option>
                                    <option value="KH">Cambogia </option>
                                    <option value="CM">Camerun </option>
                                    <option value="CA">Canada </option>
                                    <option value="CV">Capo Verde </option>
                                    <option value="TD">Ciad </option>
                                    <option value="CL">Cile </option>
                                    <option value="CN">Cina </option>
                                    <option value="CY">Cipro </option>
                                    <option value="VA">Città del Vaticano </option>
                                    <option value="CO">Colombia </option>
                                    <option value="KM">Comore </option>
                                    <option value="CG">Congo </option>
                                    <option value="CD">Congo RDP </option>
                                    <option value="KR">Corea </option>
                                    <option value="KP">Corea del Nord </option>
                                    <option value="CI">Costa d'Avorio </option>
                                    <option value="CR">Costa Rica </option>
                                    <option value="HR">Croazia </option>
                                    <option value="CU">Cuba </option>
                                    <option value="DK">Danimarca </option>
                                    <option value="DM">Dominica </option>
                                    <option value="EC">Ecuador </option>
                                    <option value="EG">Egitto </option>
                                    <option value="SV">El Salvador </option>
                                    <option value="AE">Emirati Arabi </option>
                                    <option value="ER">Eritrea </option>
                                    <option value="EE">Estonia </option>
                                    <option value="ET">Etiopia </option>
                                    <option value="FJ">Fiji </option>
                                    <option value="PH">Filippine </option>
                                    <option value="FI">Finlandia </option>
                                    <option value="FR">Francia </option>
                                    <option value="GA">Gabon </option>
                                    <option value="GM">Gambia </option>
                                    <option value="GE">Georgia </option>
                                    <!--<option value="GS">Georgia del Sud e Isole Sandwich Meridionali </option>-->
                                    <option value="DE">Germania </option>
                                    <option value="GH">Ghana </option>
                                    <option value="JM">Giamaica </option>
                                    <option value="JP">Giappone </option>
                                    <option value="GI">Gibilterra </option>
                                    <option value="DJ">Gibuti </option>
                                    <option value="JO">Giordania </option>
                                    <option value="UK">Gran Bretagna </option>
                                    <option value="GR">Grecia </option>
                                    <option value="GD">Grenada </option>
                                    <option value="GL">Groenlandia </option>
                                    <option value="GP">Guadalupe </option>
                                    <option value="GU">Guam </option>
                                    <option value="GT">Guatemala </option>
                                    <option value="GF">Guiana francese </option>
                                    <option value="GN">Guinea </option>
                                    <option value="GQ">Guinea equatoriale </option>
                                    <option value="GW">Guinea-Bissau </option>
                                    <option value="GY">Guyana </option>
                                    <option value="HT">Haiti </option>
                                    <option value="HN">Honduras </option>
                                    <option value="HK">Hong Kong </option>
                                    <option value="IN">India </option>
                                    <option value="ID">Indonesia </option>
                                    <option value="IR">Iran </option>
                                    <option value="IQ">Iraq </option>
                                    <option value="IE">Irlanda </option>
                                    <option value="IS">Islanda </option>
                                    <!-- <option value="BV">Isola Bouvet </option>
<option value="KY">Isole Cayman </option>
<option value="CX">Isole Christmas </option>
<option value="CC">Isole Cocos (Keeling) </option>
<option value="CK">Isole Cook </option>
<option value="FK">Isole Falkland (Islas Malvinas) </option>
<option value="FO">Isole Faroe </option>
<option value="HM">Isole Heard e Mcdonald </option>
<option value="MP">Isole Marianne Settentrionali </option>
<option value="MH">Isole Marshall </option>
<option value="UM">Isole minori degli Stati Uniti </option>
<option value="NF">Isole Norfolk </option>
<option value="SB">Isole Salomone </option>
<option value="SJ">Isole Svalbard e Jan Mayen </option>
<option value="TC">Isole Turks e Caicos </option>
<option value="VG">Isole Vergini (GB) </option>
<option value="VI">Isole Vergini (USA) </option>
<option value="WF">Isole Wallis e Futuna </option>-->
                                    <option value="IL">Israele </option>
                                    <option selected="selected" value="IT">Italia </option>
                                    <option value="YU">Iugoslavia </option>
                                    <option value="KZ">Kazakistan </option>
                                    <option value="KE">Kenya </option>
                                    <option value="KG">Kirghizistan </option>
                                    <option value="KI">Kiribati </option>
                                    <option value="KW">Kuwait </option>
                                    <option value="LA">Laos </option>
                                    <option value="LS">Lesotho </option>
                                    <option value="LV">Lettonia </option>
                                    <option value="LB">Libano </option>
                                    <option value="LR">Liberia </option>
                                    <option value="LY">Libia </option>
                                    <option value="LI">Liechtenstein </option>
                                    <option value="LT">Lituania </option>
                                    <option value="LU">Lussemburgo </option>
                                    <option value="MO">Macao </option>
                                    <option value="MK">Macedonia </option>
                                    <option value="MG">Madagascar </option>
                                    <option value="MW">Malawi </option>
                                    <option value="MV">Maldive </option>
                                    <option value="MY">Malesia </option>
                                    <option value="ML">Mali </option>
                                    <option value="MT">Malta </option>
                                    <option value="MA">Marocco </option>
                                    <option value="MQ">Martinica </option>
                                    <option value="MR">Mauritania </option>
                                    <option value="MU">Mauritius </option>
                                    <option value="YT">Mayotte </option>
                                    <option value="MX">Messico </option>
                                    <option value="FM">Micronesia </option>
                                    <option value="MD">Moldavia </option>
                                    <option value="MC">Monaco </option>
                                    <option value="MN">Mongolia </option>
                                    <option value="MS">Montserrat </option>
                                    <option value="MZ">Mozambico </option>
                                    <option value="MM">Myanmar </option>
                                    <option value="NA">Namibia </option>
                                    <option value="NR">Nauru </option>
                                    <option value="NP">Nepal </option>
                                    <option value="NI">Nicaragua </option>
                                    <option value="NE">Niger </option>
                                    <option value="NG">Nigeria </option>
                                    <option value="NU">Niue </option>
                                    <option value="NO">Norvegia </option>
                                    <option value="NC">Nuova Caledonia </option>
                                    <option value="NZ">Nuova Zelanda </option>
                                    <option value="OM">Oman </option>
                                    <option value="NL">Paesi Bassi </option>
                                    <option value="PK">Pakistan </option>
                                    <option value="PW">Palau </option>
                                    <option value="PA">Panama </option>
                                    <option value="PG">Papua Nuova Guinea </option>
                                    <option value="PY">Paraguay </option>
                                    <option value="PE">Perù </option>
                                    <option value="PN">Pitcairn </option>
                                    <option value="PF">Polinesia francese </option>
                                    <option value="PL">Polonia </option>
                                    <option value="PT">Portogallo </option>
                                    <option value="PR">Puerto Rico </option>
                                    <option value="QA">Qatar </option>
                                    <option value="CZ">Repubblica Ceca </option>
                                    <option value="CF">Repubblica Centrafricana </option>
                                    <option value="DO">Repubblica Dominicana </option>
                                    <option value="RE">Reunion </option>
                                    <option value="RO">Romania </option>
                                    <option value="RW">Ruanda </option>
                                    <option value="RU">Russia </option>
                                    <option value="KN">Saint Kitts e Nevis </option>
                                    <option value="LC">Saint Lucia </option>
                                    <option value="PM">Saint Pierre et Miquelon </option>
                                    <option value="VC">Saint Vincent e Grenadine </option>
                                    <option value="WS">Samoa </option>
                                    <option value="AS">Samoa Americane </option>
                                    <option value="SM">San Marino </option>
                                    <option value="SH">Sant'Elena </option>
                                    <option value="ST">Sao Tome e Principe </option>
                                    <option value="SN">Senegal </option>
                                    <option value="SC">Seychelles </option>
                                    <option value="SL">Sierra Leone </option>
                                    <option value="SG">Singapore </option>
                                    <option value="SY">Siria </option>
                                    <option value="SK">Slovacchia </option>
                                    <option value="SI">Slovenia </option>
                                    <option value="SO">Somalia </option>
                                    <option value="ES">Spagna </option>
                                    <option value="LK">Sri Lanka </option>
                                    <option value="US">Stati Uniti </option>
                                    <option value="ZA">Sudafrica </option>
                                    <option value="SD">Sudan </option>
                                    <option value="SR">Suriname </option>
                                    <option value="SE">Svezia </option>
                                    <option value="CH">Svizzera </option>
                                    <option value="SZ">Swaziland </option>
                                    <option value="TJ">Tagikistan </option>
                                    <option value="TH">Tailandia </option>
                                    <option value="TW">Taiwan </option>
                                    <option value="TZ">Tanzania </option>
                                    <!--   <option value="TF">Territori australi francesi </option>
<option value="IO">Territori inglesi dell'Oceano Indiano </option>-->
                                    <option value="TP">Timor Est </option>
                                    <option value="TG">Togo </option>
                                    <option value="TK">Tokelau </option>
                                    <option value="TO">Tonga </option>
                                    <option value="TT">Trinidad e Tobago </option>
                                    <option value="TN">Tunisia </option>
                                    <option value="TR">Turchia </option>
                                    <option value="TM">Turkmenistan </option>
                                    <option value="TV">Tuvalu </option>
                                    <option value="UA">Ucraina </option>
                                    <option value="UG">Uganda </option>
                                    <option value="HU">Ungheria </option>
                                    <option value="UY">Uruguay </option>
                                    <option value="UZ">Uzbekistan </option>
                                    <option value="VU">Vanuatu </option>
                                    <option value="VE">Venezuela </option>
                                    <option value="VN">Vietnam </option>
                                    <option value="YE">Yemen </option>
                                    <option value="ZM">Zambia </option>
                                    <option value="ZW">Zimbabwe </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Email</b></td>
                            <td>
                                <input name="email" required="required" type="email" placeholder="Your email address" size="35"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>University</b></td>
                            <td>
                                <select id="uni" name="uni" required="required">
                                    <option disabled selected value> -- select an option -- </option>
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
                                    <option value="altro">Other</option>
                                    <option value="estero">Foreign University</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="altro" style="display:none;">
                            <td width="30%" align="right"><b>Other</b></td>
                            <td>
                                <input name="altra_uni" type="text" size="35" placeholder="Altra università"/>
                            </td>
                        </tr>
                        <tr id="estero" style="display:none;">
                            <td width="30%" align="right"><b>Foreign University</b></td>
                            <td>
                                <input name="estero_uni" type="text" size="35" placeholder="Università estera"/>
                            </td>
                        </tr>
                        <script>
                            $('#uni').on('change', function() {
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
                        <tr>
                            <td width="30%" align="right"><b>Currently enrolled in</b></td>
                            <td>
                                <select id="studi" name="studi" required="required">
                                    <option disabled selected value> -- select an option -- </option>
                                    <option value="triennale">Bachelor</option>
                                    <option value="magistrale">Master</option>
                                    <option value="phd">Ph.D.</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right"><b>Privacy policy</b></td>
                            <td>
                                <input type="radio" name="imgsel"  value="" checked> I agree with the treatment of my personal data according to the <a href='http://ai-sf.it/informativa_privacy1.pdf'>privacy information</a> (art. 13 D.Lgs. 196/2003).
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary" type="submit" value="Submit">Submit</button>
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