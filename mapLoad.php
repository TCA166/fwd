<?php //returns the requested data json file
    function getFileData($filename){
        $path = realpath("./fwdData/" . $filename . ".json");
        //open the file
        $file = fopen($path, "r") or header("HTTP/1.1 500 Internal Server Error");
        //get the data
        $fileData = fread($file, filesize($path));
        //get out of there
        fclose($file);
        return $fileData;
    }

    //Script for fetching and returning the JSON data
    $filename = $_POST["filename"];
    echo getFileData($filename); //return the data to js
?>