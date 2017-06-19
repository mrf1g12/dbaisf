<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">-->
<html>
    <head>
        <title>Log in</title>
      <!--  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:342px;height:124px;" src="../AISF_logo.png" alt="AISF_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h1 style="text-align:center">La tua iscrizione all'AISF</h1>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br><br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><b>Autenticati con email e password:</b></div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form action="https://www.ai-sf.it/dbaisf/single_public/singleEntry_public.php" method="POST">
                    email: <input type="text" name="email">        Password: <input type="password" name="password">
                    <input type="submit" name="submit" value="Login" > 
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>


    </body>
</html>