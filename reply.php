<?php
set_time_limit(15);
require_once(dirname(__FILE__).'/core.php');
authorize();

$postdata = file_get_contents("php://input");
if(!isset($postdata)) exit ('Invalid replies: ');
$replies = json_decode($postdata);
if(!isset($replies) || count($replies)<=0) exit ('Invalid replies: '.htmlspecialchars($postdata));
$result = array();
foreach($replies as $reply){
	if(!isset($reply->id))continue;
	$id = $reply->id;
	
	$result[$id]=false;
	if(!file_exists(generateRequestPath($id))){
		$result[$id]='Request file does not exist';
		continue;
	}
	
	$replyPath = generateReplyPath($id);
	
	//Open
	if (!$replyHandle = fopen($replyPath,'w')){
		$result[$id]="Cannot open reply file";
		fclose($replyHandle);
		continue;
	}
	
	//Lock
	if (flock($replyHandle,LOCK_EX)===false){
		$result[$id]="Cannot acquire exclusive lock on reply file";
		fclose($replyHandle);
		continue;
	}
	
	//Write
	if (fwrite($replyHandle,json_encode($reply))===false){
		$result[$id]="Cannot write to reply file";
		fclose($replyHandle);
		continue;
	}

	//Close
	fclose($replyHandle);
	$result[$id]=true;
}
echo json_encode($result);