<?
	include("../inc/_header.php");

	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mSugup = $_POST['mSugup'] != "" ? $_POST['mSugup'] : "all";
?>
<form name="f" method="post">
<table style="width:100%;">
<tr>
<td class="noborder" style="height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
<input name="mCode" type="hidden" value="<?=$mCode;?>">
<select name="mKind" style="width:150px;">
<?
	for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
	?>
		<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $mKind){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
	<?
	}
?>
</select>
<select name="mSugup">
<option value="all">전체</option>
<?
	$sql = "select distinct
			       m03_name"
		 . ",      t13_jumin"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_type = '2'"
		 . "   and t13_misu_amt > 0"
		 . " order by m03_name";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($mSugup == $row['t13_jumin']){
			$selected = 'selected';
		}else{
			$selected = '';
		}
		echo '<option value="'.$row['t13_jumin'].'" '.$selected.'>'.$row['m03_name'].'</option>';
	}
	$conn->row_free();
?>
</select>
<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="getNotAccountList('<?=$mCode;?>','<?=$mKind;?>',document.f.mSugup.value);">조회</button></span>
</td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:100%;">
<tr>
<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">수급자</th>
<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">주민번호</th>
<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">등급</th>
<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">구분</th>
<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">미수금액</th>
<th style="width:35%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">비고</th>
</tr>
<tbody id="rowList" style="display:;">
<?
	$sql = "select m03_name
			,      t13_jumin
			,      LVL.m81_name as lvl_name
			,      t13_bonin_yul
			,     (select m92_cont
				    from m92boninyul
				   where t13_bonin_yul = m92_code
				     and t13_pay_date between left(m92_sdate, 6) and left(m92_edate, 6)
				   order by m92_sdate, m92_edate
				   limit 1) as bonin_cont
			,      sum(t13_misu_amt - t13_misu_inamt) as misuAmt
			,      m03_key
			  from t13sugupja
			 inner join m03sugupja
			    on m03_ccode = t13_ccode
			   and m03_mkind = t13_mkind
			   and m03_jumin = t13_jumin
			 inner join m81gubun as LVL
			    on m81_gbn  = 'LVL'
			   and m81_code = m03_ylvl
			 where t13_ccode = '$mCode'
			   and t13_mkind = '$mKind'
			   and t13_type = '2'";

	if (Trim($mSugup) != 'all'){
		$sql .= " and t13_jumin = '".$mSugup."'";
	}

	$sql .= "
			group by m03_name, t13_jumin, LVL.m81_name, t13_bonin_yul, m03_key
			having misuAmt > 0
			order by m03_name
			";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$misuAmt = 0;
	$table = '';

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		
		$table .= '<tr>';
		$table .= '<td style="padding-left:0px; text-align:center;">'.$row['m03_name'].'</td>';
		$table .= '<td style="padding-left:0px; text-align:center;">'.getSSNStyle($row['t13_jumin']).'</td>';
		$table .= '<td style="padding-left:0px; text-align:center;">'.$row['lvl_name'].'</td>';
		$table .= '<td style="padding-left:0px; text-align:left;">'.$row['bonin_cont'].'</td>';
		$table .= '<td style="padding-left:0px; text-align:right;">'.number_format($row['misuAmt']).'</td>';
		$table .= '<td style="padding-left:0px; text-align:left;"><input type="button" onClick="popupDeposit(document.f.mCode.value, document.f.mKind.value, \''.$row['m03_key'].'\');" value="" style="width:67px; height:18px; border:0px; background:url(\'../image/btn_in.png\') no-repeat; cursor:pointer;"></td>';
		$table .= '</tr>';

		$misuAmt += $row['misuAmt'];

	}
	$conn->row_free();

	if ($row_count == 0){
		echo '<tr>';
		echo '<td style="padding-left:0px; text-align:center;" colspan="8">::검색된 데이타가 없습니다.::</td>';
		echo '</tr>';
	}else{
		$table = '
				<tr>
				<td style="padding-left:0px; text-align:center; font-weight:bold; background:#eee;"></td>
				<td style="padding-left:0px; text-align:center; font-weight:bold; background:#eee;"></td>
				<td style="padding-left:0px; text-align:center; font-weight:bold; background:#eee;"></td>
				<td style="padding-left:0px; text-align:center; font-weight:bold; background:#eee;">계</td>
				<td style="padding-left:0px; text-align:right;  font-weight:bold; background:#eee;">'.number_format($misuAmt).'</td>
				<td style="padding-left:0px; text-align:center; font-weight:bold; background:#eee;"></td>
				</tr>
				 '.$table;
		echo $table;
	}
?>
</tbody>
</table>
<input name="inAmt" type="hidden" value="<?=$inAmount;?>">
<input name="noAmt" type="hidden" value="<?=$noAmount;?>">
</form>
<?
	include("../inc/_footer.php");
?>