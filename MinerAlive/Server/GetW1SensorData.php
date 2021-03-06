<html>
<body>


<?php
 
//Make sure that it is a POST request.
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    throw new Exception('Request method must be POST!');
}
 
//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0){
    var_dump($contentType);
  var_dump($_SERVER);
  throw new Exception('Content type must be: application/json');
  
}
 
//Receive the RAW post data.
$content = trim(file_get_contents("php://input"));

//var_dump($content);
 
//Attempt to decode the incoming RAW post data from JSON.
$decoded = json_decode($content, true);
 
//If json_decode failed, the JSON is invalid.
if(!is_array($decoded)){
    throw new Exception('Received content contained invalid JSON!');
}
 
//Process the JSON.

//--------------------------
//Prepare Data
//--------------------------

//var_dump($decoded);




//--------------------------
//SQL-Database
//--------------------------

$mysqli = new mysqli("127.0.0.1", "root", "", "mineralive");
$Timestamp = $decoded["Timestamp"];
$sql ="";
 
 
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}



//--------------------------
//SQL-Database -- Insert into miner
//--------------------------

foreach ($decoded as $key => $value) {
  var_dump($key);
  var_dump($value);
  $sql .= "INSERT INTO temperature (SensorId, Temperature, Timestamp) VALUES ('$key', '$value[Temp]', '$value[Time]');\n";
}




var_dump ($sql);

if (!$mysqli->multi_query($sql)) {
    echo "Multi query failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

do {
    if ($res = $mysqli->store_result()) {
        var_dump($res->fetch_all(MYSQLI_ASSOC));
        $res->free();
    }
} while ($mysqli->more_results() && $mysqli->next_result());



?>  

</body>
</html>