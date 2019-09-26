<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');
	
	/*
	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}
	*/
	
	$year = date('Y');

	$colgrp = '
		<col width="150px">
		<col width="150px">
		<col width="150px">
		<col width="130px">
		<col>';
?>
<script type="text/javascript">
	$(document).ready(function(){
		current_menu('R');
	});

	
	/*********************************************************

		년월이동

	*********************************************************/
	function moveYear(pos){
		var mode = $('#mode').val();
		var year = parseInt($('#year').text()) + pos;

		
		$('#year').text(year);

		current_menu($('#linkGbn').val());
	}
	
	function current_menu(re_gbn){
		if(re_gbn == 'R'){
			$('#BTN_TAB_1').css("fontWeight","bold");
			$('#BTN_TAB_1').css("color","#0000ff");
			$('#BTN_TAB_2').css("fontWeight","normal");
			$('#BTN_TAB_2').css("color","#000000");
		}else {
			$('#BTN_TAB_2').css("fontWeight","bold");
			$('#BTN_TAB_2').css("color","#0000ff");
			$('#BTN_TAB_1').css("fontWeight","normal");
			$('#BTN_TAB_1').css("color","#000000");
		}

		$('#linkGbn').val(re_gbn);

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./budget_list.php'
		,	data:{
				'year':$('#year').text()
			,	're_gbn':re_gbn
			}
		,	beforeSend:function(){		
			}
		,	success:function(html){
				$('#tbl_tab').html(html);
				__init_form(document.f);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfBudgetSave(){
		var re_gbn = '', year = $('#year').text();
		
		re_gbn = $('#linkGbn').val();
		
		var data = {
			'path':'fa/budget'
		,	'page':'budget_save'
		,	're_gbn':re_gbn
		,	'year':year
		,	'para':''
		};

		$('#tbl_tab :text[id="amt"]').each(function(){
			data['para'] += (data['para'] ? '?' : '')+'gwan_cd='+$(this).attr('gwan_cd')+'&hang_cd='+$(this).attr('hang_cd')+'&mog_cd='+$(this).attr('mog_cd')+'&amt='+__str2num($(this).val());
		});

		if (!confirm(year+'년 '+(re_gbn == 'R' ? '세입' : '세출')+'예산을 저장하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./budget_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				alert('정상적으로 처리되었습니다');		
			}
		,	error:function(){
			}
		}).responseXML;
	}


	function lfPDF(type){
		//if(!type) type = $('#type').val();
		var year = $('#year').text();
		var re_gbn = $('#linkGbn').val();

		var dir = 'P';

		if(type=='1'){
			var file = 'budget';
			var showform = 'BUDGET';
		}else {
			var file = 'spec';
			var showform = 'SPEC';
		}

		var arguments	= 'root=account'
						+ '&dir='+dir
						+ '&fileName='+file
						+ '&fileType=pdf'
						+ '&target=show.php'
						+ '&year='+year
						+ '&re_gbn='+re_gbn
						+ '&showForm='+showform;
		
		
		__printPDF(arguments);
	}

</script>
<div class="title title_border">계정항목</div>
<form name="f" method="post">
<input name="linkGbn" id="linkGbn" type="hidden" value=""> 
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="360px">
		<col width="300px">
		<col width="350px">
	</colgroup>

<tbody>
	<tr>
		<td class="last">
			<div id="DIV_BODY" class="bd_s w1">
				<span class="btn_pack m" ><button id='BTN_TAB_1' style='' onclick="current_menu('R');">세입항목</button></span>
				<span class="btn_pack m" ><button id='BTN_TAB_2' style='' onclick="current_menu('E');">세출항목</button></span>
			</div>
		</td>
		<td class="last">
		<div class='left' style='padding-top:2px;'>
		<div style='float:left; width:auto; padding-top:2px;'><img src='../image/btn/btn_pre_out.gif' style='cursor:pointer;' onclick='moveYear(-1);' onmouseover='this.src="../image/btn/btn_pre_over.gif";' onmouseout='this.src="../image/btn/btn_pre_out.gif";'></div>
		<div style='float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;' id='year'><?=$year;?></div>
		<div style='float:left; width:auto; padding-top:2px;'><img src='../image/btn/btn_next_out.gif' style='cursor:pointer;' onclick='moveYear(1);' onmouseover='this.src="../image/btn/btn_next_over.gif";' onmouseout='this.src="../image/btn/btn_next_out.gif";'></div>
		</div>
		</td>
		<td class="last">
		<div class="right">
			<span class='btn_pack m'><button type='button' onclick='lfBudgetSave();'>예산저장</button></span>
			<span class='btn_pack m'><button type='button' onclick='lfPDF("1");'>예산서출력</button></span>
			<span class='btn_pack m'><button type='button' onclick='lfPDF("2");'>명세서출력</button></span>
		</div>
		</td>
	</tr>
</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<td colspan="3">과목</td>
			<td rowspan="2">예산액</td>
			<td rowspan="2" class="last">비고</td>
		</tr>
		<tr>
			<td>관</td>
			<td>항</td>
			<td>목</td>
		</tr>
	</thead>
	<tbody id="tbl_tab"></tbody>
</table>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>