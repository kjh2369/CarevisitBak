<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year  = Date('Y');
	$month = IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#chkAll').unbind('click').bind('click',function(){
			$('input:checkbox[id="chk"]').attr('checked',$(this).attr('checked'));
			lfSetSum();
		});

		lfSetMonth();
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'year':$('#lblYear').text()
			,	'month':$('#txtMonth').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var html = '';
				var row  = data.split(String.fromCharCode(3));
				var list = row[0].split(String.fromCharCode(1));

				if (row[1] == 'Y'){
					$('#btnMakeCharge').attr('disabled',true);
					$('#btnDelete').attr('disabled',false);
				}else{
					$('#btnMakeCharge').attr('disabled',false);
					$('#btnDelete').attr('disabled',true);
				}

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						var overCost = 0;
						var totCost = 0;

						val[3] = __str2num(val[3]);
						val[4] = __str2num(val[4]);
						val[5] = __str2num(val[5]);
						val[6] = __str2num(val[6]);

						if (val[2] != 'Y' && val[5] > val[4]){
							overCost = (val[5] - val[4]) * val[6];
						}

						totCost = val[3] + overCost;

						if (row[1] == 'N'){
							var lsNo = (totCost > 0 ? '<input id="chk" name="cbo" type="checkbox" value="'+i+'" class="checkbox" onclick="lfSetSum();">' : '-');
						}else{
							var lsNo = i + 1;
						}

						var other = '';

						if (val[7] == 'Y'){
						}else if (val[7] == 'N'){
							other = '<span style="color:red;">미계약</span>';
						}else{
							other = '<span style="color:blue;">계산누락</span>';
						}

						html += '<tr class="clsRow" contYn="'+val[7]+'">'
							 +  '<td class="center">'+lsNo+'</td>'
							 +  '<td class="left" id="lblCode">'+val[0]+'</td>'
							 +  '<td class="left">'+val[1]+'</td>'
							 +  '<td class="center" id="lblHold" style="color:'+(val[2] == 'N' ? 'blue' : 'red')+'">'+val[2]+'</td>'
							 +  '<td class="right" id="lblBasic">'+__num2str(val[3])+'</td>'
							 +  '<td class="right" id="lblLimit">'+__num2str(val[4])+'</td>'
							 +  '<td class="right" id="lblClient">'+__num2str(val[5])+'</td>'
							 +  '<td class="right" id="lblOver" cost="'+val[6]+'">'+__num2str(overCost)+'</td>'
							 +  '<td class="right bold" id="lblTot">'+__num2str(totCost)+'</td>'
							 +  '<td class="left last">'+other+'</td>'
							 +  '</tr>';
					}
				}

				if (!html){
					html = '<tr><td class="center last" colspan="9">::검색된 데이타가 없습니다.::</td></tr>';
					$('#sum').hide();
				}else{
					$('#sum').show();
				}

				$('#list').html(html);
				$('#tempLodingBar').remove();

				lfSetSum();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetSum(){
		var liBasic = 0;
		var liOver = 0;
		var liTot = 0;

		$('.clsRow').each(function(){
			if ($('#chk',this).attr('checked')){
				liBasic  += __str2num($('#lblBasic',this).text());
				liOver   += __str2num($('#lblOver',this).text());
				liTot    += __str2num($('#lblTot',this).text());
			}
		});

		$('#lblTotBasic').text(__num2str(liBasic));
		$('#lblTotOver').text(__num2str(liOver));
		$('#lblTotTotal').text(__num2str(liTot));
		$('#lblCenterCnt').text(__num2str($('.clsRow').length));
	}

	function lfApplyCharge(){
		var data = '';

		$('.clsRow').each(function(){
			var code      = $('#lblCode',this).text();
			var hold      = $('#lblHold',this).text();
			var basicAmt  = __str2num($('#lblBasic',this).text());
			var overAmt   = __str2num($('#lblOver',this).text());
			var totAmt    = __str2num($('#lblTot',this).text());
			var limitCnt  = __str2num($('#lblLimit',this).text());
			var clientCnt = __str2num($('#lblClient',this).text());
			var clientCost= __str2num($('#lblOver',this).attr('cost'));
			var contYn    = $(this).attr('contYn');


			if (!$('#chk',this).attr('checked')){
				basicAmt = 0;
				overAmt  = 0;
				totAmt   = 0;
			}

			data += code+String.fromCharCode(2)
				 +  hold+String.fromCharCode(2)
				 +  basicAmt+String.fromCharCode(2)
				 +  overAmt+String.fromCharCode(2)
				 +  totAmt+String.fromCharCode(2)
				 +  clientCnt+String.fromCharCode(2)
				 +  limitCnt+String.fromCharCode(2)
				 +  clientCost+String.fromCharCode(2)
				 +  contYn+String.fromCharCode(1);
		});

		$.ajax({
			type :'POST'
		,	url  :'./apply.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'year':$('#lblYear').text()
			,	'month':$('#txtMonth').val()
			,	'data':data
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다.\n잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();

				if (result == 1){
					lfSearch();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(){
		if (!confirm('삭제후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./delete.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'year':$('#lblYear').text()
			,	'month':$('#txtMonth').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
				}else if (result == 9){
				}else{
					alert(result);
				}
				lfSearch();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<div class="title title_border">기관요금관리</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="460px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center">
				<div class="left" style="padding-top:2px;">
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
				<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left last"><? echo $myF->_btn_month($month, 'lfMoveMonth(', ');', null, false);?></td>
			<td class="right last">
				<span class="btn_pack m"><button id="btnMakeCharge" type="button" onclick="lfApplyCharge();">요금내역생성</button></span>
				<span class="btn_pack m"><button id="btnDelete" type="button" onclick="lfDelete();">요금내역삭제</button></span>
			</td>
		</tr>
	</tbody>
</table>
<input id="txtMonth" name="txt" type="hidden" value="<?=$month;?>">

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="200px">
		<col width="60px">
		<col width="60px">
		<col width="40px" span="2">
		<col width="60px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head"><input id="chkAll" name="cbo" type="checkbox" class="checkbox"></th>
			<th class="head">기관코드</th>
			<th class="head">기관명</th>
			<th class="head">정액여부</th>
			<th class="head">기본금</th>
			<th class="head">제한수</th>
			<th class="head">고객수</th>
			<th class="head">추가요금</th>
			<th class="head">요금합계</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
	<tbody id="sum">
		<tr>
			<th class="left bold bottom last" colspan="3">검색된 갯수 : <span id="lblCenterCnt">0</span></th>
			<th class="right bold bottom">합계</th>
			<th class="right bold bottom"><span id="lblTotBasic">0</span></th>
			<th class="right bold bottom"><span id="lblTotLimit"></span></th>
			<th class="right bold bottom"><span id="lblTotClient"></span></th>
			<th class="right bold bottom"><span id="lblTotOver">0</span></th>
			<th class="right bold bottom"><span id="lblTotTotal">0</span></th>
			<th class="head bold bottom last"></th>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>