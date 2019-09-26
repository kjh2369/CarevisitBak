<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	
	$code   = $_GET['code'];
	$injungNo  = $_GET['injungNo'];
	
	//기관이용자조회 
	$sql = "select m03_name
			  from m03sugupja
			 inner join client_his_lvl
			    on org_no = m03_ccode
			   and svc_cd = m03_mkind
			   and jumin  = m03_jumin
			   and app_no = '".$injungNo."'
			   and app_no != ''
			   and DATE_FORMAT(from_dt,'%Y%m') <= DATE_FORMAT(now(),'%Y%m')
			   and DATE_FORMAT(to_dt,'%Y%m') >= DATE_FORMAT(now(),'%Y%m')
			 where m03_ccode = '".$code."'
			   and m03_del_yn = 'N'
			   and m03_mkind = '0'";
	$name = $conn->get_data($sql);
	
	echo $name;

	include_once("../inc/_db_close.php");
?>
