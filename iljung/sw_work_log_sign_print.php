<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$fromDt  = $_POST['fromDt'];
	$toDt	 = $_POST['toDt'];
	$printDt = date('Y-m-d');

?>
<script type="text/javascript">

	$(document).ready(function(){
		$('input[init="Y"]').each(function(){
			__init_object(this);
		});
	});
	
	function lfFindWorker(){
		
		var width = 500;
		var height = 400;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './sw_mem_list.php';
		var win = window.open('about:blank', 'SW_MEM', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'year':$('#fromDt').val().substr(0,4)
			,	'month':$('#fromDt').val().substr(5,2)
			,	'result':'lfFindWorkerResult'
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', 'SW_MEM');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
	
	function lfFindWorkerResult(jumin,name,from,to){
		$('#divSW').attr('jumin',jumin).attr('from',from).attr('to',to).text(name);
	}
	
	function lfPrint(){		
		
		if (!$('#divSW').attr('jumin')){
			alert('사회복지사를 선택하여 주십시오.');
			return;
		}

		var para = 'root=iljung'
				 + '&dir=N'
				 + '&fileName=sw_work_log_sign'
				 + '&fileType=pdf'
				 + '&target=show.php'
				 + '&showForm=SW_WORK_LOG_SIGN'
				 + '&jumin='+$('#divSW').attr('jumin')
				 + '&fromDt='+$('#fromDt').val()
				 + '&toDt='+$('#toDt').val()
			     + '&printDt='+$('#printDt').val()
				 + '&param=';

		__printPDF(para);
	}


	function lfExcel(){
		var parm = new Array();
			parm = {
				'jumin'	:$('#divSW').attr('jumin')
			,	'fromDt':$('#fromDt').val()
			,	'toDt'	:$('#toDt').val()
			,	'&printDt':$('#printDt').val()
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		//form.setAttribute('target', '');
		form.setAttribute('method', 'post');

		form.setAttribute('action', './sw_work_log_sign_excel.php');
		
		document.body.appendChild(form);

		form.submit();
	}

</script>
<div class="title title_border">방문사회복지사 보고서(출력)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">출력기간</th>
			<td class="last">
				<input id='fromDt' name='fromDt' type='text' init="Y" value='<?=$fromDt;?>' class='date'> ~ <input id='toDt' name='toDt' type='text' init="Y" value='<?=$toDt;?>' class='date' style=" margin-right:10px;">
			</td>
		</tr>
		<tr>
			<th class="center">사회복지사</th>
			<td class="left last">
				<div style="float:left; width:auto; height:25px; margin-left:2px; margin-top:1px;"><span class="btn_pack find" onclick="lfFindWorker();" <?=$disabled;?>></span></div>
				<div id="divSW" jumin="" from="" to="" style="float:left; width:auto; line-height:25px;"></div>
			</td>
		</tr>
		<tr>
			<th class="center">출력일자</th>
			<td class="last">
				<input id='printDt' name='printDt' type='text' init="Y" value='<?=$printDt;?>' class='date'> 
			</td>
		</tr>
	</tbody>
</table>
<div class="center" style="height:28px; border-top:1px solid #CCCCCC;">
	<div style="margin-top:30px;">
		<span class="btn_pack m"><span class="pdf"></span><button id="btnExcel" type="button" onclick="lfPrint();">출력</button></span>
		<span class="btn_pack m"><span class="excel"></span><button id="btnExcel" type="button" onclick="lfExcel();">출력</button></span>
		<span class="btn_pack m"><button id="btnExcel" type="button" onclick="self.close();">닫기</button></span>
	</div>
</div>
<input id="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<?
	include_once('../inc/_footer.php');
?>