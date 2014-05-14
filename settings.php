<?php
$requestsPath = dirname(__FILE__).'/requests';
$GLOBALS['requestsPath']=$requestsPath;

$requestsFileExtension = '.json';
$GLOBALS['requestsFileExtension']=$requestsFileExtension;

$repliesPath = dirname(__FILE__).'/replies';
$GLOBALS['repliesPath']=$repliesPath;

$repliesFileExtension = '.json';
$GLOBALS['repliesFileExtension']=$repliesFileExtension;

$staticFilesUploadDir = dirname(__FILE__).'/static';
$GLOBALS['staticFilesUploadDir']=$staticFilesUploadDir;

$staticFilesAcceptedExtensions = array('html','htm','css','js','jpg','jpeg','png','gif', 'ico');
$GLOBALS['staticFilesAcceptedExtensions']=$staticFilesAcceptedExtensions;

$noProxyFile = dirname(__FILE__).'/noproxy.html';
$GLOBALS['noProxyFile']=$noProxyFile;

$sharedKey = 'hackme';
$GLOBALS['sharedKey']=$sharedKey;