<?php //Script for handling an incoming add FWD file request
    //Such a request should be a POST request with a file
    if(isset($_FILES['FWD'])){
        if(pathinfo($_FILES["FWD"]['name'])['extension'] != "FWD"){
            header("HTTP/1.1 415 Wrong file extension");
            die();
        }
        include "functions.php";
        $fileData = file_get_contents($_FILES["FWD"]['tmp_name']); //get the contents of the FWD file
        $json = parseFWD($fileData); //pass it to function
        if(!$json){
            echo "Unexpected error has occured during FWD parsing.";
            die();
        }
        $filename = pathinfo($_FILES["FWD"]["tmp_name"], PATHINFO_FILENAME);
        $path = realpath("./fwdData/") . "/" . $filename . ".json";
        $jsonFile = fopen($path, "w") or header("HTTP/1.1 500 Internal Server Error"); //create the new json file
        fwrite($jsonFile, json_encode($json)); //write the stringified json
        fclose($jsonFile);
        //chmod($path, 0777); 
        require_once "dbconnect.php"; //input a new row into the database
        //Connect to db
        $conn = @new mysqli($host, $user, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            //die("Connection failed: " . $conn->connect_error);
            header("HTTP/1.1 500 Internal Server Error");
        }
        $lines = preg_split("/\r\n|\n|\r/", $fileData);
        $startKM = substr($lines[7],17,8);
        $endKM = substr($lines[7],24,8);
        $date = substr($fileData, 11, 8);
        $rowid = $_POST["sessionSelect"];
        $sql = "INSERT INTO sekcje VALUES ('$startKM','$endKM',NULL,'$rowid','$filename')";
        if ($conn->query($sql) == TRUE) {
            //redirect home
            $conn->close();
            header("Location: http://sump-osad.pl/osad_FWD-Dynatest/manage.php");
            die();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            $conn->close();
            header("HTTP/1.1 500 Internal Server Error");
        }
    }  
?>
