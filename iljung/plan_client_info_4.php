<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	$sql = 'select min(m03_mkind) as kind
			,      m03_jumin as jumin
			,      m03_name as name
			,      m03_bipay1 as bipay1
			,      m03_bipay2 as bipay2
			,      m03_bipay3 as bipay3
			,      svc_val as val
			,      svc_lvl as lvl
			  from m03sugupja as mst
			 inner join client_his_dis as dis
				on dis.org_no = m03_ccode
			   and dis.jumin  = m03_jumin
			   and date_format(dis.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(dis.to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			 where m03_ccode  = \''.$code.'\'
			   and m03_jumin  = \''.$jumin.'\'';

	$row   = $conn->get_array($sql);
	$svcNm = $conn->_svcNm($svcCd);
	
	if($row['val'] == '3'){
		$lvlNm = $myF->_lvlNm($row['lvl'], '40');
	}else {
		$lvlNm = $myF->_lvlNm($row['lvl']);
	}
	
	$lsCNm = $row['name'];
	$lsCCd = $row['jumin'];

	$liBipay1 = $row['bipay1'];
	$liBipay2 = $row['bipay2'];
	$liBipay3 = $row['bipay3'];

	switch($row['val']){
		case '1':
			$valNm = '성인';
			break;

		case '2':
			$valNm = '아동';
			break;

		case '3':
			$valNm = '성인';
			break;
	}

	unset($row);

	//장애인활동지원 등록정보
	$sql = 'SELECT svc_val as val
			,      svc_lvl as lvl
			  FROM client_his_dis
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND DATE_FORMAT(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
			   AND DATE_FORMAT(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'';
	$loDis = $conn->get_array($sql);

	$lsSvcVal = $loDis['val'];
	$lsSvcLvl = $loDis['lvl'];

	switch($lsSvcVal){
		case '1':
			$lsSvcVal = 'A';
			break;

		case '2':
			$lsSvcVal = 'C';
			break;
		
		case '3':
			$lsSvcVal = 'D';
			break;

		default:
			$lsSvcVal = 'X';
	}

	UnSet($loDis);

	if($lsSvcVal == 'D'){
		$lsSugaCd = 'VA'.$lsSvcVal.($lsSvcLvl < 10 ? '0'.$lsSvcLvl : $lsSvcLvl);;
	}else {
		$lsSugaCd = 'VA'.$lsSvcVal.$lsSvcLvl.'0';
	}

	$sql = 'SELECT service_code as code
			,      service_cost as cost
			  FROM suga_service
			 WHERE org_no       = \'goodeos\'
			   AND service_code = \''.$lsSugaCd.'\'
			   AND DATE_FORMAT(service_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   AND DATE_FORMAT(service_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
	$laSvcSuga = $conn->_fetch_array($sql,'code');
	$liSvcCost = $laSvcSuga[$lsSugaCd]['cost'];


	$sql = 'select voucher_gbn as val
			,      voucher_gbn2 as tm
			,      voucher_lvl as lvl
			,      voucher_addtime as add_time
			,      voucher_addpay as add_pay
			,      voucher_totaltime as limit_cnt
			,      voucher_totalpay as limit_amt
			,      voucher_suga_cd as suga_cd
			,      voucher_suga_cost as suga_cost
			,      voucher_makepay as makepay
			,      voucher_addtime1 as addtime1
			,      voucher_addtime2 as addtime2
			  from voucher_make
			 where org_no        = \''.$code.'\'
			   and voucher_kind  = \''.$svcCd.'\'
			   and voucher_jumin = \''.$jumin.'\'
			   and voucher_yymm  = \''.$year.$month.'\'
			   and del_flag      = \'N\'';
	$row = $conn->get_array($sql);

	//$liLimitPay = $row['limit_amt']; //총한도
	//$liAddPay   = $row['add_pay']; //시도/자치
	//$liMakePay  = $liLimitPay - $liAddPay; //생성금액

	$liLimitPay = $row['limit_amt']; //한도
	$liAddPay   = ($row['addtime1'] + $row['addtime2']) * $liSvcCost; //시도자치비
	$liMakePay  = $liLimitPay - $liAddPay; //생성금액

	unset($row);?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="80px">
			<col width="40px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">고객명</th>
				<td class="center"><div id="lblCNm" class="left"><a href="#" onclick="retrun false;"><span class="bold"><?=$lsCNm;?></span></a></div></td>
				<th class="center">구분</th>
				<td class="center last"><div class="left"><?=$valNm;?></div></td>
			</tr>
			<tr>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">생년월일</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?>"><div class="left"><?=$myF->issToBirthday($lsCCd,'.');?></div></td>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">등급</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?> last"><div class="left"><?=$lvlNm;?></div></td>
			</tr>
		</tbody>
	</table>
	<div id="infoClient" style="display:none;"
		limitAmt="<?=$liLimitPay;?>"
		makePay="<?=$liMakePay;?>"
		addPay="<?=$liAddPay;?>"
		svcCost="<?=$liSvcCost;?>"
		svcVal="<?=$lsSvcVal;?>"
		svcLvl="<?=$lsSvcLvl;?>"
		bipay200="<?=$liBipay1;?>"
		bipay500="<?=$liBipay2;?>"
		bipay800="<?=$liBipay3;?>"
		ynLoad="N">Y</div>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#infoClient').attr('ynLoad','Y');
		_planSetLimitAmt();
	});
	</script>