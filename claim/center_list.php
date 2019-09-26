<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'];
?>
<table class="view_type1" style="width:100%; height:100%;">
<tr style="height:24px;">
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">월</th>
<th style="width:20%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">서비스총액</th>
<th style="width:20%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">공단청구액</th>
<th style="width:20%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">본인부담액</th>
<th style="width:30%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">비고</th>
</tr>
<?
	$sql = "select substring(t13_pay_date, 5, 2) as payDate"
		 . ",      sum(t13_suga_tot4) as sugaAmt"
		 . ",      sum(t13_chung_amt4) as chungAmt"
		 . ",      sum(t13_bonbu_tot4) as boninAmt"
		 . "  from t13sugupja"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_pay_date like '".$mYear
		 . "%' and t13_type = '2'"
		 . " group by substring(t13_pay_date, 5, 2)"
		 . " order by substring(t13_pay_date, 5, 2)";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';
			echo '<td style="text-align:right;">'.$row['payDate'].'월</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['sugaAmt'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['chungAmt'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['boninAmt'],'원').'</td>';
			echo '<td style="text-align:left;">';
			echo '<input type="button" value="상세" class="btnSmall2" onClick=\'getDetailBill(myDetail, document.f.mCode.value, document.f.mKind.value, document.f.mYear.value, "'.$row['payDate'].'");\'>';

			if ($row['sugaAmt'] > 0){
				echo ' <input type="button" value="출력" class="btnSmall2" onClick=\'printPerson("'.$mCode.'", "'.$mKind.'", "'.$mYear.$row['payDate'].'", "all", "all");\'>';
			}
			echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr>';
		echo '<td style="text-align:center;" colspan="5">::검색된 데이타가 없습니다.::</td>';
		echo '</tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>