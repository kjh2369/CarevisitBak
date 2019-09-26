<?
	/**************************************************

		상담기록지 메뉴

	**************************************************/
?>
<script language='javascript'>
<!--

function show_record_menu(id){
	if (id == undefined){
		id = document.getElementById('record_menu').value;

		if (id == '') id = 'record_visit';
	}

	show_svc_layer('record');
	show_record_list(document.getElementById(id));
}

function show_record_list(menu){
	var record_menu = document.getElementById('record_menu');

	if (record_menu.value != '')
		eval('go_'+record_menu.value.split('record_').join('')+'_list()');

	record_menu.value = menu.id;

	var menus = new Array();

	menus[0] = document.getElementById('record_visit');
	menus[1] = document.getElementById('record_phone');
	menus[2] = document.getElementById('record_stress');
	menus[3] = document.getElementById('record_case');
	menus[4] = document.getElementById('record_stat');

	for(var i=0; i<menus.length; i++){
		menus[i].className = 'normal_text';
	}

	menu.className = 'current_text';

	set_button(2);
	show_record_svc();
}

function show_record_svc(){
	var record_menu = document.getElementById('record_menu');
	var svc_menu = record_menu.value.split('record').join('svc');

	var svcs = new Array();

	svcs[0] = document.getElementById('svc_visit');
	svcs[1] = document.getElementById('svc_phone');
	svcs[2] = document.getElementById('svc_stress');
	svcs[3] = document.getElementById('svc_case');
	svcs[4] = document.getElementById('svc_stat');

	for(var i=0; i<svcs.length; i++){
		svcs[i].style.display = 'none';
	}

	var svc = document.getElementById(svc_menu);

	if (svc == null){
		alert('준비중입니다.');
		return;
	}

	svc.style.display = '';

	eval('go_'+svc_menu.split('svc_').join('')+'_list()');
}

function go_record_reg(){
	var record_menu = document.getElementById('record_menu');

	eval('go_'+record_menu.value.split('record_').join('')+'_reg(\'\',0)');
}

function go_record_list(){
	var record_menu = document.getElementById('record_menu');

	eval('go_'+record_menu.value.split('record_').join('')+'_list()');
}

-->
</script>

<div id="svc_record_menu" style="margin-top:10px; margin-bottom:10px; margin-left:10px; margin-right:10px; display:none;">

	<table class="my_table my_border_blue" style="width:100%; margin-bottom:10px;">
		<colgroup>
			<col width="20%" span="5">
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="5">과정상담 메뉴</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left"><span id="record_visit" class="norma_text" onclick="show_record_list(this);" style="cursor:pointer;">고객방문상담</span></td>
				<td class="left"><span id="record_phone" class="norma_text" onclick="show_record_list(this);" style="cursor:pointer;">전화상담</span></td>
				<td class="left"><span id="record_stress" class="norma_text" onclick="show_record_list(this);" style="cursor:pointer;">불만 및 고충처리</span></td>
				<td class="left"><span id="record_case" class="norma_text" onclick="show_record_list(this);" style="cursor:pointer;">사례관리 회의</span></td>
				<td class="left">
					<span id="record_stat" class="norma_text" onclick="show_record_list(this);" style="cursor:pointer;"><?
						if ($code == '31138000044'){
							echo '수급자(보호자)상담일지';
						}else{
							echo '상태변화일지';
						}?>
					</span>
				</td>
			</tr>
		</tbody>
	</table>

	<input name="record_menu" type="hidden" value="<?=$record_menu;?>">

	<?
		/**************************************************

			방문상담기록지

		**************************************************/
			include_once('client_reg_visit.php');
		/*************************************************/



		/**************************************************

			전화상담기록지

		**************************************************/
			include_once('client_reg_phone.php');
		/*************************************************/



		/**************************************************

			불만 및 고충처리기록지

		**************************************************/
			include_once('client_reg_stress.php');
		/*************************************************/



		/**************************************************

			사례관리기록지

		**************************************************/
			include_once('client_reg_case.php');
		/*************************************************/

		//상태변화
		include_once('client_reg_stat.php');
	?>

</div>