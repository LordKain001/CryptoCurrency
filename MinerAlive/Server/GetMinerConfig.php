
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
 
 //var_dump($decoded);
 
if (isset($decoded["ipAdress"]))
{
     $ipAdress = $decoded["ipAdress"];
}else
{
     $ipAdress = NULL;
}
if (isset($decoded["minerUid"]))
{
     $minerUid = $decoded["minerUid"];
	 
}else
{
     $minerUid = NULL;
}

if(is_null($minerUid))
{
 $minerUid = "New_Miner_" . $ipAdress;
}


//--------------------------
//SQL-Database
//--------------------------

$mysqli = new mysqli("127.0.0.1", "root", "", "mineralive");

$sql ="";
 
if ($mysqli->connect_errno) {
     throw new Exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

$sql .= "SELECT * FROM `miner` WHERE `MinerId` = '$minerUid';\n";    

//if no entry is found, generate a new one
if(empty(multiquerry($mysqli,$sql)))
{
	$walletAdress = "47fWF6DkSumWrMxkpkM1vJ7ZBKrs8SaK7FJUgeVi622y5wedi39TNroQpyCFLyAF59BUGauxFeKXjXMZJiV2dU6iKoPdx2r";
	$poolAdress = "pool.supportxmr.com:3333";
	$currency = "monero7";
	$multipleIntesity = 50;
	//echo "create NEw Miner";
	$sql = "";
    $sql .= "INSERT INTO miner (MinerId, PoolAdress, WalletAdress, Currency, multipleIntesity) VALUES ('$minerUid', '$poolAdress','$walletAdress', '$currency', '$multipleIntesity');\n";
	multiquerry($mysqli,$sql);
}else
{
	echo json_encode(multiquerry($mysqli,$sql));
	
	exit;
}

$sql = "";
$sql .= "SELECT * FROM `miner` WHERE `MinerId` = '$minerUid';\n";    
echo json_encode( multiquerry($mysqli,$sql));


exit;


function multiquerry($mysqli,$sql)
{
	//var_dump($mysqli);
	//var_dump($sql);
	if (!$mysqli->multi_query($sql)) {
		echo "Multi query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	do {
		if ($res = $mysqli->store_result()) {
			return $res->fetch_all(MYSQLI_ASSOC);
			
			$res->free();
		}
	} while ($mysqli->more_results() && $mysqli->next_result());

}



?>  
