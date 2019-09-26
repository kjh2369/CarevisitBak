<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');

	$code = Explode(chr(1),$_GET['code']);
	$deccode = '';

	foreach($code as $unicode){
		if ($unicode != ''){
			if (SubStr($unicode,0,1) == 'u'){
				$unicode = SubStr($unicode,1);
			}

			$deccode .= '&#'.hexdec($unicode).';';
		}
	}

	echo $deccode;

	include_once('../inc/_db_close.php');
?>