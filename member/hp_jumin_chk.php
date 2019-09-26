<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	
	$code   = $_GET['code'];
	$memGbn = $_GET['memGbn'];
	$jumin  = $ed->de($_GET['jumin']);
	
	if($memGbn == 'Y'){
		//기관직원조회 
		$sql = "select m02_yname
				  from m02yoyangsa
				 where m02_ccode = '".$code."'
				   and m02_yjumin = '".$jumin."'
				   and m02_ygoyong_stat != '9'
				   and m02_del_yn = 'N'";
		$name = $conn->get_data($sql);
		
		//회원정보조회 카운트
		$sql = "select code, pswd, email
				  from member
				 where org_no = '".$code."'
				   and jumin  = '".$jumin."'
				   and del_yn = 'N'";
		$mem = $conn->get_array($sql);
		
	}else {
		
		//기관이용자조회 
		$sql = "select m03_name
				  from m03sugupja
				 where m03_ccode = '".$code."'
				   and m03_jumin = '$jumin'
				   and m03_del_yn = 'N'";
		$name = $conn->get_data($sql);
		
	}
	
	echo ''.'//'.sizeof($name).'//'.sizeof($mem['code']).'//'.$name.'//'.$mem['code'].'//'.$mem['pswd'].'//'.$mem['email'].'//';

	
	include_once("../inc/_db_close.php");

?>
