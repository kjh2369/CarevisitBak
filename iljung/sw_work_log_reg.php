<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$year = $_POST['year'];
	$month = $_POST['month'];
	$objId = $_POST['objId'];
	$IsWrk	= $_POST['IsWrk'];
	
	

	//주야간보호 여부
	if ($IsWrk == 'DAN'){
		$IsDanYn = 'Y';
	}else{
		$IsDanYn = 'N';
	}

	if ($year.$month == Date('Ym')){
		$date = Date('Y-m-d');
		$time = Date('H:i');
	}else{
		$date = $year.'-'.$month.'-01';p;
		$time = '10:00';
	}
	
	
	//수급자명
	$sql = 'SELECT	m03_name AS name, m03_key AS cd_key
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'0\'
			AND		m03_jumin = \''.$jumin.'\'';

	$row = $conn->get_array($sql);

	$name = $row['name'];
	$key = $ed->en64($row['cd_key']);

	Unset($row);

	//등급 및 유효기간
	$sql = 'SELECT	level
			,		from_dt
			,		to_dt
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		jumin = \''.$jumin.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'';

	$row = $conn->get_array($sql);
	
	/*
	if ($row['level']){
		$lvl = $row['level'].'등급';
	}else{
		$lvl = '일반';
	}
	*/

	$lvl = $myF->_lvlNm($row['level']);


	$from = str_replace('-','',$row['from_dt']);
	$to = str_replace('-','',$row['to_dt']);

	Unset($row);


	//서비스 적용기간
	$sql = 'SELECT	REPLACE(from_dt,\'-\',\'\') AS from_dt
			,		REPLACE(to_dt,\'-\',\'\') AS to_dt
			FROM	client_his_svc
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		jumin  = \''.$jumin.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
			ORDER	BY from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['from_dt'] < $year.$month.'01'){
			$row['from_dt'] = $year.$month.'01';
		}

		if ($row['to_dt'] > $year.$month.$myF->lastday($year,$month)){
			$row['to_dt'] = $year.$month.$myF->lastday($year,$month);
		}

		$arrHisSvc[$i]['fromDt'] = $row['from_dt'];
		$arrHisSvc[$i]['toDt'] = $row['to_dt'];
	}

	$conn->row_free();


