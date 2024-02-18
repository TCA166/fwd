<?php //Script loaded in index.php made for loading and displaying the select options
    require_once "dbconnect.php";
    //Connect to db
    $conn = @new mysqli($host, $user, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    include_once 'functions.php';
    if($_POST['mode'] == 1){
        $sql = "SELECT * FROM sekcje WHERE pasID='" . sanitise($_POST['id']) . "'";
    }
    else{
        $sql = "SELECT * FROM sekcje WHERE sesjaID='" . sanitise($_POST['id']) . "'";
    }
    $result = $conn->query($sql);
    $arr = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($_POST['mode'] == 1){
                $sesjaID = $row["sesjaID"];
                $sql = "SELECT * FROM sesje WHERE ID='$sesjaID'";
                $query = $conn->query($sql);
                $sesja = $query->fetch_assoc();
                $row["sesja"] = $sesja;
            }
            $arr[] = $row;
        }
    }
    $conn -> close();
    echo json_encode($arr);
?>