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
	#if ($debug) $conn->mode = 2;

	$mode      = $_POST['mode']; //작업구분
	$code      = $_POST['code']; //기관코드
	$kind      = $_POST['kind']; //서비스코드
	$svc_id    = $conn->kind_code($conn->kind_list($code, true), $kind, 'id');
	$jumin     = $ed->de($_POST['jumin']); //고객
	$seq       = $_POST['seq'];
	$key       = $_POST['key'];
	$year      = $_POST['year'];  //년도
	$month     = $_POST['month']; //월
	$month     = (intval($month) < 10 ? '0' : '').intval($month);
	$seq       = $_POST['seq'];
	$gbn       = $_POST['gbn'];
	$gbn2      = $_POST['gbn2'];
	$lvl       = $_POST['lvl'.($kind == '2' ? $gbn : '')];
	$svc_lvl   = $_POST['svc_lvl'];
	$svc_kind  = $_POST['svc_kind'];


	/*********************************************************

		장애인활동지원 구분

	*********************************************************/
	$cltGbn = $gbn;

	if ($kind == '4'){
		if (substr($gbn,0,1) == 'X'){
			$cltGbn = substr($gbn,1,1);
			$gbn    = substr($gbn,0,1);
		}
	}


	if ($kind == '4'){
		$maketime  = intval($_POST['pay_stnd_time']);  //생성시간
		$makepay   = intval(str_replace(',', '', $_POST['pay_stnd_tot'])); //생성금액
		$addtime1  = intval($_POST['pay_sido_time']);  //시도비시간
		$addtime2  = intval($_POST['pay_jach_time']);  //자치비시간
		$totaltime = intval($_POST['pay_total_time']); //총시간
		$totalpay  = intval(str_replace(',', '', $_POST['pay_total_tot']));  //총금액
		$overtime  = floatval($_POST['pay_over_time']);  //이월시간
		$overpay   = intval(str_replace(',', '', $_POST['pay_over_use'])); //이월금액
	}else{
		$maketime  = intval($_POST['makeTime']);  //생성시간
		$makepay   = intval(str_replace(',', '', $_POST['makePay'])); //생성금액
		$addtime1  = intval($_POST['addTime1']);  //시도비시간
		$addtime2  = intval($_POST['addTime2']);  //자치비시간
		$totaltime = intval($_POST['totalTime']); //총시간
		$totalpay  = intval(str_replace(',', '', $_POST['totalPay']));  //총금액
		$overtime  = floatval($_POST['overTime']);  //이월시간
		$overpay   = intval(str_replace(',', '', $_POST['overPay'])); //이월금액
	}

	$addtime   = intval($_POST['pay_add_time']); //추가시간
	$addpay    = intval(str_replace(',', '', $_POST['pay_add_tot'])); //추가금액

	$onload    = $_POST['onload'];

	#######################################################################
	#
	# 수가코드를 찾는다.
		if ($svc_id == 24)
			$suga_cd = $myF->voucher_suga($svc_id, $cltGbn, $svc_lvl, '0');
		else
			$suga_cd = $myF->voucher_suga($svc_id, $cltGbn, $svc_lvl, $gbn2);
	#
	#######################################################################

	#######################################################################
	#
	# 단가를 찾는다.

		$sql = "select service_cost
				  from suga_service
				 where org_no                    = '$code'
				   and service_code              = '$suga_cd'
				   and left(service_from_dt, 7) <= '$year-$month'
				   and left(service_to_dt, 7)   >= '$year-$month'";

		$suga_cost = $conn->get_data($sql);

	#
	#######################################################################



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

	$addPayGbn = '';

	if (is_array($_POST['addPayGbn'])){
		foreach($_POST['addPayGbn'] as $i => $val){
			$addPayGbn .= '/'.$val;
		}
	}

	$sql = "update voucher_make
			   set voucher_gbn        = '$gbn'
			,      voucher_gbn2       = '$gbn2'
			,      voucher_lvl        = '$lvl'
			,      voucher_svc_kind   = '$svc_kind'
			,      voucher_overtime   = '$overtime'
			,      voucher_overpay    = '$overpay'
			,      voucher_addtime1   = '$addtime1'
			,      voucher_addtime2   = '$addtime2'
			,      voucher_maketime   = '$maketime'
			,      voucher_makepay    = '$makepay'
			,      voucher_addtime    = '$addtime'
			,      voucher_addpay     = '$addpay'
			,      voucher_totaltime  = '$totaltime'
			,      voucher_totalpay   = '$totalpay'
			,      voucher_suga_cd    = '$suga_cd'
			,      voucher_suga_cost  = '$suga_cost'
			,      voucher_month_time = case when voucher_kind = '2' or voucher_kind = '4' then voucher_overtime+voucher_maketime else 0 end
			,      voucher_month_pay  = case when voucher_kind = '4' then voucher_overpay+voucher_makepay else 0 end
			,      voucher_add_pay_gbn= '$addPayGbn'";

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

	if ($conn->mode != 1) exit;
?>
<script language='javascript'>
	alert('<?=$myF->message("ok","N");?>');

	if ('<?=$debug;?>' == '1')
		location.replace('iljung_voucher_make.php?code=<?=$code;?>&svc_cd=<?=$kind;?>&ssn=<?=$ed->en($jumin);?>&key=<?=$key;?>&year=<?=$year;?>&month=<?=$month;?>&seq=<?=$seq;?>&onload=<?=$onload;?>');
</script>