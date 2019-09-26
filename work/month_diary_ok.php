<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_ed.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->begin();
	
	// 수정된 확정 데이타를 저장한다.
	$listCount = sizeOf($_POST['changeFlag']);

	for($i=0; $i<$listCount; $i++){
		if ($_POST['changeFlag'][$i] == 'Y'){
			$sql = "update t01iljung"
				 . "   set t01_conf_date       = '".$_POST['confYear'].str_replace('/', '', $_POST['workDate'][$i])
				 . "',     t01_conf_fmtime     = '".str_replace(':', '', $_POST['workFmTime'][$i])
				 . "',     t01_conf_totime     = '".str_replace(':', '', $_POST['workToTime'][$i])
				 . "',     t01_conf_soyotime   = '".$_POST['planProcTime'][$i]
				 . "',     t01_conf_suga_code  = '".$_POST['sugaCode'][$i]
				 . "',     t01_conf_suga_value = '".$_POST['sugaPrice'][$i]
				 . "',     t01_status_gbn      = '1'"
				 . " ,     t01_modify_yn       = 'M'"
				 . " where t01_ccode = '".$_POST['mCode']
				 . "'  and t01_mkind = '".$_POST['mKind']
				 . "'  and t01_jumin = '".$ed->de($_POST['mSugupja'])
				 . "'  and t01_sugup_date   = '".$_POST['sugupDate'][$i]
				 . "'  and t01_sugup_fmtime = '".$_POST['sugupFmTime'][$i]
				 . "'  and t01_sugup_seq    = '".$_POST['sugupSeq'][$i]
				 . "'";
			if (!$conn->query($sql)){
				$conn->rollback();
				echo '<script>alert("데이타 저장중 오류가 발생하였습니다."); history.back();</script>';
				exit;
			}
		}
	}
	
	$conn->commit();

	include('../inc/_db_close.php');
?>
<form name="f" method="post">
<input name="curYear" type="hidden" value="">
<input name="curMonth" type="hidden" value="">
<input name="curMcode" type="hidden" value="">
<input name="curMkind" type="hidden" value="">
<input name="curSugupja" type="hidden" value="">
</form>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
	goMonthConfSugupja("<?=$_POST['confYear'];?>", "<?=$_POST['confMonth'];?>", "<?=$_POST['mCode'];?>", "<?=$_POST['mKind'];?>", "<?=$_POST['mSugupja'];?>");
</script>