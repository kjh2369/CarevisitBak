<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo $myF->header_script();

	$debug_mdoe = 1;
	$conn->mode = $debug_mdoe;

	$code	= $_POST['code'];
	$svc_cd	= $_GET['svc_cd'];
	$svc_id	= $_GET['svc_id'];
	$jumin	= $ed->de($_POST['jumin']);
	$page	= $_POST['page'];

	// 복원할 데이타 조회
	$sql = "select m31_ccode
			,      m31_mkind
			,      m31_jumin
			,      m31_sdate
			,      m31_edate
			,      m31_level
			,      m31_kind
			,      m31_bonin_yul
			,      m31_kupyeo_max
			,      m31_kupyeo_1
			,      m31_kupyeo_2
			,      m31_status
			,      m31_gaeyak_fm
			,      m31_gaeyak_to
			,      m31_vlvl
			,      m31_sgbn
			,      m31_stop_reason
			,      m31_overtime
			,      m31_add_time1
			,      m31_add_time2
			,	   m31_add_pay_gbn
			  from m31sugupja
			 where m31_ccode = '$code'
			   and m31_mkind = '$svc_cd'
			   and m31_jumin = '$jumin'
			 order by m31_sdate desc
			 limit 1";

	$client = $conn->get_array($sql);

	$conn->begin();

	// 데이타 복원
	$sql = "update m03sugupja
			   set m03_sdate		= '".$client['m31_sdate']."'
			,      m03_edate		= '99991231'
			,      m03_ylvl			= '".$client['m31_level']."'
			,      m03_skind		= '".$client['m31_kind']."'
			,      m03_bonin_yul	= '".$client['m31_bonin_yul']."'
			,      m03_kupyeo_max	= '".$client['m31_kupyeo_max']."'
			,      m03_kupyeo_1		= '".$client['m31_kupyeo_1']."'
			,      m03_kupyeo_2		= '".$client['m31_kupyeo_2']."'
			,      m03_sugup_status	= '".$client['m31_status']."'
			,      m03_gaeyak_fm	= '".$client['m31_gaeyak_fm']."'
			,      m03_gaeyak_to	= '".$client['m31_gaeyak_to']."'
			,      m03_vlvl         = '".$client['m31_vlvl']."'
			,      m03_sgbn         = '".$client['m31_sgbn']."'
			,      m03_stop_reason  = '".$client['m31_stop_reason']."'
			,      m03_overtime     = '".$client['m31_overtime']."'
			,      m03_add_time1    = '".$client['m31_add_time1']."'
			,      m03_add_time2    = '".$client['m31_add_time2']."'
			,      m03_add_pay_gbn  = '".$client['m31_add_pay_gbn']."'
			 where m03_ccode		= '".$code."'
			   and m03_mkind		= '".$svc_cd."'
			   and m03_jumin		= '".$jumin."'";

	//echo $sql.'<br><br>';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	// 히스토리 삭제
	$sql = "delete
			  from m31sugupja
			 where m31_ccode = '".$client['m31_ccode']."'
			   and m31_mkind = '".$client['m31_mkind']."'
			   and m31_jumin = '".$client['m31_jumin']."'
			   and m31_sdate = '".$client['m31_sdate']."'";

	//echo $sql.'<br><br>';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('client_reg.php?code=<?=$code;?>&kind=<?=$kind;?>&jumin=<?=$ed->en($jumin);?>&page=<?=$page;?>');
</script>