<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		lfResize();
		lfLoadClient();

		$('#ID_CLIENT_LIST,#ID_CLIENT_BODY').scroll(function(e){
			if (this.scrollTop + this.clientHeight >= this.scrollHeight){
				$('body').css('overflow','hidden');
			}else{
				$('body').css('overflow','');
			}
		});
	});

	$(window).bind('resize', function(e){
		window.resizeEvt;
		$(window).resize(function(){
			clearTimeout(window.resizeEvt);
			window.resizeEvt = setTimeout(function(){
				lfResize();
			}, 250);
		});
	});

	function lfResize(){
		var obj = $('#ID_CLIENT_LIST');
		var height = document.body.offsetHeight - $(obj).offset().top - $('#copyright').height();
		var menu = $('li:last',$('#left_box')).offset().top - $(obj).offset().top;
		var h = $('li:last',$('#left_box')).offset().top + $('li:last',$('#left_box')).height() - $(obj).offset().top;

		if (height > menu){
			$(obj).height(h + 10);
		}else{
			h -= ($('body').attr('scrollHeight') - document.body.offsetHeight - $('#copyright').height() - 10);
			$(obj).height(h);
		}

		$('#ID_CLIENT_BODY').height($(obj).height());
	}

	function lfLoadClient(){
		$.ajax({
			type:'POST'
		,	url:'../find/client_list.php'
		,	data:{
				'svcCd'	:'0'
			,	'type'	:'SIMPLE'
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

				$('#ID_BODY').html('');
			}
		,	success:function(html){
				$('#ID_CLIENT_LIST').html(html);

				$('div[id^="ID_ROW_"]',$('#ID_CLIENT_LIST')).attr('selYn','N')
				.unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#D9E5FF');
				})
				.unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#FFFFFF');
				})
				.unbind('click').bind('click',function(){
					$('div[id^="ID_ROW_"]',$('#ID_CLIENT_LIST')).attr('selYn','N').css('background-color','#FFFFFF');
					$(this).attr('selYn','Y').css('background-color','#FAF4C0');

					lfSearch($(this).attr('jumin'));
				});

				$('#tempLodingBar').remove();
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}

	function lfSearch(jumin){
		$.ajax({
			type:'POST'
		,	url:'./tgt_his_list.php'
		,	data:{
				'jumin':jumin
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_CLIENT_BODY').html(html);
				$('#tempLodingBar').remove();
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}
</script>
<div class="title title_border">고격변경이력</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="230px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">수급자</th>
			<th class="center last">변경이력</th>
		</tr>
		<tr>
			<td class="bottom">
				<div id="ID_CLIENT_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;"></div>
			</td>
			<td class="top bottom last">
				<div id="ID_CLIENT_BODY" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;"></div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>