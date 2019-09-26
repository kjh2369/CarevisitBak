<?
	include("../inc/_header.php");

	$code	= $_GET["mCode"];
	$kind	= $_GET["mKind"];
	$key	= $_GET["mKey"];
	$ym		= $_GET['ym'];
	$type	= $_GET['mType'];
	$jumin	= $conn->get_sugupja_jumin($code, $kind, $key);

	// 수급자의 급여한도를 조회한다.
	$sql = "select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_skind
			  from (
				   select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_skind
				   ,      m03_sdate
				   ,      m03_edate
				     from m03sugupja
					where m03_ccode = '$code'
					  and m03_mkind = '$kind'
					  and m03_jumin = '$jumin'
					union all
				   select m31_kupyeo_max, m31_kupyeo_1, m31_bonin_yul, m31_kind
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
	$client_array = $conn->get_array($sql);
	$max_amount   = $client_array[0];	//한도금액
	$max_group    = $client_array[1];	//정부지원금한도액
	$bonin_yul    = $client_array[2];	//본인부담율
	$client_kind  = $client_array[3];	//수급자구분

	if ($type == 'search'){
		$title = '수급자 월수급 현황(실적기준)';
	}else{
		$title = '수급자 월수급 현황';
	}

	$sql = "select t01_svc_subcode as code
			,      case t01_bipay_umu when 'Y' then 'Y' else 'N' end as bipay_yn
			,      t01_suga_tot as pay
			  from t01iljung
			 where t01_ccode = '$code'
			   and t01_mkind = '$kind'
			   and t01_jumin = '$jumin'
			   and t01_sugup_date like '$ym%'
			   and t01_del_yn ='N'";

	if ($type == 'search'){
		$sql .= " and t01_status_gbn = '1'";
	}

	$sql .= "
			 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$temp_max_amount = 0;

	$amt = array('200' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0),
				 '500' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0),
				 '800' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0),
				 'tot' => array('total' => 0, 'bonin' => 0, 'over' => 0, 'bipay' => 0));

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($row['bipay_yn'] == 'Y'){
			$amt[$row['code']]['bipay'] += $row['pay'];
		}else{
			$temp_max_amount += $row['pay'];

			if ($max_amount > $temp_max_amount){
				$amt[$row['code']]['total'] += $row['pay'];
			}else{
				if ($max_amount >= $amt['200']['total'] + $amt['500']['total'] + $amt['800']['total']){
					$temp_max_prc = $max_amount - ($amt['200']['total'] + $amt['500']['total'] + $amt['800']['total']);
					$amt[$row['code']]['total'] += $temp_max_prc;
				}
				$amt[$row['code']]['over'] += $row['pay'] - $temp_max_prc;
			}
			$amt[$row['code']]['bonin'] += ($row['pay'] * $bonin_yul / 100);
		}
	}

	$amt['200']['bonin'] = floor($amt['200']['bonin']);
	$amt['500']['bonin'] = floor($amt['500']['bonin']);
	$amt['800']['bonin'] = floor($amt['800']['bonin']);

	$amt['tot']['total'] = $amt['200']['total'] + $amt['500']['total'] + $amt['800']['total'];
	$amt['tot']['bonin'] = $amt['200']['bonin'] + $amt['500']['bonin'] + $amt['800']['bonin'];
	$amt['tot']['over']  = $amt['200']['over']  + $amt['500']['over']  + $amt['800']['over'];
	$amt['tot']['bipay'] = $amt['200']['bipay'] + $amt['500']['bipay'] + $amt['800']['bipay'];

	$sur_amount = $max_group - ($amt['tot']['total'] + $amt['tot']['over']);

	$conn->row_free();
