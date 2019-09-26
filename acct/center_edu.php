<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$today = Date('Y-m-d');
	$colgroup = '<col width="40px"><col width="80px"><col width="250px"><col width="30px"><col width="50px"><col width="50px"><col width="200px"><col>';
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

	function lfAdd(){
		var code  = '';
		var date  = '';
		var amt   = '';
		var other = '';

		if (!$('#lblCode').text()){
			lfFindCenter();
		}

		if (!$('#txtDate').val()){
			alert('일자를 입력하여 주십시오.');
			$('#txtDate').focus();
			return;
		}

		code  = $('#lblCode').text();
		date  = $('#txtDate').val();
		amt   = __str2num($('#txtAmt').val());
		other = $('#txtOther').val();

		$.ajax({
			type :'POST'
		,	url  :'./add.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'code':code
			,	'date':date
			,	'amt':amt
			,	'other':other
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
					$('#txtDate').val('');
					$('#txtAmt').val('0');
				}

				$('#tempLodingBar').remove();

				lfSearch();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(obj){
		if (!confirm('삭제후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./delete.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'code':$('td',obj).eq(1).text()
			,	'date':$('td',obj).eq(3).text().split('.').join('-')
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

									html += '<tr onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
										 +  '<td class="center">'+val[0]+'</td>'
										 +  '<td class="left">'+val[1]+'</td>'
										 +  '<td class="left" colspan="2">'+val[2]+'</td>'
										 +  '<td class="center">'+val[3]+'</td>'
										 +  '<td class="right">'+__num2str(val[4])+'</td>'
										 +  '<td class="left">'+val[5]+'</td>'
										 +  '<td class="left last">'
											+ '<span class="btn_pack m"><button type="button" onclick="lfDelete($(this).parent().parent().parent());">삭제</button></span>'
										 +  '</td>'
										 +  '</tr>';
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
			<th class="head">일자</th>
			<th class="head">금액</th>
			<th class="head last" colspan="2">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="left"><span id="lblCode"></span></td>
			<td class="left"><span id="lblName"></span></td>
			<td class="center"><span class="btn_pack find" onclick="lfFindCenter();"></span></td>
			<td class="center"><input id="txtDate" name="txt" type="text" value="<?=$today;?>" class="date"></td>
			<td class="center"><input id="txtAmt" name="txt" type="text" value="0" class="number" style="width:70px;"></td>
			<td class="center"><input id="txtOther" name="txt" type="text" value="" style="width:100%;"></td>
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