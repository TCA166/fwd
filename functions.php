<?php //a whole lotta functions. Better to store them here innit?
    //Temperature normalisation calculations
    function calc($normalT, $asphaltT, $d){
        $fT = 1 + (0.02 * ($normalT - $asphaltT));
        return $fT * $d;
    }
    //Gets the avg value of all Dx (D1,D2 etc). element must be the standard for this project json
    function getAvgDx($element, $x){
        $res = 0;
        $i = 0;
        foreach($element["drops"] as &$drop){
            $res += $drop["D" . strval($x)];
            $i++;
        }
        return $res/$i;
    }

    function parseFWD($fileData){
        //find the first drop data
        //$strPos = strpos($fileData, "STANDARD"); //get the beginning of the drop data section
        $strPos = strrpos($fileData, "*");
        $dropData = substr($fileData, $strPos, strlen($fileData) - $strPos); //get the drop data
        $stations = array(); //array of string data containing drop data from stations
        $dropData = preg_replace('! +!', ' ', $dropData); //remove double or triple spaces
        $lines = preg_split("/\r\n|\n|\r/", $dropData); //divide into lines
        $coordPresent = (strpos($dropData, "G0000000") == true);
        $station = array();
        $startIndex = 1 + $coordPresent;
        for($i = $startIndex; $i < count($lines); $i++){ //we iterate over the lines in the part of the file where drops are
            //print_r($lines[$i] . "\n");
            if(substr($lines[$i], 0, 1) == "S"){//if this line is the start of a new station def
                if($i != $startIndex){ //reset of the $station variable and append to the list
                    $station["SCI"] = calc($station["surface"], $station["asphalt"], getAvgDx($station, 1)) - calc($station["surface"], $station["asphalt"], getAvgDx($station, 2));
                    $station["BDI"] = calc($station["surface"], $station["asphalt"], getAvgDx($station, 2)) - calc($station["surface"], $station["asphalt"], getAvgDx($station, 3));
                    $station["BCI"] = calc($station["surface"], $station["asphalt"], getAvgDx($station, 3)) - calc($station["surface"], $station["asphalt"], getAvgDx($station, 4));
                    $stations[] = $station; 
                    $station = array(); 
                }
                if($coordPresent){ //if we have the good variant
                    $linePrev = explode(' ', $lines[$i - 1]); //get the coords
                    //print_r($linePrev);
                    $station["lat"] = $linePrev[1];
                    $station["lon"] = $linePrev[2];
                }
                else{ //else we just assign null
                    $station["lat"] = NULL;
                    $station["lon"] = NULL;
                }
                //get the general version data
                $line0 = explode(' ', $lines[$i]);
                $station["station"] = $line0 [1];
                $station["asphalt"] = $line0 [2];
                $station["surface"] = $line0 [3];
                $station["air"] = $line0 [4];
                $station["drops"] = array();
            }
            else if(substr($lines[$i], 0, 3) == "EOF"){ //If it's EOF break
                $station["SCI"] = calc($station["surface"], $station["asphalt"], getAvgDx($station, 1)) - calc($station["surface"], $station["asphalt"], getAvgDx($station, 2));
                $station["BDI"] = calc($station["surface"], $station["asphalt"], getAvgDx($station, 2)) - calc($station["surface"], $station["asphalt"], getAvgDx($station, 3));
                $station["BCI"] = calc($station["surface"], $station["asphalt"], getAvgDx($station, 3)) - calc($station["surface"], $station["asphalt"], getAvgDx($station, 4));
                $stations[] = $station; 
                $station = array(); 
            }
            else if(substr($lines[$i], 0, 1) != "G"){ //If it's just a regular line
                $lineX = explode(' ', $lines[$i]);
                $subElement = array();
                $subElement["stress"] = $lineX[1];
                $F_rz = $subElement["stress"] * pi() * (0.15*0.15);
                $subElement["stress"] = $F_rz;
                $y = 1;
                for($x = 2; $x < count($lineX); $x++){
                    if($lineX[$x] != ""){
                        $di = $lineX[$x];
                        $subElement["D" . $y] = (50 / $F_rz) * floatval($di);
                        $y++;
                    }
                }
                $subElement["SCI"] = calc($station["surface"], $station["asphalt"], $subElement["D1"]) - calc($station["surface"], $station["asphalt"], $subElement["D2"]);
                $subElement["BDI"] = calc($station["surface"], $station["asphalt"], $subElement["D2"]) - calc($station["surface"], $station["asphalt"], $subElement["D3"]);
                $subElement["BCI"] = calc($station["surface"], $station["asphalt"], $subElement["D3"]) - calc($station["surface"], $station["asphalt"], $subElement["D4"]);
                $station["drops"][] = $subElement;
            }
        }
        //now we have an array of stations
        return $stations;
    }   

    function getSectionsAsOptions(){
        //Script loaded in index.php made for loading and displaying the select options
        require "dbconnect.php";
        //Connect to db
        $conn = @new mysqli($host, $user, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM sesje";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo '<option value="' . $row["ID"] . '">' . $row["name"] . " | " .$row["date"] . '</option>';
            }
        }
        else{
            echo '<option value="err" >Tablica jest pusta</option>';
        }
        $conn -> close();
    }

    function sanitise($string){
        $string = str_replace("'", "", $string);
        $string = str_replace(";", "", $string);
        return $string;
    }
    //used in navtool to get a clickable list of road coordinates
    function getCoordsList(){
        require "dbconnect.php";
        $conn = @new mysqli($host, $user, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT name,startCoords,endCoords FROM nawierzchnie";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["startCoords"] != "" or $row["endCoords"] != ""){
                    echo '<li>' . $row["name"] . " | <span class='a' onclick='jumpTo(\"" . $row["startCoords"] . "\")'>start</span> do <span class='a' onclick='jumpTo(\"" . $row["endCoords"] . "\")'>koniec</span></li>";
                }
                
            }
        }
    }
?>