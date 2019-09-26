<?
	#######################################################################
	#
	#

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$sql = 'select m03_sgbn as add_pay1
			,      m03_add_pay_gbn as add_pay2
			,      m03_add_time1 as sido_time
			,      m03_add_time2 as jach_time
			,      m03_bath_add_yn as bath_add
			  from m03sugupja
			 where m03_ccode  = \''.$code.'\'
			   and m03_mkind  = \''.$__CURRENT_SVC_CD__.'\'
			   and m03_jumin  = \''.$jumin.'\'
			   and m03_del_yn = \'N\'';

	$laDisOption = $conn->get_array($sql);

	$current_svc_nm = $conn->kind_name($laSvcList, $__CURRENT_SVC_ID__, 'id');
	$body_w = '100%';

	#
	#######################################################################

	$sql = 'select svc_val
			  from client_his_dis
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
			   and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')';
	$disVal = $conn -> get_data($sql);		   

	$disVal = $disVal != '' ? $disVal : '1';


	include('./client_reg_sub_reason.php');
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>목욕초과산정</th>
			<td>
			<?
				if ($view_type == 'read'){?>
					<div class="left"> <?=($client['bath_add'] == 'Y' ? '예' : '아니오');?></div><?
				}else{?>
					<input id="bathAddY_<?=$__CURRENT_SVC_ID__;?>" name="<?=$__CURRENT_SVC_ID__;?>_bathAddYn" type="radio" class="radio" value="Y" <? if($laDisOption['bath_add'] == 'Y'){?>checked<?}?>><label for="bathAddY_<?=$__CURRENT_SVC_ID__;?>">예</label>
					<input id="bathAddN_<?=$__CURRENT_SVC_ID__;?>" name="<?=$__CURRENT_SVC_ID__;?>_bathAddYn" type="radio" class="radio" value="N" <? if($laDisOption['bath_add'] != 'Y'){?>checked<?}?>><label for="bathAddN_<?=$__CURRENT_SVC_ID__;?>">아니오</label><?
				}
			?>
			</td>
		</tr><?
		if($lsDisMenu == '1'){ ?>
			<tr>
				<th>장애급여구분</th>
				<td>
					<input id="disGbn_1" name="disGbn" type="radio" class="radio" value="1" <? if($disVal == '1'){?>checked<?}?> onclick="divViewType(this);" ><label for="disGbn_1">구</label>
						<input id="disGbn_3" name="disGbn" type="radio" class="radio" value="3" <? if($disVal == '3'){?>checked<?}?> onclick="divViewType(this);"><label for="disGbn_3" >신</label>
				</td>
			</tr><?
		} ?>
	</tbody>
