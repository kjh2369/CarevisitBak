<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_SESSION['userCenterCode'];
	$kind	= $_SESSION['userCenterKind'][0];
	$year	= $_REQUEST['year'];
	$month	= $_REQUEST['month'];
	$jumin	= $ed->de($_REQUEST['jumin']);
	$ent_dt	= $ed->de($_REQUEST['ent_dt']);
	$ent_seq= $ed->de($_REQUEST['ent_seq']);

	$sql = "select m03_name as name
			,      case when m03_mkind = '0' then lvl.m81_name else '' end as lvl
			,      case when m03_mkind = '0' then stp.m81_name else '' end as stp
			,      m03_bonin_yul as bonin_yul
			,     (select ifnull(sum(t13_bonbu_tot4 /*t13_misu_amt*/), 0)
					 from t13sugupja
				    where t13_ccode = m03_ccode
					  and t13_jumin = m03_jumin
					  and t13_type  = '2')
		    -     (select ifnull(sum(deposit_amt), 0)
					 from unpaid_deposit
				    where org_no        = m03_ccode
					  and deposit_jumin = m03_jumin
					  and del_flag      = 'N') as deposit_amt
			  from m03sugupja
			  left join m81gubun as lvl
				on lvl.m81_gbn  = 'LVL'
			   and lvl.m81_code = m03_ylvl
			  left join m81gubun as stp
				on stp.m81_gbn  = 'STP'
			   and stp.m81_code = m03_skind
			 where m03_ccode = '$code'
			   and m03_jumin = '$jumin'
			   and m03_mkind = ".$conn->_client_kind();

	$conn->fetch_type = 'assoc';
	$client = $conn->get_array($sql);

	$find_name = $_POST['find_name'];

	if (strlen($ent_dt) == 10 && intval($ent_seq) > 0){
		$mode = true;
		$sql = "select deposit_reg_dt
				,      deposit_type
				,      deposit_amt
				  from unpaid_deposit
				 where org_no         = '$code'
				   and deposit_ent_dt = '$ent_dt'
				   and deposit_seq    = '$ent_seq'";

		$deposit_data = $conn->get_array($sql);

		$deposit_date = $deposit_data['deposit_reg_dt'];
		$deposit_type = $deposit_data['deposit_type'];
		$deposit_amt  = $deposit_data['deposit_amt'];

		unset($deposit_data);
	}

	if (!$deposit_type) $deposit_type = '01';
	if (!$deposit_date) $deposit_date = date('Y-m-d', mktime());

	if ($mode){
		$return_uri = 'deposit_day_list.php';
	}else{
		$return_uri = 'unpaid_list.php';
	}
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.action = '<?=$return_uri;?>';
	f.submit();
}

function set_amt(){
	var in_amt      = document.getElementById('in_amt');						//입금금액
	var tot_unpaid	= __str2num(document.getElementById('tot_unpaid').value);	//총미수금액
	var unpaid_amt  = document.getElementsByName('unpaid_amt[]');				//미납금액
	var deposit_amt = document.getElementsByName('deposit_amt[]');				//입금금액

	if (__str2num(in_amt.value) > tot_unpaid){
		in_amt.value = tot_unpaid;
	}

	var tmp_amt = __str2num(in_amt.value);

	for(var i=0; i<deposit_amt.length; i++){
		deposit_amt[i].value = __num2str(deposit_amt[i].tag);

		if (tmp_amt > (__str2num(unpaid_amt[i].value) - __str2num(deposit_amt[i].value))){
			tmp_amt = tmp_amt - (__str2num(unpaid_amt[i].value) - __str2num(deposit_amt[i].value));
			deposit_amt[i].value = __num2str(unpaid_amt[i].value);
		}else{
			deposit_amt[i].value = __num2str(tmp_amt - __str2num(deposit_amt[i].value));
			tmp_amt = 0;
		}
	}
}

