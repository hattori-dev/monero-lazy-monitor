<?php 
// ************************************************
// *  FILENAME : semi-proxy.php
// *  (simple Data fetcher)
// ************************************************
if($_GET)
{
	$ch = curl_init();
	$url = $_GET["ip"] . ":" . $_GET["port"] . ($_GET["soft"]=="stak" ? "/api.json" : "/2/summary" );

	$token = $_GET["xmrigtoken"];
	$stakuser = $_GET["stakuser"];
	$stakpass = $_GET["stakpass"];
	switch($_GET["soft"]){
		case "stak": 
			if($stakuser!="") {
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);

				$content = curl_exec($ch); //just to get digest headers

				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
				curl_setopt($ch, CURLOPT_USERPWD, $stakuser . ":" . $stakpass);

				$output = curl_exec($ch); 
				$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
				//echo $status_code ."<hr>".$output;
				$error = curl_error($ch);
			}
			else
			{
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_FAILONERROR, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch); 
				$error = curl_error($ch);
			}
			break;
		case "xmrig":
			if($token!=""){
				$token = $_GET["xmrigtoken"];
				$headers = [
					"Authorization: Bearer " . $token ,
					"Cache-Control: no-store"
				];
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_FAILONERROR, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$output = curl_exec($ch); 
				$error = curl_error($ch);
			}
			else{
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_FAILONERROR, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch); 
				$error = curl_error($ch);
			}
			break;
	}

	curl_close($ch); 
	
	if (($output === false) || ($output=="") || ($error!="")) {
		$tempObj = new \stdClass();
		$tempObj->worker_id = $_GET["ip"];
		$tempObj->hashrate["highest"] = "ERROR!";
		$tempObj->hashrate["total"][] = "ERROR!";
		$tempObj->hashrate["total"][] = "ERROR!";
		$tempObj->hashrate["total"][] = "ERROR!";
		$tempObj->hashrate["threads"][0][0] = "ERROR!";
		$tempObj->hashrate["threads"][0][1] = "ERROR!";
		$tempObj->hashrate["threads"][0][2] = "ERROR! " . $error;
		$output = json_encode($tempObj, JSON_PRETTY_PRINT);
		//die($output);
	}

	echo $output;
}
?>
