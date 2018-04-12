<?php

gc_enable();

 $descriptorspec = array(
	   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
	   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
	   2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
	);
	
$env = array('some_option' => 'aeiou');
	


	
$minerAliveProcess = NULL;
$xmrStakProcess = NULL;


$xmrStakPid = pcntl_fork();

		
if ($xmrStakPid == -1) {
     die('xmrStakPid Konnte nicht verzweigen');
} else if ($xmrStakPid) {
     // Wir sind der Elternprozess
     pcntl_wait($status); //Schützt uns vor Zombie Kindern
} else {	
    echo "xmr-stak Fork start $xmrStakPid \n";
	$xmrStakProcess = array(
		"process" => "php ./startXmrStak.php",
		"directory" => "/xmr-stak",
		"descriptorspec"  => $descriptorspec,
		"pipes" => NULL,
		"resource" => NULL,
		);
	
	$xmrStakProcess["resource"] = proc_open($xmrStakProcess["process"], $xmrStakProcess["descriptorspec"], $xmrStakProcess["pipes"], $xmrStakProcess["directory"], $env );	
	var_dump($xmrStakProcess);	
	echo "xmr-stak Fork Succes\n";
}


$minerAlivePid = pcntl_fork();

	

if ($minerAlivePid == -1) {
     die('minerAlivePid Konnte nicht verzweigen');
} else if ($minerAlivePid) {
     // Wir sind der Elternprozess
     pcntl_wait($status); //Schützt uns vor Zombie Kindern
} else {
    echo "MinerAlive Fork start $minerAlivePid\n";
	$minerAliveProcess = array(
		"process" => "php ./MinerAlive.php",
		"directory" => "/MinerAlive",
		"descriptorspec"  => $descriptorspec,
		"pipes" => NULL,
		"resource" => NULL,
		);
	$minerAliveProcess["resource"] = proc_open($minerAliveProcess["process"], $minerAliveProcess["descriptorspec"], $minerAliveProcess["pipes"], $minerAliveProcess["directory"], $env );	
	var_dump($minerAliveProcess);	
	echo "Miner Alive Fork Succes\n";
}



while (1) {
   
   //$stdout = fread($pipes[1], 1024);
  echo "running \n";
  if (is_resource($xmrStakProcess["resource"])) {
    // $pipes now looks like this:
    // 0 => writeable handle connected to child stdin
    // 1 => readable handle connected to child stdout
    // Any error output will be appended to /tmp/error-output.txt
	
    $stdout = fread($xmrStakProcess["pipes"][1], 40);
}else
 {
 echo "No Data\n";
 }
 
   if (is_resource($minerAliveProcess["resource"])) {
    // $pipes now looks like this:
    // 0 => writeable handle connected to child stdin
    // 1 => readable handle connected to child stdout
    // Any error output will be appended to /tmp/error-output.txt
	$pipes = $minerAliveProcess["pipes"];
	var_dump($pipes);
	echo stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    
}else
 {
 echo "No Data\n";
 }
  
  sleep(5);
}

?>
