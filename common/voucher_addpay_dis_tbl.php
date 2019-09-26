<?
	include_once('../inc/_http_uri.php');

	$laRoot = explode('/',$_SERVER['PHP_SELF']);
	$lsRoot = $laRoot[1];

	if ($lsRoot == 'sugupja'){
		$lbOver = false;
		$liSido = intval($laDisOption['sido_time']);
		$liJach = intval($laDisOption['jach_time']);
	}else{
		$lbOver = true;
		$liSido = 0;
		$liJach = 0;
	}

	if ($lsRoot == 'sugupja'){?>
		<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-bottom:2px solid #0e69b0;"><?
	}else{?>
		<table id="loSvcDisTbl" class="my_table" style="width:100%; display:none;"><?
	}?>
	<colgroup>
		<col width='80px'>
		<col width='80px'>
		<col width='80px'>
		<col width='80px'><?
		if ($lsRoot != 'sugupja'){?>
			<col width='80px'><?
		}?>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class='head'>급여구분</th>
			<th class='head'>합계</th>
			<th class='head'>시간</th>
			<th class='head'>지원금액</th>
			<th class='head'>본인부담금</th><?
			if ($lsRoot != 'sugupja'){?>
				<th class='head'>비고</th><?
			}?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class='center'><div class="left">기본급여</div></th>
			<td class='center'><div id="stndTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><div id="stndTime" class="right clsTime" value="0">0</div></td>
			<td class='center'><div id="stndSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="stndExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<tr>
			<th class='center'><div id="addTxt" class="left">추가급여</div></th>
			<td class='center'><div id="addTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><div id="addTime" class="right clsTime" value="0">0</div></td>
			<td class='center'><div id="addSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="addExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<tr id="trAddSido" >
			<th class='center'><div class="left">시도비추가</div></th>
			<td class='center'><div id="sidoTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><input id="sidoTime" name='sidoTime' type='text' value='<?=number_format($liSido);?>' class='number readonly clsTime' style='width:100%; background-color:#f6f4d3;'></td>
			<td class='center'><div id="sidoSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="sidoExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<tr id="trAddJach" >
			<th class='center'><div class="left">자치비추가</div></th>
			<td class='center'><div id="jachTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><input id="jachTime" name='jachTime' type='text' value='<?=number_format($liJach);?>' class='number readonly clsTime' style='width:100%; background-color:#f6f4d3;'></td>
			<td class='center'><div id="jachSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="jachExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<?
			if ($lbOver){?>
				<tr>
					<th class='center'><div class="left">이월급여</div></th>
					<td class='center'><div id="overTot" class="right clsTot" value="0">0</div></td>
					<td class='center'><div id="overTime" class="right clsTime" value="0">0</div></td>
					<td class='center'><input id="overSupport" name="overSupport" type="text" value="0" class="number readonly clsSupport" style="width:100%;background-color:#f6f4d3;"> </td>
					<td class='center'><div id="overExpense" class="right clsExpense" value="0">0</div></td>
					<td class='center'></td>
				</tr><?
			}
		?>
		<tr>
			<th class='center'><div class="left">총이용합계</div></th>
			<td class='center'><div id="totalTot" class="right" value="0">0</div></td>
			<td class='center'><div id="totalTime" class="right" value="0">0</div></td>
			<td class='center'><div id="totalSupport" class="right" value="0">0</div></td>
			<td class='center'><div id="totalExpense" class="right" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
	</tbody>
</table>
<?
	$sql = 'select lvl_id as id
			,      lvl_rate as rate
			,      lvl_pay as pay
			,      lvl_from_dt as from_dt
			,      lvl_to_dt as to_dt
			  from income_lvl_self_pay
			 where lvl_kind = \'4\'
			   and lvl_gbn  = \'2\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	echo '<script type="text/javascript">
			var laExpense = new Array();';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($id != $row['id']){
			$id  = $row['id'];
			$idx = 0;

			echo 'laExpense['.$id.'] = new Array();';
		}

		echo 'laExpense['.$id.']['.$idx.'] = {"rate":'.$row['rate'].',"pay":'.$row['pay'].',"from":"'.$row['from_dt'].'","to":"'.$row['to_dt'].'"};';

		$idx ++;
	}

	echo '</script>';

	$conn->row_free();
?>

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
		
		if ('<?=$debug;?>' == '1'){
			//alert($('input:hidden[id="month"]').length+'/'+year+'/'+month);
		}
		if (year+month >= '201801'){
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

		$('input:radio[name="addPay1"]:checked').each(function(){
			lfCalAddPay(this,'add');
		});

		$('input:checkbox[name="addPay2"]:checked').each(function(){
			lfCalAddPay(this,'add');
		});
		
		lfSetAddPay('add');
		lfTotAddPay();
	
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
</script>