<?php
set_time_limit(60);
require_once(dirname(__FILE__).'/core.php');

$path = null;
if(isset($_REQUEST['path']))$path=$_REQUEST['path'];
else exit;

$data = null;
if(isset($_REQUEST['d']))$data=(get_magic_quotes_gpc()?stripcslashes($_REQUEST['d']):$_REQUEST['d']);

$id = rand();
$request = array('id'=>$id,'path'=>$path,'cookies'=>$_COOKIE);
if(isset($data))$request['d']=$data;
else if(isset($_POST)){
	if (get_magic_quotes_gpc() === 1) $request['d']=array_map('stripcslashes', $_POST);
	else $request['d']=$_POST;
}
$requestStr = json_encode($request);
$requestPath = generateRequestPath($id);

//Open
if (!$requestHandle = fopen($requestPath,'w')){
	exit ("Cannot open request file");
}

//Lock
if (flock($requestHandle,LOCK_EX)===false){
	exit ("Cannot acquire exclusive lock on request file");
}

//Write
if (fwrite($requestHandle,$requestStr)===false){
	exit ("Cannot write to request file");
}

//Close
fclose($requestHandle);

$replyPath = generateReplyPath($id);
$reply = null;
for($i = 0; $i<90; $i++){
	if(file_exists($replyPath)){
		//Open
		if (!$replyHandle = fopen($replyPath,'r')){
			//echo ("Cannot open reply file");
			fclose($replyHandle);
			break;
		}

		//Lock
		if (flock($replyHandle,LOCK_SH)===false){
			//echo ("Cannot acquire shared lock on reply file");
			fclose($replyHandle);
			break;
		}
		
		//Calculate size
		$replySize = filesize($replyPath);
		if($replySize<1){
			//echo ("Empty reply file");
			fclose($replyHandle);
			continue;
		}
		
		//Read
		$replyStr = fread($replyHandle, $replySize);
		if($replyStr===false){
			//echo ("Cannot read reply file");
			fclose($replyHandle);
			break;
		}
		
		fclose($replyHandle);
		
		//Set Reply
		if($replyStr!=null){
			$reply = $replyStr;
			break;
		}
	}
	time_nanosleep(0, 500000000);
}
if(isset($reply))$reply = json_decode($reply);
if(isset($reply)){
	if(isset($reply->status)){
		header('HTTP/'.$_SERVER['version'].' '.$reply->status);
	}
	if(isset($reply->headers)){
		foreach($reply->headers as $header){
			header($header->Key.': '.$header->Value);
		}
	}
	if(isset($reply->body))echo $reply->body;
}
else{
	header('HTTP/'.$_SERVER['version'].' 504 Gateway Timeout');
}

flush();

//Remove request file
if(file_exists($requestPath))unlink($requestPath);
if(file_exists($replyPath))unlink($replyPath);