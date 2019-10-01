<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoad()',200);
	});

	//조회
	function lfLoad(){
		var html = '';

		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sp':'<?=$sp;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						//구분
						if (col['gbn'] == '1'){
							col['gbn'] = '공공';
						}else if (col['gbn'] == '2'){
							col['gbn'] = '기업';
						}else if (col['gbn'] == '3'){
							col['gbn'] = '단체';
						}else if (col['gbn'] == '4'){
							col['gbn'] = '개인';
						}

						//분류
						if (col['kindS'] == 'Y' && col['kindW'] == 'Y'){
							col['kind'] = '후+봉';
						}else if (col['kindS'] == 'Y'){
							col['kind'] = '후원자';
						}else if (col['kindW'] == 'Y'){
							col['kind'] = '봉사자';
						}else{
							col['kind'] = '';
						}

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['nm']+'</td>';
						html += '<td class="center">'+col['gbn']+'</td>';
						html += '<td class="center">'+col['kind']+'</td>';
						html += '<td class="center">'+col['manager']+'</td>';
						html += '<td class="center">'+col['phone']+'</td>';
						//html += '<td class="center">'+col['fax']+'</td>';
						html += '<td class="center">'+col['addr']+'</td>';
						html += '<td class="center">'+col['per']+'</td>';
						html += '<td class="center">'+col['support']+'</td>';
						html += '<td class="center">'+col['resource']+'</td>';
						html += '<td class="left last">';
						html += '<span class="btn_pack m"><button type="button" onclick="lfModify(\''+col['cd']+'\');">변경</button></span> ';
						html += '<span class="btn_pack m"><button type="button" onclick="lfDelete(\''+col['cd']+'\');">삭제</button></span>';
						html += '</td>';
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

	//등록및변경
	function lfModify(cd){
		var objModal = new Object();
		var url = './care_cust_reg.php';
		var style = 'dialogWidth:500px; dialogHeight:550px; dialogHide:yes; scroll:no; status:no';

		if (!cd) cd = '';

		objModal.win = window;
		objModal.type = '<?=$type;?>';
		objModal.cd = cd;

		//window.showModalDialog(url, objModal, style);
		window.showModelessDialog(url, objModal, style);

		//if (objModal.result == 1){
		//	setTimeout('lfLoad()',200);
		//}
	}

	function lfDelete(cd){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곘습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':'<?=$type;?>_DELETE'
			,	'cd':cd
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					setTimeout('lfLoad()',100);
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">
	<div style="float:left;width:auto;">자원관리</div>
	<div style="float:right; width:auto; margin-top:10px;"><span class="btn_pack m"><button type="button" onclick="lfModify();">등록</button></span></div>
</div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="50px">
		<col width="70px">
		<col width="70px">
		<col width="90px">
		<col width="120px">
		<col width="70px">
		<col width="40px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">명칭</th>
			<th class="head">구분</th>
			<th class="head">분류</th>
			<th class="head">대표자명</th>
			<th class="head">연락처</th>
			<th class="head">주소</th>
			<th class="head">담당자</th>
			<th class="head">지원</th>
			<th class="head">자원</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
