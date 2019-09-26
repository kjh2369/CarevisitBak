<?
	include('../inc/_header.php');
	include('../inc/_myFun.php');

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'];
?>
<table class="view_type1" style="width:100%; height:100%;">
<tr style="height:24px;">
<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">월</th>
<th style="width:15%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">서비스총액</th>
<th style="width:15%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">공단청구액</th>
<th style="width:15%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">본인부담액</th>
<th style="width:51%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">비고</th>
</tr>
<?
	$sql = "select substring(t13_pay_date, 5, 2) as payMonth"
		 . ",      sum(t13_suga_tot4) as sugaAmt"
		 . ",      sum(t13_chung_amt4) as chungAmt"
		 . ",      sum(t13_bonbu_tot4) as boninAmt"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_pay_date like '".$mYear
		 . "%' and t13_type = '2'"
		 . "   and t13_bonbu_tot4 > 0"
		 . " group by substring(t13_pay_date, 5, 2)"
		 . " order by substring(t13_pay_date, 5, 2)";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';

			if ($tempPayMonth != $row['payMonth']){
				$tempPayMonth  = $row['payMonth'];
				echo '<td style="text-align:center;">'.$row['payMonth'].'월</td>';
			}else{
				echo '<td style="text-align:center; border-top:1px solid #ffffff;"></td>';
			}
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['sugaAmt'], '원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['chungAmt'], '원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['boninAmt'], '원').'</td>';
			echo '<td style="text-align:left;">';
			//echo '<input type="button" value="상세" class="btnSmall1" onClick=\'getPaymentsBillDetail(myDetail, document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'");\' title="본인부담금 청구서 상세내역">';
			//echo '<input type="button" value="수납일자별" class="btnNot" onClick=\'_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'", "1");\' style="margin-left:2px;" title="본인부담금 수냅대장(24호)">';
			//echo '<input type="button" value="월별(가나다)" class="btnNot" onClick=\'_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'", "2");\' style="margin-left:2px;" title="본인부담금 수냅대장(가나다순)">';
			//echo '<input type="button" value="월별(수납일)" class="btnNot" onClick=\'_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'", "3");\' style="margin-left:2px;" title="본인부담금 수냅대장(수납일순)">';
			//echo '<input type="button" value="발급대장" class="btnNot" onClick=\'_printPaymentIssu(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'")\' style="margin-left:2px;" title="장기요양급여비용 명세서 발급대장">';
			echo '<span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick=\'getPaymentsBillDetail(myDetail, document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'");\' title="본인부담금 청구서 상세내역">상세</button></span> ';

			if ($mCode == '31150000051'){
				echo '<span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick=\'_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'", "1", 1);\' title="본인부담금 수냅대장(24호)">수납일자별</button></span> ';
			}else{
				echo '<span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick=\'_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'", "1");\' title="본인부담금 수냅대장(24호)">수납일자별</button></span> ';
			}

			echo '<span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick=\'_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'", "2");\' title="본인부담금 수냅대장(가나다순)">월별(가나다)</button></span> ';
			echo '<span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick=\'_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'", "3");\' title="본인부담금 수냅대장(수납일순)">월별(수납일)</button></span> ';
			echo '<span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick=\'_printPaymentIssu(document.f.mCode.value, document.f.mKind.value, "'.($mYear.$row['payMonth']).'")\' title="장기요양급여비용 명세서 발급대장">발급대장</button></span>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
	}else{
		echo '<tr><td style="text-align:center;" colspan="8">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>