</table>
<div id="svcDiv_1"> 
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col width="55px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="2">지원등급</th>
			<th>등급</th>
			<td class="left">
				<div id="disVal" value="" class="clsData"></div>
				<div id="disSeq" value="" class="clsData" style="display:none;"></div>
				<div id="disSpt" value="" class="clsData" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="disFrom" value="" class="clsData"></span><span id="disTo" value="" class="clsData"></span></div>
				<div style="float:left; width:auto;"><?
					if ($view_type != 'read'){?>
						<span class="btn_pack m"><button type="button" onclick="_clientDisShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<th rowspan="2">소득등급</th>
			<th>등급</th>
			<td class="left">
				<div id="disLvl" value="" class="clsData"></div>
				<div id="disLvlSeq" value="" class="clsData" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="disLvlFrom" value="" class="clsData"></span><span id="disLvlTo" value="" class="clsData"></span></div>
				<div style="float:left; width:auto;"><?
					if ($view_type != 'read'){?>
						<span class="btn_pack m"><button type="button" onclick="_clientLvlShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<th class="bottom">추가급여분류</th>
			<td class="bottom" colspan="2">
				<div style="margin-bottom:5px; padding-bottom:5px; border-bottom:1px solid #cccccc;"><?
				$sql = 'select svc_gbn_cd as cd
						,      svc_gbn_nm as nm
						,      svc_pay as pay
						,      svc_time as time
						  from suga_service_add
						 where svc_kind     = \''.$__CURRENT_SVC_CD__.'\'
						   and svc_from_dt <= \''.date('Y-m-d').'\'
						   and svc_to_dt   >= \''.date('Y-m-d').'\'
						   and svc_group    = \'R\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				if ($view_type != 'read'){?>
					<input id="addPay1_0" name="addPay1" type="radio" value="" value1="0" value2="0" class="radio clsAddPay" checked><label for="addPay1_0">해당없음</label><?

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);?>
						<div><input id="addPay1_<?=$row['cd'];?>" name="addPay1" type="radio" value="<?=$row['cd'];?>" value1="<?=$row['pay'];?>" value2="<?=$row['time'];?>" class="radio clsAddPay" <?if($laDisOption['add_pay1'] == $row['cd']){?>checked<?}?>><label for="addPay1_<?=$row['cd'];?>"><?=$row['nm'];?>(<?=$row['time'];?>시간)</label></div><?
					}
				}else{
					if (empty($laDisOption['add_pay1'])){?>
						<div style="line-height:17px;"><span style="margin-left:5px; margin-right:3px; font-weight:bold; color:#ff0000;">√</span><span>해당없음</span></div>
						<input id="addPay1_0" name="addPay1" type="checkbox" value="" value1="0" value2="0" style="display:none;" class="clsAddPay" checked><?
					}
					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);?>
						<div style="line-height:17px;"><span style="margin-left:5px; width:10px; font-weight:bold; color:#ff0000;"><?=$laDisOption['add_pay1'] == $row['cd'] ? '√' : '&nbsp;';?></span><span><?=$row['nm'];?>(<?=$row['time'];?>시간)</span></div><?
						if ($laDisOption['add_pay1'] == $row['cd']){?>
							<input id="addPay1_<?=$row['cd'];?>" name="addPay1" type="checkbox" value="<?=$row['cd'];?>" value1="<?=$row['pay'];?>" value2="<?=$row['time'];?>" style="display:none;" class="clsAddPay" <?if($laDisOption['add_pay1'] == $row['cd']){?>checked<?}?>><?
						}
					}
				}

				$conn->row_free();?>
				</div>
				<div style="margin-bottom:5px;"><?
				$ltAddPay2 = explode('/',substr($laDisOption['add_pay2'],1));

				if (is_array($ltAddPay2)){
					foreach($ltAddPay2 as $val){
						$laAddPay2[$val] = $val;
					}
				}

				$sql = 'select svc_gbn_cd as cd
						,      svc_gbn_nm as nm
						,      svc_pay as pay
						,      svc_time as time
						  from suga_service_add
						 where svc_kind     = \''.$__CURRENT_SVC_CD__.'\'
						   and svc_from_dt <= \''.date('Y-m-d').'\'
						   and svc_to_dt   >= \''.date('Y-m-d').'\'
						   and svc_group    = \'C\'
						   and svc_gbn_cd  < 13';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				if ($view_type != 'read'){
					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);?>
						<div><input id="addPay2_<?=$row['cd'];?>" name="addPay2" names="addPay2" type="checkbox" value="<?=$row['cd'];?>" value1="<?=$row['pay'];?>" value2="<?=$row['time'];?>" class="checkbox clsAddPay" <?if($laAddPay2[$row['cd']] == $row['cd']){?>checked<?}?>><label for="addPay2_<?=$row['cd'];?>"><?=$row['nm'];?>(<?=$row['time'];?>시간)</label></div><?
					}
				}else{
					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);?>
						<div style="line-height:17px;"><span style="margin-left:5px; width:10px; font-weight:bold; color:#ff0000;"><?=$laAddPay2[$row['cd']] == $row['cd'] ? '√' : '&nbsp;';?></span><span><?=$row['nm'];?>(<?=$row['time'];?>시간)</span></div>
						<input id="addPay2_<?=$row['cd'];?>" name="addPay2" names="addPay2" type="checkbox" value="<?=$row['cd'];?>" value1="<?=$row['pay'];?>" value2="<?=$row['time'];?>" style="display:none;" class="clsAddPay" <?if($laAddPay2[$row['cd']] == $row['cd']){?>checked<?}?>><?
					}
				}

				$conn->row_free();?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include('../common/voucher_addpay_tbl.php');
