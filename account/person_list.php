<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	
	$mSearch = $_POST['mSearch'];
	$mCode   = $_POST['mCode'];
	$mKind   = $_POST['mKind'];
	$mYear   = $_POST['mYear'];
	
	$setYear = $conn->get_iljung_year($mCode);
	
	if ($mYear == ''){
		$mYear = date('Y', mkTime());
	}
?>
<table class="view_type1" style="width:100%; height:100%;">
<tr>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">월</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">계획</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">실적</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">차익</th>
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">비고</th>
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
<th style="width:%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">부담액</th>
</tr>
<?
	$sql = "select substring(t13_pay_date, 5, 2) as payDate"
		 . ",      t13_type"
		 . ",      sum(t13_suga_tot4) as t13_suga_tot4"
		 . ",      sum(t13_chung_amt4) as t13_chung_amt4"
		 . ",      sum(t13_bonbu_tot4) as t13_bonbu_tot4"
		 . "  from t13sugupja"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_pay_date like '".$mYear
		 . "%'"
		 . " group by substring(t13_pay_date, 5, 2), t13_type"
		 . " order by substring(t13_pay_date, 5, 2), t13_type";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($payDate != $row['payDate']){
				if ($payDate != ''){
					if ($tempType != '2'){
						$sugaTot['2']  = 0;
						$chungAmt['2'] = 0;
						$bonbuAmt['2'] = 0;

						echo '<td style="padding-left:0px; text-align:right;">0</td>';
						echo '<td style="padding-left:0px; text-align:right;">0</td>';
						echo '<td style="padding-left:0px; text-align:right;">0</td>';	
					}

					echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($sugaTot['1'] - $sugaTot['2'],'원').'</td>';
					echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($chungAmt['1'] - $chungAmt['2'],'원').'</td>';
					echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($bonbuAmt['1'] - $bonbuAmt['2'],'원').'</td>';
					echo getDetailString($mCode, $mKind, $mYear, $payDate);
					echo '</tr>';
					echo getDetailTr($payDate);
				}
				$payDate = $row['payDate'];

				echo '<tr>';
				echo '<td style="padding-left:0px; text-align:center;">'.$row['payDate'].'월</td>';
				
				$tempType = '';
			}

			if ($tempType != $row['t13_type']){
				$tempType  = $row['t13_type'];

				$sugaTot[$tempType]  = $row['t13_suga_tot4'];
				$chungAmt[$tempType] = $row['t13_chung_amt4'];
				$bonbuAmt[$tempType] = $row['t13_bonbu_tot4'];

				echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($row['t13_suga_tot4'],'원').'</td>';
				echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($row['t13_chung_amt4'],'원').'</td>';
				echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($row['t13_bonbu_tot4'],'원').'</td>';
			}
		}

		if ($row_count > 0){
			if ($tempType != '2'){
				$sugaTot['2']  = 0;
				$chungAmt['2'] = 0;
				$bonbuAmt['2'] = 0;

				echo '<td style="padding-left:0px; text-align:right;">0</td>';
				echo '<td style="padding-left:0px; text-align:right;">0</td>';
				echo '<td style="padding-left:0px; text-align:right;">0</td>';
			}

			echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($sugaTot['1']  - $sugaTot['2'],'원').'</td>';
			echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($chungAmt['1'] - $chungAmt['2'],'원').'</td>';
			echo '<td style="padding-left:0px; text-align:right;">'.$myF->numberFormat($bonbuAmt['1'] - $bonbuAmt['2'],'원').'</td>';
			echo getDetailString($mCode, $mKind, $mYear, $payDate);
			echo '</tr>';
			//echo getDetailTr($payDate);
		}else{
			echo '<tr><td style="padding-left:0px; text-align:center;" colspan="13">::검색된 데이타가 없습니다.::</td></tr>';
		}
	}else{
		echo '<tr><td style="text-align:center;" colspan="13">::검색된 데이타가 없습니다.::</td></tr>';
	}

	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");

	function getDetailString($c, $k, $y, $m){
		//$value = '<td style="padding-left:0px; text-align:center;">'
		//	   . '<input type="button" value="상세" class="btnSmall2" onClick=\'getPersonAccountDetail("detailTr_", detailDiv_'.$m.', "'.$c.'", "'.$k.'", "'.$y.'", "'.$m.'")\''
		//	   . '</td>';
		$value = '<td style="padding-left:0px; text-align:center;">'
			   . '<input type="button" value="상세" class="btnSmall2" onClick=\'getPersonAccountDetail(myDetail, "'.$c.'", "'.$k.'", "'.$y.'", "'.$m.'")\''
			   . '</td>';
		return $value;
	}

	function getDetailTr($m){
		return '<tr id="detailTr_'.$m.'" style="display:none;"><td style="border-top:1px solid #ffffff; border-bottom:none;">&nbsp;</td><td style="text-align:right; padding:0; border-top:1px solid #ffffff; border-bottom:none;" colspan="10"><div id="detailDiv_'.$m.'"></div></td></tr>';
	}
?>