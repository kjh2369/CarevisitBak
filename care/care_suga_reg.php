<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	if ($debug) echo $_SESSION['userArea'];

	if (Date('Ymd') >= '20180116' &&  $_SESSION['userArea'] == '05' && $_GET['sr'] == 'S'){
		$IsSugaFixed = true;
	}else if ($_SESSION['userArea'] == '03' && $_GET['sr'] == 'S'){
		$IsSugaFixed = true;
	}else if ($_SESSION['userArea'] == '14' && $_GET['sr'] == 'S'){
		$IsSugaFixed = true;
	}else if ($_SESSION['userArea'] == '08' && $_GET['sr'] == 'S'){
		$IsSugaFixed = true;
	}else if ($_SESSION['userArea'] == '02' && $_GET['sr'] == 'S'){
		$IsSugaFixed = true;
	}else if ($_SESSION['userArea'] == '04' && $_GET['sr'] == 'S'){
		$IsSugaFixed = true;
	}else{
		$IsSugaFixed = false;
	}


	//$IsSugaFixed = false;
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 9;

		var title = '';

		switch(opener.type){
			case '1_POP':
				title = '수가조회';
				break;

			default:
				self.close();
				return;
		}

		if (opener.sr == 'S'){
			title += '(재가지원)';
		}else if (opener.sr == 'R'){
			title += '(자원연계)';
		}else{
			self.close();
			return;
		}

		__init_form(document.f);

		$('#lsTitle').text(title);

		setTimeout('lfResize()',10);
		setTimeout('lfLoadGbn("M")',100);
	});

	function lfResize(){
		var h = $(document).height();
		var t = $('#gbnM').offset().top;
		var height = h - t - 230;

		$('#gbnM').height(height);
		$('#gbnS').height(height);
		$('#gbnD').height(height);
		$('#gbnB').height(height);
	}

	function lfLoadGbn(gbn,obj){
		var objId = $(obj).attr('id');
		var objGbn = $(obj).attr('gbn');
		var cd1 = $(obj).attr('cd1');
		var cd2 = $(obj).attr('cd2');
		var cd3 = $(obj).attr('cd3');

		$('div',$('#gbn'+objGbn)).attr('sel','N').removeClass('bold');
		$('#'+objId).attr('sel','Y').addClass('bold');

		if (objGbn == 'B'){
			$('#selCd').attr('cd',objId).attr('nm',$('div[sel="Y"]',$('#gbnD')).text());
			$('#selNm').text($('div[sel="Y"]',$('#gbnM')).text()+'/'+$('div[sel="Y"]',$('#gbnS')).text()+'/'+$('div[sel="Y"]',$('#gbnD')).text());

			if ($('#txtSubNm').length > 0){
				$('#txtSubNm').val($('div[sel="Y"]',$('#gbnB')).text().substring(3));
				//$('#fromDt').attr('disabled',true);
				//$('#toDt').attr('disabled',true);
			}else if ($('#sub_name').length > 0){

				$('#sub_name').text($('div[sel="Y"]',$('#gbnB')).text().substring(3));
				//$('#from_dt').text(__getDate($(obj).attr('from_dt'), '.'));
				//$('#to_dt').text(__getDate($(obj).attr('to_dt'), '.'));
			}

			$('#fromDt').attr('disabled',true);
			$('#toDt').attr('disabled',true);

			$('#cost').attr('disabled',true);
			$('#chkReYn').attr('disabled',true);

			lfLoadSuga();
			return;
		}else {
			$('#sub_name').text('');
		}


		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type
			,	'sr':opener.sr
			,	'gbn':gbn
			,	'cd1':cd1
			,	'cd2':cd2
			,	'cd3':cd3
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(13));
				var no = 1;
				var html = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);
						var lsGbn = '', id = '', nm = '';

						if (gbn == 'M'){
							lsGbn = 'S';
							id = col['cd1'];
							nm = col['nm1'];
						}else if (gbn == 'S'){
							lsGbn = 'D';
							id = col['cd1']+col['cd2'];
							nm = col['nm2'];
						}else if (gbn == 'D'){
							lsGbn = 'B';
							id = col['cd1']+col['cd2']+col['cd3'];
							nm = col['nm3'];
						}else{
							id = col['cd1']+col['cd2']+col['cd3']+col['cd4'];
							nm = col['nm4'];
						}

						html += '<div';
						html += ' id="'+id+'"';
						html += ' gbn="'+gbn+'"';
						html += ' cd1="'+col['cd1']+'"';
						html += ' cd2="'+col['cd2']+'"';
						html += ' cd3="'+col['cd3']+'"';
						html += ' cd4="'+col['cd4']+'"';
						html += ' from_dt="'+col['from_dt']+'"';
						html += ' to_dt="'+col['to_dt']+'"';
						html += ' sel="N"';
						html += ' onclick="lfLoadGbn(\''+lsGbn+'\',this);"';
						html += ' class="left"';
						html += ' style="clear:both; width:100%; line-height:1.3em; padding-top:5px; padding-bottom:5px; border-bottom:1px solid #cccccc;"';
						html += ' onmouseover="this.style.backgroundColor=\'#efefef\';"';
						html += ' onmouseout="this.style.backgroundColor=\'#ffffff\';"';
						html += '>';
						html += '<div style="float:left; width:auto;">'+no+'.&nbsp;</div>';
						html += '<div style="float:left; width:auto;">'+nm+'</div>';
						html += '</div>';

						no ++;
					}
				}

				$('#gbn'+gbn).html(html);
				$('#tempLodingBar').remove();

				if (gbn == 'M'){
					if (cd1){
						$('div[cd1="'+cd1+'"]',$('#gbnM')).click();
					}else{
						$('div:first',$('#gbnM')).click();
					}
				}else if (gbn == 'S'){
					if (cd1 && cd2){
						$('div[cd1="'+cd1+'"][cd2="'+cd2+'"]',$('#gbnS')).click();
					}else{
						$('div:first',$('#gbnS')).click();
					}
				}else if (gbn == 'D'){
					if (cd1 && cd2 && cd3){
						$('div[cd1="'+cd1+'"][cd2="'+cd2+'"][cd3="'+cd3+'"]',$('#gbnD')).click();
					}else{
						$('div:first',$('#gbnD')).click();
					}
				}else{
					lfSubInit();

					$('div:first',$('#gbnB')).click();

					if('<?=$IsSugaFixed?>'){
						if($('div:first',$('#gbnB')).text()==''){
							$('#saveBtn').hide();
						}else {
							$('#saveBtn').show();
						}
					}

					return false;
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSubInit(){
		$('#selCd').attr('cd',$('div[sel="Y"]',$('#gbnD')).attr('cd1')+$('div[sel="Y"]',$('#gbnD')).attr('cd2')+$('div[sel="Y"]',$('#gbnD')).attr('cd3')).attr('nm','');
		$('#selNm').text($('div[sel="Y"]',$('#gbnM')).text()+'/'+$('div[sel="Y"]',$('#gbnS')).text()+'/'+$('div[sel="Y"]',$('#gbnD')).text());
		$('#txtSubNm').val($('div[sel="Y"]',$('#gbnD')).text().substring(3));
		$('#fromDt').attr('disabled',false).val('');
		$('#toDt').attr('disabled',false).val('');
		$('#cost').attr('disabled',false).val('0');
		$('#chkReYn').attr('checked',false);
		$('#tbodyList').html('');
		$('#txtSubNm').focus();
	}

	function lfLoadSuga(){
		var cd = $('#selCd').attr('cd');
		var html = '';

		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type+'_1'
			,	'cd':cd
			,	'sr':opener.sr
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(13));
				var first = true;
				var fromDt = '', toDt = '', cost = 0, sub = '', seq = '0';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr>';
						html += '<td class="center">'+col['seq']+'</td>';
						html += '<td class="center">'+col['from'].split('-').join('.')+'</td>';
						html += '<td class="center">'+col['to'].split('-').join('.')+'</td>';
						//html += '<td class="center"><div class="right">'+col['cost']+'</div></td>';

						if (first){
							fromDt	= col['from'];
							toDt	= col['to'];
							cost	= col['cost'];
							sub		= col['sub'];
							seq		= col['seq'];

							html += '<td class="left"><span class="btn_pack m"><button type="button" onclick="lfDelete(\''+cd+'\',\''+seq+'\',\''+fromDt+'\',\''+toDt+'\');">삭제</button></span></td>';
						}else{
							html += '<td class="center"></td>';
						}
						html += '</tr>';

						first = false;
					}
				}

				$('#tempLodingBar').remove();
				$('#tbodyList').html(html);

				$('#fromDt').val(fromDt).attr('value1',fromDt).attr('disabled',false);
				$('#toDt').val(toDt).attr('value1',toDt).attr('disabled',false);
				$('#cost').val(cost).attr('value1',cost).attr('disabled',false);
				$('#chkReYn').attr('checked',false).attr('disabled',false);
				$('#selCd').attr('seq',seq);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfApply(){
		var cd = $('#selCd').attr('cd');
		var nm = $('#selCd').attr('nm');
		var seq = $('#selCd').attr('seq');

		if (!cd){
			alert('수가를 선택하여 주십시오.');
			return false;
		}

		var sub_name = '', modifyFlag = 'N', from_dt = '', to_dt = '';

		if ($('#txtSubNm').length > 0){
			if (!$('#txtSubNm').val()){
				alert('적용수가명을 입력하여 주십시오.');
				$('#txtSubNm').focus();
				return false;
			}

			if ($('#fromDt').val() == ''){
				alert('적용일자를 입력하여 주십시오');
				$('#fromDt').focus();
				return false;
			}

			if ($('#toDt').val() == ''){
				alert('적용일자를 입력하여 주십시오');
				$('#toDt').focus();
				return false;
			}

			sub_name = $('#txtSubNm').val();
			modifyFlag = $('#txtSubNm').attr('modifyFlag');
			from_dt = $('#fromDt').val();
			to_dt = $('#toDt').val();
		}else if ($('#sub_name').length > 0){
			sub_name = $('#sub_name').text();
			//from_dt = $('#from_dt').text();
			//to_dt = $('#to_dt').text();
			from_dt = $('#fromDt').val();
			to_dt = $('#toDt').val();
		}else{
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':opener.type
			,	'sr':opener.sr
			,	'cd':cd
			,	'nm':sub_name //$('#txtSubNm').val()
			,	'seq':seq
			,	'from':from_dt //$('#fromDt').val()
			,	'to':to_dt //$('#toDt').val()
			,	'cost':__str2num($('#cost').val())
			,	'modifyName':modifyFlag //$('#txtSubNm').attr('modifyFlag')
			,	'reYn':($('#chkReYn').attr('checked') ? 'Y' : 'N')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result){
					alert(result);
					return;
				}

				opener.result = 1;

				/*if ($('#txtSubNm').attr('modifyFlag') == 'Y'){
					lfLoadGbn('D',$('div[sel="Y"]',$('#gbnD')));
				}else{
					//lfLoadSuga();
					lfLoadGbn('D',$('div[sel="Y"]',$('#gbnD')));
				}*/
				lfLoadGbn('D',$('div[sel="Y"]',$('#gbnD')));
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfChkDt(){
		var fromDt = $('#fromDt').val();
		var toDt = $('#toDt').val();
		var cost = $('#cost').val();
		var reYn = $('#chkReYn').attr('checked') ? 'Y' : 'N';

		if (reYn == 'Y'){
			try{
				var newDt = __addDate('d', 1, toDt);

				$('#fromDt').val(newDt);
				$('#toDt').val('');
			}catch(e){
				alert('재등록 처리할 수 없습니다.');

				fromDt = $('#fromDt').attr('value1');
				toDt = $('#toDt').attr('value1');
				cost = $('#cost').attr('value1');

				$('#fromDt').val(fromDt);
				$('#toDt').val(toDt);
				$('#cost').val(cost);
				$('#chkReYn').attr('checked',false);
			}
		}else{
			fromDt = $('#fromDt').attr('value1');
			toDt = $('#toDt').attr('value1');
			cost = $('#cost').attr('value1');

			$('#fromDt').val(fromDt);
			$('#toDt').val(toDt);
			$('#cost').val(cost);
			$('#chkReYn').attr('checked',false);
		}
	}

	function lfDelete(cd,seq,fmDt,toDt){
		if (!confirm('삭제된 데이타는 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':opener.type+'_DELETE'
			,	'sr':opener.sr
			,	'cd':cd
			,	'seq':seq
			,	'fmDt':fmDt
			,	'toDt':toDt
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 'ERROR'){
					alert('삭제 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
					return;
				}else if (result){
					alert('현재 서비스로 등록된 일정이 '+result+'건이 있어서 서비스를 삭제할 수 없습니다.\n확인하여 주십시오.');
					return;
				}

				opener.result = 1;

				//lfLoadSuga();
				lfLoadGbn('D',$('div[sel="Y"]',$('#gbnD')));
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<form id="f" name="f" method="post">
<div id="lsTitle" class="title title_border"></div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="140px">
		<col width="230px">
		<col width="220px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류(사업)</th>
			<th class="head">중분류(프로그램)</th>
			<th class="head">소분류(서비스)</th>
			<th class="head">상세서비스내역</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">
				<div id="gbnM" style="overflow-y:auto;width:100%;height:100px;"></div>
			</td>
			<td class="center">
				<div id="gbnS" style="overflow-y:auto;width:100%;height:100px;"></div>
			</td>
			<td class="center">
				<div id="gbnD" style="overflow-y:auto;width:100%;height:100px;"></div>
			</td>
			<td class="center">
				<div id="gbnB" style="overflow-y:auto;width:100%;height:100px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="180px">
		<!--
		<col width="40px">
		<col width="70px">
		-->
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold">서비스명</th>
			<td class="left bold" colspan="5">
				<div id="selCd" cd="" nm="" seq="0" style="display:none;"></div>
				<div id="selNm"></div>
			</td>
		</tr>
		<tr>
			<th class="left bold">상세서비스</th>
			<td class="bold" colspan="4"><?
				if ($IsSugaFixed){
					echo '<span id="sub_name" style="padding-left:5px;"></span>';
				}else{?>
					<input id="txtSubNm" name="txt" type="text" value="" style="width:100%;" modifyFlag="N" onchange="$(this).attr('modifyFlag','Y');"><?
				}?>
			</td>
			<td class="left"><?
				if (!$IsSugaFixed){?>
					<span class="btn_pack m"><button type="button" onclick="lfSubInit();">상세서비스 추가</button></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="left bold">적용기간</th>
			<td class="bold">
				<input id="fromDt" name="dt" type="text" value="" value1="" class="date" disabled="true"> ~
				<input id="toDt" name="dt" type="text" value="" value1="" class="date" disabled="true">
			</td>
			<!--
			<th class="left bold">수가</th>
			<td class="bold">
				<input id="cost" name="cost" type="text" value="0" value1="0" class="number" style="width:100%;" disabled="true">
			</td>
			-->
			<td class="bold">
				<input id="chkReYn" name="chk" type="checkbox" class="checkbox" value="Y" disabled="true" onclick="lfChkDt();"><label for="chkReYn">재등록</label>
			</td>
			<td class="left" style="padding-top:1px;" colspan="3">
				<span class="btn_pack m" id="saveBtn"><button type="button" onclick="lfApply();">저장</button></span>
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span><?
				if ($IsSugaFixed){?>
					<span class="btn_pack m"><button type="button" onclick="document.f.submit();">ReLoad</button></span><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<!--col width="70px"-->
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<!--th class="head">수가</th-->
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center" colspan="10">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
</table>
</form>

<?
	include_once('../inc/_footer.php');
?>