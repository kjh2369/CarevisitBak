<?
	include("../inc/_header.php");

	$code = $_GET["mCode"];
	$kind = $_GET["mKind"];
	$key  = $_GET["mKey"];
	$ym   = $_GET['ym'];
	$jumin= $conn->get_sugupja_jumin($code, $kind, $key);

	// 수급자의 급여한도를 조회한다.
	/*
	$sql = "select m03_kupyeo_max"
	     . ",      m03_jumin"
		 . "  from m03sugupja"
		 . " where m03_ccode = '".$_GET["mCode"]
		 . "'  and m03_mkind = '".$_GET["mKind"]
		 . "'  and m03_key   = '".$_GET["mKey"]
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();
	$mMaxAmount = $row["m03_kupyeo_max"];
	$conn->row_free();
	*/
	$sql = "select m03_kupyeo_max
			  from (
				   select m03_kupyeo_max
				   ,      m03_sdate
				   ,      m03_edate
				     from m03sugupja
					where m03_ccode = '$code'
					  and m03_mkind = '$kind'
					  and m03_jumin = '$jumin'
					union all
				   select m31_kupyeo_max
				   ,      m31_sdate
				   ,      m31_edate
					 from m31sugupja
					where m31_ccode = '$code'
					  and m31_mkind = '$kind'
				      and m31_jumin = '$jumin'
				   ) as t
			 where '$ym' between left(m03_sdate, 6) and left(m03_edate, 6)
			 order by m03_sdate desc, m03_edate desc
			 limit 1";
	$mMaxAmount = $conn->get_data($sql);

	if ($_GET['mType'] == 'search'){
		$title = '수급자 월수급 현황(실적기준)';
	}else{
		$title = '수급자 월수급 현황';
	}
?>
<table style="width:900px;">
	<tr>
		<td style="background-color:#eeeeee; font-weight:bold;" colspan="7"><?=$title;?></td>
	</tr>
	<tr>
		<td style="width:60px; height:24px; background-color:#eeeeee; font-weight:bold;">구분</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">수급(급여)계</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">본인부담액</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">초과</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">비급여</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">본인부담계</td>
		<td style="width:140px;" rowspan="5">
			<table style="width:140px;">
				<tr>
					<td class="noborder" style="background-color:#b2cef4; font-weight:bold;">급여한도</td>
				</tr>
				<tr>
					<td class="noborder" style="text-align:right; font-weight:bold; padding-right:2px;">
						<?=number_format($mMaxAmount);?>원<input name="maxAmount" type="hidden" value="<?=$mMaxAmount;?>">
					</td>
				</tr>
				<tr>
					<td class="noborder" style="background-color:#b2cef4; font-weight:bold;">급여잔액</td>
				</tr>
				<tr>
					<td class="noborder" style="text-align:right; font-weight:bold;">
						<span id="txtSurAmount">0</span>원<input name="surAmount" type="hidden" value="<?=$mSurAmount;?>">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="height:24px;">요양</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtSugub200Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBonin200Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtOver200Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBiPay200Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBoninSum200Amt">0</td>
	</tr>
	<tr>
		<td style="height:24px;">목욕</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtSugub500Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBonin500Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtOver500Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBiPay500Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBoninSum500Amt">0</td>
	</tr>
	<tr>
		<td style="height:24px;">간호</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtSugub800Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBonin800Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtOver800Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBiPay800Amt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBoninSum800Amt">0</td>
	</tr>
	<tr>
		<td style="height:24px;">계</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtSugubTotAmt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBoninTotAmt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtOverTotAmt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBiPayTotAmt">0</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txtBoninSumTotAmt">0</td>
	</tr>
	<tr>
		<td style="height:24px; font-weight:bold; text-align:left; padding-left:15px; color:#000000;" colspan="7" id="totAmount"></td>
	</tr>
</table>
<input name="sugub200Amt" type="hidden" value="">
<input name="sugub500Amt" type="hidden" value="">
<input name="sugub800Amt" type="hidden" value="">
<input name="sugubTotAmt" type="hidden" value="">

<input name="bonin200Amt" type="hidden" value="">
<input name="bonin500Amt" type="hidden" value="">
<input name="bonin800Amt" type="hidden" value="">
<input name="boninTotAmt" type="hidden" value="">

<input name="over200Amt" type="hidden" value="">
<input name="over500Amt" type="hidden" value="">
<input name="over800Amt" type="hidden" value="">
<input name="overTotAmt" type="hidden" value="">

<input name="biPay200Amt" type="hidden" value="">
<input name="biPay500Amt" type="hidden" value="">
<input name="biPay800Amt" type="hidden" value="">
<input name="biPayTotAmt" type="hidden" value="">

<input name="boninSum200Amt" type="hidden" value="">
<input name="boninSum500Amt" type="hidden" value="">
<input name="boninSum800Amt" type="hidden" value="">
<input name="boninSumTotAmt" type="hidden" value="">

<input name="amtSurAmount" type="hidden" value="">
<input name="amtMaxAmount" type="hidden" value="<?=$mMaxAmount;?>">
<?
	include("../inc/_footer.php");
?>