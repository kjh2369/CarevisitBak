<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo $myF->header_script();

	/*
	 * mode
	 * - 1 : 등록
	 * - 2 : 수정
	 */
	$debug = 1;
	$conn->mode = $debug;

	$mode      = $_POST['mode']; //작업구분
	$code      = $_POST['code']; //기관코드
	$kind      = $_POST['use_kind']; //서비스코드
	$jumin     = $ed->de($_POST['jumin']); //고객
	$seq       = $_POST['seq'];
	$key       = $_POST['key'];
	$year      = $_POST['year'];  //년도
	$month     = $_POST['month']; //월
	$month     = (intval($month) < 10 ? '0' : '').intval($month);
	$overtime  = $_POST['overtime'];  //이월시간

	$writer    = $_SESSION['userCode'];
	$today     = date('Y-m-d', mktime());

	$conn->begin();

	if ($mode == 1){
		$sql = "select ifnull(max(voucher_seq), 0) + 1
				  from voucher_make
				 where org_no        = '$code'
				   and voucher_kind  = '$kind'
				   and voucher_jumin = '$jumin'
				   and voucher_yymm  = '$year$month'";

		$seq = $conn->get_data($sql);

		$sql = "insert into voucher_make (
				 org_no
				,voucher_kind
				,voucher_jumin
				,voucher_yymm
				,voucher_seq
				,insert_id
				,insert_dt) values (
				 '$code'
				,'$kind'
				,'$jumin'
				,'$year$month'
				,'$seq'
				,'$writer'
				,'$today')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}

	$sql = "update voucher_make
			   set voucher_month_time = '$overtime'";

	if ($mode == 2){
		$sql .= ", update_id = '$writer'
		         , update_dt = '$today'";
	}

	$sql .= " where org_no        = '$code'
			    and voucher_kind  = '$kind'
				and voucher_jumin = '$jumin'
				and voucher_yymm  = '$year$month'
				and voucher_seq   = '$seq'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
<script language='javascript'>
	alert('<?=$myF->message("ok","N");?>');

	if ('<?=$debug;?>' == '1')
		location.replace('iljung_voucher_overtime.php?code=<?=$code;?>&svc_cd=<?=$kind;?>&ssn=<?=$ed->en($jumin);?>&key=<?=$key;?>&year=<?=$year;?>&month=<?=$month;?>&seq=<?=$seq;?>&onload=<?=$onload;?>');
</script>