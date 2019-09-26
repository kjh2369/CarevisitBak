<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_mySuga.php');

	$code  = $_SESSION['userCenterCode'];
	$year  = $_GET['year'] != '' ? $_GET['year'] : date('Y', mktime());
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

		title = '업무수행일지(빈양식출력)';

		$('#titleBody').text(title);
		$('#mode').val(mode);
		$('#param').val(param);

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
				url : './sw_emp_list.php',
				data: {
					'code'		:$('#code').attr('value')
				,	'mode'		:$('#mode').attr('value')
				,	'year'      :'<?=$year;?>'
				,	'param'		:$('#param').attr('value')
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


	function EmpPrint(){

		var data = '';
		var cnt = 0;

		$('input:checkbox[name="chkIn"]').each(function(){
			var obj = $(this).parent().parent();

			if ($(this).attr('checked')){

				if(cnt != 0) data += '||';

				data += $(this).attr('cltCd');

				cnt ++;
			}
		});

		var left = (screen.availWidth - (width = 800)) / 2, top = (screen.availHeight - (height = 600)) / 2;
		var win = window.open('../showWeb?path=../iljung/sw_work_log_print.php&data='+__parseSet('type/=/blank&jumin/=/'+data), 'WEB_PRINT', 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no');

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
<div id='titleBody' class='title title_border' style="float:left;"></div>
<div id='listBody' style='display:none;'></div>

<input id='code' name='code' type='hidden' value='<?=$code;?>'>
<input id='kind' name='kind' type='hidden' value=''>

</form>

<iframe id='frmWork' name='frmWork' src='about:blank' style='width:100%; height:200px; display:none;?>;'></iframe>
