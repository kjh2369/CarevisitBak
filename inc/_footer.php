<div id="quickMenu" style="position:absolute;z-index:2;top:0;left:0;width:100px; border:2px solid #0e69b0; background-color:#ffffff; display:none;"></div>
</body>
</html><?
	if ($debug){?>
		<iframe src="../refresh.html" width="100%" height="0" frameborder="0"></iframe><?
	}else{?>
		<iframe src="../refresh.html" width="0" height="0" frameborder="0"></iframe><?
	}
	if ($_SESSION['userLevel'] == 'C'){?>
		<!--iframe src="../ltc.html" width="0" height="0" frameborder="0"></iframe--><?
	}

	if ($debug){?>
		<!--iframe id="ID_WEB_PRT" name="ID_WEB_PRT" src="../showWeb/" width="0" height="0" frameborder="0"></iframe--><?
	}

	//if (__BODY__ == 1) include_once('../work/result_confirm_reserve.php');

	//요구문서 발생시 팝업실행
	//include_once('../inc/set_doc.php');

	//include_once('../inc/_loop.php');
	include_once('../inc/_db_close.php');
	//include_once('../inc/_carlendarLayer.php');
?>