<?php
$workers = array();
$file = fopen("workers.txt","r");
while ($tempStr=fgets($file)){
	$tempStr=rtrim($tempStr);
	$tempW = explode(":", $tempStr);
	array_push($workers, array( "id" => $tempW[0], "ip" => $tempW[1], "port" => $tempW[2], "soft" => $tempW[3], "alert" => 0));
};
fclose($file);

$newConfigDataArr = array_merge(array('refresh'=>90),array('workers'=>$workers));

$newConfigDataJSON = json_encode($newConfigDataArr, JSON_PRETTY_PRINT);
echo "<hr><pre>";
echo $newConfigDataJSON;
echo "</pre><hr>";
file_put_contents("config.json", $newConfigDataJSON);
echo "config.json created";
?>