?>
</div>
<div id="svcDiv_2" style="display:none;"> 
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col width="55px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="2">지원등급</th>
			<th>등급</th>
			<td class="left">
				<div id="disVal2" value="3" class="clsData"></div>
				<div id="disSeq2" value="" class="clsData" style="display:none;"></div>
				<div id="disSpt2" value="" class="clsData" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="disFrom2" value="" class="clsData"></span><span id="disTo2" value="" class="clsData"></span></div>
				<div style="float:left; width:auto;"><?
					if ($view_type != 'read'){?>
						<span class="btn_pack m"><button type="button" onclick="_clientDisShow2('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<th rowspan="2">소득등급</th>
			<th>등급</th>
			<td class="left">
				<div id="disLvl2" value="" class="clsData"></div>
				<div id="disLvlSeq2" value="" class="clsData" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="disLvlFrom2" value="" class="clsData"></span><span id="disLvlTo2" value="" class="clsData"></span></div>
				<div style="float:left; width:auto;"><?
					if ($view_type != 'read'){?>
						<span class="btn_pack m"><button type="button" onclick="_clientLvlShowNew('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<th class="bottom">특별지원급여</th>
			<td class="bottom" colspan="2">
				
				<div style="margin-bottom:5px;"><?
				$ltAddPay2 = explode('/',substr($laDisOption['add_pay2'],1));

				if (is_array($ltAddPay2)){
					foreach($ltAddPay2 as $val){
						$laAddPay2[$val] = $val;
					}
				}

				$sql = 'select svc_gbn_cd as cd
						,      svc_gbn_nm as nm
						,      svc_pay as pay
						,      svc_time as time
						  from suga_service_add
						 where svc_kind     = \''.$__CURRENT_SVC_CD__.'\'
						   and svc_from_dt <= \''.date('Y-m-d').'\'
						   and svc_to_dt   >= \''.date('Y-m-d').'\'
						   and svc_group    = \'C\'
						   and svc_gbn_cd  >= 13';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				if ($view_type != 'read'){
					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);?>
						<div><input id="addPay2_<?=$row['cd'];?>" name="addPay2" names="addPay2New" type="checkbox" value="<?=$row['cd'];?>" value1="<?=$row['pay'];?>" value2="<?=$row['time'];?>" class="checkbox clsAddPay" <?if($laAddPay2[$row['cd']] == $row['cd']){?>checked<?}?>><label for="addPay2_<?=$row['cd'];?>"><?=$row['nm'];?>(<?=$row['time'];?>시간)</label></div><?
					}
				}else{
					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);?>
						<div style="line-height:17px;"><span style="margin-left:5px; width:10px; font-weight:bold; color:#ff0000;"><?=$laAddPay2[$row['cd']] == $row['cd'] ? '√' : '&nbsp;';?></span><span><?=$row['nm'];?>(<?=$row['time'];?>시간)</span></div>
						<input id="addPay2_<?=$row['cd'];?>" name="addPay2" names="addPay2New" type="checkbox" value="<?=$row['cd'];?>" value1="<?=$row['pay'];?>" value2="<?=$row['time'];?>" style="display:none;" class="clsAddPay" <?if($laAddPay2[$row['cd']] == $row['cd']){?>checked<?}?>><?
					}
				}

				$conn->row_free();?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include('../common/voucher_addpay_tbl_new.php');
?>
</div>

<input id="disVals" value="<?=$disVal;?>" type="hidden" />
<div id="disLoadYn" value1="N" value2="N" style="display:none;"></div>

