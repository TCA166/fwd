<?php //Script for creating a readable json tree of the structures in the db

    function createTree($limit){
        //Script loaded in index.php made for loading and displaying the select options
        require "dbconnect.php";
        //Connect to db
        $conn = @new mysqli($host, $user, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM nawierzchnie";
        $nawierzchnie = $conn->query($sql);
        $result = array();
        while($nawierzchnia = $nawierzchnie->fetch_assoc()){
            $sql = "SELECT * FROM pasy WHERE nawierchniaID='" . $nawierzchnia["ID"] . "'";
            $nawierzchniaArr = array();
            $nawierzchniaArr["text"] = $nawierzchnia["name"];
            $nawierzchniaNodes = array();
            $nawierzchniaArr["type"] = 0;
            $nawierzchniaArr["ID"] = $nawierzchnia["ID"];
            $nawierzchniaArr["backColor"] = "#DEE0E0";
            if($limit < 2){
                $pasy = $conn->query($sql);
                while($pas = $pasy->fetch_assoc()){
                    $pasArr = array();
                    $text = $pas["pos"];
                    if($pas["dir"] == 1){
                        $text = $text . "+";
                    }
                    else{
                        $text = $text . "-";
                    }
                    $pasArr["text"] = "Pas " . $text;
                    $pasArr["type"] = 1;
                    $pasArr["ID"] = $pas["ID"];
                    $pasArr["backColor"] = "#D2D5D6";
                    if($limit < 1){
                        $sql = "SELECT * FROM sekcje WHERE pasID='" . $pas["ID"] . "'";
                        $sekcje = $conn->query($sql);
                        $pasNodes = array();
                        while($sekcja = $sekcje->fetch_assoc()){
                            $sekcjaArr = array();
                            $sekcjaArr["type"] = 2;
                            $sekcjaArr["backColor"] = "#B1B5BA";
                            $sekcjaArr["ID"] = $sekcja["filename"];
                            $sesjaID = $sekcja["sesjaID"];
                            $sql = "SELECT * FROM sesje WHERE ID='$sesjaID'";
                            $query = $conn->query($sql);
                            $sesja = $query->fetch_assoc();
                            $sekcjaArr["sesja"] = $sesja;
                            $sekcjaArr["text"] = $sekcja["startKM"] . "km-" . $sekcja["endKM"] . "km | " . $sesja["date"];
                            $pasNodes[] = $sekcjaArr;
                        }
                        if(count($pasNodes) > 0){
                            $pasArr["nodes"] = $pasNodes;
                        }
                    }
                    $nawierzchniaNodes[] = $pasArr;
                }
                if(count($nawierzchniaNodes) > 0){
                    $nawierzchniaArr["nodes"] = $nawierzchniaNodes;
                }
            }
            $result[] = $nawierzchniaArr;
        }
        return json_encode($result);
    }

    function createCampaignTree(){
        require "dbconnect.php";
        //Connect to db
        $conn = @new mysqli($host, $user, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM sesje";
        $json = array();
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $el = array();
            $ID = $row["ID"];
            $el["ID"] = $ID;
            $el["text"] = $row["name"] . " | " . $row["date"];
            $el["type"] = 0;
            $el["nodes"] = array();
            $sql = "SELECT * FROM sekcje WHERE sesjaID='$ID'";
            $sekcje = $conn->query($sql);
            while($sekcja = $sekcje->fetch_assoc()){
                $sekArr = array();
                $sekArr["ID"] = $sekcja["filename"];
                $sekArr["text"] = $sekcja["startKM"] . "-" . $sekcja["endKM"];
                $sekArr["type"] = 1;
                $el["nodes"][] = $sekArr;
            }
            $json[] = $el;
        }
        return json_encode($json);
    }
?>