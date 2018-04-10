
<html>
<meta http-equiv="refresh" content="5; url=index.php" />
<head>
<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    background: xF00;
}
</style>
</head>

<body>



<?php
 
//--------------------------
//SQL-Database
//--------------------------

$mysqli = new mysqli("127.0.0.1", "root", "", "mineralive");
$sql ="";
 
 
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$sql .= "SELECT * FROM `miner` WHERE 1;\n";


//var_dump ($sql);

if (!$mysqli->multi_query($sql)) {
    echo "Multi query failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

do {
    if ($res = $mysqli->store_result())
    {
  		$Minerinfo = $res->fetch_all(MYSQLI_ASSOC);	
	   	$MinerKeys = array_keys($Minerinfo[0]);
    }
    $res->free();

} while ($mysqli->more_results() && $mysqli->next_result());

//--------------------------
//SQL-Database
//--------------------------

$mysqliGpu = new mysqli("127.0.0.1", "root", "", "mineralive");
$sql ="";
 
 
if ($mysqliGpu->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqliGpu->connect_errno . ") " . $mysqliGpu->connect_error;
}

$sql .= "SELECT * FROM `gpus` WHERE 1;\n";


//var_dump ($sql);

if (!$mysqliGpu->multi_query($sql)) {
    echo "Multi query failed: (" . $mysqliGpu->errno . ") " . $mysqliGpu->error;
}

do {
    if ($res = $mysqliGpu->store_result())
    {
      $sqlGpu = $res->fetch_all(MYSQLI_ASSOC); 
      $sqlGpuKeys = array_keys($sqlGpu[0]);
    }
    $res->free();

} while ($mysqliGpu->more_results() && $mysqliGpu->next_result());





//--------------------------
//Get proxy Api-Database
//--------------------------

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

  $worker[3] = "".$worker[3]."/".$worker[4]."/".$worker[5];
  $worker[7] = date("H:i:s",intval($timediff) );

  unset($worker[4]);
  unset($worker[5]);
}
unset($worker);

//echo '<pre>'; var_dump($Workers["workers"]); echo '</pre>';


  $ProxyClientsInfo = array(
  'WorkerName' => 'Default',
  'Connected' => 'Default',
  'InternalIp' => 'Default',
  'ExternalIp' => 'Default',
  'TimeWithoutShare' => 'Default',
  'TimeWithoutPost' => 'Default',
  'NumOfGpu' => 'Default',
   );

$ProxyClients = array();

//echo '<pre>'; var_dump($ProxyClients); echo '</pre>';
//echo '<pre>'; var_dump($Minerinfo); echo '</pre>';

$totalGpus = 0;
//echo '<pre>'; var_dump($Workers); echo '</pre>';
foreach($Workers["workers"] as $worker)
{
  //echo '<pre>'; var_dump($worker); echo '</pre>';


  $ProxyClientsInfo["WorkerName"] = $worker[0];
  $ProxyClientsInfo["ExternalIp"] = $worker[1];
  $ProxyClientsInfo["Connected"] = $worker[2];
  $ProxyClientsInfo["TimeWithoutShare"] = $worker[7];
  

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



foreach ($ProxyClients as $ProxyGpu) {
  if ($ProxyGpu["Connected"] == 1) {
    $totalGpus += (int)$ProxyGpu["NumOfGpu"];
  }
}
 
echo '<pre>'; echo "Number of Gpus Mining:" . $totalGpus ; echo '</pre>';
?>



<table style="width:100%">
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', array_keys($ProxyClients[0])); ?></th>
    </tr>
  </thead>
  <tbody>
<?php 
foreach ($ProxyClients as $row){
  array_map('htmlentities', $row);
  if ($row["Connected"] == 1) {
    echo '<tr>';
  }else{
    echo '<tr style="background-color:red;">';
  }
    echo "<td>";
    echo implode('</td><td>', $row);
    echo "</td>";
      echo "</tr>";
}
?>
  </tbody>
</table>

    

<pre> "Var_Dump" von Proxy und DB <pre>


<table>
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', $MinerKeys); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($Minerinfo as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

<?php
//echo '<pre>'; var_dump($Workers["workers"]); echo '</pre>';

?>

<table>
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', $workerinfo); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($Workers["workers"] as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

</body>
</html>






<table>
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', $sqlGpuKeys); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($sqlGpu as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>