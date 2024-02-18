<?php
    $session1 = $_POST["sessionSelect1"];
    $session2 = $_POST["sessionSelect2"];
    //ok so find the two files
    $path1 = realpath("./fwdData/" . $session1 . ".json");
    $path2 = realpath("./fwdData/" . $session2 . ".json");
    //before deleting file2 we get the contents
    $jsonData1 = file_get_contents($path1);
    $jsonData2 = file_get_contents($path2);
    $json1 = json_decode($jsonData1, true);
    $json2 = json_decode($jsonData2, true);
    $jsonM = array_merge($json1, $json2);
    $jsonS = array();
    $len = count($jsonM);
    echo $len;
    //Selection sort because im lazy
    for($i = 0; $i < $len; $i++){
        //find the smallest element
        $min = str_replace("NA", "", $jsonM[0]["station"]) + 0;
        $k = 0;
        for($j = 1; $j < count($jsonM); $j++){
            $station = $jsonM[$j]["station"];
            $station = str_replace("NA", "", $station) + 0;
            if($station < $min){
                $min = $station;
                $k = $j;
            }
        }
        $jsonS[$i] = $jsonM[$k];
        array_splice($jsonM, $k, 1);
        /*
        print_r($jsonM);
        echo "<br>";
        print_r($jsonS);
        echo "<br>";
        */
    }
    $jsonDataS = json_encode($jsonS);
    $jsonFile = fopen($path1, "w") or header("HTTP/1.1 500 Internal Server Error");
    fwrite($jsonFile, $jsonDataS);
    fclose($jsonFile);
    //the idea behind merging the json is to merge plainly and then sort
    if(!unlink($path2)){
        die("Couldn't delete the associated json file");
    }
    require_once "../dbconnect.php";
    //Connect to db
    $conn = @new mysqli($host, $user, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //we need to sanitise input to prevent Sqlinjection
    $session2 = str_replace(["'", "AND", "OR", "%", "-", " ", ";"], "", $session2); //rudimentary sanitasation
    $sql = "DELETE FROM sekcje WHERE filename='$session2'";
    if ($conn->query($sql) !== TRUE) {
        die("Error deleting record: " . $conn->error);
    }
    $conn->commit();
    $conn->close();
    header("Location: http://sump-osad.pl/osad_FWD-Dynatest/manage.php");
    die();
?>