<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
?>
<script>
/*********************************************************

	onload

*********************************************************/
try{
	$(document).ready(function(){
		_showList();
	});
}catch(e){
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

/*********************************************************

	리스트출력

*********************************************************/
function _showList(){
	try{
		$.ajax({
			type: 'POST',
			url : './iljung_plan_list.php',
			data: {
				'code'	 :$('#code').attr('value')
			,	'year'	 :$('#year').text()
			,	'month'	 :$('#month').attr('value')
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

function plan_save(){	
	var para = '';
	
	$('[id^="row_cnt"]').each(function(){
		var row_cnt = $(this).attr('id').split('row_cnt').join('');

		if (para) para += '/';

			para = para 
				 + 'jumin='+$('#jumin'+row_cnt).attr('value') 
				 + '&visitDt='+$('#visit_dt'+row_cnt).attr('value');
	
	});


	if(para){
		try{
			$.ajax({
				type: 'POST',
				url : './iljung_plan_save.php',
				data: {
					'code'	 :$('#code').attr('value')
				,	'year'	 :$('#year').text()
				,	'month'	 :$('#month').attr('value')
				,   'para'   :para
				},

				beforeSend: function (){
				},
				success: function (xmlHttp){
					alert(xmlHttp);
				},
				error: function (){
					
				}
			}).responseXML;
		}catch(e){
			
		}
	}
	
	
}
</script>
<form name="f" method="post">
<div class='title title_border'>테스트메뉴</div>
<?
	$code  = $_SESSION['userCenterCode'];
	$year  = date('Y', mktime());
	$month = date('m', mktime());
	
	if (is_numeric($month)){
		$month = (intval($month) < 10 ? '0' : '').intval($month);
	}
	
	$tag = '&nbsp;';
	$btn = '&nbsp;';
	$tab = '<div class=\'left\' style=\'padding-top:2px;\'>
			<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_pre_out.gif\' style=\'cursor:pointer;\' onclick=\'_moveYear(-1);\' onmouseover=\'this.src="../image/btn/btn_pre_over.gif";\' onmouseout=\'this.src="../image/btn/btn_pre_out.gif";\'></div>
			<div style=\'float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;\' id=\'year\'>'.$year.'</div>
			<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_next_out.gif\' style=\'cursor:pointer;\' onclick=\'_moveYear(1);\' onmouseover=\'this.src="../image/btn/btn_next_over.gif";\' onmouseout=\'this.src="../image/btn/btn_next_out.gif";\'></div>
			</div>';
	$str = '년도';
	
	$tag = $myF->_btn_month($month, '_moveMonth(', ');', null, true);
	$btn = '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'plan_save();\'>저장</button></span>';

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
						<td class=\'last\'>'.$tab.'</td>
						<td class=\'last\'>'.$tag.'</td>
						<td class=\'right last\'>'.$btn.'</td>
					</tr>
				</tbody>
			 </table>';

	echo $html;
?>


<div id='listBody' style='display:none;'></div>

<input id='code' name='name' type='hidden' value='<?=$code?>'>
<input id='month' name='name' type='hidden' value='<?=$month?>'>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>