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

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				//document.write(data);

				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						if (col['gbn'] == '1'){
							var gbn = '공공';
						}else if (col['gbn'] == '2'){
							var gbn = '기업';
						}else if (col['gbn'] == '3'){
							var gbn = '단체';
						}else{
							var gbn = '개인';
						}

						var phone = __getPhoneNo(col['pertel']).split('-').join('.');

						html += '<tr style="cursor:default;" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['svcNm']+'</td>';
						html += '<td class="center">'+gbn+'</td>';
						html += '<td class="center">'+col['custNm']+'</td>';
						html += '<td class="center">'+col['pernm']+'</td>';
						html += '<td class="center">'+phone+'</td>';
						html += '<td class="center">'+__getDate(col['from'],'.')+'</td>';
						html += '<td class="center">'+__getDate(col['to'],'.')+'</td>';
						html += '<td class="last">';
						html += '<span class="btn_pack small"><button type="button" onclick="lfRegResource(\''+col['svcCd']+'\',\''+col['cd']+'\',\''+col['custCd']+'\');">수정</button></span> ';
						html += '<span class="btn_pack small"><button type="button" onclick="lfDelResource(\''+col['svcCd']+'\',\''+col['cd']+'\');">삭제</button></span>';
						html += '</td>';
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					 html = '<tr><td class="center last" colspan="9">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfRegResource(svc,cd,cust){
		var objModal = new Object();
		var url = './care_resource_reg.php';
		var style = 'dialogWidth:500px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.type	= '<?=$type;?>_POP';
		objModal.sr		= '<?=$sr;?>';
		objModal.svc	= (svc	? svc	: '');
		objModal.cd		= (cd	? cd	: '');
		objModal.cust	= (cust	? cust	: '');

		window.showModalDialog(url, objModal, style);

		if (objModal.result == 1){
			lfLoad();
		}
	}

	function lfDelResource(svc,cd){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곘습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':'<?=$type;?>_DELETE'
			,	'sr':'<?=$sr;?>'
			,	'svc':svc
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
	<div style="float:left;width:auto;">자원연결</div>
	<div style="float:right; width:auto; margin-top:10px;"><span class="btn_pack m"><button type="button" onclick="lfRegResource();">등록</button></span></div>
</div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="130px">
		<col width="50px">
		<col width="100px">
		<!--col width="70px"-->
		<col width="70px">
		<col width="90px">
		<col width="80px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">서비스</th>
			<th class="head">구분</th>
			<th class="head">명칭</th>
			<!--th class="head">단가</th-->
			<th class="head">담당자</th>
			<th class="head">연락처</th>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="" style="background-color:white; border:none;"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>