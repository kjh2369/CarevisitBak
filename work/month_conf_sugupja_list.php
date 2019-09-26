<?
	include('../inc/_header.php');
	include('../inc/_ed.php');
	include('../inc/_myFun.php');

	$mYear		= $_POST['mYear'];
	$mMonth		= $_POST['mMonth'];
	$mCode		= $_POST['mCode'];
	$mKind		= $_POST['mKind'];
	$mSugupja	= $ed->de($_POST['mSugupja']);
	$mKey		= $conn->get_data("select m03_key from m03sugupja where m03_ccode = '".$mCode."' and m03_mkind = '".$mKind."' and m03_jumin = '".$mSugupja."'");
	$isManager	= $_POST['isManager'];

	$sql = "select count(*)"
		 . "  from t13sugupja"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_jumin = '".$mSugupja
		 . "'  and t13_pay_date = '".$mYear.$mMonth
		 . "'  and t13_type = '2'";
	$confFlag = $conn->get_data($sql);

	// 입금여부
	$sql = "select count(*)"
		 . "  from t14deposit"
		 . " where t14_ccode = '".$mCode
		 . "'  and t14_mkind = '".$mKind
		 . "'  and t14_jumin = '".$mSugupja
		 . "'  and t14_pay_date = '".$mYear.$mMonth
		 . "'";
	$depositFlag = $conn->get_data($sql);

	if (!$isManager){
	?>
		<div style="text-align:right; padding-top:5px;">
		<?
			if ($confFlag == 0){
			//	echo '<input type="button" onClick="sugupDiaryOk();" value="저장" class="btnSmall1" onFocus="this.blur();">';
			//	echo '&nbsp;';
			//	echo '<input type="button" onClick="sugupConfOk();" value="확정처리" class="btnNot" onFocus="this.blur();">';
			?>
				<span class="btn_pack m icon"><span class="save"></span><button type="button" onClick="sugupDiaryOk();">저장</button></span>
				<span class="btn_pack m"><button type="button" onClick="sugupConfOk();">확정처리</button></span>
			<?
			}else{
				if ($depositFlag == 0){
				//	echo '<input type="button" onClick="sugupConfCancel();" value="확정취소" class="btnNot" onFocus="this.blur();">';
				?>
					<span class="btn_pack m"><button type="button" onClick="sugupConfCancel();">확정취소</button></span>
				<?
				}else{
				}
			}
			?>
			<!--input type="button" onClick="location.href='month_conf.php?mKind=<?=$mKind;?>&mYear=<?=$mYear;?>';" value="월별리스트" class="btnNot" onFocus="this.blur();"-->
			<span class="btn_pack m"><button type="button" onClick="location.href='month_conf.php?mKind=<?=$mKind;?>&mYear=<?=$mYear;?>';">월별리스트</button></span>
		</div>
	<?
	}
