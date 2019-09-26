<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$code = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfMemList()',150);
		setTimeout('lfMenuList()',300);

		var height = $(window).innerHeight(); //screen.availHeight;
		var top = $('#divLeft').offset().top;

		height = height - top - 10;

		$('#divLeft').height(height);
		$('#divRight').height(height);
	});

	//직원
	function lfMemList(){
		$.ajax({
			type :'POST'
		,	url  :'./permit_mem_list.php'
		,	data :{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tempLodingBar').remove();
				$('#tbodyMem').html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//메뉴
	function lfMenuList(){
		$.ajax({
			type :'POST'
		,	url  :'./permit_menu_list.php'
		,	data :{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tempLodingBar').remove();
				$('#tbodyMenu').html(html);

				$('input:checkbox[id^="chk"]').unbind('click').bind('click',function(){
					var id	= $(this).attr('id');
					var chk = $(this).attr('checked');

					$('input:checkbox[id^="'+id+'"]').attr('checked',chk);
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//조회
	function lfMenuGet(obj,jumin){
		$('.clsA').css('font-weight','normal').css('color','');
		$(obj).css('font-weight','bold').css('color','blue');

		$('#txtJumin').val(jumin);

		$.ajax({
			type :'POST'
		,	url  :'./permit_menu_search.php'
		,	data :{
				'jumin':$('#txtJumin').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				$('input:checkbox[id^="chk"]').attr('checked',false);

				var menu = data.split('/');

				for(var i=0; i<menu.length; i++){
					$('#chk'+menu[i]).attr('checked',true);
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//등록
	function lfSave(){
		if (!$('#txtJumin').val()){
			alert('권한을 설정할 직원을 선택하여 주십시오.');
			return;
		}

		var data = {};

		data['jumin'] = $('#txtJumin').val();

		$('.clsMenu:checked').each(function(){
			var id = $(this).attr('id').split('chk').join('');

			data['menu'] = (data['menu'] ? data['menu'] : '') + ('/'+id);
		});

		$.ajax({
			type :'POST'
		,	url  :'./permit_menu_save.php'
		,	data :data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리 되었습니다.');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">권한관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="250px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top"><?
				$colgroup = '	<col width="40px">
								<col>';?>
				<table class="my_table" style="width:100%;">
					<colgroup><?=$colgroup?></colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head last">성명</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="bottom last" colspan="2">
								<div id="divLeft" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
									<table class="my_table" style="width:100%;">
										<colgroup><?=$colgroup?></colgroup>
										<tbody id="tbodyMem"></tbody>
									</table>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="center top last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head last">
								<div style="float:right; width:auto;"><span class="btn_pack m"><a href="#" onclick="lfSave(); return false;">저장</a></span></div>
								<div style="float:center; width:auto;">권환설정</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="bottom last">
								<div id="divRight" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
									<table class="my_table" style="width:100%;">
										<colgroup>
											<col width="8px">
											<col width="230px">
											<col>
										</colgroup>
										<tbody id="tbodyMenu"></tbody>
									</table>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<input id="txtJumin" type="hidden" value="">
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>