<script type="text/javascript">
	var liAddTot     = 0;
	var liAddTime    = 0;
	var liAddSupport = 0;
	var liAddExpense = 0;
	var liCost       = lfGetCost();

	//단가저장
	function lfGetCost(){
		var year  = $('#year').val();
		var month = $('#month').val();

		if (!year || !month){
			var date = new Date();

			year  = date.getFullYear();
			month = date.getMonth()+1;
			month = (month < 10 ? '0' : '')+month;
		}
		
		if (year+month >= '201901'){
			return 12960;
		}else if (year+month >= '201801'){
			return 10760;
		}else if (year+month >= '201701'){
			return 9240;
		}else if (year+month >= '201601'){
			return 9000;
		}else if (year+month >= '201502'){
			return 8810;
		}else if (year+month >= '201302'){
			return 8550;
		}else{
			return 8300;
		}
	}
</script>
<?
	if ($lsRoot == 'sugupja'){?>
		<script type="text/javascript">
		$('.clsAddPay').unbind('click').click(function(){
			lfInitAddPay();
		});
		</script><?
	}else{?>
		<script type="text/javascript">
		$('#overSupport').unbind('change').change(function(){
			var cost = lfGetCost();
			var time = __round(__str2num($(this).val()) / cost, 1);

			$('#overTot').attr('value',$(this).val()).text(__num2str($(this).val()));
			$('#overTime').attr('value',time).text(__num2str(time));

			lfTotAddPay();
		});
		</script><?
	}
