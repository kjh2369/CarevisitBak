<?
	include_once("../../inc/_db_open.php");
	include_once("../../inc/_login.php");
	include_once("../../inc/_myFun.php");

	$orgNo = $_SESSION["userCenterCode"];

	$sql = 'SELECT	cont_com
			,		CASE cont_com WHEN \'1\' THEN \'굿이오스\'
								  WHEN \'2\' THEN \'지케어\'
								  WHEN \'3\' THEN \'케어비지트\' ELSE \'\' END AS com_name
			FROM	cv_reg_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
			AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')';

	$row = $conn->get_array($sql);

	$contCom = $row['cont_com'];

	Unset($row);
?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>법인 변경에 따른 안내문</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='imagetoolbar' content='no'>
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<script type="text/javascript" src="../../js/script.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(window).focus();
	});
</script>
</head>

<body style="margin:0;"><?
if ($contCom == '1' || $contCom == '2'){?>
	<div><img src="./pop_head_1_2.jpg"></div>
	<div style="overflow-x:hidden; overflow-y:scroll; width:600px; height:722px;"><img src="./pop_1_2.jpg" border="0" usemap="#CAREVISIT"></div>
	<map name="CAREVISIT">
		<area shape="rect" coords="127, 432, 215, 458" href="../../_center/carevisit_contract.php?type=re">
		<area shape="rect" coords="222, 432, 401, 458" href="../../_center/download.php?downType=2">
		<area shape="rect" coords="417, 432, 545, 458" href="../../_center/download.php?downType=3">
	</map><?
}else{?>
	<div><img src="./pop_head_3.jpg"></div>
	<div style="overflow-x:hidden; overflow-y:scroll; width:600px; height:722px;"><img src="./pop_3.jpg" border="0" usemap="#CAREVISIT"></div>
	<map name="CAREVISIT">
		<area shape="rect" coords="127, 427, 215, 453" href="../../_center/carevisit_contract.php?type=re">
		<area shape="rect" coords="222, 427, 401, 453" href="../../_center/download.php?downType=2">
		<area shape="rect" coords="417, 427, 545, 453" href="../../_center/download.php?downType=3">
	</map><?
}?>
</body>
</html>
<?
	include_once("../../inc/_db_close.php");
?>