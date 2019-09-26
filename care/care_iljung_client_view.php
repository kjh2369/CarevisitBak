<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
?>
<script language='javascript'>
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		var title = __getDate(opener.date,'.')+' 대상자';

		$('#lblTitle').text(title);

		var h = $(this).height() - $('#divList').offset().top - 1;
		$('#divList').height(h);

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url	:'./care_iljung_client_view_list.php'
		,	data:{
				'SR'	:opener.SR
			,	'date'	:opener.date
			,	'time'	:opener.time
			,	'suga'	:opener.sugaCd
			,	'res'	:opener.resCd
			,	'mem'	:opener.memCd
			,	'rqst'	:opener.request
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);
			}
		,	error:function(){
				alert('test');
			}
		}).responseXML;
	}

	function lfRemoe(obj, para){
		if (!obj || !para) return;

		obj = __GetTagObject($(obj),'TR');
		para = __parseVal(para);

		$.ajax({
			type:'POST'
		,	url	:'./care_iljung_client_view_remove.php'
		,	data:{
				'SR'	:opener.SR
			,	'jumin'	:para['jumin']
			,	'date'	:opener.date
			,	'time'	:opener.time
			,	'seq'	:para['seq']
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					$(obj).remove();

					opener.result = true;
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
				alert('test');
			}
		}).responseXML;
	}

	//재가지원 업무내용등록
	function lfWorkLogReg(obj, para){
		var objModal = new Object();
		var url = '../care/care_works_log_reg.php';
		var style = 'dialogWidth:800px; dialogHeight:550px; dialogHide:yes; scroll:no; status:no';

		para = __parseVal(para);

		objModal.svcCd	= opener.SR;
		objModal.date	= opener.date.split('-').join('');
		objModal.time	= opener.time.split(':').join('');
		objModal.seq	= para['seq'];
		objModal.jumin	= para['jumin'];
		objModal.suga	= opener.sugaCd;
		objModal.resource = opener.resCd;
		objModal.mem	= opener.memCd;
		objModal.result = true;

		/*if ('<?=$debug;?>' == '1'){
			alert(objModal.svcCd
				+'\n'+objModal.date
				+'\n'+objModal.time
				+'\n'+objModal.seq
				+'\n'+objModal.jumin
				+'\n'+objModal.suga
				+'\n'+objModal.resource
				+'\n'+objModal.mem);
		}*/

		window.showModalDialog(url, objModal, style);

		if (objModal.worklog){
			$('#ID_WORKLOG',obj).text('일지');
			lfSearch();
		}else{
			$('#ID_WORKLOG',obj).text('');
		}
	}

	function lfWorkLogAllReg(){
		var objModal = new Object();
		var url = '../care/care_works_log_reg_all.php';
		var style = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';
		
	

		objModal.svcCd	= opener.SR;
		objModal.date	= opener.date.split('-').join('');
		objModal.time	= opener.time.split(':').join('');
		objModal.suga	= opener.sugaCd;
		objModal.resource = opener.resCd;
		objModal.mem	= opener.memCd;
		objModal.request = opener.request;
		objModal.result = true;

		window.showModalDialog(url, objModal, style);

		if (objModal.worklog){
			lfSearch();
		}
	}
</script>
<div class="title title_border">
	<div style="float:right; width:auto; margin-top:9px; margin-right:5px;">
		<span class="btn_pack m"><button onclick="lfWorkLogAllReg();">업무내용 일괄등록</button></span>
	</div>
	<div id="lblTitle" style="float:left; width:auto;"></div>
</div><?
$colgroup = '
	<col width="40px">
	<col width="80px">
	<col width="50px">
	<col width="90px">
	<col width="320px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">성별</th>
			<th class="head">생년월일</th>
			<th class="head">업무내용</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top bottom last" colspan="6">
				<div id="divList" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>