<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");

	$mType  = $_POST['mType'];
	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mYear  = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
	$mYM = $mYear.$mMonth;
?>
<table style="width:100%;">
<tr>
	<td class="title"><?=$mMonth;?>월 미수금 상세내역</td>
</tr>
</table>
<table class="view_type1" style="width:100%; margin-top:-10px; padding:0;">
<tr>
	<th style="width:5%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">No.</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">수급자</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">주민번호</th>
	<th style="width:5%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">등급</th>
	<th style="width:20%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">구분</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">본인부담총액</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">입금금액</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">미수금액</th>
</tr>
<?
	$sql = "select m03_name as sugupjaName
			,      t13_jumin as sugupjaJumin
			,      LVL.m81_name as level
			,      STP.m81_name as kind
			,      sum(t13_misu_amt) as totalMisuAmt
			,      sum(t13_misu_inamt) as inAmt
			,      sum(t13_misu_amt - t13_misu_inamt) as misuAmt
			  from t13sugupja
			 inner join m03sugupja
				on m03_ccode = t13_ccode
			   and m03_mkind = t13_mkind
			   and m03_jumin = t13_jumin
			 inner join m81gubun as LVL
				on LVL.m81_gbn = 'LVL'
			   and LVL.m81_code = m03_ylvl
			 inner join m81gubun as STP
				on STP.m81_gbn = 'STP'
			   and STP.m81_code = m03_skind
			 where t13_ccode = '$mCode'
			   and t13_mkind = '$mKind'
			   and t13_pay_date = '$mYM'
			   and t13_type = '2'
			   and t13_misu_amt > 0
			 group by m03_name, t13_jumin, LVL.m81_name, STP.m81_name
			 order by sugupjaName";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	$totalMisuAmt = 0;
	$inAmt = 0;
	$misuAmt = 0;

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$totalMisuAmt += $row['totalMisuAmt'];
			$inAmt += $row['inAmt'];
			$misuAmt += $row['misuAmt'];

			echo '<tr>';
			echo '<td style="text-align:right;">'.($i+1).'.</td>';
			echo '<td style="text-align:left;">'.$row['sugupjaName'].'</td>';
			echo '<td style="text-align:left;">'.$myF->issStyle($row['sugupjaJumin']).'</td>';
			echo '<td style="text-align:left;">'.$row['level'].'</td>';
			echo '<td style="text-align:left;">'.$row['kind'].'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['totalMisuAmt'], '원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['inAmt'], '원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['misuAmt'], '원').'</td>';
			echo '</tr>';
		}

		echo '<tr>';
		echo '<td style="text-align:right; font-weight:bold; background:#eeeeee;" colspan="5">계</td>';
		echo '<td style="text-align:right; font-weight:bold; background:#eeeeee;">'.$myF->numberFormat($totalMisuAmt, '원').'</td>';
		echo '<td style="text-align:right; font-weight:bold; background:#eeeeee;">'.$myF->numberFormat($inAmt, '원').'</td>';
		echo '<td style="text-align:right; font-weight:bold; background:#eeeeee;">'.$myF->numberFormat($misuAmt, '원').'</td>';
		echo '</tr>';
	}else{
		echo '<tr><td style="text-align:center;" colspan="8">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>