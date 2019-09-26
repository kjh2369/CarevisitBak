<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	
	$mSearch = $_POST['mSearch'];
	$mCode   = $_POST['mCode'];
	$mKind   = $_POST['mKind'];
	$mYear   = $_POST['mYear'];
	$mMonth  = $_POST['mMonth'];
?>
<table style="width:100%;">
<tr>
	<td class="title"><?=$mMonth;?>월별미수금현황 상세조회</td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:100%; margin-top:-10px; padding:0;">
<tr>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">수급자명</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">주민번호</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">등급</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">계획</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">실적</th>
<th style="width:%; height:24px; padding:0px; text-align:center;" colspan="3">차익</th>
</tr>
<tr>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">총금액</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">청구액</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">부담액</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">총금액</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">청구액</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">부담액</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">총금액</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">청구액</th>
<th style="width:%; height:24px; padding:0px; text-align:center;">부담액</th>
</tr>
<?
	$sql = "select m03_name"
		 . ",      LVL.m81_name as lvl_name"
		 . ",      t13_jumin"
		 . ",      t13_type"
		 . ",      sum(t13_suga_tot4) as t13_suga_tot4"
		 . ",      sum(t13_chung_amt4) as t13_chung_amt4"
		 . ",      sum(t13_bonbu_tot4) as t13_bonbu_tot4"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " inner join m81gubun as LVL"
		 . "    on m81_gbn  = 'LVL'"
		 . "   and m81_code = m03_ylvl"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_pay_date = '".$mYear.$mMonth
		 . "'"
		 . " group by m03_name, LVL.m81_name, t13_jumin, t13_type"
		 . " order by m03_name, t13_jumin, t13_type";
		
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($tempJumin != $row['t13_jumin']){
				if ($tempJumin != ''){
					if ($tempType != '2'){
						$sugaTot['2']  = 0;
						$chungAmt['2'] = 0;
						$bonbuAmt['2'] = 0;

						echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">0</td>';
						echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">0</td>';
						echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">0</td>';	
					}

					echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($sugaTot['1']  - $sugaTot['2'],'원').'</td>';
					echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($chungAmt['1'] - $chungAmt['2'],'원').'</td>';
					echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($bonbuAmt['1'] - $bonbuAmt['2'],'원').'</td>';
					echo '</tr>';
				}
				$tempJumin  = $row['t13_jumin'];

				echo '<tr>';
				echo '<td style="padding-left:0px; text-align:center; border-bottom:none;">'.$row['m03_name'].'</td>';
				echo '<td style="padding-left:0px; text-align:center; border-bottom:none;">'.getSSNStyle($row['t13_jumin']).'</td>';
				echo '<td style="padding-left:0px; text-align:center; border-bottom:none;">'.$row['lvl_name'].'</td>';

				$tempType = '';
			}

			if ($tempType != $row['t13_type']){
				$tempType  = $row['t13_type'];

				$sugaTot[$tempType]  = $row['t13_suga_tot4'];
				$chungAmt[$tempType] = $row['t13_chung_amt4'];
				$bonbuAmt[$tempType] = $row['t13_bonbu_tot4'];

				echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($row['t13_suga_tot4'],'원').'</td>';
				echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($row['t13_chung_amt4'],'원').'</td>';
				echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($row['t13_bonbu_tot4'],'원').'</td>';
			}
		}

		if ($row_count > 0){
			if ($tempType != '2'){
				$sugaTot['2']  = 0;
				$chungAmt['2'] = 0;
				$bonbuAmt['2'] = 0;

				echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">0</td>';
				echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">0</td>';
				echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">0</td>';	
			}

			echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($sugaTot['1']  - $sugaTot['2'],'원').'</td>';
			echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($chungAmt['1'] - $chungAmt['2'],'원').'</td>';
			echo '<td style="padding-left:0px; text-align:right; border-bottom:none;">'.$myF->numberFormat($bonbuAmt['1'] - $bonbuAmt['2'],'원').'</td>';
			echo '</tr>';
		}else{
			echo '<tr><td style="padding-left:0px; text-align:center;" colspan="12">::검색된 데이타가 없습니다.::</td></tr>';
		}
	}else{
		echo '<tr><td style="text-align:center;" colspan="12">::검색된 데이타가 없습니다.::</td></tr>';
	}

	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>