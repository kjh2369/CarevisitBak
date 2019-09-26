<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$colgroup = '<col width="40px"><col width="80px"><col width="200px"><col width="70px"><col width="310px"><col>';
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		__init_form(document.f);

		switch(opener.mode){
			
			case '99':
				$('#lblTitle').text('의료기관 찾기');
				break;

			default:
				self.close();
				return false;
		}

		lfResize();
		lfSearch(1);
	});

	function lfResize(){
		var h = $(this).height();
		var t = $('#list').offset().top;

		h = h - t - 27;

		$('#list').height(h);
	}

	function lfSearch(aiPage){
		var html   = '<table class="my_table" style="width:100%;"><colgroup><?=$colgroup;?></colgroup><tbody>';
		var page   = __str2num(aiPage);
		var maxCnt = 0;

		if (page < 1) page = 1;

		$.ajax({
			type :'POST'
		,	url  :'./_find_medi_center_search.php'
		,	data :{
				'page':0
			,	'code':$('#txtCode').val()
			,	'name':$('#txtName').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				maxCnt = __str2num(result);

				if (maxCnt > 0){
					$.ajax({
						type :'POST'
					,	async:false
					,	url  :'./_find_medi_center_search.php'
					,	data :{
							'mode':opener.mode
						,	'page':page
						,	'max' :maxCnt
						,	'code':$('#txtCode').val()
						,	'name':$('#txtName').val()
						}
					,	beforeSend:function(){
						}
					,	success:function(data){
							var list = data.split(String.fromCharCode(1));

							for(var i=0; i<list.length; i++){
								if (list[i]){
									var val = list[i].split(String.fromCharCode(2));

									html += '<tr id="rowId_'+i+'">'
										 +  '<td class="center">'+val[0]+'</td>'
										 +  '<td class="center"><div class="left nowrap" style="width:80px;">'+val[1]+'</div></td>'
										 +  '<td class="center"><div class="left">'+val[2]+'</div></td>'
										 +  '<td class="center"><div class="left">'+val[3]+'</div></td>'
										 +  '<td class="center"><div class="left">'+val[4]+'</div></td>'
										 +  '<td class="center last"><a href="#" onclick="lfSelect('+i+');">선택</a></td>'
										 +  '</tr>';
								}
							}

							html += '</tbody></table>';

							_lfSetPageList(maxCnt,page);

							$('span[id^="lblPage"]').hide();
							$('#list').html(html);
						}
					,	error:function(){
						}
					}).responseXML;

					_lfSetPageList(maxCnt,page);

					$('#tempLodingBar').remove();
				}else{
					html += '<tr><td class="center last" colspan="6">::검색된 데이타가 없습니다.::</td></tr>';
					html += '</tbody></table>';

					$('span[id^="lblPage"]').hide();
					$('#list').html(html);
					$('#tempLodingBar').remove();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSelect(aiIdx){
		var obj = $('#rowId_'+aiIdx);

		opener.code = $('td', obj).eq(1).text();
		opener.name = $('td', obj).eq(2).text();

		self.close();
	}
</script>

<base target="_self">
<form name="f">
<div id="lblTitle" class="title title_border">&nbsp;</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="100px">
		<col width="50px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td class="center"><input id="txtCode" name="txt" type="text" style="width:100%;"></td>
			<th class="center">기관명</th>
			<td class="center"><input id="txtName" name="txt" type="text" style="width:100%;"></td>
			<td class="left last"><span class="btn_pack m"><button type="button" onclick="lfSearch(1);">조회</button></span></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">주소</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top center last" colspan="6">
				<div id="list" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center last" colspan="6"><? include_once('../inc/_page_script.php');?></td>
		</tr>
	</tfoot>
</table>
</form>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	include_once('../inc/_footer.php');
?>