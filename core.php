<?php
require_once(dirname(__FILE__).'/settings.php');

function generateRequestPath($id){
	return $GLOBALS['requestsPath'].'/'.$id.$GLOBALS['requestsFileExtension'];
}
function generateReplyPath($id){
	return $GLOBALS['repliesPath'].'/'.$id.$GLOBALS['repliesFileExtension'];
}
function authorize(){
	$passedKey = null;
	if(isset($_REQUEST['sharedkey']))
		$passedKey = get_magic_quotes_gpc() ? stripcslashes($_REQUEST['sharedkey']) : $_REQUEST['sharedkey'];
	if($passedKey != $GLOBALS['sharedKey'])header('HTTP/'.$_SERVER['version'].' 403 Forbidden');
}