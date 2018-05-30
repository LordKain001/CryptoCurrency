<?php



function GetMySqlData($ipAdress="", $user="", $password="", $dataBase="",$table="")
{
	$mysqli = new mysqli($ipAdress, $user, $password, $dataBase);
	$sql ="";
	if ($mysqli->connect_errno)
	{
    	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$sql .= "SELECT * FROM `$table` WHERE 1;\n";
	
	$sqlData = $mysqli->query($sql);

	return $sqlData->fetch_all(MYSQLI_ASSOC);
}


function GetProxyData($value='')
{
	$Workers =  trim(file_get_contents("http://localhost:8080/workers.json", False));

//Attempt to decode the incoming RAW post data from JSON.
$Workers = json_decode($Workers, true);
 
//If json_decode failed, the JSON is invalid.
if(!is_array($Workers)){
    throw new Exception('Received content contained invalid JSON!');
}

//echo '<pre>'; var_dump($Workers["hashrate"]); echo '</pre>';
//echo '<pre>'; var_dump($Workers["workers"]); echo '</pre>';

$workerinfo = array(
    "Worker name",
    "ip address",
    "Connection count",
    "Acc/Rej/Inv shares",
    "Total hashes",
    "Time with no share",
    "1 min",
    "10 min",  
    "1 hour",
    "12 hours",
    "24 hours");


date_default_timezone_set("UTC"); 
$currentTime = time();

foreach($Workers["workers"] as &$worker)
{
  $lastSubmitTime = intval($worker[7]/1000);
  $timediff = $currentTime - $lastSubmitTime;

  $worker["shares"] = "".$worker[3]."/".$worker[4]."/".$worker[5];
  $worker[7] = date("H:i:s",intval($timediff) );

  unset($worker[4]);
  unset($worker[5]);
}
//unset($worker);

return $Workers;
}




$Workers = GetProxyData();
$Minerinfo = GetMySqlData("127.0.0.1", "root", "", "mineralive", "miner");
$sqlGpu = GetMySqlData("127.0.0.1", "root", "", "mineralive", "gpus");


foreach ($sqlGpu as &$value) {
	unset($value["GpuNumber"]);
	unset($value["GpuType"]);
}
unset($value);


var_dump($sqlGpu);


  $ProxyClientsInfo = array(
  'WorkerName' => 'Default',
  'Connected' => 'Default',
  'InternalIp' => 'Default',
  'ExternalIp' => 'Default',
  'TimeWithoutShare' => 'Default',
  'TimeWithoutPost' => 'Default',
  'NumOfGpu' => 'Default',
  'HashRate24h' => '0'
   );

$ProxyClients = array();

//echo '<pre>'; var_dump($ProxyClients); echo '</pre>';
//echo '<pre>'; var_dump($Minerinfo); echo '</pre>';

$totalGpus = 0;
$totalHashrate = 0;
//echo '<pre>'; var_dump($Workers); echo '</pre>';
foreach($Workers["workers"] as $worker)
{
  //echo '<pre>'; var_dump($worker); echo '</pre>';


  $ProxyClientsInfo["WorkerName"] = $worker[0];
  $ProxyClientsInfo["ExternalIp"] = $worker[1];
  $ProxyClientsInfo["Connected"] = $worker[2];
  $ProxyClientsInfo["TimeWithoutShare"] = $worker[7];
  $ProxyClientsInfo["HashRate24h"] = $worker[12];
  $ProxyClientsInfo["Performance"] = $worker[3];
  

  foreach ($Minerinfo as $miner) {
    if ($miner["MinerId"] == $ProxyClientsInfo["WorkerName"]) {
      $ProxyClientsInfo["InternalIp"] = $miner["IpAdress"];
      $ProxyClientsInfo["TimeWithoutPost"] = $miner["Timestamp"];
      $ProxyClientsInfo["NumOfGpu"] = $miner["NumOfGpu"];

      break;
    }else{
      $ProxyClientsInfo["InternalIp"] = "No Info";
      $ProxyClientsInfo["TimeWithoutPost"] = "No Info";
      $ProxyClientsInfo["NumOfGpu"] = "No Info";
    }

  }
  unset($worker);

    
  array_push($ProxyClients, $ProxyClientsInfo);

}

//--------------------------
//Remove Duplicates
//--------------------------
$userdupe=array();

foreach ($ProxyClients as $index=>$t) {
    if (isset($userdupe[$t["WorkerName"]])) {
        unset($ProxyClients[$index]);
        continue;
    }
    $userdupe[$t["WorkerName"]]=true;
}
//--------------------------
//Remove Duplicates end
//--------------------------


function orderBy($data, $field)
  {
    $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
    usort($data, create_function('$a,$b', $code));
    return $data;
  }
$ProxyClients = orderBy($ProxyClients, 'WorkerName');




$totalshares = array_sum(array_column($ProxyClients, 'Performance'));
$totalGpus = array_sum(array_column($ProxyClients, 'NumOfGpu'));

foreach ($ProxyClients as &$ProxyInfo) {
    $done = $ProxyInfo["Performance"]/$totalshares*100;
    $target = $ProxyInfo["NumOfGpu"]/$totalGpus*100;
    $ProxyInfo["Performance"] = round($done/$target*100) . "%";
}



$totalactiveGpus = 0;
foreach ($ProxyClients as $ProxyGpu) {
  if ($ProxyGpu["Connected"] == 1) {
    $totalactiveGpus += (int)$ProxyGpu["NumOfGpu"];
    $totalHashrate +=(float)$ProxyGpu["HashRate24h"];
  }
}
unset($ProxyGpu);
 
//echo '<pre>'; echo "Number of Gpus Mining:" . $totalactiveGpus . "  Hashrate: " . $totalHashrate . "total shares: ". $totalshares; echo '</pre>';

?>
