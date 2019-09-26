<?
	include('../inc/_header.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYM = $_POST['mYM'];
?>
<table style="width:100%;">
<tr>
<td class="title"><?=subStr($mYM, 0, 4);?>년 <?=subStr($mYM, 4, 2);?>월 본인부담청구서(상세내역)</td>
<td class="noborder" style="text-align:right; padding-bottom:3px;">전체출력
	<img src="../image/btn_24ho.png" style="cursor:pointer;" onclick="_printPayments24ho('<?=$mCode;?>','<?=$mKind;?>','<?=$mYM;?>','','', document.getElementById('misu_amt_yn').value);">
	<img src="../image/btn_24hox.png" style="cursor:pointer;" onclick="_printPayments24hox('<?=$mCode;?>','<?=$mKind;?>','<?=$mYM;?>','','', document.getElementById('misu_amt_yn').value);">
	<img src="../image/btn_receipt_2.png" style="cursor:pointer;" onclick="_printPaymentsBill('<?=$mCode;?>','<?=$mKind;?>','<?=$mYM;?>','','');">
</td>
<td class="noborder" width=20% style="text-align:right; padding-bottom:3px;">
	미수금관리(24호)
	<select name="misu_amt_yn" style="width:auto;">
		<option value="Y">예</option>
		<option value="N" selected>아니오</option>
	</select>
</td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:100%; margin-top:-10px;">
<tr style="height:24px;">
<th style="width:5%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">No</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">수급자</th>
<th style="width:15%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">구분</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">서비스총액</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">공단청구액</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">본인부담액</th>
<th style="width:23%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">청구서/명세서/영수증</th>
<th style="width:7%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">일정</th>
</tr>
<?
	$sql = "select t13_jumin"
		 . ",      m03_name"
		 . ",      m03_key"
		 . ",      t13_bonin_yul"
		 . ",     (select concat(m92_cont,'(',cast(m92_bonin_yul as char),')')"
		 . "         from m92boninyul"
		 . "        where t13_bonin_yul = m92_code"
		 . "          and t13_pay_date between left(m92_sdate, 6) and left(m92_edate, 6)"
		 . "        order by m92_sdate, m92_edate"
		 . "        limit 1) as bonin_cont"
		 . ",      t13_suga_tot4"
		 . ",      t13_chung_amt4"
		 . ",      t13_bonbu_tot4"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_pay_date = '".$mYM
		 . "'  and t13_type = '2'"
		 . "   /*and t13_bonbu_tot4 > 0*/";

	//if ($mRate != ''){
	//	$sql .= " and t13_bonin_yul = '".$mRate."'";
	//}

	$sql.= " order by m03_name, t13_bonin_yul";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		/*
		echo '<tbody id="total">';
		echo '<tr>';
		echo '<td style="background-color:#eeeeee;"></td>';
		echo '<td style="background-color:#eeeeee;"></td>';
		echo '<td style="font-weight:bold; text-align:right; background-color:#eeeeee;">계</td>';
		echo '<td id="totAmount1" style="font-weight:bold; text-align:right; background-color:#eeeeee;">0</td>';
		echo '<td id="totAmount2" style="font-weight:bold; text-align:right; background-color:#eeeeee;">0</td>';
		echo '<td id="totAmount3" style="font-weight:bold; text-align:right; background-color:#eeeeee;">0</td>';
		echo '<td style="background-color:#eeeeee;"></td>';
		echo '<td style="background-color:#eeeeee;"></td>';
		echo '</tr>';
		echo '</tbody>';
		echo '<tbody id="detail">';
		*/
		$amount1 = 0;
		$amount2 = 0;
		$amount3 = 0;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$amount1 += $row['t13_suga_tot4'];
			$amount2 += $row['t13_chung_amt4'];
			$amount3 += $row['t13_bonbu_tot4'];

			echo '<tr>';
			echo '<td style="text-align:center;">'.($i+1).'</td>';
			echo '<td style="text-align:center;">'.$row['m03_name'].'</td>';
			echo '<td>'.$row['bonin_cont'].'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['t13_suga_tot4'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['t13_chung_amt4'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['t13_bonbu_tot4'],'원').'</td>';
			echo '<td style="text-align:center;">';
			//echo '<input type="button" onClick="billPrint(\''.$mCode.'\',\''.$mKind.'\',\''.$mYM.'\',\''.$ed->en($row['t13_jumin']).'\',\''.$row['t13_bonin_yul'].'\');" style="width:44px; height:16px; border:0px; background:url(\'../image/btn_bill.png\') no-repeat; cursor:pointer;"> ';
			echo '<input type="button" onClick="_printPayments24ho(\''.$mCode.'\', \''.$mKind.'\', \''.$mYM.'\', \''.$row['t13_bonin_yul'].'\', \''.$row['m03_key'].'\', document.getElementById(\'misu_amt_yn\').value);" style="width:44px; height:16px; border:0px; background:url(\'../image/btn_24ho.png\') no-repeat; cursor:pointer;"> ';
			echo '<input type="button" onClick="_printPayments24hox(\''.$mCode.'\', \''.$mKind.'\', \''.$mYM.'\', \''.$row['t13_bonin_yul'].'\', \''.$row['m03_key'].'\', document.getElementById(\'misu_amt_yn\').value);" style="width:44px; height:16px; border:0px; background:url(\'../image/btn_24hox.png\') no-repeat; cursor:pointer;"> ';
			echo '<input type="button" onClick="_printPaymentsBill(\''.$mCode.'\', \''.$mKind.'\', \''.$mYM.'\', \''.$row['t13_bonin_yul'].'\', \''.$row['m03_key'].'\');" style="width:44px; height:16px; border:0px; background:url(\'../image/btn_receipt_2.png\') no-repeat; cursor:pointer;">';
			echo '</td>';
			echo '<td style="text-align:center;"><input type="button" onClick="_showPaymentsDiary(\''.$mCode.'\', \''.$mKind.'\', \''.$mYM.'\', \''.$row['t13_bonin_yul'].'\', \''.$row['m03_key'].'\');" style="width:44px; height:16px; border:0px; background:url(\'../image/btn_dariy.png\') no-repeat; cursor:pointer;"></td>';
			echo '</tr>';
		}
		echo '</tbody>';
	}else{
		echo '<tr><td style="text-align:center;" colspan="8">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
?>
</table>
<input name="amount1" type="hidden" value="<?=$amount1;?>">
<input name="amount2" type="hidden" value="<?=$amount2;?>">
<input name="amount3" type="hidden" value="<?=$amount3;?>">
<?
	include("../inc/_footer.php");
?>