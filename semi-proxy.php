<?php 
// ************************************************
// *  FILENAME : semi-proxy.php
// *  (intermediario que vai recolher os dados)
// ************************************************
if($_GET)
{
	switch ($_GET["soft"]) {
		case "xmrig": $url = $_GET["ip"] . ":" . $_GET["port"] . "/"; break;
		case "stak": $url = $_GET["ip"] . ":" . $_GET["port"] . "/h"; break;
	}
	$tempObj->worker_id = $_GET["ip"];
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch); 
	
	if (($output === false) || ($output=="")) {
		//echo  "CURL Error: " . curl_error($ch);
		$tempObj->worker_id = $_GET["id"];
		$tempObj->hashrate["highest"] = "ERRO! " . curl_error($ch);
		$tempObj->hashrate["total"][] = "ERRO!";
		$tempObj->hashrate["total"][] = "ERRO!";
		$tempObj->hashrate["total"][] = "ERRO!";
		$output = json_encode($tempObj);
		die($output);
	}
	curl_close($ch); 
	
	switch ($_GET["soft"]) {
		// running XMRIG *********************************************
		case "xmrig":  
			// que maravilha não é preciso fazer nada !!!!
		break;
		
		// running XMR-STAK ******************************************
		case "stak":  
			if($output)
			{
				//tentar criar um JSON com os mesmos dados do xmrig (worker_id, hashrate[highest e totals[]])
				libxml_use_internal_errors(true);
				$doc = new DOMDocument();
				@$doc->loadHTML($output);
				$table = $doc->getElementsByTagName('table')->item(0);
				//print_r($table);
				if($table==false) {
					$tempObj->worker_id = $_GET["id"];
					$tempObj->hashrate["highest"] = "ERRO! stak?" ;
					$tempObj->hashrate["total"][] = "ERRO!";
					$tempObj->hashrate["total"][] = "ERRO!";
					$tempObj->hashrate["total"][] = "ERRO!";
					$output = json_encode($tempObj);
					die($output);
				}
				$hr = "";
				$contador=0;
				foreach($table->getElementsByTagName('tr') as $tr)
				{
					$th = $tr->getElementsByTagName('th');
					switch ($th->item(0)->nodeValue) {
						case "Thread ID" : // ignore
							break;
						case "Totals:" :
							foreach($tr->getElementsByTagName('td') as $td)	{ $tempObj->hashrate["total"][] = $td->nodeValue; }
							break;
						case "Highest:" :
							$tempObj->hashrate["highest"] = $tr->getElementsByTagName('td')->item(0)->nodeValue;
							break;
						default :
							
							foreach($tr->getElementsByTagName('td') as $td)	{ 
								$tempObj->hashrate["threads"][$contador][] = $td->nodeValue; 
							}
							$contador++;
							break;
					}
				}
			}
			else
			{
				$tempObj->hashrate["highest"] = "ERRO! Sem output!";
				$tempObj->hashrate["total"][] = "ERRO!";
				$tempObj->hashrate["total"][] = "ERRO!";
				$tempObj->hashrate["total"][] = "ERRO!";
				
			}
			$output = json_encode($tempObj);
			break;
	}
	echo $output;
}
?>
