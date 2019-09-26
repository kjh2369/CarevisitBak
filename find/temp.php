<?
function curl($url){
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$g = curl_exec($ch);
	curl_close($ch);
	return $g;
}

echo curl("http://openapi.epost.go.kr/postal/retrieveNewAdressAreaCdSearchAllService/retrieveNewAdressAreaCdSearchAllService/getNewAddressListAreaCdSearchAll?ServiceKey=BZoHapiuJMXWTy4xVXM7q3x8Z9QiGSLJ6SMGGLzj8mdHQUT7c5KB1EHiiLYbDSwwANMQ5WJqNbeKUVnEYtbFCg%3D%3D&countPerPage=&currentPage=&srchwrd=".urlencode("산성대로"));


exit;

class HTTP{
	var $Socket, $Server, $Port, $Timeout, $HttpVersion = "1.0", $Url, $Length, $ResponseTime, $ErrNum, $ErrMsg;
	var $headers = array();
	var $Response = array();
	var $Err = false;
	var $_chunkedLength =0;

	function HTTP($Server,$Port = 80,$Timeout = 30){
		$this->Server = $Server;
		$this->Port = $Port;
		$this->Timeout = $Timeout;

		$this->Socket = @fsockopen ($this->Server,$this->Port, $errno, $errstr, $this->Timeout);

		if (!$this->Socket){
			$this->Error(0,"Socket Connection Fail");
		}
	}

	function setTimeout($Timeout){
		$this->Timeout = $Timeout;
	}

	function setHttpVersion($HttpVersion){
		$this->HttpVersion = $HttpVersion;
	}

	function Head($Url = "/") {
		$this->Url = $Url;
		$msg = sprintf("HEAD %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);

		return $this->Read();
	}

	function isHead($Url = "/"){
		$this->Url = $Url;
		$msg = sprintf("HEAD %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if ($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);

		return $this->isOK();
	}

	function GetHead($Url = "/") {
		$this->Url = $Url;
		$msg = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);
		$out = $this->ReadHeader();
		return $out;
	}

	function Get($Url = "/",$Cookie="") {
		$this->Url = $Url;
		$msg = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if ($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);
		return $this->Read();
	}

	function isGet($Url = "/",$Cookie=""){
		$this->Url = $Url;
		$msg = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);
		return $this->isOK();
	}

	function isGetAll($Url = "/",$Cookie=""){
		$this->Url = $Url;
		$msg = sprintf("GET %s HTTP/%s\r\n",$this->Url,$this->HttpVersion);
		if ($Cookie != ""){
			$msg .= $this->PutCookie($Cookie);
		}
		$msg .= $this->PutHead();
		$msg .= "\n\n";
		fputs($this->Socket,$msg);

		$data = $this->Read();
		$this->Length = strlen($data);
		return $this->isOK($data);
	}

	function Post($Url ,$Data,$Cookie = ""){
		$this->Url = $Url;
		fputs ($this->Socket,sprintf("POST %s HTTP/%s\r\n",$this->Url,$this->HttpVersion));
		if ($Cookie != ""){
			$this->PutCookie($Cookie);
		}
		$this->PutHead();
		fputs ($this->Socket, "Content-type: application/x-www-form-urlencoded\r\n");
		$out = "";
		while (list ($k, $v) = each ($Data)) {
		if (strlen($out) != 0) $out .= "&";
			$out .= rawurlencode($k). "=" .rawurlencode($v);
		}
		fputs ($this->Socket, "Content-length: ".strlen($out)."\n\n");
		fputs ($this->Socket, "$out");
		fputs ($this->Socket, "\n");
		return $this->Read();
	}

	function IsPost($Url,$Data,$Cookie = ""){
		$this->Url = $Url;
		fputs ($this->Socket,sprintf("POST %s HTTP/%s\r\n",$this->Url,$this->HttpVersion));
		if ($Cookie != ""){
			$this->PutCookie($Cookie);
		}
		$this->PutHead();
		fputs ($this->Socket, "Content-type: application/x-www-form-urlencoded\r\n");
		$out = "";
		while (list ($k, $v) = each ($Data))  {
		if (strlen($out) != 0) $out .= "&";
			$out .= rawurlencode($k). "=" .rawurlencode($v);
		}
		fputs ($this->Socket, "Content-length: ".strlen($out)."\n\n");
		fputs ($this->Socket, "$out");
		fputs ($this->Socket, "\n");
		return $this->isOk();
	}

