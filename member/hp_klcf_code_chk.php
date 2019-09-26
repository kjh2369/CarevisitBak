<?

include_once("../inc/_db_open.php");
include_once("../inc/_ed.php");

/*********************************************

정보나눔회 회원가입시 기관코드체크

*********************************************/

# 아이디를 소문자로 치환한다.
	$ucode = strtolower($ed->de($_GET['ucode']));
	
	#기관로그인비번조회	
	$sql = 'select m00_store_nm as cname
			  from m00center
			 inner join b02center
			    on b02_center = m00_mcode
			 where m00_mcode = \''.$ucode.'\'
			   and m00_domain != \'dwcare.com\'
			   and m00_del_yn = \'N\'';
	$cname = $conn->get_data($sql);	
	
	echo $cname;

include_once("../inc/_db_close.php");

?>