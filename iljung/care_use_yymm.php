<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$r_date   = date('Y-m-d');
	$mode   = $_POST['mode'];
	$find_name = $_POST['find_name'];
	$find_type = $_POST['find_type'];
	$code   = $_POST['code'];
	$year   = $_POST['year'];
	$month  = $_POST['month'];
	//$fromDt = date('Y-m', mktime()).'-01';
	//$toDt   = date('Y-m', mktime()).'-'.$myF->lastDay(date('Y', mktime()), date('m', mktime()));
	$fromDt = $_POST['fromDate'] != '' ? $_POST['fromDate'] : date('Y-m', mktime()).'-01';
	$toDt   = $_POST['toDate'] != '' ? $_POST['toDate'] : date('Y-m', mktime()).'-'.$myF->lastDay(date('Y', mktime()), date('m', mktime()));

	if (is_numeric($month)){
		$month = (intval($month) < 10 ? '0' : '').intval($month);
	}
	
	parse_str($_POST['param'], $para);
	
	?>
		<script language='javascript'>
		<!--
			$(document).ready(function(){
				
				$('input:checkbox[name="chkSvc"]').unbind('click').bind('click',function(){
					
					var key = $(this).attr('id');
					var chk = $(this).attr('checked');
					
					$('input:checkbox[id^="'+key+'"]').attr('checked',chk);
				});
				

				$('#chkSvc_200').unbind('click').bind('click',function(){
						
					if($('input:checkbox[id^="chkSvc_200"]').attr('checked') == true  &&
						$('input:checkbox[id^="chkSvc_500"]').attr('checked') == true &&
						$('input:checkbox[id^="chkSvc_800"]').attr('checked') == true ){
						$('#chkSvc').attr('checked',true);
					}
					if($('input:checkbox[id^="chkSvc_200"]').attr('checked') == false){
						$('#chkSvc').attr('checked',false);
					}
				});
				
				$('#chkSvc_500').unbind('click').bind('click',function(){
											
					if($('input:checkbox[id^="chkSvc_200"]').attr('checked') == true  &&
						$('input:checkbox[id^="chkSvc_500"]').attr('checked') == true &&
						$('input:checkbox[id^="chkSvc_800"]').attr('checked') == true ){
						$('#chkSvc').attr('checked',true);
					}

					if($('input:checkbox[id^="chkSvc_500"]').attr('checked') == false){
						$('#chkSvc').attr('checked',false);
					}
				});

				$('#chkSvc_800').unbind('click').bind('click',function(){
											
					if($('input:checkbox[id^="chkSvc_200"]').attr('checked') == true  &&
						$('input:checkbox[id^="chkSvc_500"]').attr('checked') == true &&
						$('input:checkbox[id^="chkSvc_800"]').attr('checked') == true ){
						$('#chkSvc').attr('checked',true);
					}

					if($('input:checkbox[id^="chkSvc_800"]').attr('checked') == false){
						$('#chkSvc').attr('checked',false);
					}
				});

				$('#chkSvc').attr('checked',true).click().attr('checked',true);
					
			});


		-->
		</script>
		<script src="../js/work.js" type="text/javascript"></script>
	<?
	$tag = '&nbsp;';
	$btn = '&nbsp;';
	$tab = '<div class=\'left\' style=\'padding-top:2px;\'>
			<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_pre_out.gif\' style=\'cursor:pointer;\' onclick=\'_moveYear(-1);\' onmouseover=\'this.src="../image/btn/btn_pre_over.gif";\' onmouseout=\'this.src="../image/btn/btn_pre_out.gif";\'></div>
			<div style=\'float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;\' id=\'year\'>'.$year.'</div>
			<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_next_out.gif\' style=\'cursor:pointer;\' onclick=\'_moveYear(1);\' onmouseover=\'this.src="../image/btn/btn_next_over.gif";\' onmouseout=\'this.src="../image/btn/btn_next_out.gif";\'></div>
			</div>';
	$str = '년도';

	if ($mode == 'plan'){
		$tag = $myF->_btn_month($month, '_moveMonth(', ');');
		
	}else if ($mode == 'statement'){
		$tag = $myF->_btn_month($month, '_moveMonth(', ');');
	}

	$html = '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'45px\'>
					<col width=\'85px\'>
					<col width=\'500px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class=\'head\'>'.$str.'</th>
						<td class=\''.($mode != 'client_month' ? 'last' : '').'\'>'.$tab.'</td>
						<td class=\'last\'>'.$tag.'</td>
						<td class=\'right last\'>'.$btn.'</td>
					</tr>
				</tbody>
			 </table>';

	
	$html .= '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'50px\'>
					<col width=\'450px\'>
					<col width=\'100px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class=\'head\'>출력일자</th>
						<td class=\'last\'>
							<input id=\'printDT\' name=\'printDT\' type=\'text\' value=\''.$r_date.'\' class=\'date\'>
						</td>
					</tr>
					<tr>
						<th class=\'head\'>급여종류</th>
						<td class=\'last\'>
							<input id=\'chkSvc\' name=\'chkSvc\' type=\'checkbox\' value=\'all\' class=\'checkbox\' ><label for=\'expenseYn\'>전체</label>
							<input id=\'chkSvc_200\' name=\'chkSvc\' type=\'checkbox\' value=\'200\' class=\'checkbox\' ><label for=\'expenseYn\'>방문요양</label>
							<input id=\'chkSvc_500\' name=\'chkSvc\' type=\'checkbox\' value=\'500\' class=\'checkbox\' ><label for=\'expenseYn\'>방문목욕</label>
							<input id=\'chkSvc_800\' name=\'chkSvc\' type=\'checkbox\' value=\'800\' class=\'checkbox\' ><label for=\'expenseYn\'>방문간호</label>
						</td>
					</tr>
				</tbody>
			 </table>';
	
	echo $html;

	include_once('../inc/_db_close.php');
?>