<?php

require('access.php');
$ID = $_SESSION['ID'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Archivio AISF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:342px;height:124px;" src="AISF_logo.png" alt="AISF_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center">Archivio Telematico AISF</h2>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><h3>Autenticato come ID =
                <font color="green"><?php echo $ID;?>    </font>
                <small><a href="logout.php" ><b>LOG OUT</b></a></small>
                </h3></div>
            <div class="col-md-2"></div>
        </div>

        <br><br><br>

        <div class="row" style="text-align:center">
            <form action="search.php" method="GET">

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-3"><b>Cerca per nome</b></div>
                    <div  class="col-md-3"><b>Cerca per cognome</b></div>
                    <div class="col-md-2"><b>Cerca per Università</b></div>
                    <div  class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-3">
                        <input type="text" name="nome" placeholder="lasciare vuoto per 'tutti'" size=30/>
                    </div>
                    <div  class="col-md-3">
                        <input type="text" name="cognome" placeholder="lasciare vuoto per 'tutti'" size=30/>
                    </div>
                    <div  class="col-md-2">
                        <input type="text" name="uni" placeholder="lasciare vuoto per 'tutte'" size=30/>
                    </div>
                </div>

                <div class="small_skip" style="text-align:left">
                    <div></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div class="col-md-3"><b>Filtra su quota associativa</b></div>
                    <div class="col-md-3"><b>Filtra su approvato</b></div>
                    <div class="col-md-2"><b>Ordina per</b></div>
                    <div  class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div class="col-md-3">
                        <select name="qfilter">
                            <option value="all"></option>
                            <option value="Pagato">Pagato</option>
                            <option value="Non Pagato">Non pagato</option>
                            <option value="-">-</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="afilter">
                            <option value="all"></option>
                            <option value="Approvato">Approvato</option>
                            <option value="Non Approvato">Non Approvato</option>
                            <option value="-">-</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sorting">
                            <option value="data">Data iscrizione</option>
                            <option value="Cognome">Cognome</option>
                            <option value="Nome">Nome</option>
                            <option value="uni">Università</option>
                            <option value="quota">Quota</option>
                            <option value="approvato">Approvato</option>
                        </select>
                    </div>
                </div>

                <div class="med_skip" style="text-align:left">
                    <div></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-2"><input type="submit" value="Ricerca" class="btn btn-primary"/></div>
                </div>

            </form>
        </div>
        
        <div class="small_skip" style="text-align:left"></div>
        
        <div class="row" style="text-align:left">
            <div  class="col-md-2"></div>
            <div  class="col-md-8">
                <hr>
            </div>
            <div  class="col-md-2"></div>
        </div>
        
        <div class="small_skip" style="text-align:left"></div>
        
        <div class="row" style="text-align:left">
            <div class="row" style="text-align:left">
                <div  class="col-md-2"></div>
                <div  class="col-md-10">
                    <a class="btn btn-default" href="group_by_LC.php" >Membri per LC</a>
                    <a class="btn btn-default" href="group_by_uni.php" >Membri per Univ.</a>
                    <a class="btn btn-default" href="map.php" >Mappa</a>
                </div>
            </div>
        </div>
        
        <div class="med_skip" style="text-align:left"></div>
        
        <div class="row" style="text-align:left">
            <div  class="col-md-2"></div>
            <div  class="col-md-8">
                <hr>
            </div>
            <div  class="col-md-2"></div>
        </div>

        <?php
        if ($ID=='admin'){
            echo "
                        <div class=\"small_skip\" style=\"text-align:left\"></div>
                        <div class=\"row\" style=\"text-align:left\">
                            <div  class=\"col-md-2\"></div>
                            <div  class=\"col-md-3\">
                            <b>Pagamento</b><br>
                                <form action=\"edit_pay_appr.php\" method=\"GET\" style=\"float: left;\">
                                    <div class=\"row\" style=\"text-align:left\">
                                        <textarea name=\"name_string\" rows=\"4\" cols=\"40\"></textarea>
                                        <input type=\"hidden\" name=\"type\" value=\"pagamento\"></input>
                                    </div>
                                    <div class=\"row\" style=\"text-align:left\">
                                        <input type=\"submit\" value=\"Submit\" class=\"btn btn-default\"></input>
                                    </div>
                                </form>
                            </div>   
                            <div  class=\"col-md-3\">
                            <b>Approvazione</b><br>
                                <form action=\"edit_pay_appr.php\" method=\"GET\" style=\"float: left;\">
                                    <div class=\"row\" style=\"text-align:left\">
                                        <textarea name=\"name_string\" rows=\"4\" cols=\"40\"></textarea>
                                        <input type=\"hidden\" name=\"type\" value=\"approvato\"></input>
                                    </div>
                                    <div class=\"row\" style=\"text-align:left\">
                                        <input type=\"submit\" value=\"Submit\" class=\"btn btn-default\"></input>
                                    </div>
                                </form>
                            </div>
                            <div  class=\"col-md-2\">
                                <b>Controllo duplicati</b><br>
                                <a class=\"btn btn-default\" href=\"check_duplicates.php\">Check</a>
                            </div>
                        </div>
                ";
        }
        ?>




        <br><br>
        <div class="med_skip"></div>
        <div class="med_skip"></div>
        <p><small>

            <?php
            echo 'Current PHP version: ' . phpversion() . '<br>';
            ?>
            EV.MRF.
            </small></p>


    </body>
</html>