function in_amt_ok(){
	var f = document.f;
	var deposit_amt = document.getElementsByName('deposit_amt[]');

	if (deposit_amt.length == 0){
		alert('미납내역이 없습니다.');
		return;
	}

	if (!__object_is_checked('in_type')){
		alert('입금구분을 선택하여 주십시오.');
		return;
	}

	if (!checkDate(f.in_dt.value)){
		alert('입금일자를 입력하여 주십시오.');
		f.in_dt.focus();
		return;
	}

	if (__str2num(f.in_amt.value) == 0){
		alert('입금금액을 입력하여 주십시오.');
		f.in_amt.focus();
		return;
	}

	f.action = 'unpaid_reg_ok.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<form name="f" method="post">

<div class="title">미수금 입금처리</div>

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="40px">
		<col width="80px">
		<col width="40px">
		<col width="120px">
		<col width="80px">
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">수급자명</th>
			<td class="left"><?=$client['name'];?></td>
			<th class="head">등급</th>
			<td class="left"><?=$client['lvl'];?></td>
			<th class="head">구분</th>
			<td class="left"><?=$row['stp_name'].(!empty($row['stp_name']) ? '('.$row['bonin_yul'].')' : '');?></td>
			<th class="head">총미수금액</th>
			<td class="right"><?=number_format($client['deposit_amt']);?></td>
			<td class="right last" style="padding-top:1px;">
				<img src="../image/btn_prev.png" style="cursor:pointer;" onclick="list();">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">입금구분</th>
			<td class="last" colspan="3">
			<?
				$deposit_list = $definition->DepositList();
				$deposit_cnt  = sizeof($deposit_list);

				for($i=0; $i<$deposit_cnt; $i++){
					if ($deposit_list[$i]['use']){?>
						<input name="in_type" type="radio" class="radio" value="<?=$deposit_list[$i]['cd'];?>" <? if($deposit_type == $deposit_list[$i]['cd']){?>checked<?} ?>><a href="#" onclick="__object_checked('in_type', '<?=$deposit_list[$i]['cd'];?>');"><?=$deposit_list[$i]['nm'];?></a>
						<input name="in_type_cal_<?=$deposit_list[$i]['cd'];?>" type="hidden" value="<?=$deposit_list[$i]['cal'];?>">
						<input name="in_type_income_<?=$deposit_list[$i]['cd'];?>" type="hidden" value="<?=$deposit_list[$i]['income'];?>"><?
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<th class="center">입금일자</th>
			<td><input name="in_dt" type="text" class="date" value="<?=$deposit_date;?>" maxlength="8" onkeydown="__onlyNumber(this);" onclick="_carlendar(this);"></td>
			<th class="center">입금금액</th>
			<td class="last"><input name="in_amt" type="text" class="number" value="<?=number_format($deposit_amt);?>" maxlength="8" onkeydown="__onlyNumber(this);" onchange="set_amt();"></td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="center last bottom" colspan="4">
				<span class="btn_pack m"><button type="button" onclick="in_amt_ok();">입금처리</button></span>
			</td>
		</tr>
	</tbody>
</table>

<div class="title title_border" style="width:50%; float:left;">미납내역</div>
<div class="title title_border" style="width:100%;">입금내역</div>
<div style="width:50%; float:left;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">미납년월</th>
				<th class="head">미납금액</th>
				<th class="head">입금금액</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
		<tbody>
		<?
			$sql = "select concat(left(t13_pay_date, 4), '.', substring(t13_pay_date, 5)) as yymm
					,     sum(t13_bonbu_tot4 /*t13_misu_amt*/) as unpaid_amt
					,    (select ifnull(sum(deposit_amt), 0)
							 from unpaid_deposit_list
							where org_no = t13_ccode
							  and unpaid_jumin = t13_jumin
							  and unpaid_yymm  = t13_pay_date) as deposit_amt
					  from t13sugupja
					 where t13_ccode = '$code'
					   and t13_jumin = '$jumin'
					   and t13_type  = '2'
					 group by t13_pay_date";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			$unpaid_cnt = 0;

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				if ($row['unpaid_amt'] - $row['deposit_amt'] > 0){
					$unpaid_cnt ++;?>
					<tr>
						<td class="center"><?=$row['yymm'];?></td>
						<td class="right"><?=number_format($row['unpaid_amt'] - $row['deposit_amt']);?></td>
						<td class="center"><input name="deposit_amt[]" type="text" value="0" tag="$row['unpaid_amt'] - $row['deposit_amt']" class="number" style="width:100%;" readonly></td>
						<td class="left last">&nbsp;</td>
						<input name="unpaid_yymm[]" type="hidden" value="<?=$row['yymm'];?>">
						<input name="unpaid_amt[]" type="hidden" value="<?=$row['unpaid_amt'] - $row['deposit_amt'];?>">
					</tr><?
				}
			}

			$conn->row_free();
		?>
		</tbody>
		<tbody>
			<tr><?
				if ($unpaid_cnt > 0){?>
					<td class="left last bottom" colspan="4"><?=$myF->message($unpaid_cnt, 'N');?></td><?
				}else{?>
					<td class="center last bottom" colspan="4"><?=$myF->message('nodata', 'N');?></td><?
				}?>
			</tr>
		</tbody>
	</table>
</div>
<div style="width:100%;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="80px">
			<col width="80px">
			<col width="100px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">입금일자</th>
				<th class="head">입금구분</th>
				<th class="head">입금금액</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
		<tbody>
		<?
			$sql = "select deposit_reg_dt
					,      deposit_yymm
					,      deposit_type
					,      deposit_amt
					,      cash_bill_no
					,      card_no
					,      approval_no
					  from unpaid_deposit
					 where org_no        = '$code'
					   and deposit_jumin = '$jumin'
					   and del_flag      = 'N'
					 order by deposit_reg_dt desc";

			$conn->fetch_type = 'assoc';
			$conn->query($sql);
			$conn->fetch_assoc();
			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);?>
				<tr>
					<td class="center"><?=$i+1;?></td>
					<td class="center"><?=str_replace('-','.',$row['deposit_reg_dt']);?></td>
					<td class="left"><?=$definition->DepositName($row['deposit_type']);?></td>
					<td class="right"><?=number_format($row['deposit_amt']);?></td>
					<td class="left last"></td>
				</tr><?
			}

			$conn->row_free();
		?>
		</tbody>
		<tbody>
			<tr><?
				if ($row_count > 0){?>
					<td class="left last bottom" colspan="5"><?=$myF->message($row_count, 'N');?></td><?
				}else{?>
					<td class="center last bottom" colspan="5"><?=$myF->message('nodata', 'N');?></td><?
				}?>
			</tr>
		</tbody>
	</table>
</div>

<input type="hidden" name="code"		value="<?=$code;?>">
<input type="hidden" name="kind"		value="<?=$kind;?>">
<input type="hidden" name="jumin"		value="<?=$ed->en($jumin);?>">
<input type="hidden" name="find_name"	value="<?=$find_name;?>">

<input type="hidden" name="year"	value="<?=$year;?>">
<input type="hidden" name="month"	value="<?=$month;?>">

<input type="hidden" name="ent_dt"	value="<?=$ent_dt;?>">
<input type="hidden" name="ent_seq"	value="<?=$ent_seq;?>">

<input type="hidden" name="tot_unpaid" value="<?=$client['deposit_amt'];?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>