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
//print_r($decoded);
//$date = date('Y_M_d_h_i_s', time());
//print_r("\r\n" . $date);



//--------------------------
//Prepare Data
//--------------------------

//var_dump($decoded);

//--------------------------
//Mininglog
//--------------------------
/*
$decoded["mininglog"] = explode("\n",$decoded["mininglog"]);
foreach ($decoded["mininglog"] as &$value) {
    $value = explode("]:",$value);
	$value[0] = explode(" ",$value[0]);
	array_pop($value[0]);
	array_pop($value[0]);
	$value[0] = implode(" ",$value[0]);
	$value[0] = substr($value[0],7);
//	echo $value[0] ."\n";
//	$date = date_create_from_format('M(space)d(space)H:i:s', $value[0]);
	
	//var_dump($date);
	//var_dump($value);
}
*/
//--------------------------
//GPUInfo
//--------------------------
$decoded["gpuInfo"] = explode("\n",$decoded["gpuInfo"]);
array_shift($decoded["gpuInfo"]);
foreach ($decoded["gpuInfo"] as &$value) {
   $value = substr($value,12);
  
}
array_pop($decoded["gpuInfo"]);
unset($value);
$gpuInfo = implode(';\r\n',$decoded["gpuInfo"]);
$numOfGpu = count($decoded["gpuInfo"]);


//--------------------------
//Sensors
//--------------------------
if (isset($decoded["Sensors"]))
{
     $sensors = $decoded["Sensors"];
	 $sensors =explode("\n",$sensors);
	 $sensors = preg_grep("/^temp1/",$sensors);
	 Var_dump($sensors);
	 $sensors = implode("\n",$sensors);
}else
{
     $sensors = "NA";
}


//--------------------------
//MinerDetails
//--------------------------
if (isset($decoded["scriptversion"]))
{
     $scriptVersion = $decoded["scriptversion"];
}else
{
     $scriptVersion = "NA";
}
echo("Version of Script:". $scriptVersion . "\r\n");   




if (isset($decoded["hostname"]))
{
     $hostName = $decoded["hostname"];
}else
{
     $hostName = "NA";
}
echo("hostName:". $hostName. "\r\n");  



$minerId = $decoded["name"];
//var_dump($decoded["ipAdress"]);
$ipAdress = preg_split("/\\r\\n|\\r|\\n/",$decoded["ipAdress"]);
//var_dump($ipAdress);
$ipAdress = array_shift($ipAdress);
//var_dump($ipAdress);




//--------------------------
//File-Logging
//--------------------------
// $filename = "log/" . $decoded["name"].".log";
// file_put_contents($filename, $decoded["mininglog"] , FILE_APPEND | LOCK_EX);
// $lines = file($filename);
// $lines = array_unique($lines);
// file_put_contents($filename, implode($lines)); 






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


$sql .= "INSERT INTO miner (MinerId, IpAdress, NumOfGpu, Timestamp, HostName, ScriptVersion) VALUES ('$minerId', '$ipAdress','$numOfGpu','$Timestamp', '$hostName', '$scriptVersion') ON DUPLICATE KEY UPDATE IpAdress='$ipAdress', NumOfGpu='$numOfGpu',Timestamp='$Timestamp', HostName = '$hostName', ScriptVersion = '$scriptVersion';\n";


//--------------------------
//SQL-Database -- Insert into gpus
//--------------------------
/*
$i=0;
foreach ($decoded["gpuInfo"] as $value) {
	$i++;
    $sql .= "INSERT INTO gpus ( MinerId,GpuType, GpuNumber) VALUES ('$minerId','$value', '$i' ); ON DUPLICATE KEY UPDATE GpuType='$value';\n";
}
*/


$sql .= "INSERT INTO gpus (MinerId,Temperature,GpuType) VALUES ('$minerId','$sensors', '$gpuInfo' ) ON DUPLICATE KEY UPDATE Temperature = '$sensors', GpuType = '$gpuInfo)';\n";

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