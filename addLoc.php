<?php //Script for adding location data in a csv file to a pregenerated json data file
    if(isset($_FILES['csv'])){
        if(pathinfo($_FILES["csv"]['name'])['extension'] != "csv"){
            header("HTTP/1.1 415 Wrong file extension");
            die();
        }
        $fileData = file_get_contents($_FILES["csv"]['tmp_name']);
        if(strpos($fileData, ";") == false){
            header("HTTP/1.1 400 CSV file must be ; seperated");
            die();
        }
        $csv = str_getcsv($fileData, ";");
        $session = $_POST["sekcjaSelect"];
        include "mapLoad.php";
        $jsonData = json_decode(getFileData($session), true);
        if(!seeIfAnyNull($jsonData)){ //check if the file has localisation data
            header("HTTP/1.1 400 The session already has localisation data");
            echo "Błąd 400: Sesja ma już dane lokalizacyjne";
            die();
        }
        $arr = [];
        $dict = [];
        if($csv[9] == "LON3"){ //GGDKIA format
            $i = 13;
            $off = 9;
            $offLo = 3;
            $offLa = 2;
            $offKm = 0;
        }
        else{ //Our stripped down format
            $i = 3;
            $off = 3;
            $offLo = 1;
            $offLa = 0;
            $offKm = 2; 
        }
        while($i < count($csv)){
            $dict["lon"] = str_replace(",", ".", $csv[$i + $offLo]);
            //echo $csv[$i + 3];
            $dict["lat"] = str_replace(",", ".", $csv[$i + $offLa]);
            //echo $csv[$i + 2];
            $arr[floatval(str_replace(",", ".", $csv[$i + $offKm]))] = $dict;
            //echo floatval(str_replace(",", ".", $csv[$i])) . "\n";
            $dict = [];
            $i = $i + $off;
        }
        //echo " test ";
        //die();
        for($i = 0; $i < count($jsonData); $i++){
            $station = $jsonData[$i]["station"];
            $station = str_replace("NA", "", $station) + 0;
            //echo $station . "\n";
            try{
                $locData = $arr[$station];
                $jsonData[$i]["lon"] = $locData["lon"];
                $jsonData[$i]["lat"] = $locData["lat"];
            }
            catch (Exception $e){
                //echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
        $path = realpath("./fwdData/") . "/" . $session . ".json";
        $jsonFile = fopen($path, "w") or header("HTTP/1.1 500 Internal Server Error"); //create the new json file
        fwrite($jsonFile, json_encode($jsonData)); //write the stringified json
        fclose($jsonFile);
        //die();
    }
    header("Location: http://sump-osad.pl/osad_FWD-Dynatest/index.php");
    die();

    function seeIfAnyNull($arr){
        for($i = 0; $i < count($arr); $i++){
            if($arr[$i]["lat"] == null || $arr[$i]["lon"] == null){
                return true;
            }
        }
        return false;
    }
?>