	function PutHead(){
		$msg = "";
		$msg .= "Accept: */*\r\n";
		$msg .= "Accept-Language: ko\r\n";
		$msg .= "Accept-Encoding: gzip, deflate\r\n";
		$msg .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)\r\n";
		while (list($name,$value) = each ($this->headers)) {
			$msg .= "$name: $value\r\n";
		}
		$msg .= "Host: ".$this->Server.":".$this->Port."\r\n";
		$msg .= "Connection: close\r\n";
		return $msg;
	}

	function AddHeader($name,$value){
		$this->headers[$name] = $value;
	}

	function PutCookie($cookie){
		$msg = "";
		if (is_array($cookie)){
			$out = "";
			while (list ($k, $v) = each ($cookie)) {
				if(strlen($out) != 0) $out .= ";";
				$out .= rawurlencode($k). "=" .rawurlencode($v);
			}
			$msg = "Cookie: $out\n";
		}else{
			$msg = "Cookie: $cookie\n";
		}
		return $msg;
	}

	function Read(){
		$out = $this->ReadHeader();
		$chunked = isset($this->Response['transfer-encoding']) && ('chunked' == $this->Response['transfer-encoding']);
		$gzipped = isset($this->Response['content-encoding']) && ('gzip' == $this->Response['content-encoding']);
		$body = '';
		while(!feof($this->Socket)){
			if ($chunked) {
				$buf = $this->_readChunked();
			} else {
				$buf = fread($this->Socket,4096);
			}
			$body .= $buf;
		}
		if ($gzipped){
			$body = gzinflate(substr($body, 10));
		}
		$this->Response['body'] = $body;
		$out .= $body;
		return $out;
	}

	function ReadHeader(){
		$out = '';
		$buf = $this->_readLine();
		if (sscanf($buf, 'HTTP/%s %s', $http_version, $returncode) != 2) {
			$this->Error(0,"Malformed response");
			return false;
		} else {
			$this->Response["protocol"] = 'HTTP/' . $http_version;
			$this->Response["code"] = intval($returncode);
		}
		$out .= $buf;
		while(!feof($this->Socket)){
			$buf = $this->_readLine();
			$out .= $buf;

			if($buf == "\n" || $buf == "\r\n"){ break; }

			list($name,$value) = split(":",rtrim($buf,"\r\n"),2);
			$this->Response[strtolower($name)] = trim($value);
		}
		$this->Response["header"] = $out;
		return $out;
	}

	function isOk($buffer = ""){
		if($buffer == "") $buffer .= fgets($this->Socket,128);
		if(preg_match('/^HTTP\/.* (2\d{2}|3\d{2}).*/',$buffer)){
			return true;
		}
		return false;
	}

	function _readLine(){
		$line = '';
		while(!feof($this->Socket)){
			$line .= fgets($this->Socket,4096);
			if (substr($line, -2) == "\r\n" || substr($line, -1) == "\n") {
				return $line;
			}
		}
		return $line;
	}

	function _readAll(){
		$data = '';
		while(!feof($this->Socket)){
			$data .= fread($this->Socket,4096);
		}
		return $data;
	}

	function _readChunked(){
		// at start of the next chunk?
		if (0 == $this->_chunkLength) {
			$line = $this->_readLine();
			if (preg_match('/^([0-9a-f]+)/i', $line, $matches)) {
				$this->_chunkLength = hexdec($matches[1]);
				// Chunk with zero length indicates the end
				if (0 == $this->_chunkLength) {
					$this->_readAll(); // make this an eof()
					return '';
				}
			}
		}
		$data = fread($this->Socket,$this->_chunkLength);
		$this->_chunkLength -= strlen($data);
		if (0 == $this->_chunkLength) {
			$this->_readLine(); // Trailing CRLF
		}
		return $data;
	}

	function Close(){
		fclose($this->Socket);
	}

	function Error($errnum,$errmsg){
		$this->Err = true;
		$this->ErrNum = $errnum;
		$this->ErrMsg = $errmsg;
	}
}

/* 소켓 통신을 통하여 필요한 html정보를 가져옴 Start */
$server = "www.naver.com";
$http = new HTTP($server, 80, 30);
$http->setHttpVersion("1.1");
$url = "/index.html";
$cookie = "";
$http->Get($url, $cookie);
$html_body = $http->Response["body"];

/* 소켓 통신을 통하여 필요한 html정보를 가져옴 End */
/*가져온 html정보를 이용하여 필요한 일을 처리함 Start */
//print_r($http);

echo $html_body;

/*가져온 html정보를 이용하여 필요한 일을 처리함 End */


/*
$server = "openapi.epost.go.kr";
$http = new HTTP($server, 80, 30);
$http->setHttpVersion("1.1");
$url = "/postal/retrieveNewAdressAreaCdSearchAllService/retrieveNewAdressAreaCdSearchAllService/getNewAddressListAreaCdSearchAll?ServiceKey=BZoHapiuJMXWTy4xVXM7q3x8Z9QiGSLJ6SMGGLzj8mdHQUT7c5KB1EHiiLYbDSwwANMQ5WJqNbeKUVnEYtbFCg%3D%3D&countPerPage=&currentPage=&srchwrd=산성대로";
$cookie = "";
$http->Get($url, $cookie);
//$html_body = $http->Response["body"];

print_r($http);
*/


exit;






	#include_once('../inc/_db_open.php');
	#include_once('../inc/_login.php');
	#include_once('../inc/_http_uri.php');
	#include_once('../inc/_myFun.php');

	#$rstFun = $_POST['rstFun'];

	$host = 'http://openapi.epost.go.kr';
	$url = '/postal/retrieveNewAdressAreaCdSearchAllService/retrieveNewAdressAreaCdSearchAllService/getNewAddressListAreaCdSearchAll?ServiceKey=BZoHapiuJMXWTy4xVXM7q3x8Z9QiGSLJ6SMGGLzj8mdHQUT7c5KB1EHiiLYbDSwwANMQ5WJqNbeKUVnEYtbFCg%3D%3D&countPerPage=&currentPage=&srchwrd=산성대로';

	$ip = gethostbyname($host);
	$port = 80;

	$sock = socket_create(AF_INET,  SOCK_STREAM, SOL_TCP);
	if (!$sock) exit;

	@$ret = socket_connect($sock, $ip, $port);

	$header = "GET ".$url." HTTP/1.1\r\n";
	$header .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
	$header .= "User-Agent: Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.24\r\n";
	$header .= "Host: ".$host."\r\n";
	$header .= "\r\n";

	socket_send($sock, $header, strlen($header), 0);

	socket_recv($sock, $responses, 10000, 0);

	socket_close($sock);

	echo nl2br($responses);

	#include_once('../inc/_db_close.php');
?>