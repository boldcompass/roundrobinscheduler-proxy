<?php
require_once(dirname(__FILE__).'/core.php');
authorize();

if(!isset($_REQUEST['uploadpath']))exit('Invalid upload path.');

$file = reset($_FILES);
if(!isset($_REQUEST['uploadpath']))exit('Invalid file.');

$path = $_REQUEST['uploadpath'];
if(!isAllowedExtension($path,$staticFilesAcceptedExtensions))exit('Not an allowed file type.');

$uploadfile = $staticFilesUploadDir.'/'.$path;
if(!is_dir(dirname($uploadfile)))mkdir(dirname($uploadfile));
if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
	echo 'true';
} else {
	echo 'Move file failed.';
}

function isAllowedExtension($fileName, $extensions)
{
	  return in_array(strtolower(end(explode('.', $fileName))), $extensions, true);
}