<?
	class ed{
		var $myKey = '케어비지트.넷'; //

		function en($msg){
			return urlencode($this->encode($msg));
		}

		function de($msg){
			return $this->decode(urldecode($msg));
		}

		function en64($msg){
			return base64_encode($this->encode($msg));
		}

		function de64($msg){
			return $this->decode(base64_decode($msg));
		}

		function encode($msg){
			return $this->encrypt_md5($msg, $this->myKey);
		}

		function decode($msg){
			return $this->decrypt_md5($msg, $this->myKey);
		}

		function bytexor($a,$b){
			$c="";
			for($i=0;$i<16;$i++)$c.=$a{$i}^$b{$i};
			return $c;
		}

		function decrypt_md5($msg,$key){
			$string="";
			$buffer="";
			$key2="";

			while($msg){
				$key2=pack("H*",md5($key.$key2.$buffer));
				$buffer=$this->bytexor(substr($msg,0,16),$key2);
				$string.=$buffer;
				$msg=substr($msg,16);
			}
			return($string);
		}

		function encrypt_md5($msg,$key){
			$string="";
			$buffer="";
			$key2="";

			while($msg){
				$key2=pack("H*",md5($key.$key2.$buffer));
				$buffer=substr($msg,0,16);
				$string.=$this->bytexor($buffer,$key2);
				$msg=substr($msg,16);
			}
			return($string);
		}
	}

	$ed = new ed();
?>