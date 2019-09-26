<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");

	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mYear  = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
?>
<table class="view_type2" style="width:100%; margin:0; padding:0; border-bottom:none;">
<tr>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">일자</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">수급자</th>
	<th style="width:20%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">주민번호</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">등급</th>
	<th style="width:20%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">구분</th>
	<th style="width:20%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">입금금액</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">비고</th>
</tr>
<?
	$sql = "select substring(t14_date, 7, 2) as inDate"
		 . ",      m03_name as sugupjaName"
		 . ",      t14_jumin as sugupjaJumin"
		 . ",      LVL.m81_name as lvlName"
		 . ",      STP.m81_name as stpName"
		 . ",      sum(t14_amount) as amount"
		 . "  from t14deposit"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t14_ccode"
		 . "   and m03_mkind = t14_mkind"
		 . "   and m03_jumin = t14_jumin"
		 . " inner join m81gubun as LVL"
		 . "    on LVL.m81_gbn  = 'LVL'"
		 . "   and LVL.m81_code = m03_ylvl"
		 . " inner join m81gubun as STP"
		 . "    on STP.m81_gbn  = 'STP'"
		 . "   and STP.m81_code = m03_skind"
		 . " where t14_ccode = '".$mCode
		 . "'  and t14_mkind = '".$mKind
		 . "'  and t14_pay_date = '".$mYear.$mMonth
		 . "'"
		 . " group by t14_date, m03_name, LVL.m81_name, STP.m81_name, t14_jumin"
		 . " order by t14_date, m03_name";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';
			echo '<td style="text-align:right; border-bottom:none;">'.$row['inDate'].'일</td>';
			echo '<td style="text-align:center; border-bottom:none;">'.$row['sugupjaName'].'</td>';
			echo '<td style="text-align:center; border-bottom:none;">'.$myF->issStyle($row['sugupjaJumin']).'</td>';
			echo '<td style="text-align:center; border-bottom:none;">'.$row['lvlName'].'</td>';
			echo '<td style="text-align:left; border-bottom:none;">'.$row['stpName'].'</td>';
			echo '<td style="text-align:right; border-bottom:none;">'.$myF->numberFormat($row['amount'],'원').'</td>';
			echo '<td style="text-align:center; border-bottom:none;">';

			if ($i == 0) echo '<input type="button" value="닫기" class="btnSmall2" onFocus="this.blur();" onClick="document.getElementById(\'monthTr_'.$mMonth.'\').style.display=\'none\';">';

			echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr><td style="text-align:center;" colspan="7">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>