<?php 
// ************************************************
// *  FILENAME : semi-proxy.php
// *  (intermediario que vai recolher os dados)
// ************************************************
if($_GET)
{
	$url = $_GET["ip"] . ":" . $_GET["port"] . ($_GET["soft"]=="stak" ? "/api.json" : "/" );

	$tempObj = new \stdClass();
	$tempObj->worker_id = $_GET["ip"];
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch); 
	
	if (($output === false) || ($output=="")) {
		$tempObj->worker_id = $_GET["id"];
		$tempObj->hashrate["highest"] = "ERRO!";
		$tempObj->hashrate["total"][] = "ERRO!";
		$tempObj->hashrate["total"][] = "ERRO!";
		$tempObj->hashrate["total"][] = "ERRO!";
		$tempObj->hashrate["threads"][0][2] = "ERRO! " . curl_error($ch);
		$output = json_encode($tempObj);
		die($output);
	}
	curl_close($ch); 
	echo $output;
}
?>
