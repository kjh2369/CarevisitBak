<?
	include("../inc/_db_open.php");

	//$sql = "select count(*)"
	//	 . "  from m02yoyangsa"
	//	 . " where m02_ccode  = '".$_GET["mCode"]
	//	 . "'  and m02_mkind  = '".$_GET["mKind"]
	//	 . "'  and m02_yjumin = '".$_GET["yJumin"]
	//	 . "'";


	/*
	$sql = "select count(*)"
		 . "  from m02yoyangsa"
		 . " where m02_ccode  = '".$_GET['mCode']."'
		       and m02_yjumin = '".$_GET["yJumin"]
		 . "'";
	*/


	$code  = $_GET['mCode'];
	$jumin = $_GET["yJumin"];

	if (!empty($code)){
		$returnType = 'cnt';
	}else{
		$code  = $_GET['code'];
		$jumin = $_GET['jumin'];

		$returnType = 'str';
	}

	if ($returnType == 'cnt'){
		$sql = 'select count(*)
				  from m02yoyangsa
				 where m02_ccode  = \''.$code.'\'
				   and m02_yjumin = \''.$jumin.'\'';

		$requestString = $conn->get_data($sql);

		if ($requestString == 0){
			$requestString = "N";
		}
	}else{
		$sql = 'select min(m02_mkind) as kind
				,      m02_yname as name
				  from m02yoyangsa
				 where m02_ccode  = \''.$code.'\'
				   and m02_yjumin = \''.$jumin.'\'
				 group by m02_yname';

		$data = $conn->get_array($sql);

		$requestString = $data['name'];
	}

	include("../inc/_db_close.php");

	echo $requestString;
?>