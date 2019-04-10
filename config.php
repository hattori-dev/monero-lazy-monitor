<?php
$strJSON = file_get_contents("config.json");
$configData = json_decode($strJSON, true);

$refresh = $configData["refresh"];
$workers = $configData["workers"];
$newWorkers = array();
$saveFile = false;
$htmlTop  = "<!DOCTYPE html>";
$htmlTop .=	"<html>";
$htmlTop .=	"<head>";
$htmlTop .=	"<title>Monero Lazy Monitor - Config</title>";
$htmlTop .=	"<link rel='stylesheet' href='style.css' />";
$htmlTop .=	"<meta name='viewport' content='width=device-width' />";
$htmlTop .=	"</head>";
$htmlTop .=	"<body>";
$htmlTop .=	"<div class='mainwrapper'>";

$htmlBottom  = "</table>";
$htmlBottom .= "</div>";
$htmlBottom .= "</body>";
$htmlBottom .= "</html>";

if(isset($_GET['action']) && isset($_GET['oldid'])) {
	$id = urldecode($_GET['oldid']);
	foreach($workers as $worker) {
		$newWorker=$worker;
		if($worker["id"]==$id){
			switch($_GET['action']){
				case "NONE": 
					echo $htmlTop;
					echo "<hr><h3 align='center'>-<( $id )>-</h3><hr>";
					echo "<form action=" . $_SERVER['PHP_SELF'] . " method='get'>";
					echo "<input type='hidden' name='oldid' value='" . $worker["id"] . "'>";
					echo "<table>";
					echo "<tr class='rowTitle'><td class='workerID' align='right'>ID:&nbsp;&nbsp;</td><td><input type='text' name='id' value='" . $worker["id"] . "'></td></tr>";
					echo "<tr class='rowData'><td class='workerID' align='right'>IP:&nbsp;&nbsp;</td><td><input type='text' name='ip' value='" . $worker["ip"] . "'></td></tr>";
					echo "<tr class='rowData'><td class='workerID' align='right'>Port:&nbsp;&nbsp;</td><td><input type='text' name='port' value='" . $worker["port"] . "'></td></tr>";
					echo "<tr class='rowData'><td class='workerID' align='right'>Soft:&nbsp;&nbsp;</td><td>";
					echo "<select name='soft'>";
					echo "	<option " . ($worker["soft"]=="xmrig" ? "selected" : "") . " value='xmrig'>xmrig</option>";
					echo "	<option " . ($worker["soft"]=="stak" ? "selected" : "") . " value='stak'>stak</option>";
					echo "</select></td></tr>";
					echo "<tr class='rowData'><td align='right'>Alert:&nbsp;&nbsp;</td><td colspan='2'><input type='text' name='alert' value='" . $worker["alert"] . "'></td></tr>";
					echo "<tr class='rowTitle'><td>&nbsp;</td><td align='left'>";
					echo "<input type='submit' name='action' value='EDIT'>&nbsp;&nbsp;";
					echo "<input type='submit' name='action' value='REMOVE'>";
					echo "</td></tr>";
					echo "<table>";
					echo "</form>";
					echo $htmlBottom;
					break;
				case "EDIT":
					$newWorker = array("id" => $_GET["id"], "ip" => $_GET["ip"], "port" => $_GET["port"], "soft" => $_GET["soft"], "alert" => (int) $_GET["alert"]);
					$saveFile = true;
					array_push($newWorkers, $newWorker);
					break;
				case "REMOVE":
					//echo "REMOVE";
					//$newWorker = array("id" => $_GET["id"], "ip" => $_GET["ip"], "port" => $_GET["port"], "soft" => $_GET["soft"], "alert" => (int) $_GET["alert"]);
					$saveFile = true;
					break;
				default:
					break;
			}
		} else { array_push($newWorkers, $newWorker); }
	}
	
	if($_GET['action']=='ADD') {
		if($id!="")
		{
			$newWorker = array("id" => $id, "ip" => $_GET["ip"], "port" => $_GET["port"], "soft" => $_GET["soft"], "alert" => (int) $_GET["alert"]);
			array_push($newWorkers, $newWorker);
			$saveFile = true;
		} else {header('Location: index.php');}
	}
	if($saveFile)
	{
		$newConfigDataArr = array_merge(array('refresh'=>$refresh),array('workers'=>$newWorkers));
		$newConfigDataJSON = json_encode($newConfigDataArr, JSON_PRETTY_PRINT);
		//echo $newConfigDataJSON;
		file_put_contents("config.json", $newConfigDataJSON);
		header('Location: index.php');
	}
} else { 
	echo $htmlTop;
	echo "<hr><h3 align='center'>-<( ADD NEW )>-</h3><hr>";
	echo "<form action=" . $_SERVER['PHP_SELF'] . " method='get'>";
	//echo "<input type='hidden' name='oldid' value='" . $worker["id"] . "'>";
	echo "<table>";
	echo "<tr class='rowTitle'><td class='workerID' align='right'>ID:&nbsp;&nbsp;</td><td><input type='text' name='oldid' value=''></td></tr>";
	echo "<tr class='rowData'><td class='workerID' align='right'>IP:&nbsp;&nbsp;</td><td><input type='text' name='ip' value=''></td></tr>";
	echo "<tr class='rowData'><td class='workerID' align='right'>Port:&nbsp;&nbsp;</td><td><input type='text' name='port' value=''></td></tr>";
	echo "<tr class='rowData'><td class='workerID' align='right'>Soft:&nbsp;&nbsp;</td><td>";
	echo "<select name='soft'>";
	echo "	<option value='xmrig'>xmrig</option>";
	echo "	<option value='stak'>stak</option>";
	echo "</select></td></tr>";
	echo "<tr class='rowData'><td align='right'>Alert:&nbsp;&nbsp;</td><td colspan='2'><input type='text' name='alert' value=''></td></tr>";
	echo "<tr class='rowTitle'><td>&nbsp;</td><td align='left'>";
	echo "<input type='submit' name='action' value='ADD'>&nbsp;&nbsp;";
	//echo "<input type='submit' name='action' value='REMOVE'>";
	echo "</td></tr>";
	echo "<table>";
	echo "</form>";
	echo $htmlBottom;
}
?>
