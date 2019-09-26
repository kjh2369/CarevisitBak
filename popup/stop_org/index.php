<?
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_db_open.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_myFun.php");

	$orgNo = $_SESSION['userCenterCode'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
	<title>::재가지원서비스::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='imagetoolbar' content='no'>
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="keywords" content="방문서비스 관리 시스템, 케어,돌봄, 재가, 요양보호사, 방문요양, 방문목욕, 방문간호" />
	<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<script language='javascript'>
	function setCookie(name, value, expiredays ){
		var todayDate = new Date();
			todayDate.setDate( todayDate.getDate() + expiredays );

		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
	}
	function end(){
		var f = document.f;

		if (f.check.checked){
			setCookie('STOPORG','DONE',1);
		}

		self.close();
	}
</script>
<body style="margin:0;">
<table style="width:100%;">
	<tr>
		<td>
			<div><img src="./stop_org.jpg"></div>
		</td>
	</tr>
	<tr>
		<td>
			<form name="f" method="post" action="">
			<label><input type="checkbox" name="check" value="checkbox"  style="border:0;" onClick="end();"/>오늘 하루동안 열지않기</label>
			<a href="#" onclick="end();">닫기</a>
			</form>
		</td>
	</tr>
</table>
<?
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_db_close.php");
?>