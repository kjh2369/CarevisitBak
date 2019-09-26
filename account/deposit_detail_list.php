<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	include('../inc/_ed.php');

	$mType  = $_POST['mType'];
	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mYear  = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
?>
<table style="width:100%;">
<tr>
	<td class="title"><?=$mMonth;?>월 입금내역 상세조회</td>
</tr>
</table>
<table class="view_type1" style="width:100%; margin-top:-10px; padding:0;">
<tr>
	<?
		if ($mType == 'day'){
		?>
			<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">입금일자</th>
			<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">수급자</th>
			<th style="width:20%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">주민번호</th>
		<?
		}else{
		?>
			<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">수급자</th>
			<th style="width:20%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">주민번호</th>
			<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">입금일자</th>
		<?
		}
	?>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">등급</th>
	<th style="width:20%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">구분</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">입금구분</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">입금금액</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:none;">비고</th>
</tr>
<?
	$sql = "select t14_date as inDate"
		 . ",      m03_name as sugupjaName"
		 . ",      t14_jumin as sugupjaJumin"
		 . ",      LVL.m81_name as lvlName"
		 . ",      STP.m81_name as stpName"
		 . ",      t14_type as type"
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
		 . "'  and t14_amount > 0"
		 . " group by t14_date, m03_name, LVL.m81_name, STP.m81_name, t14_jumin, t14_type";
	
	if ($mType == 'day'){
		$sql .= " order by t14_date, m03_name";
	}else{
		$sql .= " order by m03_name, t14_date";
	}
		
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';

			if ($mType == 'day'){
				if ($inDate != $row['inDate']){
					$inDate  = $row['inDate'];
					echo '<td style="text-align:center; border-bottom:none;">'.$myF->dateStyle($row['inDate']).'</td>';
				}else{
					echo '<td style="text-align:right; border-bottom:none; border-top:none;"></td>';
				}
				
				echo '<td style="text-align:center; border-bottom:none;">'.$row['sugupjaName'].'</td>';
				echo '<td style="text-align:center; border-bottom:none;">'.$myF->issStyle($row['sugupjaJumin']).'</td>';
			}else{
				if ($sugupjaName != $row['sugupjaName']){
					$sugupjaName  = $row['sugupjaName'];
					echo '<td style="text-align:center; border-bottom:none;">'.$row['sugupjaName'].'</td>';
					echo '<td style="text-align:center; border-bottom:none;">'.$myF->issStyle($row['sugupjaJumin']).'</td>';
				}else{
					echo '<td style="text-align:center; border-bottom:none; border-top:none;"></td>';
					echo '<td style="text-align:center; border-bottom:none; border-top:none;"></td>';
				}
				
				echo '<td style="text-align:center; border-bottom:none;">'.$myF->dateStyle($row['inDate']).'</td>';
			}
			
			echo '<td style="text-align:center; border-bottom:none;">'.$row['lvlName'].'</td>';
			echo '<td style="text-align:left; border-bottom:none;">'.$row['stpName'].'</td>';
			echo '<td style="text-align:left; border-bottom:none;">'.$definition->DepositGbn($row['type']).'</td>';
			echo '<td style="text-align:right; border-bottom:none;">'.$myF->numberFormat($row['amount'],'원').'</td>';
			echo '<td style="text-align:center; border-bottom:none;"><input type="button" onFocus="this.blur();" onClick="printReceipt(\''.$mCode.'\',\''.$mKind.'\',\''.$mYear.$mMonth.'\',\''.$row['inDate'].'\',\''.$row['type'].'\',\''.$ed->en($row['sugupjaJumin']).'\');" value="" style="width:45px; height:18px; border:0px; background:url(\'../image/btn_receipt.png\') no-repeat; cursor:pointer;"></td>';
			echo '</tr>';
		}
	}else{
		echo '<tr><td style="text-align:center; border-bottom:none;" colspan="8">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>