<?php
$result="";
/*
if(isset($_POST["ip"]))
{
	if(($_POST["id"]=="") || ($_POST["ip"]=="") || ($_POST["port"]=="") || ($_POST["port"]=="")) { $result = "Erro há campos em branco\n"; }
	else{
		$file = fopen("workers.txt","a");
		$linha = $_POST["id"] . ":" . $_POST["ip"] . ":" . $_POST["port"] . ":" . $_POST["soft"] . PHP_EOL;
		fputs($file, $linha) ;
		$result = "adicionado";
	}
	//die(print_r($_POST));
}
*/
$workers = array();
$jsVar=array();
$file = fopen("workers.txt","r");
while ($tempStr=fgets($file)){
	$tempStr=rtrim($tempStr);
	$tempW = explode(":", $tempStr);
	array_push($workers, array( "ID" => $tempW[0], "IP" => $tempW[1], "PORT" => $tempW[2], "SOFT" => $tempW[3]));
	array_push($jsVar, "['" .$tempW[0]. "','" .$tempW[1]. "','" .$tempW[2] . "','" . $tempW[3]. "']");
	
};

$tempStr=implode(",", $jsVar);
$jsVarWorkers="[" . $tempStr . "]";
fclose($file);

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
	document.getElementById("S" + item[0]).innerHTML = "<span>" +item[0] + "&nbsp;(" + item[3] + ")</span><br><span class='highestHashrate'>...loading...</span>"; 
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var bb = this.responseText;
			if((item[3]=="xmrig")||(item[3]=="stak")){
				var myObj = JSON.parse(bb);
				var iHTML = "<td class='workerID' id='S"+ item[0]+"'><span>" +item[0] + "&nbsp;(" + item[3] + ")</span><br>";
				iHTML += "<span class='highestHashrate'>" + myObj.hashrate["highest"] + "</span></td>";
				iHTML += "<td class='totalHashrate'>" + myObj.hashrate["total"][2] + "</td>";
				iHTML += "<div>";
				var threads = myObj.hashrate["threads"];
				if(threads)	threads.forEach(function(item) {
						iHTML += "<span class='thread'>" + item[2] + "</span>";
					});
				iHTML += "</div>";
			}else{
				iHTML = bb;
			}
			document.getElementById("D" + item[0]).innerHTML = iHTML; 
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
	setTimeout(refresh, 90000);
}
setTimeout(refresh, 90000);

</script>
</head>
<body onload="NINJA()">
<?php /*
<h3>Monitor para pregui&ccedil;osos</h3>
<?php
if($result==""){
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	ID: <input type="text" name="id">&nbsp;&nbsp;&nbsp;
	IP: <input type="text" name="ip">&nbsp;&nbsp;&nbsp;
	Port: <input type="text" name="port">&nbsp;&nbsp;&nbsp;
	<select name="soft">
		<option value="xmrig">xmrig</option>
		<option value="stak">stak</option>
	</select>
	<input type="submit" name="submit" value="Adicionar">
	</form>
<?php
} else { echo $result; }
?>
<hr>
*/
?>
<div class='mainwrapper'>
<table>
	<tr class="rowTitle">
		<th><span>ID (soft)</span><br><span>Highest</span></th>
		<th>Total<br>Hashrate</th>
		<th>Threads</th>
	</tr>
<?php 
foreach($workers as $worker) {
	echo "<tr class='rowTitle' id='D" . $worker["ID"] . "'>";
	echo "<td class='workerID' id='S" . $worker["ID"] . "'><span>" . $worker["ID"] . "&nbsp;(". $worker["SOFT"]. ")</span><br><span class='highestHashrate'>&nbsp0&nbsp</span></td>";
	echo "<td class='totalHashrate'>&nbsp;0&nbsp;</td>";
	echo "<td class='threadData'><div>&nbsp;</div></td>";
}
?>
</table>
</div>
</body>
</html>
