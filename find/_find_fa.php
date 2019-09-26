<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$colgroup = '<col width="40px"><col width="100px"><col width="40px"><col width="100px"><col width="40px"><col width="110px"><col width="390px"><col>';
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		__init_form(document.f);

		$('#lblTitle').text('수입/지출 항목');

		lfResize();
		lfSearch();
	});

	function lfResize(){
		var h = $(this).height();
		var t = $('#list').offset().top;

		h = h - t;

		$('#list').height(h);
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./_find_fa_search.php'
		,	data :{
				'gbn':opener.gbn
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var html = '<table class="my_table" style="width:100%;"><colgroup><?=$colgroup;?></colgroup><tbody>';
				var list = data.split(String.fromCharCode(1));

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						val[2] = __str2num(val[2]);
						val[5] = __str2num(val[5]);

						html += '<tr'
							 +  ' cd1="'+val[0]+'"'
						     +  ' nm1="'+val[1]+'"'
							 +  ' cd2="'+val[3]+'"'
						     +  ' nm2="'+val[4]+'"'
							 +  ' cd3="'+val[6]+'"'
						     +  ' nm3="'+val[7]+'"'
						     +  '>';

						if (val[2] > 0){
							html += '<td class="center top" rowspan="'+val[2]+'">'+val[0]+'</td>'
								 +  '<td class="center top" rowspan="'+val[2]+'"><div class="left">'+val[1]+'</div></td>';
						}

						if (val[5] > 0){
							html += '<td class="center top" rowspan="'+val[5]+'">'+val[3]+'</td>'
								 +  '<td class="center top" rowspan="'+val[5]+'"><div class="left">'+val[4]+'</div></td>';
						}

						html += '<td class="center top">'+val[6]+'</td>'
							 +  '<td class="center top"><div class="left">'+val[7]+'</div></td>'
							 +  '<td class="center"><div class="left">'+val[8]+'</div></td>'
							 +  '<td class="center last"><div class="left"><a href="#" onclick="lfSelect($(this).parent().parent().parent());">선택</a></div></td>'
							 +  '</tr>';
					}
				}

				html += '</tbody></table>';

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSelect(obj){
		opener.result = true;
		opener.cd1 = $(obj).attr('cd1');
		opener.nm1 = $(obj).attr('nm1');
		opener.cd2 = $(obj).attr('cd2');
		opener.nm2 = $(obj).attr('nm2');
		opener.cd3 = $(obj).attr('cd3');
		opener.nm3 = $(obj).attr('nm3');
		self.close();
	}
</script>

<base target="_self">
<form name="f">
<div id="lblTitle" class="title title_border">&nbsp;</div>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">코드</th>
			<th class="head">관</th>
			<th class="head">코드</th>
			<th class="head">항</th>
			<th class="head">코드</th>
			<th class="head">목</th>
			<th class="head">내역</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top center last" colspan="8">
				<div id="list" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
</form>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	include_once('../inc/_footer.php');
?>