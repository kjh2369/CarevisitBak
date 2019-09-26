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


	$colgrp = '
		<col width="50px">
		<col width="120px">
		<col width="50px">
		<col width="120px">
		<col width="50px">
		<col width="120px">
		<col>';
?>
<script type="text/javascript">
	$(document).ready(function(){
		current_menu('R');
	});

	
	function current_menu(re_gbn){
		/*
		var menu_obj = document.getElementById('menu_select');

		for(var i=0; i<mem_menu.length; i++){
			var menu_id = document.getElementById('BTN_TAB_'+mem_menu[i]['cd']);
			
			if (mem_menu[i]['cd'] == cd){
				menu_id.style.fontWeight = 'bold';
				menu_id.style.color = '#0000ff';
				menu.style.display = '';
				menu_obj.value = cd;
			}else{
				menu_id.style.fontWeight = 'normal';
				menu_id.style.color = '#000000';
				menu.style.display = 'none';
			}
		}
		*/

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
		
		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./item_org_list.php'
		,	data:{
				're_gbn':re_gbn
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbl_tab').html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}

</script>
<div class="title title_border">계정항목</div>
<div id="DIV_BODY" class="bd_s w1">
	<span class="btn_pack m" ><button id='BTN_TAB_1' style='' onclick="current_menu('R');">세입항목</button></span>
	<span class="btn_pack m" ><button id='BTN_TAB_2' style='' onclick="current_menu('E');">세출항목</button></span>
</div>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<td colspan="6">과목</td>
			<td rowspan="3" class="last">내역</td>
		</tr>
		<tr>
			<td colspan="2">관</td>
			<td colspan="2">항</td>
			<td colspan="2">목</td>
		</tr>
		<tr>
			<td>코드</td>
			<td>항목</td>
			<td>코드</td>
			<td>항목</td>
			<td>코드</td>
			<td>항목</td>
		</tr>
	</thead>
</table>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<tbody id="tbl_tab"></tbody>
</table>


<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>