?>
<table style="width:900px;">
	<tr>
		<td style="background-color:#eeeeee; font-weight:bold;" colspan="6"><?=$title;?></td>
		<td style="background-color:#b2cef4; font-weight:bold;">급여한도금액</td>
	</tr>
	<tr>
		<td style="width:60px; height:24px; background-color:#eeeeee; font-weight:bold;">구분</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">수급(급여)계</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">본인부담액</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">초과</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">비급여</td>
		<td style="width:140px; height:24px; background-color:#eeeeee; font-weight:bold;">본인부담계</td>
		<!--td style="width:140px;" rowspan="5">
			<table style="width:140px;">
				<tr>
					<td class="noborder" style="background-color:#b2cef4; font-weight:bold;">공단부담한도액</td>
				</tr>
				<tr>
					<td class="noborder" style="text-align:right; font-weight:bold; padding-right:2px;">
						<?=number_format($max_amount);?>원<input name="maxAmount" type="hidden" value="<?=$max_amount;?>">
					</td>
				</tr>
				<tr>
					<td class="noborder" style="background-color:#b2cef4; font-weight:bold;">공단부담한도잔액</td>
				</tr>
				<tr>
					<td class="noborder" style="text-align:right; font-weight:bold;">
						<span id="txt_sur_amt" style="color:<? if($sur_amount > 0){?>#0000ff<?}else{?>#ff0000;<?} ?>;" tag="<?=$sur_amount;?>"><?=number_format($sur_amount);?></span>원
					</td>
				</tr>
			</table>
		</td-->
		<td style="width:140px; height:24px; font-weight:bold; text-align:right; padding-right:3px;"><?=number_format($max_amount);?>원<input name="maxAmount" type="hidden" value="<?=$max_group;?>"></td>
	</tr>
	<tr>
		<td style="height:24px;">요양</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_200_total"	tag="<?=$amt['200']['total'];?>"><?=number_format($amt['200']['total']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_200_bonin"	tag="<?=$amt['200']['bonin'];?>"><?=number_format($amt['200']['bonin']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_200_over"	tag="<?=$amt['200']['over'];?>" ><?=number_format($amt['200']['over']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_200_bipay"	tag="<?=$amt['200']['bipay'];?>"><?=number_format($amt['200']['bipay']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_200_sum"		tag="<?=$amt['200']['bonin']+$amt['200']['over']+$amt['200']['bipay'];?>"><?=number_format($amt['200']['bonin']+$amt['200']['over']+$amt['200']['bipay']);?></td>
		<td style="height:24px; background-color:#b2cef4; font-weight:bold;">청구한도금액</td>
	</tr>
	<tr>
		<td style="height:24px;">목욕</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_500_total"	tag="<?=$amt['500']['total'];?>"><?=number_format($amt['500']['total']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_500_bonin"	tag="<?=$amt['500']['bonin'];?>"><?=number_format($amt['500']['bonin']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_500_over"	tag="<?=$amt['500']['over'];?>" ><?=number_format($amt['500']['over']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_500_bipay"	tag="<?=$amt['500']['bipay'];?>"><?=number_format($amt['500']['bipay']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_500_sum"		tag="<?=$amt['500']['bonin']+$amt['500']['over']+$amt['500']['bipay'];?>"><?=number_format($amt['500']['bonin']+$amt['500']['over']+$amt['500']['bipay']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px;"><?=number_format($max_group);?>원</td>
	</tr>
	<tr>
		<td style="height:24px;">간호</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_800_total"	tag="<?=$amt['800']['total'];?>"><?=number_format($amt['800']['total']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_800_bonin"	tag="<?=$amt['800']['bonin'];?>"><?=number_format($amt['800']['bonin']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_800_over"	tag="<?=$amt['800']['over'];?>" ><?=number_format($amt['800']['over']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_800_bipay"	tag="<?=$amt['800']['bipay'];?>"><?=number_format($amt['800']['bipay']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000;" id="txt_800_sum"		tag="<?=$amt['800']['bonin']+$amt['800']['over']+$amt['800']['bipay'];?>"><?=number_format($amt['800']['bonin']+$amt['800']['over']+$amt['800']['bipay']);?></td>
		<td style="height:24px; background-color:#b2cef4; font-weight:bold;">청구한도잔액</td>
	</tr>
	<tr>
		<td style="height:24px; background-color:#eee;">계</td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000; background-color:#eee;" id="txt_tot_total"	tag="<?=$amt['tot']['total'];?>"><?=number_format($amt['tot']['total']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000; background-color:#eee;" id="txt_tot_bonin"	tag="<?=$amt['tot']['bonin'];?>"><?=number_format($amt['tot']['bonin']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000; background-color:#eee;" id="txt_tot_over"		tag="<?=$amt['tot']['over'];?>" ><?=number_format($amt['tot']['over']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000; background-color:#eee;" id="txt_tot_bipay"	tag="<?=$amt['tot']['bipay'];?>"><?=number_format($amt['tot']['bipay']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px; color:#000000; background-color:#eee;" id="txt_tot_sum"		tag="<?=$amt['tot']['bonin']+$amt['tot']['over']+$amt['tot']['bipay'];?>"><?=number_format($amt['tot']['bonin']+$amt['tot']['over']+$amt['tot']['bipay']);?></td>
		<td style="height:24px; font-weight:bold; text-align:right; padding-right:3px;"><span id="txt_sur_amt" style="color:<? if($sur_amount > 0){?>#0000ff<?}else{?>#ff0000;<?} ?>;" tag="<?=$sur_amount;?>"><?=number_format($sur_amount);?></span>원</td>
	</tr>
	<tr>
		<td style="height:24px; font-weight:bold; text-align:left; padding-left:15px; color:#000000;" colspan="7" id="totAmount"></td>
	</tr>
</table>

<input name="max_amount" type="hidden" value="<?=$max_group;?>" tag="<?=$max_group;?>">
<input name="bonin_yul"  type="hidden" value="<?=$bonin_yul;?>" tag="<?=$bonin_yul;?>">
<?
	include("../inc/_footer.php");
?>