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
	array_push($jsVar, 	"['" 
		. $tempW['id']. "','" 
		. $tempW['ip']. "','" 
		. $tempW['port'] . "','" 
		. $tempW['soft']. "','" 
		. $tempW['alert'] . "','" 
		. $tempW['xmrigtoken'] . "','" 
		. $tempW['stakuser'] . "','" 
		. $tempW['stakpass'] 
		. "'] ");
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
function showThread(tItem, alertlimit, cssClass)
{
	var color="";
	var value="";
	var result="";
	
	//XMR-STAK returns null and XMRIG returns zero, so I change null to zero 
	if(tItem[2]==null) tItem[2]=0; 
	if(tItem[1]==null) tItem[1]=0; 
	if(tItem[0]==null) tItem[0]=0; 
 
	if (((tItem[2]==0) || (tItem[1]==0) || (tItem[0]==0)) && ((tItem[2]!=0) || (tItem[1]!=0) || (tItem[0]!=0))) {
			color="orange";
			if(tItem[1]!=0) value=tItem[1];
			if(tItem[2]!=0) value=tItem[2];
		    //result = "<span class='thread" + color  + "'>" + tItem[index] + "</span>";
	} else {
		if((tItem[2]<=alertlimit) || (tItem[0]=="ERROR!")) {
			color="red";
			value = tItem[2];
		} else {
		color="";
		value= tItem[2];
		//result = "<span class='" + cssClass + " " + color  + "'>" + value + "</span>";
		}
	}
	result = "<span class='" + cssClass + " " + color  + "'>" + value + "</span>";
	return result;
}

function HATTORI(item)
{
	var xmlhttp = new XMLHttpRequest();
	document.getElementById("rd" + item[0]).innerHTML = "<td class='threadData'><span class='threadData'>...loading...</span></td>"
	//document.getElementById("S" + item[0]).innerHTML = "<span>" +item[0] + "&nbsp;(" + item[3] + ")</span>&nbsp;|&nbsp;<span class='highestHashrate'>...loading...</span>"; 
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var bb = this.responseText;
			//alert(bb);
			if((item[3]=="xmrig")||(item[3]=="stak")){
				var myObj = JSON.parse(bb);
				var rtHTML = " "; //rowTitle
				var rdHTML = " "; //rowData

				rtHTML += "<td class='workerID' id='S"+ item[0]+"'>";
				rtHTML += "<div style='float: left;'>";
				rtHTML += "<span><a href='http://" + item[1] + ":" + item[2] + (item[3]=="stak" ? "/h" : "")+ "'>" + item[0] + "</a>&nbsp;(" + item[3] + ")&nbsp;";
				rtHTML += showThread(myObj.hashrate["total"], -1, "totalHashrate");
				//rtHTML += "<span class='totalHashrate'>" + myObj.hashrate["total"][2] + "</span>";
				rtHTML += "<span class='highestHashrate'>(" + myObj.hashrate["highest"] + ")</span>";
				rtHTML += "</div>";
				rtHTML += "<div style='float: right;'><span><a href='config.php?action=NONE&oldid=" + item[0] + "'>EDIT</a></span></div>";
				rtHTML += "</td>";
				rdHTML += "<td class='threadData'><div>";
				var threads = myObj.hashrate["threads"];
				if(threads)	threads.forEach(function(threadItem) {
						rdHTML += showThread(threadItem, item[4], "thread");
					});
				rdHTML += "</div></td>"; 
			}else{
				iHTML = bb;
			}
			document.getElementById("rt" + item[0]).innerHTML = rtHTML; 
			document.getElementById("rd" + item[0]).innerHTML = rdHTML; 
		}
	};
	urlStr = "semi-proxy.php?id=" + item[0] 
		+ "&ip=" + item[1] 
		+ "&port=" +  item[2] 
		+ "&soft=" + item[3] 
		+ "&xmrigtoken=" + item[5]
		+ "&stakuser=" + item[6]
		+ "&stakpass=" + item[7]
		;
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
	echo "<div style='float: left;'>";
	echo "  <span><a href='http://" . $worker['ip'] . ":" . $worker['port'] . ($worker['soft']=="stak" ? "/h": "") . "'>" . $worker["id"] . "</a>&nbsp;(". $worker["soft"]. ")</span>";
	echo "  <span class='totalHashrate'>&nbsp;0&nbsp;</span>";
	echo "  <span class='highestHashrate'>(&nbsp;0&nbsp;)</span>";
	echo "</div>";
	echo "<div style='float: right;'><span><a href='config.php?action=NONE&oldid=" . $worker["id"] . "'>EDIT</a></span></div>";
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
