<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$svc_homecare = $_POST['svc_homecare'];
	$svc_voucher  = $_POST['svc_voucher'];
?>

<script src="../js/claim.js" type="text/javascript"></script>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function before(){
	var f = document.f;

	f.target = '_self';
	f.action = 'my_amt_list.php';
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title">본인부담금 내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="70px">
		<col>
		<col width="60px">
		<col width="150px">
		<col width="100px">
		<col width="70px">
		<col width="60px">
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>년 <?=$month;?>월</td>
			<td></td>
			<th class="center">전체출력</th>
			<td class="left">
				<img src="../image/btn_24ho.png" style="cursor:pointer;" onclick="_show_bill('24ho','all');">
				<img src="../image/btn_24hox.png" style="cursor:pointer;" onclick="_show_bill('24hox','all');">
				<img src="../image/btn_receipt_2.png" style="cursor:pointer;" onclick="_show_bill('receipt','all');">
			</td>
			<th>미수금관리(24호)</th>
			<td>
				<select id="misu_amt_yn" name="misu_amt_yn" style="width:auto;">
					<option value="Y">예</option>
					<option value="N" selected>아니오</option>
				</select>
			</td>
			<td class="left last" style="padding-top:1px;">
				<img src="../image/btn_prev.png" style="cursor:pointer;" onclick="before();">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="150px">
		<col width="100px" span="3">
		<col>
		<col width="55px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자명</th>
			<th class="head">구분</th>
			<th class="head">실적금액</th>
			<th class="head">청구금액</th>
			<th class="head">본인부담금액</th>
			<th class="head">청구서/명세서/영수증</th>
			<th class="head last">일정</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select t13_mkind as kind
				,      t13_jumin as c_cd
				,      m03_name as c_nm
				,      m03_key as c_key
				,      t13_bonin_yul as c_rate
				,      case when t13_mkind = '0' then concat('[',STP.m81_name,'(', m03_bonin_yul, ')]')
				            when t13_mkind = '4' then concat('[',m03_ylvl,'등급]') else '' end as c_gbn
				,      t13_suga_tot4 as c_suga
				,      t13_chung_amt4 as c_public
				,      t13_bonbu_tot4 as c_bonin
				  from t13sugupja
				  inner join (
					   select m03_mkind, m03_name, m03_jumin, m03_ylvl, m03_skind, m03_bonin_yul, m03_sdate, m03_edate, m03_key
						 from m03sugupja
						where m03_ccode = '$code'
						union all
					   select m31_mkind, m03_name, m31_jumin, m31_level, m31_kind, m31_bonin_yul, m31_sdate, m31_edate, m03_key
						 from m31sugupja
						inner join m03sugupja
						   on m31_ccode = m03_ccode
						  and m31_mkind = m03_mkind
						  and m31_jumin = m03_jumin
						where m31_ccode = '$code'
					   ) as sugupja
					on t13_jumin  = m03_jumin
				   and t13_mkind  = m03_mkind
				   and t13_pay_date between left(m03_sdate, 6) and left(m03_edate, 6)
				  left join m81gubun as STP
					on STP.m81_gbn  = 'STP'
				   and STP.m81_code = m03_skind
				 where t13_ccode    = '$code'
				   and t13_pay_date = '$year$month'
				   and t13_type     = '2'";

		if ($svc_homecare == 'Y' && $svc_voucher == 'Y'){
		}else if ($svc_homecare == 'Y'){
			$sql .= " and t13_mkind = '0'";
		}else{
			$sql .= " and t13_mkind != '0'";
		}

		$sql .= " order by c_nm, kind, c_rate";

		$conn->fetch_type = 'assoc';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($tmp_cd != $row['c_cd']){
				$tmp_cd  = $row['c_cd'];
				$j = sizeof($client);

				$client[$j]['cd']  = $row['c_cd'];
				$client[$j]['nm']  = $row['c_nm'];
				$client[$j]['key'] = $row['c_key'];

				$j = sizeof($client) - 1;
				$k = 0;
			}

			$client[$j]['dtl'][$k]['kind']   = $row['kind'];
			$client[$j]['dtl'][$k]['rate']   = $row['c_rate'];
			$client[$j]['dtl'][$k]['gbn']    = $row['c_gbn'];
			$client[$j]['dtl'][$k]['suga']   = $row['c_suga'];
			$client[$j]['dtl'][$k]['public'] = $row['c_public'];
			$client[$j]['dtl'][$k]['bonin']  = $row['c_bonin'];

			$k ++;
		}

		$conn->row_free();

		if (is_array($client)){
			foreach($client as $index => $c){
				$rows = sizeof($c['dtl']);

				if ($rows > 1)
					$rowspan = $rows + 1;
				else
					$rowspan = 1;

				$suga   = 0;
				$public = 0;
				$bonin  = 0;

				for($i=0; $i<$rows; $i++){
					if ($i == 0){
						echo '<tr>';
						echo '<td class=\'center\' rowspan=\''.$rowspan.'\'>'.($index + 1).'</td>';
						echo '<td class=\'left\' rowspan=\''.$rowspan.'\'>'.$c['nm'].'</td>';
					}

					echo '<td class=\'left\'>'.$conn->kind_name_svc($c['dtl'][$i]['kind']).$c['dtl'][$i]['gbn'].'</td>';
					echo '<td class=\'right\'>'.number_format($c['dtl'][$i]['suga']).'</td>';

					if ($c['dtl'][$i]['kind'] == '0'){
						//재가는 공단청구금액 출력
						echo '<td class=\'right\'>'.number_format($c['dtl'][$i]['public']).'</td>';
					}else{
						//바우처는 총구매역(실적금액)과 청구금액을 같이본다.
						echo '<td class=\'right\'>'.number_format($c['dtl'][$i]['suga']).'</td>';
					}

					echo '<td class=\'right\'>'.number_format($c['dtl'][$i]['bonin']).'</td>';

					$suga   += $c['dtl'][$i]['suga'];
					$public += $c['dtl'][$i]['public'];
					$bonin  += $c['dtl'][$i]['bonin'];

					if ($i == 0){
						echo '<td class=\'center\' rowspan=\''.$rowspan.'\'>';
						echo '<img src=\'../image/btn_24ho.png\' style=\'cursor:pointer;\' onclick=\'_show_bill("24ho","'.$ed->en($c['cd']).'");\'> ';
						echo '<img src=\'../image/btn_24hox.png\' style=\'cursor:pointer;\' onclick=\'_show_bill("24hox","'.$ed->en($c['cd']).'");\'> ';
						echo '<img src=\'../image/btn_receipt_2.png\' style=\'cursor:pointer;\' onclick=\'_show_bill("receipt","'.$ed->en($c['cd']).'");\'> ';
						echo '<img src=\'../image/btn_detail.gif\' style=\'cursor:pointer;\' onclick=\'_show_bill("detail","'.$ed->en($c['cd']).'");\'>';
						echo '</td>';
						echo '<td class=\'center last\' rowspan=\''.$rowspan.'\'>';
						echo '<img src=\'../image/btn_dariy.png\' style=\'cursor:pointer;\' onclick=\'_showPaymentsDiary("'.$code.'","'.$c['dtl'][$i]['kind'].'","'.$year.$month.'","'.$c['dtl'][$i]['rate'].'","'.$c['key'].'");\'>';
						echo '</td>';
						echo '</tr>';
					}
				}

				if ($rows > 1){
					echo '<tr>';
					echo '<td class=\'right bold\' style=\'background-color:#efefef;\'>소계</td>';
					echo '<td class=\'right bold\' style=\'background-color:#efefef;\'>'.number_format($suga).'</td>';
					echo '<td class=\'right bold\' style=\'background-color:#efefef;\'>'.number_format($public).'</td>';
					echo '<td class=\'right bold\' style=\'background-color:#efefef;\'>'.number_format($bonin).'</td>';
					echo '</tr>';
				}
			}
		}

		$row_count = sizeof($client);

		unset($client);




		/*
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$i + 1;?></td>
				<td class="left"><?=$row['c_nm'];?></td>
				<td class="left"><?=$conn->kind_name_svc($row['kind']).$row['c_gbn'];?></td>
				<td class="right"><?=number_format($row['c_suga']);?></td>
				<td class="right"><?=number_format($row['c_public']);?></td>
				<td class="right"><?=number_format($row['c_bonin']);?></td>
				<td class="left">
				<?
					if ($debug){

					}?>
					<img src="../image/btn_24ho.png"      style="cursor:pointer;" onclick="_printPayments24ho('<?=$code;?>','<?=$row['kind'];?>','<?=$year.$month;?>','<?=$row['c_rate'];?>','<?=$row['c_key'];?>', document.getElementById('misu_amt_yn').value);">
					<img src="../image/btn_24hox.png"     style="cursor:pointer;" onclick="_printPayments24hox('<?=$code;?>','<?=$row['kind'];?>','<?=$year.$month;?>','<?=$row['c_rate'];?>','<?=$row['c_key'];?>', document.getElementById('misu_amt_yn').value);">
					<img src="../image/btn_receipt_2.png" style="cursor:pointer;" onclick="_printPaymentsBill('<?=$code;?>','<?=$row['kind'];?>','<?=$year.$month;?>','<?=$row['c_rate'];?>','<?=$row['c_key'];?>');">
					<img src="../image/btn_detail.gif"    style="cursor:pointer;" onclick="_printDetailBill('<?=$code;?>','<?=$row['kind'];?>','<?=$year.$month;?>','<?=$row['c_rate'];?>','<?=$row['c_key'];?>', document.getElementById('misu_amt_yn').value);">
				</td>
				<td class="left last">
					<img src="../image/btn_dariy.png" style="cursor:pointer;" onclick="_showPaymentsDiary('<?=$code;?>','<?=$kind;?>','<?=$year.$month;?>','<?=$row['c_rate'];?>','<?=$row['c_key'];?>');">
				</td>
			</tr><?
		}

		$conn->row_free();
		*/
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="8"><?=$myF->message($row_count, 'N');?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" id="code"			name="code"         value="<?=$code;?>">
<input type="hidden" id="kind"			name="kind"         value="<?=$kind;?>">
<input type="hidden" id="year"			name="year"         value="<?=$year;?>">
<input type="hidden" id="month"			name="month"        value="<?=$month;?>">
<input type="hidden" id="jumin"			name="jumin"  		value="">
<input type="hidden" id="type"			name="type"  		value="">
<input type="hidden" id="svc_homecare"	name="svc_homecare" value="<?=$svc_homecare;?>">
<input type="hidden" id="svc_voucher"	name="svc_voucher"	value="<?=$svc_voucher;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>