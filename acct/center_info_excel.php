<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		__fileUploadInit($('#frmFile'), 'fileUploadCallback');

		$('input:checkbox').click(function(){
			if ($(this).attr('name') == 'chkDateYn'){
				if ($(this).attr('checked')){
					$('input:checkbox[name="chkRemoveYn"]').attr('checked', true).attr('disabled', true);
				}else{
					$('input:checkbox[name="chkRemoveYn"]').attr('disabled', false);
				}
			}
		});
	});

	function fileUpload(gbn){
		if ('<?=$debug;?>' == '1' || '<?=$gDomain;?>' == 'dolvoin.net'){
			if (!$('#filename').val()){
				alert('엑셀파일을 선택하여 주십시오.');
				$('#filename').focus();
				return;
			}

			if (!confirm($('#yymm').attr('year')+'년 '+$('#yymm').attr('month')+'월 일정계획이 맞습니까?')) return;

			$.ajax({
				type:'POST'
			,	url:'./plan_excel_chk.php'
			,	data:{
					'year':$('#yymm').attr('year')
				,	'month':$('#yymm').attr('month')
				}
			,	beforeSend:function(){
				}
			,	success:function(result){
					var uplode = true;

					if (result){
						var val = __parseVal(result);
						var planCnt = __str2num(val['planCnt']);
						var confCnt = __str2num(val['confCnt']);

						if (planCnt > confCnt){
							var msg = planCnt+'명의 수급자중 '+confCnt+'명의 실적이 등록되어 있습니다.\n일정계획을 다시 업로드 하시면 기존의 실적이 모두 삭제됩니다.\n 일정계획을 업로드 하시겠습니까?';

							if (!confirm(msg)) uplode = false;
						}
					}

					if (uplode){
						var exp = $('#filename').val().split('.');

						if (exp[exp.length-1].toLowerCase() != 'xlsx'){
							alert('EXCEL 파일을 선택하여 주십시오.');
							return;
						}

						if (!gbn) gbn = 'UPLOAD';

						var frm = $('#frmFile');
							frm.attr('action', './plan_excel_upload.php?gbn='+gbn+'&year='+$('#yymm').attr('year')+'&month='+$('#yymm').attr('month'));
							frm.submit();

						$('#msgBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'>'+__get_loading()+'</div></center></div>');
					}
				}
			,	error: function (request, status, error){
					alert('[ERROR No.02]'
						 +'\nCODE : ' + request.status
						 +'\nSTAT : ' + status
						 +'\nMESSAGE : ' + request.responseText);
				}
			});
		}else{
			if (!$('#filename').val()){
				alert('엑셀파이을 선택하여 주십시오.');
				$('#filename').focus();
				return;
			}

			if (!confirm($('#yymm').attr('year')+'년 '+$('#yymm').attr('month')+'월 일정계획이 맞습니까?')) return;

			var exp = $('#filename').val().split('.');

			if (exp[exp.length-1].toLowerCase() != 'xlsx'){
				alert('EXCEL 파일을 선택하여 주십시오.');
				return;
			}

			if (!gbn) gbn = 'UPLOAD';

			var frm = $('#frmFile');
				frm.attr('action', './plan_excel_upload.php?gbn='+gbn+'&year='+$('#yymm').attr('year')+'&month='+$('#yymm').attr('month'));
				frm.submit();

			$('#msgBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'>'+__get_loading()+'</div></center></div>');
		}
	}

	function fileUploadCallback(result, state){
		if (!result){
			alert('정상적으로 처리되었습니다.');
		}else{
			if ('<?=$debug;?>' == '1'){
				$('#tblList').html(result).show();
			}else{
				if (result == 'ERROR'){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					$('#tblList').html(result);
				}
			}
		}
		//$('#tblList').html(html);
		$('#tempLodingBar').remove();
	}

	function lfMoveYear(pos){
		var year = __str2num($('#yymm').attr('year'));

		year += pos;

		$('#yymm').attr('year',year).text(year);
	}

	function lfMoveMonth(month){
		$('div[id^="btnMonth_"]').removeClass('my_month_y').addClass('my_month_1');
		$('#btnMonth_'+month).removeClass('my_month_1').addClass('my_month_y');
		$('#yymm').attr('month',month);
	}
</script>

<div class="title title_border">일정계획(공단엑셀)</div>

<form id="frmFile" name="frmFile" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left last" colspan="2">
				※ "공단 엑셀다운로드" - "일정계획"에서 "엑셀 다운로드"하여 다운로드한 엑셀파일을 업로드하여 주십시오.<br>
				※ 엑셀 업로드 시 다운로드 받은 엑셀파일의 이름을 변경하지 마시고 업로드 하여 주십시오.
			</td>
		</tr>
		<tr>
			<th>년월</th>
			<td class="last">
				<div style="float:left; width:auto;">
					<div class="left" style="width:auto; margin-top:1px;">
						<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
						<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="yymm" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
						<div style="float:left; width:auto; padding-top:2px; margin-right:5px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					</div>
					<div style="width:auto;"><?=$myF->_btn_month($month,'lfMoveMonth(');?></div>
				</div>
			</td>
		</tr>
		<tr>
			<th>파일선택</th>
			<td class="left last" colspan="2">
				<div style="float:left; width:auto;">
					<input type="file" name="filename" id="filename" style="width:250px; margin:0;">
					<span class="btn_pack m"><button type="button" onclick="fileUpload();">업로드</button></span><?
					if ($debug){?>
						<span class="btn_pack m"><button type="button" onclick="fileUpload('COMPARE');">일정비교</button></span><?
					}?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<div id="msgBody" style="display:none;"></div>
<div>
	<label><input name="chkAllMonDelYn" type="checkbox" class="checkbox" value="Y" checked>1.월일정을 모두 삭제합니다.</label><br>
	<label><input name="chkDateYn" type="checkbox" class="checkbox" value="Y" checked>2.해당 일자의 모든 일정을 삭제 후 등록합니다.</label><br>
	<label><input name="chkRemoveYn" type="checkbox" class="checkbox" value="Y" checked disabled="true">3.실적이 등록된 경우에도 계획을 변경합니다.(실적삭제 후 계획등록)</label><br><?
	if ($gDayAndNight){?>
		<label><input name="chkDanBipayYn" type="checkbox" class="checkbox" value="Y" checked>4.주야간보호 비급여내역을 삭제합니다.</label><br><?
	}?>
</div>
<div style="margin:10px; border:2px solid #0e69b0; padding:10px;">
	※ 공단 업무포털 "<b>기타메뉴</b>" - "<b>게시판</b>" - "<b>엑셀다운로드</b>"에서 "<b>일정계획</b>" 탭을 선택 조회 후 엑셀을 다운로드 받을 수 있습니다.<br>
	&nbsp;&nbsp;&nbsp;&nbsp;<b>일정계획을 다운 받은 후 파일이름을 변경하지 마시고 업로드 하시면 <span style="color:blue;">공단의 일정이 케어비지트로 저장</span>됩니다.</b><br>
	&nbsp;&nbsp;&nbsp;&nbsp;1번 : 모든 수급자의 월일정을 삭제 후 엑셀의 일정을 저장합니다.(실적이 등록된 일정포함)<br>
	&nbsp;&nbsp;&nbsp;&nbsp;2번 : 수급자의 일정이 있는 일자만 초기화 후 일정을 저장합니다.<br>
	&nbsp;&nbsp;&nbsp;&nbsp;3번 : 실적이 등록된 일정의 수정여부를 선택합니다.<br>
</div>
<div id="tblList"></div>
</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>