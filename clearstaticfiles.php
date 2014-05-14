<?php
require_once(dirname(__FILE__).'/core.php');
authorize();

echo json_encode(clearDir($staticFilesUploadDir));
if(is_file($noProxyFile) && !file_exists($staticFilesUploadDir.'/index.html'))
	copy($noProxyFile,$staticFilesUploadDir.'/index.html');

function clearDir($dir,$delete = false){
	if (!is_dir($dir))return false;
	$success=true;
	$objects = scandir($dir); 
	foreach ($objects as $object) { 
		if ($object == '.' || $object == '..')continue;
		if (filetype($dir.'/'.$object) == 'dir'){
			if(clearDir($dir."/".$object,true)===false)$success=false;
		}
		else{
			if(unlink($dir.'/'.$object)===false)$success=false;
		}
	}
	reset($objects); 
    if($delete){
		if(rmdir($dir)===false)$success=false;
	}
	return $success;
}