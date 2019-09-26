<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');
?>
<script type="text/javascript">
	var fixHeight = 0;

	$(document).ready(function(){
		fixHeight = $('#left_box').height(); //$(document).height() - $('#ID_DEPT_LIST').offset().top - $('#copyright').height();
		lfResize();
		lfDeptList();
		lfMemberList();
	});

	$(window).bind('resize', function(e){
		window.resizeEvt;
		$(window).resize(function(){
			clearTimeout(window.resizeEvt);
			window.resizeEvt = setTimeout(function(){
				lfResize();
			}, 250);
		});
	}).bind('scroll', function(){
		lfResize();
	});

	function lfResize(){
		lfResizeSub('#ID_DEPT_LIST');
		lfResizeSub('#ID_DEPT_ATTACH');
		lfResizeSub('#ID_MEMBER_LIST');
	}

	function lfResizeSub(tagId){
		var obj = __GetTagObject($(tagId),'DIV');
		var h = document.body.offsetHeight - $(obj).offset().top + $(window).scrollTop();

		if (h > fixHeight) h = fixHeight;

		$(obj).height(h);
	}

	function lfDeptList(){
		$.ajax({
			type:'POST',
			url:'./dept_list_search.php',
			data:{
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#ID_DEPT_LIST').html(html);
				$('tr',$('#ID_DEPT_LIST')).css('cursor','default').attr('selYn','N').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#FFFFFF');
				}).unbind('click').bind('click',function(){
					$('tr',$('#ID_DEPT_LIST')).attr('selYn','N').css('background-color','#FFFFFF');
					$(this).attr('selYn','Y').css('background-color','#FAF4C0');

					lfAttachList($(this).attr('deptCd'));
				});
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

	function lfAttachList(deptCd){
		if (!deptCd) return;

		$.ajax({
			type:'POST',
			url:'./dept_attach_search.php',
			data:{
				'deptCd':deptCd
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#ID_DEPT_ATTACH').html(html);
				$('tr',$('#ID_DEPT_ATTACH')).css('cursor','default').unbind('mouseover').bind('mouseover',function(){
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					$(this).css('background-color','#FFFFFF');
				}).unbind('click').bind('click',function(){
					alert($(this).attr('jumin'));
				});
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

	function lfMemberList(){
		$.ajax({
			type:'POST',
			url:'./dept_member_search.php',
			data:{
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#ID_MEMBER_LIST').html(html);
				$('tr',$('#ID_MEMBER_LIST')).css('cursor','default').unbind('mouseover').bind('mouseover',function(){
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					$(this).css('background-color','#FFFFFF');
				}).unbind('click').bind('click',function(){
					lfSetDeptAttach($(this).attr('jumin'));
				});
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

	function lfSetDeptAttach(jumin){
		var deptCd = $('tr[selYn="Y"]',$('#ID_DEPT_LIST')).attr('deptCd');

		if (!deptCd){
			alert('부서를 선택하여 주십시오.');
			return;
		}

		$.ajax({
			type:'POST',
			url:'./dept_attach_in.php',
			data:{
				'deptCd':deptCd
			,	'jumin':jumin
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(result){
				$('#tempLodingBar').remove();

				if (!result){
					$('tr[jumin="'+jumin+'"]',$('#ID_MEMBER_LIST')).remove();
					$('td',$('tr:first',$('#ID_MEMBER_LIST'))).css('border-top','none');
					lfAttachList(deptCd);
				}else{
					alert(result);
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
</script>
<div class="title title_border">부서명단관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="150px">
		<col width="350px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head last">부서리스트</th>
						</tr>
					</thead>
				</table>
				<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col>
						</colgroup>
						<tbody id="ID_DEPT_LIST"></tbody>
					</table>
				</div>
			</td>
			<td class="center top bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center last" colspan="2">부서 명단리스트</th>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">부서장</th>
							<td class="left last"></td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40px">
						<col width="80px">
						<col width="90px">
						<col width="50px">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">성명</th>
							<th class="head">생년월일</th>
							<th class="head">성별</th>
							<th class="head last">비고</th>
						</tr>
					</thead>
				</table>
				<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="80px">
							<col width="90px">
							<col width="50px">
							<col>
						</colgroup>
						<tbody id="ID_DEPT_ATTACH"></tbody>
					</table>
				</div>
			</td>
			<td class="center top bottom last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center last">직원 명단리스트</th>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40px">
						<col width="80px">
						<col width="90px">
						<col width="50px">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">성명</th>
							<th class="head">생년월일</th>
							<th class="head">성별</th>
							<th class="head last">비고</th>
						</tr>
					</thead>
				</table>
				<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="80px">
							<col width="90px">
							<col width="50px">
							<col>
						</colgroup>
						<tbody id="ID_MEMBER_LIST"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>