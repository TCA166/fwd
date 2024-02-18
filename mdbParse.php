<?php //not working mdb parser. Screw atthost and their bad hosting "sry my dude we can't run A SINGLE COMMAND TO MAKE UR WHOLE PROJECT WORK CLEARLY ITS TOO HARD FOR US"
    if(isset($_FILES['MDB'])){
        $file = $_FILES["MDB"]['tmp_name'];
        if (!file_exists($file)) {
            die("Could not find database file.");
        }
        $query = 'SELECT * FROM Sessions';
        $db = new PDO("odbc:Driver=MDBTools;DBQ=$mdb_file;");
        if(!$db){
            exit("Connection failed");
        }
        $sql = "SELECT * FROM Sessions";
        $result = $db->query($sql);
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        
    }
?>
<form action="./mdbParse.php" method="post" enctype="multipart/form-data">
    <input type="file" name="MDB"/>
    <button type="submit">Submit</button>
</form>