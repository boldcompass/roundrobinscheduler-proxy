<?php
set_time_limit(60);
require_once(dirname(__FILE__).'/core.php');
authorize();

$requests = array();
$i=0;
while(true){
	$requestPaths = glob($requestsPath.'/*.json');
	if(isset($requestPaths) && $requestPaths && count($requestPaths)>0){
		foreach($requestPaths as $requestPath){
			
			//Open
			if (!$requestHandle = fopen($requestPath,'r+'))continue;
			
			//Lock
			if (flock($requestHandle,LOCK_SH)===false)continue;
			
			//Calculate size
			$requestSize = filesize($requestPath);
			if($requestSize<1){
				fclose($requestHandle);
				continue;
			}
			
			//Read
			$requestStr = fread($requestHandle,$requestSize);
			if($requestStr===false){
				fclose($requestHandle);
				continue;
			}
			
			//Decode
			$request = json_decode($requestStr);
			if($request==null){
				fclose($requestHandle);
				continue;
			}
			if(isset($request->handletime) && time()-$request->handletime>120){
				fclose($requestHandle);
				try{
					//Delete requests/replies older than 2 minutes
					if(file_exists($requestPath))unlink($requestPath);
					if(isset($request->id)){
						$replyPath = generateReplyPath($request->id);
						if(file_exists($replyPath))unlink($replyPath);
					}
				}
				catch(Exception $e){}
				continue;
			}
			else if(!isset($request->handletime) || time()-$request->handletime>5){
				$requests[] = $request;
				
				if (flock($requestHandle,LOCK_EX)===false){
					fclose($requestHandle);
					continue;
				}
				if (ftruncate($requestHandle, 0)===false){
					fclose($requestHandle);
					continue;
				}
				if (rewind($requestHandle)===false){
					fclose($requestHandle);
					continue;
				}
				$request->handletime = time();
				if (fwrite($requestHandle,json_encode($request))===false){
					fclose($requestHandle);
					continue;
				}
			}
			fclose($requestHandle);
		}
	}
	if(count($requests)>0 || $i>44)break;
	$i++;
	sleep(1);
}
echo json_encode($requests);