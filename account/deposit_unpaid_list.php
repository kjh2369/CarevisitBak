<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");

	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
?>
<table class="view_type2" style="width:100%; height:100%; margin:-1px; padding:0; border-bottom:1px solid #ccc;">
<tr>
	<th style="width:8%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">년도</th>
	<th style="width:7%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">월</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">본인부담총액</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">입금금액</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">미수금액</th>
	<th style="width:40%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">비고</th>
</tr>
<?
	$sql = "select t13_pay_date as payYM
			,      sum(t13_misu_amt) as notInAmount
			,      sum(t13_misu_inamt) as inAmount
			,      sum(t13_misu_inamt - t13_misu_amt) as diffAmount
			  from t13sugupja
			 where t13_ccode = '$mCode'
			   and t13_mkind = '$mKind'
			   and t13_type  = '2'
			 group by substring(t13_pay_date, 5)
			 order by t13_pay_date";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';

			if ($payY != subStr($row['payYM'], 0, 4)){
				$payY = subStr($row['payYM'], 0, 4);
				echo '<td style="text-align:center; border-bottom:0;">'.$payY.'년</td>';
			}else{
				echo '<td style="border-top:0; border-bottom:0;"></td>';
			}

			$payM = subStr($row['payYM'], 4, 2);

			echo '<td style="text-align:center; border-bottom:0;">'.$payM.'월</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['notInAmount'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['inAmount'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['diffAmount'],'원').'</td>';
			echo '<td>';

			if ($mGubun != 'NOT'){
				echo "<span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"getDeppsitDetailList('day','$payY','$payM');\">일별입금내역</button></span> ";
				echo "<span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"getDeppsitDetailList('sugupja','$payY','$payM');\">수급자별입금내역</button></span> ";
			}else{
				echo '<input type="button" value="미수금내역" class="btnM2" style="margin-top:2px;" onFocus="this.blur();" onClick="getNotDepositList(myDetail,\''.$mCode.'\',\''.$mKind.'\',\''.$mYear.'\',\''.$row['payMonth'].'\');">';
			}
			echo '</td>';
			echo '</tr>';
			echo '<tr id="monthTr_'.$row['payMonth'].'" style="display:none;"><td style="border-top:1px solid #ffffff;">&nbsp;</td><td style="text-align:right; padding:0;" colspan="4"><div id="monthDay_'.$row['payMonth'].'" style="width:100%; text-align:right;"></div></td></tr>';
		}
	}else{
		echo '<tr><td style="text-align:center;" colspan="6">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>