?>
<script type="text/javascript">
	$('#sidoTime').change(function(){
		lfInitSJPay(this,'sido');
	});

	$('#jachTime').change(function(){
		lfInitSJPay(this,'jach');
	});
	
	divViewType();

	function lfAddPayLoad(){
		lfInitAddPay();
		lfInitSJPay($('#sidoTime'),'sido');
		lfInitSJPay($('#jachTime'),'jach');
	}

	function lfInitAddPay(){
		liAddTot     = 0;
		liAddTime    = 0;
		liAddSupport = 0;
		liAddExpense = 0;

		lfSetAddPay('add');
		lfSetAddPayNew('add');
		
		if($('#disVals').val() == '1' || $('#disVals').val() == '2'){
			$('input:radio[name="addPay1"]:checked').each(function(){
				lfCalAddPay(this,'add');
			});

			$('input:checkbox[names="addPay2"]:checked').each(function(){
				lfCalAddPay(this,'add');
			});
		}else {
			$('input:checkbox[names="addPay2New"]:checked').each(function(){
				lfCalAddPay(this,'add');		
			});
		}
		
		lfSetAddPay('add');
		lfTotAddPay();
		
		lfSetAddPayNew('add');
		lfTotAddPayNew();
	
	}

	function lfInitSJPay(obj, gbn){
		var cost = lfGetCost();

		liAddTot     = __str2num($(obj).attr('value')) * cost;
		liAddSupport = liAddTot;

		$('#'+gbn+'Tot').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#'+gbn+'Support').attr('value',liAddSupport).text(__num2str(liAddSupport));

		lfTotAddPay();
	}

	function lfSetAddPay(gbn){
		$('#'+gbn+'Tot').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#'+gbn+'Time').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#'+gbn+'Support').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#'+gbn+'Expense').attr('value',liAddExpense).text(__num2str(liAddExpense));
	}

	function lfSetAddPayNew(gbn){
		$('#'+gbn+'Tot2').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#'+gbn+'Time2').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#'+gbn+'Support2').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#'+gbn+'Expense2').attr('value',liAddExpense).text(__num2str(liAddExpense));
	}

	function lfCalAddPay(obj,gbn){
		try{
			
			var pay  = __str2num($(obj).attr('value1'));
			var time = __str2num($(obj).attr('value2'));
			var lvl  = __str2num($('#disLvl').attr('value'));
			
		
			var expenseAmt = 0;
			var supportAmt = 0;

			var result = 1;
			var tday = new Date();
			var date  = tday.getFullYear()+'-'+(((tday.getMonth()+1) < 10 ? '0' : '')+(tday.getMonth()+1))+'-'+((tday.getDate() < 10 ? '0' : '')+tday.getDate());
			var today  = date;
			//var today  = getToday();
			
			try{
				for(var i=0; i<laExpense[lvl].length; i++){
					if (laExpense[lvl][i]['from'] <= today && laExpense[lvl][i]['to'] >= today){
						if (laExpense[lvl][i]['pay'] > 0){
							result = laExpense[lvl][i]['pay'];
						}else{
							result = laExpense[lvl][i]['rate'];
						}
						break;
					}
				}
			}catch(e){
			}
			
			if (result > 1){
				expenseAmt = result;
			}else{
				expenseAmt = cut(pay * result, 100);
			}

			supportAmt = pay - expenseAmt;

			liAddTot     += pay;
			liAddTime    += time;
			liAddSupport += supportAmt;
			liAddExpense += expenseAmt;

		}catch(e){
		}
	}

	function lfTotAddPay(){
		var liAddTot     = 0;
		var liAddTime    = 0;
		var liAddSupport = 0;
		var liAddExpense = 0;

		$('.clsTot').each(function(){
			liAddTot += __str2num($(this).attr('value'));
		});
		$('.clsTime').each(function(){
			liAddTime += __str2num($(this).attr('value'));
		});
		$('.clsSupport').each(function(){
			liAddSupport += __str2num($(this).attr('value'));
		});
		$('.clsExpense').each(function(){
			liAddExpense += __str2num($(this).attr('value'));
		});

		$('#totalTot').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#totalTime').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#totalSupport').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#totalExpense').attr('value',liAddExpense).text(__num2str(liAddExpense));

		try{
			var liOverTime = __str2num($('#overTime').text());
			var liButTime  = __str2num($('#totalTime').text());
			var liMakeTime = liButTime - liOverTime;

			$('#txtOverTime').text(liOverTime);
			$('#txtMakeTime').text(liMakeTime);
			$('#txtBuyTime').text(liButTime);
		}catch(e){
		}
		
	}

	function lfTotAddPayNew(){
		var liAddTot     = 0;
		var liAddTime    = 0;
		var liAddSupport = 0;
		var liAddExpense = 0;

		$('.clsTotNew').each(function(){
			liAddTot += __str2num($(this).attr('value'));
		});
		$('.clsTimeNew').each(function(){
			liAddTime += __str2num($(this).attr('value'));
		});
		$('.clsSupportNew').each(function(){
			liAddSupport += __str2num($(this).attr('value'));
		});
		$('.clsExpenseNew').each(function(){
			liAddExpense += __str2num($(this).attr('value'));
		});

		$('#totalTot2').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#totalTime2').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#totalSupport2').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#totalExpense2').attr('value',liAddExpense).text(__num2str(liAddExpense));

		try{
			var liOverTime = __str2num($('#overTime2').text());
			var liButTime  = __str2num($('#totalTime2').text());
			var liMakeTime = liButTime - liOverTime;

			$('#txtOverTime2').text(liOverTime);
			$('#txtMakeTime2').text(liMakeTime);
			$('#txtBuyTime2').text(liButTime);
		}catch(e){
		}
		
	}

	function divViewType(obj){
	
		if(!obj){
			
			if('<?=$disVal;?>' == 1){
				$('#svcDiv_2').hide();
				$('#svcDiv_1').show();
			}else {
				$('#svcDiv_1').hide();
				$('#svcDiv_2').show();
			}
		}else {
			if(obj.value == '1'){
				$('#svcDiv_2').hide();
				$('#svcDiv_1').show();
				$('#disVals').val('1');
			}else {
				$('#svcDiv_1').hide();
				$('#svcDiv_2').show();
				$('#disVals').val('3');
			}
		}

		lfInitAddPay();
	}
</script>

<?
	unset($laDisOption);
?>