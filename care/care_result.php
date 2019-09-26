<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$year	= Date('Y');
	$month	= Date('m');
	$today	= Date('Y-m-d');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:radio[name="optOrder"]').unbind('click').bind('click',function(){
			lfSearch();
		});

		lfSearch();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#yymm').attr('year'));

		year += pos;

		$('#yymm').attr('year',year).text(year);

		lfSearch();
	}

	function lfMonth(month){
		$('div[id^="btnMonth_"]').removeClass('my_month_y').addClass('my_month_1');
		$('#btnMonth_'+month).removeClass('my_month_1').addClass('my_month_y');

		month = (__str2num(month) < 10 ? '0' : '')+__str2num(month);

		$('#yymm').attr('month',month);

		lfSearch();
	}

	function lfSearch(){
		if (!$('#txtFrom').val()){
			alert('조회기간을 입력하여 주십시오.');
			$('#txtFrom').focus();
			return;
		}

		if (!$('#txtTo').val()){
			alert('조회기간을 입력하여 주십시오.');
			$('#txtTo').focus();
			return;
		}

		if ($('#txtFrom').val() > $('#txtTo').val()){
			alert('조회기간 입력오류입니다. 확인 후 다시 입력하여 주십시오.');
			$('#txtTo').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./care_result_search.php'
		,	data:{
				'SR':$('#sr').val()
			,	'year':$('#yymm').attr('year')
			,	'month':$('#yymm').attr('month')
			,	'from':$('#txtFrom').val()
			,	'to':$('#txtTo').val()
			,	'order':$('input:radio[name="optOrder"]:checked').val()
			,	'resultGbn':$('#cboResultGbn').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	complete:function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfContent(obj){
		var rowId = $(obj).parent().parent().attr('id');
		var top = $(obj).parent().offset().top - 1;
		var left = $(obj).parent().offset().left;
		var width = $(obj).parent().width() + 2;

		$('#rowId').val(rowId);
		$('#txtContent').val($(obj).text());
		$('#divContent').css('top',top).css('left',left).css('width',width).show();
	}

	function lfContentSave(){
		var rowId = $('#rowId').val();
		var obj = $('#'+rowId);
		var para = $(obj).attr('para');

		$.ajax({
			type:'POST'
		,	url:'./care_result_save.php'
		,	data:{
				'SR':$('#sr').val()
			,	'content':$('#txtContent').val()
			,	'no':$(obj).attr('no')
			,	'stat':$(obj).attr('stat')
			,	'para':para
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 'ERROR'){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (!isNaN(result)){
					alert('정상적으로 처리되었습니다.');
					$(obj).attr('no',result).attr('stat','1');
					$('div:first',$('td',$(obj)).eq(9)).text($('#txtContent').val());
					$('#divContent').hide();
				}else{
					alert(result);
				}
			}
		,	complete:function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfPictureReg(obj){
		var rowId = $(obj).parent().parent().attr('id');
		var para = $(obj).parent().parent().attr('para');
		var no = $(obj).parent().parent().attr('no');
		var width = 1024;
		var height = 768;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './care_result_pic.php';
		var win = window.open('', 'CARE_RESULT', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'rowId':rowId
			,	'para':para
			,	'no':no
			,	'SR':$('#sr').val()
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

		form.setAttribute('target', 'CARE_RESULT');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfPictureView(rowId,no,img){
		var obj = $('#btnPic',$('#'+rowId));

		if (img){
			$(obj).html('<img src="../image/f_list.gif">');
		}else{
			$(obj).html('등록');
		}
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'SR':$('#sr').val()
			,	'from':$('#txtFrom').val()
			,	'to':$('#txtTo').val()
			,	'order':$('input:radio[name="optOrder"]:checked').val()
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

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_result_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">실적등록(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="560px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기간</th>
			<td class="">
				<input id="txtFrom" type="text" value="<?=$today;?>" class="date"> ~
				<input id="txtTo" type="text" value="<?=$today;?>" class="date">
			</td>
			<!--
				<th class="center">년월</th>
				<td class="left">
					<div style="float:left; width:auto;">
						<div style="width:auto; margin-top:1px;">
							<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
							<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="yymm" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
							<div style="float:left; width:auto; padding-top:2px; margin-right:5px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
						</div>
						<div style="width:auto;"><?=$myF->_btn_month($month,'lfMonth(');?></div>
					</div>
				</td>
			-->
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel();">Excel</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">정렬</th>
			<td class="">
				<input id="optOrder1" name="optOrder" type="radio" class="radio" value="1" checked><label for="optOrder1">일자/시간순</label>
				<input id="optOrder2" name="optOrder" type="radio" class="radio" value="2"><label for="optOrder2"><?=($type == 41 ? '대상자순' : '직원명순');?></label>
				<input id="optOrder3" name="optOrder" type="radio" class="radio" value="3"><label for="optOrder3">서비스순</label>
				<input id="optOrder4" name="optOrder" type="radio" class="radio" value="4"><label for="optOrder4"><?=($type == 41 ? '자원순' : '대상자순');?></label>
			</td>
		</tr>
		<tr>
			<th class="center">선택</th>
			<td class="last" colspan="2">
				<select id="cboResultGbn" style="width:auto;" onclick="lfSearch();">
					<option value="">실적 등록/미등록 조회</option>
					<option value="Y">실적 등록 일정만 조회</option>
					<option value="N">실적 미등록 일정만 조회</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="40px">
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="30px">
		<col width="100px">
		<col width="100px">
		<col width="70px">
		<col width="200px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">시간</th>
			<th class="head">대상자</th>
			<th class="head">생년월일</th>
			<th class="head">성별</th>
			<th class="head">서비스</th>
			<th class="head">자원</th>
			<th class="head">담당</th>
			<th class="head">내용</th>
			<th class="head">사진</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="50">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<div id="divContent" style="position:absolute; z-index:10; top:0; left:0; width:0; height:0; background-color:#FFFFFF; display:none;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<td class="center"><textarea id="txtContent" style="width:100%; height:50px; margin:0; border:none;"></textarea></td>
			</tr>
			<tr>
				<th class="center">
					<span class="btn_pack m"><button type="button" onclick="lfContentSave();">저장</button></span>
					<!--<span class="btn_pack m"><button type="button" onclick="">삭제</button></span>-->
					<span class="btn_pack m"><button type="button" onclick="$('#divContent').hide();">닫기</button></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<input id="rowId" type="hidden">
<?
	include_once('../inc/_db_close.php');
?>