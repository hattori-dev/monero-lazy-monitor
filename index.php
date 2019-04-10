<?php
$result="";

$workers = array();
$jsVar=array();

$strJSON = file_get_contents("config.json");
$configData = json_decode($strJSON, true);

$workers = $configData["workers"];
$refresh = $configData["refresh"] * 1000;

foreach($workers as $tempW)
{
	array_push($jsVar, "['" .$tempW['id']. "','" .$tempW['ip']. "','" .$tempW['port'] . "','" . $tempW['soft']. "','" . $tempW['alert'] . "']");
}
$tempStr=implode(",", $jsVar);
$jsVarWorkers="[" . $tempStr . "]";

?>
<!DOCTYPE html>
<html>
<head>
<title>Monero Lazy Monitor</title>
<link rel='stylesheet' href='style.css' />
<meta name='viewport' content='width=device-width' />
<script>
function HATTORI(item)
{
	var xmlhttp = new XMLHttpRequest();
	document.getElementById("rd" + item[0]).innerHTML = "<td class='threadData'><span class='threadData'>...loading...</span></td>"
	//document.getElementById("S" + item[0]).innerHTML = "<span>" +item[0] + "&nbsp;(" + item[3] + ")</span>&nbsp;|&nbsp;<span class='highestHashrate'>...loading...</span>"; 
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var bb = this.responseText;
			if((item[3]=="xmrig")||(item[3]=="stak")){
				var myObj = JSON.parse(bb);
				var rtHTML = " "; //rowTitle
				var rdHTML = " "; //roeData

				rtHTML += "<td class='workerID' id='S"+ item[0]+"'>";
				rtHTML += "<span><a href='config.php?action=NONE&oldid=" + item[0] + "'>" + item[0] + "</a>&nbsp;(" + item[3] + ")&nbsp;";
				rtHTML += "<span class='totalHashrate'>" + myObj.hashrate["total"][2] + "</span>";
				rtHTML += "<span class='highestHashrate'>(" + myObj.hashrate["highest"] + ")</span>";
				rtHTML += "</td>";
				rdHTML += "<td class='threadData'><div>";
				var threads = myObj.hashrate["threads"];
				if(threads)	threads.forEach(function(threadItem) {
						var red="";
						if((threadItem[2]<=item[4]) || (myObj.hashrate["highest"]=="ERROR!")) red=" red";
						rdHTML += "<span class='thread" + red  + "'>" + threadItem[2] + "</span>";
					});
				rdHTML += "</div></td>"; 
			}else{
				iHTML = bb;
			}
			document.getElementById("rt" + item[0]).innerHTML = rtHTML; 
			document.getElementById("rd" + item[0]).innerHTML = rdHTML; 
		}
	};
	urlStr = "semi-proxy.php?id="+ item[0] + "&ip=" + item[1] + "&port=" +  item[2] + "&soft=" + item[3];
	xmlhttp.open("GET", urlStr, true);
	xmlhttp.send();
}

function NINJA()
{
	var workers = <?php echo $jsVarWorkers; ?> ;
	workers.forEach(HATTORI);
}

function refresh() {
	NINJA();
	setTimeout(refresh, <?php echo $refresh; ?>);
}
setTimeout(refresh, <?php echo $refresh; ?>);

</script>
</head>
<body onload="NINJA()">
<div class='mainwrapper'>
<table>
	<tr class="rowTitle">
		<th><div style="float: left;">
			<span>WorkerID (soft)</span>
			<span class="totalHashrate">Total</span>
			<span class="highestHashrate">(Highest)</span>
			</div>
			<div style="float: right;"><span><a href="config.php?action=ADD">ADD NEW</a></span></div>
		</th>
	</tr>
<?php
foreach($workers as $worker) {
	echo "<tr class='rowTitle' id='rt" . $worker["id"] . "'>";
	echo "<td class='workerID' id='S" . $worker["id"] . "'>";
	echo "  <span><a href='config.php?action=NONE&oldid=" . $worker["id"] . "'>" . $worker["id"] . "</a>&nbsp;(". $worker["soft"]. ")</span>";
	echo "  <span class='totalHashrate'>&nbsp0&nbsp</span>";
	echo "  <span class='highestHashrate'>(&nbsp0&nbsp)</span>";
	echo "</td>";
	echo "</tr>";
	echo "<tr class='rowData' id='rd" . $worker["id"]. "'>";
	//echo "<td class='totalHashrate'>&nbsp;0&nbsp;</td>";
	echo "<td class='threadData'><div>&nbsp;</div></td>";
	echo "</tr>";
}
?>
</table>
</div>
</body>
</html>
