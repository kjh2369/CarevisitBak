<?
	include('../inc/_db_open.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	$_PARAM = $_REQUEST;
	$listCount = sizeOf($_PARAM['changeFlag']);

	if ($_PARAM['method'] == 'get'){
		$errorString = 'alert("데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.");';
	}else{
		$errorString = 'alert("데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오."); history.back();';
	}

	$sql = "select m03_key"
		 . "  from m03sugupja"
		 . " where m03_ccode = '".$_PARAM['mCode']
		 . "'  and m03_mkind = '".$_PARAM['mKind']
		 . "'  and m03_jumin = '".$_PARAM['mSugup']
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();
	$mKey = $row[0];
	$conn->row_free();

	$conn->begin();

	for($i=0; $i<$listCount; $i++){
		if ($_PARAM['changeFlag'][$i] == 'Y'){
			$sql = "update t01iljung"
				 . "   set t01_conf_date       = '".subStr($_PARAM['mDate'][$i], 0, 4).str_replace('/', '', $_PARAM['workDate'][$i])
				 . "',     t01_conf_fmtime     = '".str_replace(':', '', $_PARAM['workFmTime'][$i])
				 . "',     t01_conf_totime     = '".str_replace(':', '', $_PARAM['workToTime'][$i])
				 . "',     t01_conf_soyotime   = '".$_PARAM['workProcTime'][$i]
				 . "',     t01_conf_suga_code  = '".$_PARAM['sugaCode'][$i]
				 . "',     t01_conf_suga_value = '".$_PARAM['sugaPrice'][$i]
				 . "'"
				 . " where t01_ccode = '".$_PARAM['mCode']
				 . "'  and t01_mkind = '".$_PARAM['mKind']
				 . "'  and t01_jumin = '".$_PARAM['mSugup']
				 . "'  and t01_sugup_date   = '".$_PARAM['mDate'][$i]
				 . "'  and t01_sugup_fmtime = '".$_PARAM['mFmTime'][$i]
				 . "'  and t01_sugup_seq    = '".$_PARAM['mSeq'][$i]
				 . "'";
			if (!$conn->query($sql)){
				echo '<script>'.$errorString.'</script>';
				exit;
			}
		}
	}

	$conn->commit();

	include('../inc/_db_close.php');
	
	if ($_PARAM['method'] == 'get'){
	}else{
		echo "<script>location.replace('suguptime.php?mCode=".$_PARAM["mCode"]."&mKind=".$_PARAM["mKind"]."&mYear=".$_PARAM["mYear"]."&mMonth=".$_PARAM["mMonth"]."&mSugup=&mKey=".$mKey."');</script>";
	}
?>