<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code  = $_SESSION['userCenterCode'];
	$year  = date('Y', mktime());
	$month = date('m', mktime());
	$mode  = $_GET['mode'];
	

	$month = $month < 10 ? substr($month, -1) : $month;

	if($debug){
		$newYn = 'Y';
	}else {
		$newYn = 'N';
	}
	

?>

<script language='javascript'>
<!--
	$(document).ready(function(){
		
		$('input:checkbox[name="chkSvc"]').unbind('click').bind('click',function(){
			
			var key = $(this).attr('id');
			var chk = $(this).attr('checked');
			
			$('input:checkbox[id^="'+key+'"]').attr('checked',chk);
		});

		$('#chkSvc').attr('checked',true).click().attr('checked',true);
			
		_modeChange($('#mode').val());
	});

	function _modeChange(mode, param){
		var title = null;

		switch(mode){
			case 'plan':
				title = '장기요양급여 이용(제공) 계획서';
				break;

			case 'statement':
				title = '세부내역서 및 납부확인서';
				break;
		}

		$('#titleBody').text(title);
		$('#mode').val(mode);
		$('#param').val(param);

		_showYYMM();
	}

	/*********************************************************

		년월

	*********************************************************/
	function _showYYMM(){
		var today = new Date();
		var year  = $('#year').text();
		var month = $('#month').val();

		if (year == ''){
			year  = today.getFullYear();
			month = today.getMonth()+1;
		}
		
		try{
			$.ajax({
				type: 'POST',
				url : './care_use_yymm.php',
				data: {
					'code':$('#code').attr('value')
				,	'year':year
				,	'month':month
				,	'mode':$('#mode').val()
				,	'param':$('#param').val()
				},
				beforeSend: function (){
				},
				success: function (xmlHttp){
					$('#yymmBody').html(xmlHttp);
					_showList();
				},
				error: function (){
				}
			}).responseXML;
		}catch(e){
		}
	}
	
	
	
	/*********************************************************

		년월이동

	*********************************************************/
	function _moveYear(pos){
		$('#year').text(parseInt($('#year').text()) + pos);
		_showList();
	}

	function _moveMonth(month){
		$(document).find('.my_month').each(function(){
			if ($(this).attr('id').toString().substr($(this).attr('id').toString().length - month.toString().length - 1, $(this).attr('id').toString().length) == '_'+month.toString()){
				$(this).removeClass('my_month_1');
				$(this).addClass('my_month_y');
			}else{
				$(this).removeClass('my_month_y');
				$(this).addClass('my_month_1');
			}
		});
		$('#month').val(month);
		_showList();
	}

	
	function lfGetSvc(){
		var chkSvc = '';

		$('input:checkbox[name="chkSvc"]').each(function(){
			if ($(this).attr('checked') && $(this).val() != 'on'){
				var key  = $(this).val().split('H_').join('').split('V_').join('').split('C_').join('').split('O_').join('');
					key += String.fromCharCode(1);

				chkSvc += key;
			}
		});

		$('#txtChkSvc').val(chkSvc);

		return chkSvc;
	}

	/*********************************************************

		리스트출력

	*********************************************************/
	function _showList(){
		var chkSvc = lfGetSvc();
		
		try{
			$.ajax({
				type: 'POST',
				url : './care_use_list.php',
				data: {
					'code'		:$('#code').attr('value')
				,	'mode'		:$('#mode').attr('value')
				,	'year'		:$('#year').text()
				,	'month'		:$('#month').attr('value')
				,	'param'		:$('#param').attr('value')
				,	'chkSvc'	:chkSvc
				},
				beforeSend: function (){
					$('#listBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'>'+__get_loading()+'</div></center></div>');
				},
				success: function (xmlHttp){
					$('#tblList').remove();
					$('#listBody').before(xmlHttp);
					$('#tempLodingBar').remove();
					__init_form(document.f);
				},
				error: function (){
					
				}
			}).responseXML;
		}catch(e){
			
		}
	}
	

	// 출력 
	function showPrint(ssn, md){
		
		data = '';

		if(ssn == 'sel'){
			$('input:checkbox[name="chkIn"]').each(function(){
				var obj = $(this).parent().parent();
				
				data += (data ? '?' : '');

				if ($(this).attr('checked')){
					data += 'cltCd='+$(this).attr('cltCd');
				}
			});
		}
		
		var chkSvc = lfGetSvc();
		var code = $('#code').attr('value')
		var mode = md != undefined ? md : $('#mode').attr('value') 
		var year = $('#year').text()
		var month = $('#month').attr('value')
		var printDT = $('#printDT').attr('value')
		var data = data;
	
		var width  = 700;
		var height = 900;
		var left   = (window.screen.width  - width)  / 2;
		var top    = (window.screen.height - height) / 2;
		
		winModal = window.open('about:blank','SHOW_PDF', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
		
		
		var pdf = document.createElement('form');
	
		pdf.appendChild(__create_input('code', code));
		pdf.appendChild(__create_input('mode', mode));
		pdf.appendChild(__create_input('year', year));
		pdf.appendChild(__create_input('month', month));
		pdf.appendChild(__create_input('jumin', ssn));
		pdf.appendChild(__create_input('svcGbn', chkSvc));
		pdf.appendChild(__create_input('printDT', printDT));
		pdf.appendChild(__create_input('data', data));
			
		pdf.setAttribute('method', 'post');

		document.body.appendChild(pdf);

		pdf.target = 'SHOW_PDF';
		pdf.action = '../iljung/care_use_pdf.php';
		pdf.submit();

	}
	
	//전체 체크
	function lfChkAll(){
		var chk = $('#chkAll').attr('checked');

		$('input:checkbox[name="chkIn"]').each(function(){
			$(this).attr('checked',chk);
		});
	}

	
-->
</script>

<form id='f' name='f' method='post'>
<input id="txtChkSvc" name="hid" type="hidden">
<div id='titleBody' class='title title_border'></div>
<div id='yymmBody'></div>
<div id='listBody' style='display:none;'></div>

<input id='code' name='code' type='hidden' value='<?=$code;?>'>
<input id='kind' name='kind' type='hidden' value=''>
<input id='mode' name='mode' type='hidden' value='<?=$mode;?>'>
<input id='month' name='month' type='hidden' value='<?=$month;?>'>

</form>

<iframe id='frmWork' name='frmWork' src='about:blank' style='width:100%; height:200px; display:none;?>;'></iframe>

<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>