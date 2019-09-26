
<?
	define(__SESSION_NOT__,'Y');
	
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	
	/*************************************************

	회원정보 등록

	*************************************************/
	
	$code   = $_GET['code'];
	$id  = $_GET['id'];
	
	$sql = 'select jumin
			  from member
			 where org_no = \''.$code.'\'
			   and code   = \''.$id.'\'';
	$jumin = $conn -> get_data($sql);
	
	$sql = 'select m02_jikwon_gbn
			  from m02yoyangsa
			 where m02_ccode = \''.$code.'\'
			   and m02_yjumin = \''.$jumin.'\'';
	$work_gbn = $conn -> get_data($sql);
	

	echo $work_gbn;
	
	
include_once("../inc/_db_close.php");

?>