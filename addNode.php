<?php //script for adding different types of nodes
    $type = $_POST["type"];
    require "dbconnect.php";
    //Connect to db
    $conn = @new mysqli($host, $user, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    switch($type){
        case 0: //nawierzchnia
            $name = $_POST["name"];
            $startCoords = $_POST["startCoords"];
            $endKM = $_POST["endKM"];
            $endCoords = $_POST["endCoords"];
            $sql = "INSERT INTO nawierzchnie VALUES ('$name','$startCoords', '$endKM', '$endCoords',UUID())";
            if ($conn->query($sql) == TRUE) {
                //redirect home
                $conn->commit();
                $conn->close();
                header("Location: http://sump-osad.pl/osad_FWD-Dynatest/manage.php");
                die();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
                $conn->close();
                header("HTTP/1.1 500 Internal Server Error");
            }
            break;
        case 1: //pas
            $nawierzchniaID = $_POST["nawierzchniaID"];
            $dir = $_POST["dir"];
            $pos = $_POST["pos"];
            $sqlCheck = "SELECT * FROM pasy WHERE nawierzchniaID='$nawierzchniaID', pos='$pos'";
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                die("Overlapping pos");
            }
            $sql = "INSERT INTO pasy VALUES (UUID(),'$dir', '$nawierzchniaID', '$pos')";
            if ($conn->query($sql) == TRUE) {
                //redirect home
                $conn->commit();
                $conn->close();
                header("Location: http://sump-osad.pl/osad_FWD-Dynatest/manage.php");
                die();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
                $conn->close();
                header("HTTP/1.1 500 Internal Server Error");
            }
            break;
        case 2: //sekcja
            $pasID = $_POST["pasID"];
            $filename = $_POST["sekcjaSelect2"];
            $sql = "UPDATE sekcje SET pasID='$pasID' WHERE filename='$filename' ";
            if ($conn->query($sql) == TRUE) {
                //redirect home
                $conn->commit();
                $conn->close();
                header("Location: http://sump-osad.pl/osad_FWD-Dynatest/manage.php");
                die();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
                $conn->close();
                header("HTTP/1.1 500 Internal Server Error");
            }
            break;
    }
?>