<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$today = Date('Y-m-d');
	$colgroup = '<col width="40px"><col width="80px"><col width="250px"><col width="30px"><col width="50px"><col width="180px"><col>';
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
		lfSearch();
	});

	function lfFindCenter(){
		var objModal = new Object();
		var url      = '../find/_find_center.php';
		var style    = 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '<?=$type;?>';
		objModal.code = '';
		objModal.name = '';

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;

		$('#lblCode').text(objModal.code);
		$('#lblName').text(objModal.name);
	}

	function lfAdd(aiIdx,aiSeq){
		var seq  = __str2num(aiSeq);
		var code = '';
		var from = '';
		var to   = '';
		var bank = '';

		if (seq > 0){
			code = $('#lblCode_'+aiIdx).text();
			from = $('#lblFrom_'+aiIdx).text().split('.').join('-');
			to   = $('#txtTo_'+aiIdx).val();
			bank = $('#cboBankCd_'+aiIdx).attr('checked') ? 'Y' : 'N';
		}else{
			if (!$('#lblCode').text()){
				lfFindCenter();
			}

			if (!$('#txtFrom').val()){
				alert('적용기간을 입력하여 주십시오.');
				$('#txtFrom').focus();
				return;
			}

			if (!$('#txtTo').val()){
				alert('적용기간을 입력하여 주십시오.');
				$('#txtTo').focus();
				return;
			}

			code = $('#lblCode').text();
			from = $('#txtFrom').val();
			to   = $('#txtTo').val();
			bank = $('#cboBankCd').val();
		}

		$.ajax({
			type :'POST'
		,	url  :'./add.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'code':code
			,	'seq':seq
			,	'from':from
			,	'to':to
			,	'bank':bank
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}

				if (result == 1){
					$('#lblCode').text('');
					$('#lblName').text('');
					$('#txtFrom').val('');
					$('#txtTo').val('');
				}

				$('#tempLodingBar').remove();

				lfSearch();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(aiIdx,aiSeq){
		if (!confirm('삭제후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./delete.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'code':$('#lblCode_'+aiIdx).text()
			,	'seq':aiSeq
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

	function lfSearch(aiPage){
		var html   = '';
		var page   = __str2num(aiPage);
		var maxCnt = 0;

		if (page < 1) page = 1;

		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'page':0
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				maxCnt = __str2num(result);

				if (maxCnt > 0){
					$.ajax({
						type :'POST'
					,	url  :'./search.php'
					,	data :{
							'mode':'<?=$type;?>'
						,	'page':page
						,	'max' :maxCnt
						}
					,	beforeSend:function(){
						}
					,	success:function(data){
							var list = data.split(String.fromCharCode(1));

							for(var i=0; i<list.length; i++){
								if (list[i]){
									var val = list[i].split(String.fromCharCode(2));

									html += '<tr id="rowId_'+i+'" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
										 +  '<td class="center">'+val[0]+'</td>'
										 +  '<td class="center"><div id="lblCode_'+i+'" class="left">'+val[1]+'</div></td>'
										 +  '<td class="center" colspan="2"><div class="left">'+val[2]+'</div></td>'
										 +  '<td class="left">'+val[6]+'</td>'
										 +  '<td class="left"><span id="lblFrom_'+i+'">'+val[3]+'</span> ~ ';

									if (val[8] == 'Y'){
										html += '<input id="txtTo_'+i+'" name="txt" type="text" value="'+val[4]+'" class="date"></td>'
											 +  '<td class="left last">'
											 +  '<span class="btn_pack m"><button type="button" onclick="lfAdd('+i+','+val[7]+');">수정</button></span>&nbsp;'
											 +  '<span class="btn_pack m"><button type="button" onclick="lfDelete('+i+','+val[7]+');">삭제</button></span>'
											 +  '</td>';
									}else{
										html += val[4].split('-').join('.')+'</td>'
											 +  '<td class="center">'+val[6]+'</td>'
											 +  '<td class="left last"></td>';
									}

									html += '</tr>';
								}
							}

							$('#list').html(html);
							$('#tempLodingBar').remove();
							__init_form(document.f);
							_lfSetPageList(maxCnt,page);
						}
					,	error:function(){
						}
					}).responseXML;
				}else{
					html += '<tr><td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td></tr>';
					$('span[id^="lblPage"]').hide();
					$('#list').html(html);
					$('#tempLodingBar').remove();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<div class="title title_border">은행업무 기관관리</div>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head" colspan="2">기관명</th>
			<th class="head">은행</th>
			<th class="head">적용기간</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="left"><span id="lblCode"></span></td>
			<td class="left"><span id="lblName"></span></td>
			<td class="center"><span class="btn_pack find" onclick="lfFindCenter();"></span></td>
			<td class="center"><?
				$sql = 'SELECT code
						,      name
						  FROM bank
						 WHERE use_yn = \'Y\'
						 ORDER BY name';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();?>
				<select id="cboBankCd" name="cbo" style="width:auto;"><?
				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);?>
					<option value="<?=$row['code'];?>"><?=$row['name'];?></option><?
				}?>
				</select><?
				$conn->row_free();?>
			</td>
			<td class="center"><input id="txtFrom" name="txt" type="text" value="<?=$today;?>" class="date"> ~ <input id="txtTo" name="txt" type="text" value="9999-12-31" class="date"></td>
			<td class="left last">
				<span class="btn_pack m"><button type="button" onclick="lfAdd();">추가</button></span>
			</td>
		</tr>
	</tbody>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="7"><? include_once('../inc/_page_script.php');?></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>