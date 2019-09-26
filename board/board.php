<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
	$type = 'data';
?>
<style>
	body{
		overflow:hidden;
	}
</style>
<script type="text/javascript">
	var liPage = 0;

	$(document).ready(function(){
		lfResize();
		lfLoadBoardList();
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
		var obj = __GetTagObject($('#tbodyList'),'DIV');

		$('#divBrdList').height(document.body.offsetHeight - $('#divBrdList').offset().top - 1);
		$(obj).height(document.body.offsetHeight - $(obj).offset().top - $('#tfootBody').height() - 1);
	}

	function lfLoadBoardList(parent){
		$.ajax({
			type:'POST',
			url:'./board_menu.php',
			data:{
				'type':'<?=$type;?>'
			,	'parent':(parent ? parent : '0')
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				if (!parent){
					$('#divBrdList').html(html);
					$('#tempLodingBar').remove();

					$('div[id="ID_BRD"]',$('#divBrdList')).each(function(){
						lfLoadBoardList($(this).attr('cd'))
					});
				}else{
					$('div[id="ID_BRD_SUB"]',$('div[id="ID_BRD"][cd="'+parent+'"]',$('#divBrdList'))).html(html);
					$('#tempLodingBar').remove();
				}
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfSearch(page){
		if (!page) page = liPage;
		lfLoadBoard($('div[id="ID_BRD_ROW"][selYn="Y"]'),page);
	}

	function lfLoadBoard(obj,page){
		if (!obj) obj = $('div[id="ID_BRD_ROW"][selYn="Y"]');

		$('div[id="ID_BRD_ROW"]').attr('selYn','N');
		$(obj).attr('selYn','Y');
		$('a',$('div[id="ID_BRD_ROW"]')).css('font-weight','normal').css('color','');
		$('a',obj).css('font-weight','bold').css('color','BLUE');

		if (!page) page = 1;

		liPage = page;

		$.ajax({
			type:'POST',
			url:'./board_list.php',
			data:{
				'type'	:'<?=$type;?>'
			,	'cd'	:$(obj).parent().attr('cd')
			,	'page'	:page
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();

				//_lfSetPageList(maxCnt, page, pCnt);
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfReg(orgNo,id,mode){
		var code = $('div[id="ID_BRD_ROW"][selYn="Y"]').parent().attr('cd');

		if (!code){
			alert('자료실 구분을 선택하여 주십시오.');
			return;
		}

		if (!mode) mode = 'MOD';
		if (!orgNo) orgNo = '<?=$orgNo;?>';

		var width	= 800;
		var height	= 600;
		var top		= (screen.availHeight - height) / 2;
		var left	= (screen.availWidth - width) / 2;

		var target = 'POP_BOARD_REG';
		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './board_reg.php';
		var win = window.open('', target, option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type'	:'<?=$type;?>'
			,	'orgNo'	:orgNo
			,	'cd'	:code
			,	'id'	:id
			,	'mode'	:mode
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', target);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfSetNotice(obj,orgNo,id){
		var cd = $('div[id="ID_BRD_ROW"][selYn="Y"]').parent().attr('cd');

		if (!orgNo) orgNo = '<?=$orgNo;?>';

		$.ajax({
			type:'POST',
			url:'./board_set_notice.php',
			data:{
				'type'	:'<?=$type;?>'
			,	'orgNo'	:orgNo
			,	'cd'	:cd
			,	'id'	:id
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(result){
				if (__resultMsg(result)){
					if ($(obj).text() == '공지'){
						$(obj).text('취소').css('color','RED');
					}else{
						$(obj).text('공지').css('color','BLACK');
					}
				}
				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfRemove(obj,orgNo,id){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;
		if (!orgNo) orgNo = '<?=$orgNo;?>';

		var cd = $('div[id="ID_BRD_ROW"][selYn="Y"]').parent().attr('cd');

		$.ajax({
			type:'POST',
			url:'./board_remove.php',
			data:{
				'type'	:'<?=$type;?>'
			,	'orgNo'	:orgNo
			,	'cd'	:cd
			,	'id'	:id
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(result){
				if (__resultMsg(result)){
					lfSearch(liPage);
				}

				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}
</script>
<div class="title title_border">자료실</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">자료실 구분</th>
			<td class="center top last" rowspan="2"><?
				$colgroup = '
					<col width="40px">
					<col width="200px">
					<col width="120px">
					<col width="100px">
					<col width="70px">
					<col>';?>
				<table class="my_table" style="width:100%;">
					<colgroup><?=$colgroup;?></colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">제목</th>
							<th class="head">작성일시</th>
							<th class="head">작성자</th>
							<th class="head">파일</th>
							<th class="head last">비고</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="center top last" colspan="6">
								<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
								<table class="my_table" style="width:100%;">
									<colgroup><?=$colgroup;?></colgroup>
									<tbody id="tbodyList"></tbody>
								</table>
								</div>
							</td>
						</tr>
					</tbody>
					<tfoot id="tfootBody">
						<tr>
							<td class="center top bottom last" colspan="6">
								<div style="float:right; width:auto; margin-top:4px;"><?
									if ($_SESSION['userLevel'] == 'A' || $gDomain != 'vaerp.com'){?>
										<span class="btn_pack small"><button onclick="lfReg();">작성</button></span><?
									}?>
								</div>
								<div style="float:center; width:auto;"><?
									include_once('../inc/_page_script.php');?>
								</div>
							</td>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr>
			<td class="top center">
				<div id="divBrdList" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;"></div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>