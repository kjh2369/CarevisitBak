<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 1;

		var title = '';

		switch(opener.type){
			case '1_POP':
				title = '수가조회';
				break;

			default:
				self.close();
				return;
		}

		__init_form(document.f);

		$('#lsTitle').text(title);

		setTimeout('lfResize()',10);
		setTimeout('lfLoadGbn("M")',50);
	});

	function lfResize(){
		var h = $(document).height();
		var t = $('#gbnM').offset().top;
		var height = h - t - 230;

		$('#gbnM').height(height);
		$('#gbnS').height(height);
		$('#gbnD').height(height);
	}

	function lfLoadGbn(gbn,obj){
		var objId = $(obj).attr('id');
		var objGbn = $(obj).attr('gbn');
		var cd1 = $(obj).attr('cd1');
		var cd2 = $(obj).attr('cd2');

		$('div',$('#gbn'+objGbn)).attr('sel','N').removeClass('bold');
		$('#'+objId).attr('sel','Y').addClass('bold');

		if (objGbn == 'D'){
			$('#selCd').attr('cd',objId).attr('nm',$('div[sel="Y"]',$('#gbnD')).text());
			$('#selNm').text($('div[sel="Y"]',$('#gbnM')).text()+'/'+$('div[sel="Y"]',$('#gbnS')).text()+'/'+$('div[sel="Y"]',$('#gbnD')).text());

			$('#fromDt').attr('disabled',true);
			$('#toDt').attr('disabled',true);
			$('#chkReYn').attr('disabled',true);

			lfLoadSuga();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type
			,	'gbn':gbn
			,	'cd1':cd1
			,	'cd2':cd2
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
						}else{
							id = col['cd1']+col['cd2']+col['cd3'];
							nm = col['nm3'];
						}

						html += '<div';
						html += ' id="'+id+'"';
						html += ' gbn="'+gbn+'"';
						html += ' cd1="'+col['cd1']+'"';
						html += ' cd2="'+col['cd2']+'"';
						html += ' cd3="'+col['cd3']+'"';
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
					$('div:first',$('#gbnM')).click();
				}else if (gbn == 'S'){
					$('div:first',$('#gbnS')).click();
				}
			}
		,	error:function(){
			}
		}).responseXML;
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
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(13));
				var first = true;
				var fromDt = '', toDt = '', seq = '0';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr>';
						html += '<td class="center">'+col['seq']+'</td>';
						html += '<td class="center">'+col['from'].split('-').join('.')+'</td>';
						html += '<td class="center">'+col['to'].split('-').join('.')+'</td>';

						if (first){
							fromDt = col['from'];
							toDt = col['to'];
							seq = col['seq'];
							html += '<td class="left"><span class="btn_pack m"><button type="button" onclick="">삭제</button></span></td>';
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

		if (!checkDate($('#fromDt').val())){
			alert('적용일자를 입력하여 주십시오');
			$('#fromDt').focus();
			return false;
		}

		if (!checkDate($('#toDt').val())){
			alert('적용일자를 입력하여 주십시오');
			$('#toDt').focus();
			return false;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':opener.type
			,	'cd':cd
			,	'seq':seq
			,	'from':$('#fromDt').val()
			,	'to':$('#toDt').val()
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

				lfLoadSuga();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfChkDt(){
		var fromDt = $('#fromDt').val();
		var toDt = $('#toDt').val();
		var reYn = $('#chkReYn').attr('checked') ? 'Y' : 'N';

		if (reYn == 'Y'){
			try{
				var newDt = addDate('d', 1, toDt);

				$('#fromDt').val(newDt);
				$('#toDt').val('');
			}catch(e){
				alert('재등록 처리할 수 없습니다.');

				fromDt = $('#fromDt').attr('value1');
				toDt = $('#toDt').attr('value1');

				$('#fromDt').val(fromDt);
				$('#toDt').val(toDt);
				$('#chkReYn').attr('checked',false);
			}
		}else{
			fromDt = $('#fromDt').attr('value1');
			toDt = $('#toDt').attr('value1');

			$('#fromDt').val(fromDt);
			$('#toDt').val(toDt);
			$('#chkReYn').attr('checked',false);
		}
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
			<th class="head">비고</th>
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
			<td class="center"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="180px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold">선택수가</th>
			<td class="left bold" colspan="3">
				<div id="selCd" cd="" nm="" seq="0" style="display:none;"></div>
				<div id="selNm"></div>
			</td>
		</tr>
		<tr>
			<th class="left bold">적용기간</th>
			<td class="bold">
				<input id="fromDt" name="dt" type="text" value="" value1="" class="date" disabled="true"> ~
				<input id="toDt" name="dt" type="text" value="" value1="" class="date" disabled="true">
			</td>
			<td class="bold">
				<input id="chkReYn" name="chk" type="checkbox" class="checkbox" value="Y" disabled="true" onclick="lfChkDt();"><label for="chkReYn">재등록</label>
			</td>
			<td class="left" style="padding-top:1px;">
				<span class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span>
				<span class="btn_pack m"><button type="button" onclick="">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
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