?>
<table class="view_type1" style="width:100%; height:100%;">
<tr style="height:24px;">
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">수급자</th>
<th style="width:7%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">등급</th>
<th style="width:7%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">부담율</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">급여한도액</th>
<th style="width:8%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">서비스</th>
<th style="width:9%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">급여총액</th>
<th style="width:30%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">본인부담액</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">공단청구액</th>
<th style="width:9%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">비고</th>
</tr>
<tr style="height:24px;">
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">초과+비급여</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">순수</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">계</th>
</tr>
<?
	$sql = "select m03_name as sugupjaName"
		 . ",      LVL.m81_name as lvlName"
		 . ",      m03_skind as boninYul"
		 . ",      m03_bonin_yul as boninRate"
		 . ",      m03_kupyeo_max as maxPay"
		 . ",      m03_jumin as sugupjaJumin"
		 . ",      m03_key as sugupjaKey"
		 . "  from m03sugupja"
		 . " inner join m81gubun"
		 . "    as LVL on LVL.m81_gbn = 'LVL'"
		 . "   and LVL.m81_code = m03_ylvl"
		 . " where m03_ccode = '".$mCode
		 . "'  and m03_mkind = '".$mKind
		 . "'  and m03_jumin = '".$mSugupja
		 . "'";
	$conn->query($sql);
	$conn->fetch();

	$sql = '';

	if ($confFlag > 0){
		$row = $conn->select_row(0);
		$conn->row_free();

		for($i=1; $i<=3; $i++){
			switch($i){
				case 1:
					$gubun = '방문요양';
					$serviceCode = '200';
					break;
				case 2:
					$gubun = '방문목욕';
					$serviceCode = '500';
					break;
				case 3:
					$gubun = '방문간호';
					$serviceCode = '800';
					break;
			}

			if ($i > 1) $sql .= " union all ";

			$sql .="select '".$gubun."' as gubun"
			     . ",      '".$serviceCode."' as serviceCode"
				 . ",      t13_bonin_yul as boninYul"
				 . ",      t13_max_amt as maxAmt"
				 . ",      t13_suga_tot".$i." as sugaTot"
				 . ",      t13_over_amt".$i." as overAmt"
				 . ",      t13_bipay".$i." as bipay"
				 . ",      t13_bonin_amt".$i." as boninAmt"
				 . ",      t13_bonbu_tot".$i." as boninTot"
				 . ",      t13_chung_amt".$i." as chungAmt"
				 . "  from t13sugupja"
				 . " where t13_ccode = '".$mCode
				 . "'  and t13_mkind = '".$mKind
				 . "'  and t13_jumin = '".$mSugupja
				 . "'  and t13_pay_date = '".$mYear.$mMonth
				 . "'  and t13_type = '2'";
		}
		$sql .= "order by boninYul, serviceCode";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$total  = '<tbody id="listTotal">';
		$total .= '<tr>';
		$result = '<tbody id="listDetail">';

		for($i=0; $i<$row_count; $i++){
			$def = $conn->select_row($i);

			if ($i == 0){
				$sugupjaName = $row['sugupjaName'];
				$levelName = $row['lvlName'];
				//$boinRate = $row['boninRate'];
				//$maxPay = number_format($row['maxPay']);
				$sugupjaJumin = $row['sugupjaJumin'];
				$borderTop = '';
			}else{
				$sugupjaName = '';
				$levelName = '';
				//$boinRate = '';
				//$maxPay = '';
				$borderTop = 'border-top:1px solid #ffffff;';
			}

			switch($def['boninYul']){
			case 1:
				$boinRate = '15.0%';
				break;
			case 2:
				$boinRate = '7.5%';
				break;
			case 3:
				$boinRate = '0.0%';
				break;
			case 4:
				$boinRate = '7.5%';
				break;
			default:
				$boinRate = $def['boninYul'].'%';
			}

			$maxPay = number_format($def['maxAmt']);

			$sugaTot = number_format($def['sugaTot']);
			//$overAmt = number_format($def['overAmt'] + $def['bipay']);
			$boninAmt = number_format($def['boninAmt']);
			//$boninTot = number_format($def['boninTot']);
			$chungAmt = number_format($def['chungAmt']);
			//$overTotAmt = number_format($def['overTotAmt']);

			// 총 급여총액
			$totalSugaTot += $def['sugaTot'];

			// 총 초과+비급여
			$totalOverTot += ($def['overAmt'] + $def['bipay']);

			// 총 순수
			$totalBoninTot += $def['boninAmt'];

			// 총 본인부담계
			$totalBoninBuTot += $def['boninTot'];

			// 총 공단청구액
			$totalChungTot += $def['chungAmt'];

			$result .= '<tr>';
			$result .= '<td style="text-align:left;'.$borderTop.'">'.$sugupjaName.'</td>';
			$result .= '<td style="text-align:left;'.$borderTop.'">'.$levelName.'</td>';

			if ($tempBoinRate != $boinRate){
				$tempBoinRate  = $boinRate;
				$tempNew = true;
				$result .= '<td style="text-align:left;">'.$boinRate.'</td>';
			}else{
				$tempNew = false;
				$result .= '<td style="text-align:left;'.$borderTop.'"></td>';
			}

			if ($tempMaxPay != $boinRate.$maxPay){
				$tempMaxPay  = $boinRate.$maxPay;
				$tempNew = true;
				$result .= '<td style="text-align:right;">'.$maxPay.'</td>';
			}else{
				$result .= '<td style="text-align:right;'.$borderTop.'"></td>';
			}

			//if ($tempNew == true){
			//	$boninTot = number_format($def['boninTot'] + $def['overTotAmt']);
			//	$overAmt = number_format($def['overAmt'] + $def['bipay']);
			//}else{
				$boninTot = number_format($def['boninTot']);
				$overAmt = number_format($def['overAmt'] + $def['bipay']);
			//}

			$result .= '<td style="text-align:center;">'.$def['gubun'].'</td>';
			$result .= '<td style="text-align:right;">'.$sugaTot.'</td>';
			$result .= '<td style="text-align:right;">'.$overAmt.'</td>';
			$result .= '<td style="text-align:right;">'.$boninAmt.'</td>';
			$result .= '<td style="text-align:right;">'.$boninTot.'</td>';
			$result .= '<td style="text-align:right;">'.$chungAmt.'</td>';
			$result .= '<td style="text-align:center;">&nbsp;</td>';
			$result .= '</tr>';

			$result .= '<input name="subCode[]"   type="hidden" value="'.$def['serviceCode'].'">';
			$result .= '<input name="jumin[]"     type="hidden" value="'.$ed->en($row['sugupjaJumin']).'">';
			$result .= '<input name="boninYul[]"  type="hidden" value="'.$row['boninYul'].'">';
			$result .= '<input name="maxPay[]"    type="hidden" value="'.$row['maxPay'].'">';
			$result .= '<input name="totalPay[]"  type="hidden" value="'.$def['sugaTot'].'">';
			$result .= '<input name="overPay[]"   type="hidden" value="'.$def['overAmt'].'">';
			$result .= '<input name="biPay[]"     type="hidden" value="'.$def['bipay'].'">';
			$result .= '<input name="boninPay1[]" type="hidden" value="'.($def['overAmt'] + $def['bipay']).'">';
			$result .= '<input name="boninPay2[]" type="hidden" value="'.$def['boninAmt'].'">';
			$result .= '<input name="boninPay3[]" type="hidden" value="'.$def['boninTot'].'">';
			$result .= '<input name="centerPay[]" type="hidden" value="'.$def['chungAmt'].'">';
		}
		$conn->row_free();
	}else{
		$row = $conn->select_row(0);
		$sugupjaName = $row['sugupjaName'];
		$levelName = $row['lvlName'];
		$boinRate = $row['boninRate'];
		$maxPay = number_format($row['maxPay']);
		$sugupjaJumin = $row['sugupjaJumin'];
		$conn->row_free();

		for($i=1; $i<=3; $i++){
			$result .= '<tr>';

			if ($i == 1){
				$result .= '<td style="text-align:left;">'.$sugupjaName.'</td>';
				$result .= '<td style="text-align:left;">'.$levelName.'</td>';
				$result .= '<td style="text-align:left;">'.$boinRate.'</td>';
				$result .= '<td style="text-align:right;">'.$maxPay.'</td>';
			}else{
				$result .= '<td style="text-align:left;  border-top:1px solid #ffffff;">&nbsp;</td>';
				$result .= '<td style="text-align:left;  border-top:1px solid #ffffff;">&nbsp;</td>';
				$result .= '<td style="text-align:left;  border-top:1px solid #ffffff;">&nbsp;</td>';
				$result .= '<td style="text-align:right; border-top:1px solid #ffffff;">&nbsp;</td>';
			}

			switch($i){
				case 1:
					$serviceCode = '200';
					$result .= '<td style="text-align:center;">방문요양</td>';
					break;
				case 2:
					$serviceCode = '500';
					$result .= '<td style="text-align:center;">방문목욕</td>';
					break;
				case 3:
					$serviceCode = '800';
					$result .= '<td style="text-align:center;">방문간호</td>';
					break;
			}


			$result .= '<td style="text-align:right;">0</td>';
			$result .= '<td style="text-align:right;">0</td>';
			$result .= '<td style="text-align:right;">0</td>';
			$result .= '<td style="text-align:right;">0</td>';
			$result .= '<td style="text-align:right;">0</td>';
			$result .= '<td style="text-align:center;">&nbsp;</td>';
			$result .= '</tr>';

			$result .= '<input name="subCode[]"   type="hidden" value="'.$serviceCode.'">';
			$result .= '<input name="jumin[]"     type="hidden" value="'.$ed->en($row['sugupjaJumin']).'">';
			$result .= '<input name="boninYul[]"  type="hidden" value="'.$row['boninYul'].'">';
			$result .= '<input name="maxPay[]"    type="hidden" value="'.$row['maxPay'].'">';
			$result .= '<input name="totalPay[]"  type="hidden" value="0">';
			$result .= '<input name="overPay[]"   type="hidden" value="0">';
			$result .= '<input name="biPay[]"     type="hidden" value="0">';
			$result .= '<input name="boninPay1[]" type="hidden" value="0">';
			$result .= '<input name="boninPay2[]" type="hidden" value="0">';
			$result .= '<input name="boninPay3[]" type="hidden" value="0">';
			$result .= '<input name="centerPay[]" type="hidden" value="0">';
		}
	}

	$total  .= '<td style="text-align:center; background-color:#eeeeee;"></td>';
	$total  .= '<td style="text-align:center; background-color:#eeeeee;"></td>';
	$total  .= '<td style="text-align:center; background-color:#eeeeee;"></td>';
	$total  .= '<td style="text-align:center; background-color:#eeeeee;"></td>';
	$total  .= '<td style="text-align:center; background-color:#eeeeee; font-weight:bold;">계</td>';
	$total  .= '<td style="text-align:right;  background-color:#eeeeee; font-weight:bold;" id="amtTotalPay">'.$myF->numberFormat($totalSugaTot).'</td>';
	$total  .= '<td style="text-align:right;  background-color:#eeeeee; font-weight:bold;" id="amtBoninPay1">'.$myF->numberFormat($totalOverTot).'</td>';
	$total  .= '<td style="text-align:right;  background-color:#eeeeee; font-weight:bold;" id="amtBoninPay2">'.$myF->numberFormat($totalBoninTot).'</td>';
	$total  .= '<td style="text-align:right;  background-color:#eeeeee; font-weight:bold;" id="amtBoninPay3">'.$myF->numberFormat($totalBoninBuTot).'</td>';
	$total  .= '<td style="text-align:right;  background-color:#eeeeee; font-weight:bold;" id="amtCenterPay">'.$myF->numberFormat($totalChungTot).'</td>';
	$total  .= '<td style="text-align:center; background-color:#eeeeee;">';

	if ($confFlag == 0){
		if ($mYear.$mMonth >= getPMonth()){
			//$total .= '<input type="button" onClick="_setSugupjaDiaryReg(\''.$mCode.'\', \''.$mKind.'\', \''.$mYear.'\', \''.$mMonth.'\', \''.$ed->en($mSugupja).'\',\''.$mKey.'\');" value="일정" class="btnSmall1" onFocus="this.blur();">';
			$total .= '<span class="btn_pack m"><button type="button" onClick="_setSugupjaDiaryReg(\''.$mCode.'\', \''.$mKind.'\', \''.$mYear.'\', \''.$mMonth.'\', \''.$ed->en($mSugupja).'\',\''.$mKey.'\');">일정</button></span>';
		}
	}

	$total  .= '</td>';
	$total  .= '</tr>';
	$total  .= '</tbody>';
	$result .= '</tbody>';

	echo $total.$result;
?>
</table>
<input name="confYear" type="hidden" value="<?=$mYear;?>">
<input name="confMonth" type="hidden" value="<?=$mMonth;?>">
<input name="confSugupja" type="hidden" value="<?=$sugupjaName;?>">
<input name="mKey" type="hidden" value="<?=$sugupjaKey;?>">
<div id="myDetail" style="padding-top:5px;"></div>
<?
	include("../inc/_footer.php");
?>