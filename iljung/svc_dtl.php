<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		__fileUploadInit($('#frmFile'), 'fileUploadCallback');

		lfResize();
		lfLoadClient();
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

		if (height > menu){
			$(obj).height(height - 1);
		}else{
			$(obj).height($('li:last',$('#left_box')).offset().top + $('li:last',$('#left_box')).height() - $(obj).offset().top + 10);
		}
	}

	function lfLoadClient(){
		var year	= $('#lblYYMM').attr('year');
		var month	= $('#lblYYMM').attr('month');

		month = (month < 10 ? '0' : '')+month;

		$.ajax({
			type:'POST'
		,	url:'../find/client_list.php'
		,	data:{
				'svcCd'	:'0'
			,	'date'	:year+month
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

					lfSearch($(this).attr('key'),$(this).attr('jumin'),$(this).attr('appNo'));
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

	function lfSearch(key,jumin,appNo){
		var year	= $('#lblYYMM').attr('year');
		var month	= $('#lblYYMM').attr('month');

		month = (month < 10 ? '0' : '')+month;

		$('input[name="appNo"]').val(key);

		$.ajax({
			type:'POST'
		,	url:'./svc_dtl_search.php'
		,	data:{
				'year'	:year
			,	'month'	:month
			,	'jumin'	:jumin
			,	'appNo'	:appNo
			,	'key'	:key
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_BODY').html(html);
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

	function fileUpload(){
		var year	= $('#lblYYMM').attr('year');
		var month	= $('#lblYYMM').attr('month');

		month = (month < 10 ? '0' : '')+month;

		var frm = $('#frmFile');
			frm.attr('action', './svc_dtl_upload.php?yymm='+year+month);
			frm.submit();
	}

	function fileUploadCallback(data, state){
		if (__fileUploadCallback(data, state)){
			alert('정상적으로 처리되었습니다.');
		}else{
			alert('저장중 오류가 발생하였습니다.\n관리자에게 문의하여 주십시오.');
		}
	}

	function lfAttchFileRemove(jumin,appNo,key,index,exp){
		if (!confirm('삭제후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		var year	= $('#lblYYMM').attr('year');
		var month	= $('#lblYYMM').attr('month');

		month = (month < 10 ? '0' : '')+month;

		$.ajax({
			type:'POST'
		,	url:'./svc_dtl_remove.php'
		,	data:{
				'year'	:year
			,	'month'	:month
			,	'key'	:key
			,	'index'	:index
			,	'exp'	:exp
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					//lfSearch(key);
					lfSearch(key,jumin,appNo)
				}
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
<div class="title title_border">공단서비스 개별내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1,'lfLoadClient()');" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1,'lfLoadClient()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM"),"lfLoadClient()");');?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="200px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">수급자</th>
			<th class="center last">첨부파일</th>
		</tr>
		<tr>
			<td class="bottom">
				<div id="ID_CLIENT_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;"></div>
			</td>
			<td class="top bottom last" style="padding:10px;">
				<div>※JPG, PNG, GIF, BMP 파일만 업로드하여 주십시오.</div>
				<form id="frmFile" name="frmFile" method="post" enctype="multipart/form-data">
					<div id="ID_BODY" style="border:1px solid #CCCCCC;"></div>
					<input id="appNo" name="appNo" type="hidden" value="">
				</form>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>