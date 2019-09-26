<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	var opener = window.dialogArguments;

	$(document).ready(function(){
		var top = $('#divList').offset().top;
		var height = $(this).height();

		height = height - top - 3;

		$('#divList').height(height);

		setTimeout('lfSearch()',200);
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_normal_find_search.php'
		,	data :{
				'sr':opener.sr
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var html = '';
				var no = 1;
				var row = data.split(String.fromCharCode(11));

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center"><div style="margin-left:5px; text-align:left;">'+col['name']+'</div></td>';
						html += '<td class="center"><div style="margin-left:5px; text-align:left;">'+(col['jumin'].length == 13 ? col['jumin'].substring(0,6)+'-'+col['jumin'].substring(6,13) : col['jumin'])+'</div></td>';
						html += '<td class="center"><div class="nowrap" style="width:145px; margin-left:5px; text-align:left;">'+col['addr']+'</div></td>';
						html += '<td class="center"><div style="margin-left:5px; text-align:left;">'+__getPhoneNo(col['tel'])+'</div></td>';
						html += '<td class="center last"><div style="margin-left:5px; text-align:left;">';

						//if (col['jumin'].length == 13){
							html += '<span class="btn_pack small"><button type="button" onclick="lfSelected(\''+col['seq']+'\');">선택</button></span>';
						//}else{
						//	html += '<span style="color:red;">주민번호 오류</span>';
						//}

						html += '</div></td>';
						html += '</tr>';

						no ++;
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSelected(seq){
		$.ajax({
			type :'POST'
		,	url  :'./care_normal_find_data.php'
		,	data :{
				'sr':opener.sr
			,	'seq':seq
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				$('#tempLodingBar').remove();
				opener.data = data;
				self.close();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">일반접수자조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="95px">
		<col width="150px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자명</th>
			<th class="head">주민번호</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="10">
				<div id="divList" style="overflow-x:hidden; overflow-y:auto; width:100%; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="70px">
							<col width="95px">
							<col width="150px">
							<col width="90px">
							<col>
						</colgroup>
						<tbody id="tbodyList">
							<tr>
								<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
							</tr>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>