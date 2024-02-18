<?php
    $newJson = $_POST["json"];
    $filename = $_POST["filename"];
    $path = realpath("./fwdData/") . "/" . $session . ".json";
    $jsonFile = fopen($path, "w") or header("HTTP/1.1 500 Internal Server Error"); //create the new json file
    fwrite($jsonFile, $newJson); //write the stringified json
    fclose($jsonFile);
    header("Location: http://sump-osad.pl/osad_FWD-Dynatest/navTool.php");
    die();
?>