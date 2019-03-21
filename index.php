<?php
$result="";
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
<style>
body {font-family: Tahoma, Arial, sans-serif;font-size: 80%;background-color: rgb(240, 240, 240);}
<?php 
//a {color: rgb(44, 55, 66);}a:link {text-decoration: none;}a:visited {color: rgb(44, 55, 66);}a:hover {color: rgb(255, 153, 0);}a:active {color: rgb(204, 122, 0);}
//.all {max-width:600px;margin: auto;}
//.header {background-color: rgb(30, 30, 30);color: white;padding: 10px;font-weight: bold;margin: 0px;margin-bottom: 10px;}
//.version {font-size: 75%;text-align: right;}
//.links {padding: 7px;text-align: center;background-color: rgb(215, 215, 215);box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.2), 0px 1px 1px 0px rgba(0, 0, 0, 0.14), 0px 2px 1px -1px rgba(0, 0, 0, 0.12);}
// .letter {font-weight: bold;} h4 {background-color: rgb(0, 130, 130);color: white;padding: 10px;margin: 10px 0px;}
//.flex-container {display: -webkit-flex;display: flex;}
//.flex-item {width: 33%;margin: 3px;}
//.motd-box {background-color: #ccc;padding: 0px 10px 5px 10px;margin-bottom: 10px;}
//.motd-head {border-bottom: 1px solid #000;margin-bottom: 0.5em;padding: 0.5em 0em;font-weight: bold;}
//.motd-body {overflow: hidden;}
?>
.data th, td {padding: 5px 12px;text-align: right;border-bottom: 1px solid #ccc;}
.data th {text-align: center;}
.data tr:nth-child(even) {background-color: #ddd;}
.data tr:hover {background-color: #aaa;}
.data th {background-color: #ccc;}
.data table {width: 100%;max-width: 600px;}


</style>
<script>
function HATTORI(item)
{
	//alert(item);
	var xmlhttp = new XMLHttpRequest();
	document.getElementById("S" + item[0]).innerHTML = "loading..."; 
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var bb = this.responseText;
			//alert (bb);
			if((item[3]=="xmrig")||(item[3]=="stak")){
				var myObj = JSON.parse(bb);
				var iHTML = "<td id='S"+ item[0]+"'>" + item[3] + "</td>";
				iHTML += "<td><a href='http://" + item[1] + ":" + item[2] + (item[3]=="stak" ? "/h" : "")+ "'>" + item[0] + "</a></td>";
				iHTML += "<td align='right'>" + myObj.hashrate["highest"] + "</td>";
				//iHTML += "<td align='right'>" + myObj.hashrate["total"][0] + "</td>";
				//iHTML += "<td align='right'>" + myObj.hashrate["total"][1] + "</td>";
				iHTML += "<td align='right'>" + myObj.hashrate["total"][2] + "</td>";
				var threads = myObj.hashrate["threads"];
				if(threads)	threads.forEach(function(item) {
						iHTML += "<td align='right'>" + item[2] + "</td>";
					});
			}else{
				iHTML = bb;
			}
			document.getElementById("D" + item[0]).innerHTML = iHTML; 

		}
	};
	urlStr = "semi-proxy.php?id="+ item[0] + "&ip=" + item[1] + "&port=" +  item[2] + "&soft=" + item[3];
	//alert(urlStr);
	xmlhttp.open("GET", urlStr, true);
	xmlhttp.send();
}

function NINJA()
{
	var workers = <?php echo $jsVarWorkers; ?> ;
	//alert(workers);
	workers.forEach(HATTORI);
}

//var time = new Date().getTime();

function refresh() {
	NINJA();
	setTimeout(refresh, 90000);
}
setTimeout(refresh, 90000);

</script>
</head>
<body onload="NINJA()">
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
<hr><div class='data'>
<table border=1>
<tr><th>Software</th><th>WorkerID</th><th>Highest</th><th>Total</th><th colspan=10>Threads</th></tr>
<?php 

foreach($workers as $worker) {
	echo "<tr id='D" . $worker["ID"] . "'><td id='S" . $worker["ID"] . "'>" . $worker["SOFT"] . "</td><td>" . $worker["IP"] . "</td><td></td><td></td><td></td><td></td></tr>";
}
?>
</table>
</div>
</body>
</html>
