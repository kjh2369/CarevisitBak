<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//타이틀
	switch($type){
		case 'DAN_LIST':
			$title = '주야간보호 기관관리';
			$typeVal = '5';
			break;

		case 'WMD_LIST':
			$title = '복지용구 기관관리';
			$typeVal = '7';
			break;

		default:
			include('../inc/_http_home.php');
			exit;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#type').val('<?=$typeVal;?>');

		lfSearch();
	});

	function lfSearch(page){
		page = __str2num(page);

		if (page < 1) page = 1;

		$.ajax({
			type:'POST'
		,	url	:'./sub_svc_search.php'
		,	data:{
				'page':page
			,	'type':$('#type').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
				$('#page').val(page);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfReg(code,svcCd,seq){
		var objModal= new Object();
		var url		= './sub_svc_reg.php';
		var style	= 'dialogWidth:370px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.code	= (code ? code : '');
		objModal.svcCd	= (svcCd ? svcCd : $('#type').val());
		objModal.seq	= (seq ? seq : '');

		window.showModalDialog(url, objModal, style);

		if (objModal.result){
			/*
			var html	= '';
			var svcNm	= '';

			switch(objModal.svcCd){
				case '5':
					svcNm = '주야간보하';
					break;
			}

			html += '<tr>'
				 +	'<td class="center">-</td>'
				 +	'<td class="center">'+objModal.code+'</td>'
				 +	'<td class="center">'+objModal.name+'</td>'
				 +	'<td class="center">'+svcNm+'</td>'
				 +	'<td class="center">'+__getDate(objModal.fromDt,'.')+' ~ '+__getDate(objModal.toDt,'.')+'</td>'
				 +	'<td class="center">'+objModal.acctYn+'</td>'
				 +	'<td class="center last">'
				 +	' <span class="btn_pack small"><button onclick="lfReg(\''+objModal.code+'\',\''+objModal.svcCd+'\',\''+objModal.seq+'\');">수정</button></span>'
				 +	' <span class="btn_pack small"><button>삭제</button></span>'
				 +	'</td>'
				 +	'</tr>';

			if ($('tr',$('#tbodyList')).length > 0){
				$('tr:first',$('#tbodyList')).before(html);
			}else{
				$('#tbodyList').html(html);
			}
			*/
			lfSearch($('#page').val());
		}
	}

	function lfDelete(code,svcCd,seq){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url	:'./sub_svc_delete.php'
		,	data:{
				'code'	:code
			,	'svcCd'	:svcCd
			,	'seq'	:seq
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfSearch($('#page').val());
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;"><?=$title;?></div>
	<div style="float:right; width:auto; margin-top:10px;">
		<span class="btn_pack m"><button onclick="lfReg();">등록</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="200px">
		<col width="100px">
		<col width="140px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head">기관명</th>
			<th class="head">서비스</th>
			<th class="head">적용기간</th>
			<th class="head">과금</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="7"><? include_once('../inc/_page_script.php');?></td>
		</tr>
	</tfoot>
</table>
<input id="page" type="hidden">
<input id="type" type="hidden">
<?
	include_once('../inc/_db_close.php');
?>