?>
<script type="text/javascript" src="../js/jquery.touchToScroll.js"	></script>
<script type="text/javascript">
	var timer = null;
	var timerCnt = 0;

	window.onunload = function(){
		try{
			$('#<?=$objId;?>', opener.document).text($('tr',$('#tbodyWorkLog')).length);
		}catch(e){
		}
	};

	$(document).ready(function(){
		var top = 0; //$('#divWorkLog').offset().top;
		var height = $(this).height();
		var h = 0; //height - top - 3;

		//$('#divWorkLog').height(h);

		top = $('#divLogBody').offset().top;
		h = height - top - $('#divBtnBody').height() - 3;

		$('#divLogBody').height(h);


		var obj = __GetTagObject($('#ID_TBL_CARELIST'),'DIV');
		$(obj).height(__GetHeight($(obj)));

		if ('<?=$debug;?>' == '1'){
			$('#divLogBody').touchToScroll();
		}

		lfLogList();
		lfLoadCareList();
	});

	function lfFindWorker(){
		var width = 500;
		var height = 500;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './sw_mem_list.php';
		var win = window.open('about:blank', 'SW_MEM', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'year':$('#year').val()
			,	'month':$('#month').val()
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
		lfSetWorkerList();
	}

	function lfSetWorkerList(){
		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_reg_worker_info.php'
		,	data:{
				'jumin'	:$('#jumin').val()
			,	'memCd'	:$('#divSW').attr('jumin')
			,	'date'	:$('#txtDate').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				if (html){
					$('#ID_DIV_WORKER_LIST')
						.css('top',$('#ID_CELL_3_1').offset().top-3)
						.css('left',$('#ID_CELL_3_1').offset().left-3)
						.css('width',$('#ID_CELL_3_1').width()+2)
						.css('background-color','#D9E5FF')
						.html(html).show();

					lfSetWorkerChkTime();
				}else{
					lfTimerNothing();
					$('#ID_DIV_WORKER_LIST').html('').hide();
				}
			}
		});
	}

	function lfSetWorkerChkTime(){
		var from= __time2min($('#txtTime').val());
		var to	= __time2min($('#txtToTime').val());
		var err = false;

		//if (!to) to = from + 60;

		$('div[id^="ID_DIV_WK_"]').each(function(){
			if ($('#jumin').val() == $(this).attr('jumin')) return;

			var tmpFrom = __time2min($(this).attr('from'));
			var tmpTo	= __time2min($(this).attr('to'));

			$(this).attr('gbn','1');

			if (from && from >= tmpFrom && from <= tmpTo){
				err = true;
			}else if (to && to >= tmpFrom && to <= tmpTo){
				err = true;
			}

			if (err){
				$(this).attr('gbn','2');
				timerCnt = 1;
				setTimeout('lfSetTimer()',500);
				return false;
			}
		});

		if (err){
			//$('#ID_CELL_WK_LOG').text('방문일시가 다른 수급자와 중복됩니다. 확인하여 주십시오.');
			$('#ID_CELL_WK_LOG').text('※중복');
		}else{
			lfTimerNothing();
			return true;
		}
	}

	function lfSetTimer(){
		if (timerCnt == 0) return;
		if (timerCnt == 1){
			timerCnt = 2;
			$('div[id^="ID_DIV_WK_"][gbn="2"]').css('background-color','#FFD8D8');
		}else{
			timerCnt = 1;
			$('div[id^="ID_DIV_WK_"][gbn="2"]').css('background-color','#D9E5FF');
		}

		setTimeout('lfSetTimer()',500);
	}

	function lfTimerNothing(){
		$('div[id^="ID_DIV_WK_"]').css('background-color','#D9E5FF').attr('gbn','1');
		$('#ID_CELL_WK_LOG').text('');
		//clearInterval(timer);
		//timer = null;
		timerCnt = 0;
	}

	function lfLogList(seq){
		if (seq >= 0){
			var idx = seq;
		}else{
			var idx = -1;
		}

		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_list.php'
		,	data:{
				'jumin':$('#jumin').val()
			,	'year':$('#year').val()
			,	'month':$('#month').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyWorkLog').html(html);

				if ($('td:last', $('tr:last',$('#tbodyWorkLog'))).length > 0){
					if (idx < 0){
						$('td:last', $('tr:last',$('#tbodyWorkLog'))).click();
					}else{
						$('td:last', $('tr',$('#tbodyWorkLog')).eq(seq)).click();
					}
				}else{
					lfLogSel();
				}
			}
		});
	}

	function lfLogSel(yymm, seq, no){
		if (!yymm) yymm = '';
		if (!seq) seq = '';
		if (!no){
			var str = '신규';
		}else{
			var str = no+'회차';
		}
		
		//var date = getToday();
		//var time = getTime();
		
		
		var d = new Date();

		var mon = ((d.getMonth()+1) < 10 ? '0'+(d.getMonth()+1) : (d.getMonth()+1));
		var dt  = (d.getDate() < 10 ? '0'+d.getDate() : d.getDate());
		var hh  = (d.getHours() < 10 ? '0'+d.getHours() : d.getHours());
		var mm  = (d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes());
		

		var date = d.getFullYear()+'-'+mon+'-'+dt;
		var time = hh+':'+mm;
		
		
		var toTime = __min2time(__time2min(time) + 60);
		
		
		toTime = toTime.replace('H',':').replace('M','');

		var tmpTime = toTime.split(':');
		toTime = (tmpTime[0] < 10 ? '0'+tmpTime[0] : tmpTime[0])+':'+(tmpTime[1] < 10 ? '0'+tmpTime[1] : tmpTime[1]);
		
		
		var y = d.getFullYear();
		var m = d.getMonth()+1;
			m = (m < 10 ? '0' : '')+m;
		
		

		if (y != $('#year').val() || m != $('#month').val()){
			date = $('#year').val()+'-'+$('#month').val()+'-01';
			time = '10:00';
			toTime = '11:00';
		}
		
		
		$('#divNo').attr('seq',no-1).text(str);
		$('#txtDate').attr('org',date).val(date);
		$('#txtTime').attr('org',time).val(time);
		$('#txtToTime').attr('org',toTime).val(toTime);
		if(yymm) $('#yymm').val(yymm);
		$('#seq').val(seq);
		$('#divSW').attr('jumin','').attr('from','').attr('to','').text('');

		var strNo = '';

		no = __str2num(no);

		if (no > 0){
			if (no > 1){
				strNo = (no-1)+','+no+'회 출력';
			}else{
				if ($('tr', $('#tbodyWorkLog')).length > 1){
					strNo = no+',2회 출력';
				}else{
					strNo = '1회 출력';
				}
			}
			//$('#btnPdf1').attr('disabled',false);
			$('#btnPdf2').attr('disabled',false).text(strNo);
		}else{
			//$('#btnPdf1').attr('disabled',true);
			$('#btnPdf2').attr('disabled',true);
		}
		
		
		lfLogBody();
	}

	function lfLogBody(){
		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_reg_body.php'
		,	data:{
				'jumin':$('#jumin').val()
			,	'yymm':$('#yymm').val()
			,	'seq':$('#seq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divLogBody').html(html);
				//$('table:first',$('#divWorkMst')).show();

				$('input[init="Y"]').each(function(){
					__init_object(this);
				});

				$('textarea').each(function(){
					__init_object(this);
				});

				lfSetWorkerList();
			}
		});
	}

	function lfChkDate(){
		lfSetWorkerList();
		return true;
		/*
		var dt = $('#txtDate').val().split('-').join('');
		var from = $('#from').val();
		var to = $('#to').val();

		if (dt < from || dt > to){
			alert('업무일자를 '+__getDate(from,'.')+'~'+__getDate(to,'.')+'사이의 일자로 입력하여 주십시오.');
			$('#txtDate').focus().val('');
			return false;
		}
		*/
	}

	function lfSave(){
		if (!$('#divSW').attr('jumin')){
			alert('사회복지사를 선택하여 주십시오.');
			return;
		}

		var dt = $('#txtDate').val().split('-').join('');
		var IsChk = false;

		$('span[id^="lblFrom_"]').each(function(){
			var i = $(this).attr('id').split('lblFrom_').join('');
			var fromDt = $('#lblFrom_'+i).text().split('.').join('');
			var toDt = $('#lblTo_'+i).text().split('.').join('');

			if (dt >= fromDt && dt <= toDt){
				IsChk = true;
				return false;
			}
		});

		
		if('<?=$gDomain?>' == 'dolvoin.net'){
			//2017-02-01 돌보인 요청으로 인해 제외
		}else {
			if (!IsChk){
				alert('서비스기간을 확인 후 입력하여 주십시오.');
				$('#txtDate').focus();
				return;
			}
			
			if (!$('#txtDate').val()){
				alert('업무일자를 입력하여 주십시오.');
				$('#txtDate').focus();
				return;
			}
		
			if (!$('#txtTime').val()){
				alert('업무시간을 입력하여 주십시오.');
				$('#txtTime').focus();
				return;
			}
		}

		if (!lfSetWorkerChkTime()) return;

		var data = {};

		data['memCd'] = $('#divSW').attr('jumin');
		data['memNm'] = $('#divSW').text();
		data['danYn'] = '<?=$IsDanYn;?>';
		
		
		$('input[type="hidden"]').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});

		$('input[type="text"]').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});

		$('input[type="radio"]').each(function(){
			var id = $(this).attr('name');

			if ($(this).attr('checked')){
				data[id] = $(this).val();
			}
		});

		$('select').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});

		$('textarea').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});
		
		
		/*
		data['memList'] = '';
		$('div[id^="divMem"]').each(function(){
			if (this){
				data['memList'] += ('/'+$(this).attr('jumin')+'|'+$('span:first',this).text());
			}
		});
		*/

		/*
		if (!data['memList']){
			alert('등록된 요양보호사가 없습니다. 확인하여 주십시오.');
			return;
		}
		*/

		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_reg_save.php'
		,	data:data
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					lfSetWorkerList();
					lfLogList($('#divNo').attr('seq'));
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function lfDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		var data = {};

		$('input[type="hidden"]').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});

		if (!data['jumin'] || !data['yymm'] || !data['seq']){
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_delete.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfLogList();
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function lfCopy(){
		if (!confirm('이전에 입력된 내용으로 사용하시겠습니까?')) return;

		var seq = $('#divNo').attr('seq');
		var date = $('#txtDate').attr('org').split('-').join('');
		var time = $('#txtTime').attr('org').split(':').join('');
		var toTime = $('#txtToTime').attr('org').split(':').join('');
		var yymm = date.substring(0,6);

		if (!toTime) toTime = '';
		
		/*
		if (isNaN(seq)){
			date = '';
			time = '';
		}
		*/

		if (!date) date = '';
		if (!time) time = '';

		var data = {};
		
		data['date'] = date;
		data['time'] = time;
		data['toTime'] = toTime;

		$('input[type="hidden"]').each(function(){
			var id = $(this).attr('id');
			data[id] = $(this).val();
		});

		data['yymm'] = yymm;
		data['seq'] = 0;

		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_reg_body.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divLogBody').html(html);
				//$('table:first',$('#divWorkMst')).show();

				$('input[init="Y"]').each(function(){
					__init_object(this);
				});

				$('textarea').each(function(){
					__init_object(this);
				});
			}
		});
	}

	function lfMemFind(){
		var ynFamily = 'N';

		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url    = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win    = window.open('about:blank', 'FIND_MEMBER', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type':'member'
			,	'ynFamily':ynFamily
			,	'year':$('#year').val()
			,	'month':$('#month').val()
			,	'return':'lfFindMemResult'
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

		form.setAttribute('target', 'FIND_MEMBER');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfMemFindResult(obj){
		var val = __parseVal(obj);
		var html = '';
		var obj = $('div[id^="divMem"]:last');

		try{
			var idx = __str2num($(obj).attr('id').replace('divMem',''))+1;
		}catch(e){
			var idx = 0;
		}

		html = '<div id="divMem'+idx+'" jumin="'+val['jumin']+'" style="float:left; width:90px;">'
			 + '<span>'+val['name']+'</span>'
			 + '<span style="color:RED; cursor:pointer;" onclick="$(this).parent().remove();">X</span>'
			 + '</div>';

		if ($(obj).length > 0){
			$(obj).after(html);
		}else{
			$('#tdMemParent').html(html);
		}
	}

	function lfPrint(){

		var para = 'root=iljung'
				 + '&dir=N'
				 + '&fileName=sw_work_log'
				 + '&fileType=pdf'
				 + '&target=show.php'
				 + '&showForm=SW_WORK_LOG'
				 + '&jumin='+$('#jumin').val()
				 + '&yymm='+$('#yymm').val()
				 + '&seq='+$('#seq').val()
				 + '&param=';

		__printPDF(para);
	}

	function lfPrint2(){

		var para = 'root=iljung'
				 + '&dir=N'
				 + '&fileName=sw_work_log'
				 + '&fileType=pdf2'
				 + '&target=show.php'
				 + '&showForm=SW_WORK_LOG2'
				 + '&jumin='+$('#jumin').val()
				 + '&yymm='+$('#yymm').val()
				 + '&seq='+$('#seq').val()
				 + '&cnt='+$('#divNo').text()
				 + '&param=';

		__printPDF(para);
	}

	function lfExcel(gbn){
		var parm = new Array();
			parm = {
				'jumin'	:$('#jumin').val()
			,	'yymm'	:$('#yymm').val()
			,	'seq'	:$('#seq').val()
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

		if (gbn == 'NEW'){
			form.setAttribute('action', './sw_work_log_excel_class.php');
		}else{
			form.setAttribute('action', './sw_work_log_excel.php');
		}

		document.body.appendChild(form);

		form.submit();
	}

	//요양일정리스트
	function lfLoadCareList(){
		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_reg_carelist.php'
		,	data:{
				'jumin'	:$('#jumin').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_TBL_CARELIST').html(html);
			}
		});
	}

	//웹프린트
	/*
	function lfWebPrint(){
		
		var parm = new Array();
			parm = {
				'path'	:'../iljung/sw_work_log_web_prt.php'
			,	'jumin'	:$('#jumin').val()
			,	'yymm'	:$('#yymm').val()
			,	'seq'	:$('#seq').val()
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

		form.setAttribute('target', 'ID_WEB_PRT');
		form.setAttribute('method', 'post');
		form.setAttribute('action', '../showWeb/');

		document.body.appendChild(form);

		form.submit();
	}
	*/

	function lfSetSign(gbn){
		var ua = navigator.userAgent;
		var trident = ua.match(/Trident\/(\d.\d)/i);
		var IsSignShow = false;

		if (trident != null){
			if (trident[1] == '7.0' || trident[1] == '6.0'){
				//IE11, IE10
				IsSignShow = true;
			}
		}

		if (!IsSignShow) return;

		var obj, nam;

		if (gbn == '1'){
			obj = $('#ID_IMG_SIGN_TG');
			nam = $('#ID_IMG_NAME_TG');
		}else if (gbn == '2'){
			obj = $('#ID_IMG_SIGN_YO');
			nam = $('#ID_IMG_NAME_YO');
		}else if (gbn == '3'){
			obj = $('#ID_IMG_SIGN_VS');
			nam = $('#ID_IMG_NAME_VS');
		}else{
			return;
		}

		var objModal = new Object();
		var url = '../sign/sign.html';
		var style    = 'dialogWidth:600px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		objModal.gbn = gbn;
		objModal.yymm = $('#yymm').val();
		objModal.seq = $('#seq').val();
		objModal.key = '<?=$key;?>';
		objModal.name = $(nam).text();
		objModal.w = 0;
		objModal.h = 0;
		
		window.showModalDialog(url, objModal, style);

		if (objModal.p == 'DELETE'){
			$(obj).html('');
		}else if (objModal.p){
			var pos = lfResizeImg($(obj).width() - 25, $(obj).height() - 25, __str2num(objModal.w), __str2num(objModal.h));
			$(obj).html('<img src="'+objModal.p+'?timestamp=' + new Date().getTime()+'" style="width:'+Math.floor(pos['w'])+'; height:'+Math.floor(pos['h'])+';" border="0">');
			$(nam).text(objModal.n);
		}
	}
</script>
<div class="title title_border">사회복지사 업무일지 작성</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="220px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head last" colspan="2" style="height:27px;">수급자정보</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th class="center">수급자명</th>
							<td class="left last"><?=$name;?></td>
						</tr>
						<tr>
							<th class="center">등급</th>
							<td class="left last"><?=$lvl;?></td>
						</tr>
						<tr>
							<th class="center" style="border-bottom:1px solid #2457BD;">적용기간</th>
							<td class="left last" style="border-bottom:1px solid #2457BD;"><?=$myF->dateStyle($from,'.');?> ~ <?=$myF->dateStyle($to,'.');?></td>
						</tr>
					</tbody>
					<thead>
						<tr>
							<th class="head last" colspan="2">
								<div style="float:right; width:auto; margin-right:5px;"><span class="btn_pack small"><button type="button" onclick="lfLogSel();">신규</button></span></div>
								<div style="float:center; width:auto;"><?=$year;?>년 <?=IntVal($month);?>월 업무일지</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="center top bottom last" colspan="2">
								<div style="width:100%; height:78px; overflow-x:hidden; overflow-y:auto; margin-bottom:1px;">
									<table class="my_table" style="width:100%;">
										<colgroup>
											<col width="30px">
											<col width="70px">
											<col width="40px">
											<col>
										</colgroup>
										<tbody id="tbodyWorkLog"></tbody>
									</table>
								</div><?
								//요양서비스일정
								$colgroup = '
									<col width="40px">
									<col width="50px" span="2">
									<col>';?>
								<table class="my_table" style="width:100%; border-top:1px solid #CCCCCC;">
									<colgroup><?=$colgroup;?></colgroup>
									<thead>
										<tr>
											<th class="head last" colspan="4"><?=$year;?>년 <?=IntVal($month);?>월 서비스 일정표</th>
										</tr>
										<tr>
											<th class="head">일자</th>
											<th class="head">시작</th>
											<th class="head">종료</th>
											<th class="head last">요보사</th>
										</tr>
									</thead>
								</table>
								<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
									<table class="my_table" style="width:100%;">
										<colgroup><?=$colgroup;?></colgroup>
										<tbody id="ID_TBL_CARELIST"></tbody>
									</table>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="center top">
				<div id="divWorkMst">
						<div style="position:absolute; margin-top:-25px; text-align:right;">
							<span class="btn_pack m"><span class="list"></span><button type="button" onclick="lfCopy();">이전내용가저오기</button></span>	
							<span class="btn_pack m"><span class="save"></span><button type="button" onclick="lfSave();">저장</button></span>
							<span class="btn_pack m"><span class="delete"></span><button type="button" onclick="lfDelete();">삭제</button></span>
							<span class="btn_pack m"><span class="print"></span><button onclick="lfWebPrint(2);">출력1</button></span>
							<span class="btn_pack m"><span class="print"></span><button onclick="lfWebPrint(1);">출력2</button></span>
							<span class="btn_pack m"><span class="excel"></span><button id="btnExcel" type="button" onclick="lfExcel('NEW');">현재페이지</button></span>
							<!--span class="btn_pack m"><span class="excel"></span><button id="btnExcel" type="button" onclick="lfExcel();">현재페이지</button></span-->
							<span class="btn_pack m"><span class="pdf"></span><button id="btnPdf1" type="button" onclick="lfPrint();">현재페이지</button></span>
							<span class="btn_pack m"><span class="pdf"></span><button id="btnPdf2" type="button" onclick="lfPrint2();">2회출력</button></span>
						</div>
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="100px">
							<col width="250px">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th class="center">사회복지사</th>
								<td class="">
									<div id="divNo" seq="" class="left" style="float:left; width:auto; line-height:25px; margin-right:5px;"></div>
									<div style="float:left; width:auto; height:25px; margin-left:2px; margin-top:1px;"><span class="btn_pack find" onclick="lfFindWorker();"></span></div>
									<div id="divSW" jumin="" from="" to="" style="float:left; width:auto; line-height:25px;"></div>
								</td>
								<td class="top last" rowspan="3">
									<table class="my_table" style="width:100%; height:100%;">
										<colgroup>
											<col width="1px">
											<col width="30px">
											<col width="85px">
											<col width="30px">
											<col width="85px">
											<col width="30px">
											<col width="85px">
											<col width="1px">
											<col>
										</colgroup>
										<tbody>
											<tr>
												<td rowspan="3"></td>
												<th class="center bottom">수보</th>
												<td class="center" id="ID_IMG_NAME_TG"></td>
												<th class="center bottom">요</th>
												<td class="center" id="ID_IMG_NAME_YO"></td>
												<th class="center bottom">방</th>
												<td class="center" id="ID_IMG_NAME_VS"></td>
												<td rowspan="3"></td>
												<th class="center last">관리책임자</th>
											</tr>
											<tr>
												<th class="center bottom">급호</th>
												<td class="center bottom" rowspan="2" id="ID_IMG_SIGN_TG" onclick="lfSetSign('1');"></td>
												<th class="center bottom">보</th>
												<td class="center bottom" rowspan="2" id="ID_IMG_SIGN_YO" onclick="lfSetSign('2');"></td>
												<th class="center bottom">문</th>
												<td class="center bottom" rowspan="2" id="ID_IMG_SIGN_VS" onclick="lfSetSign('3');"></td>
												<td class="center bottom last" rowspan="2" id="ID_IMG_SIGN_MG"></td>
											</tr>
											<tr>
												<th class="center bottom">자자</th>
												<th class="center bottom">사</th>
												<th class="center bottom">자</th>
											</tr>
										</tbody>
									</table>
								</td>
								<!--td class="last" colspan="2">
									<div id="divNo" seq="" class="left" style="float:left; width:auto; line-height:25px; margin-right:5px;"></div>
									<div style="float:left; width:auto; height:25px; margin-left:2px; margin-top:1px;"><span class="btn_pack find" onclick="lfFindWorker();"></span></div>
									<div id="divSW" jumin="" from="" to="" style="float:left; width:auto; line-height:25px;"></div>
									<div style="float:right; width:auto; margin-top:2px; margin-right:5px;">
										<span class="btn_pack m"><span class="excel"></span><button id="btnExcel" type="button" onclick="lfExcel('NEW');">현재페이지</button></span>
										<span class="btn_pack m"><span class="pdf"></span><button id="btnPdf1" type="button" onclick="lfPrint();">현재페이지</button></span>
										<span class="btn_pack m"><span class="pdf"></span><button id="btnPdf2" type="button" onclick="lfPrint2();">2회출력</button></span>
									</div>
								</td-->
							</tr>
							<tr>
								<th class="center">서비스기간</th>
								<td class="left"><?
									if (is_array($arrHisSvc)){
										foreach($arrHisSvc as $idx => $row){
											if ($idx > 0){?>
												<span style="padding-left:10px; padding-right:10px;">/</span><?
											}?>
											<span id="lblFrom_<?=$idx;?>"><?=$myF->dateStyle($row['fromDt'],'.');?></span> ~ <span id="lblTo_<?=$idx;?>"><?=$myF->dateStyle($row['toDt'],'.');?></span><?
										}
									}?>
								</td>
							</tr>
							<tr>
								<th class="center">방문일시</th>
								<td class="">
									<input id="txtDate" type="text" init="Y" class="date" value="<?=$date;?>" org="<?=$date;?>" onchange="lfChkDate();">
									(<input id="txtTime" type="text" init="Y" class="no_string" value="<?=$time;?>" org="<?=$time;?>" alt="time" onchange="lfSetWorkerChkTime();"> ~ <input id="txtToTime" type="text" init="Y" class="no_string" value="<?=$toTime;?>" org="<?=$toTime;?>" alt="time" onchange="lfSetWorkerChkTime();">)
									<span id="ID_CELL_WK_LOG" style="color:RED;"></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="divLogBody" style="width:100%; height:100px; overflow-x:hidden; overflow-y:auto;"></div>
				<div id="divBtnBody" class="center" style="height:28px; border-top:1px solid #CCCCCC;">
					<div style="margin-top:5px;">
					    <span class="btn_pack small"><button type="button" onclick="lfCopy();">이전내용가저오기</button></span>	
						<span class="btn_pack small"><button type="button" onclick="lfSave();">저장</button></span>
						<span class="btn_pack small"><button type="button" onclick="lfDelete();">삭제</button></span>
						<span class="btn_pack small"><button type="button" onclick="self.close();">닫기</button></span>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<input id="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input id="from" type="hidden" value="<?=$from;?>">
<input id="to" type="hidden" value="<?=$to;?>">
<input id="year" type="hidden" value="<?=$year;?>">
<input id="month" type="hidden" value="<?=$month;?>">
<input id="yymm" type="hidden" value="<?=$year.$month;?>">
<input id="seq" type="hidden" value="">
<div id='ID_DIV_WORKER_LIST' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<iframe id="ID_FRM_PRINT" src="about:blank" style="width:100%; height:500px; display:none;" frameborder="0"></iframe>
<script type="text/javascript">
	function lfWebPrint(mode){
		//$('#ID_FRM_PRINT').attr('src','../iljung/sw_work_log_print.php?jumin=<?=$ed->en64($jumin);?>&yymm='+$('#yymm').val()+'&seq='+$('#seq').val()).show();
		
		var left = (screen.availWidth - (width = 800)) / 2, top = (screen.availHeight - (height = 800)) / 2;
		
		var win = window.open('../showWeb?path=../iljung/sw_work_log_print.php&data='+__parseSet('jumin/=/<?=$ed->en64($jumin);?>&yymm/=/'+$('#yymm').val()+'&seq/=/'+$('#seq').val()+'&mode/=/'+mode), 'WEB_PRINT', 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no');

		win.focus();
	}
</script><?

	Unset($arrHisSvc);
	include_once('../inc/_footer.php');
?>