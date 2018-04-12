<?php


shell_exec('rm amd.txt');
shell_exec('rm pools.txt');



//
//----Pools---
//


$poolAdress = "pool.supportxmr.com:7777";
$rigId = "Testing";
$Walletadress = "47fWF6DkSumWrMxkpkM1vJ7ZBKrs8SaK7FJUgeVi622y5wedi39TNroQpyCFLyAF59BUGauxFeKXjXMZJiV2dU6iKoPdx2r";

$pooldata = '"pool_list" :
[
  {"pool_address" : "'. $poolAdress . '",
  "wallet_address" : "'. $Walletadress . '",
  "rig_id" : "'. $rigId . '",
  "pool_password" : "'. $rigId . '",
  "use_nicehash" : false,
  "use_tls" : false,
  "tls_fingerprint" : "",
  "pool_weight" : 1 },
],
"currency" : "monero7",';


var_dump($pooldata);

file_put_contents("pools.txt", $pooldata);



















$gpuInfo = shell_exec('clinfo -l');
$gpuInfo = explode("\n",$gpuInfo);


foreach ($gpuInfo as &$value) {
   $value = substr($value,12);
}
array_pop($gpuInfo);
array_shift($gpuInfo);
unset($value);
$numOfGpu = count($gpuInfo);

$amdData = '
"gpu_threads_conf" : [';


var_dump($gpuInfo);

$worksize = 8;
$intensity = $worksize * 50;
$counter = 0;
foreach ($gpuInfo as $value) {
  $amdData .= '
{
	"index" : '. $counter .',
	"intensity" : '.$intensity.',
	"worksize" : '.$worksize.',
	"affine_to_cpu" : false,
	"strided_index" : 1,
	"mem_chunk" : 2,
	"comp_mode" : true
},
{
	"index" : '. $counter .',
	"intensity" : '.$intensity.',
	"worksize" : '.$worksize.',
	"affine_to_cpu" : false,
	"strided_index" : 1,
	"mem_chunk" : 2,
	"comp_mode" : true
},';
  $counter++;
}
unset($counter,$value);

$amdData .= '
],

"platform_index" : 0,';


var_dump($amdData);

file_put_contents("amd.txt", $amdData);


passthru("./xmr-stak");



?>
