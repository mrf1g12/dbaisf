<?php

require('../access.php');
$ID = $_SESSION['ID'];


if ($ID!='admin'){
    header('Location: ' . '../badPrivileges.php'); 
}

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$api = $dbinfo[13];
$gmail_pwd = $dbinfo[15];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


$rid = $_GET['rid'];
$url = $_GET['url'];

//echo $rid;
//echo $url;

$stringa = "SELECT url_doc FROM spese_LC WHERE RID='".$rid."'";
$result = $mysqli->query($stringa);
$row = $result->fetch_assoc();

$url_list = $row['url_doc'];
$new_url_list = str_replace($url,'',$url_list);

$stringa = "UPDATE spese_LC SET url_doc=\"" . $new_url_list . "\" WHERE RID = '" . $rid . "'";
$result = $mysqli->query($stringa);



$file_naked = str_replace('www.ai-sf.it/dbaisf','',$url);
$target_file = '/web/htdocs/www.ai-sf.it/home/dbaisf/'.$file_naked;
//echo $target_file;
chmod($target_file, 0644);
unlink($target_file);

#$jotformAPI = new JotForm($api);
#$edit = array("40" => $name, "41" => $surname, "26" => $cf, "5" => $email);
#$jotformAPI->editSubmission($sid, $edit);

//$result->free();
$mysqli->close();

#echo $sid;    

header("location: " . "http://www.ai-sf.it/dbaisf/spese_lc/single_mov.php?rid=".$rid);

?>