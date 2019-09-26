<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$code = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 9;

		var title = '';

		switch(opener.type){
			case '21_POP':
				title = opener.year+'년 사업계획서';
				break;

			default:
				self.close();
				return;
		}

		__init_form(document.f);

		$('#lsTitle').text(title);

		if (opener.sr == 'S'){
			$(__GetTagObject($('#subNm'),'TR')).show();
		}

		setTimeout('lfLoadTitle()',100);
		setTimeout('lfLoadPlan()',200);
	});

	function lfLoadTitle(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type
			,	'year':opener.year
			,	'code':opener.code
			,	'sr':opener.sr
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseStr(data);

				$('#mstNm').text(col['mstNm']);
				$('#proNm').text(col['proNm']);
				$('#svcNm').text(col['svcNm']);
				$('#lblUnitGbn').text(col['unit'] == '1' ? '명' : '회');

				if (col['subNm']){
					$('#subNm').text(col['subNm']);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadPlan(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type+'_1'
			,	'year':opener.year
			,	'code':opener.code
			,	'sr':opener.sr
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseStr(data);

				$('#txtTarget').val(col['target']);
				$('#txtBudget').val(col['budget']);
				$('#txtCnt').val(col['cnt']);
				$('#txtCont').val(col['cont']);
				$('#txtEffect').val(col['effect']);
				$('#txtEval').val(col['eval']);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfApply(){
		if (!$('#txtTarget').val()){
			alert('계획목표를 입력하여 주십시오.');
			$('#txtTarget').focus();
			return;
		}

		if (!$('#txtCnt').val()){
			alert('계획횟수를 입력하여 주십시오.');
			$('#txtCnt').focus();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':opener.type
			,	'sr':opener.sr
			,	'year':opener.year
			,	'code':opener.code
			,	'target':__str2num($('#txtTarget').val())
			,	'budget':__str2num($('#txtBudget').val())
			,	'cnt':$('#txtCnt').val()
			,	'cont':$('#txtCont').val()
			,	'effect':$('#txtEffect').val()
			,	'eval':$('#txtEval').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					opener.result = 1;
					opener.target = $('#txtTarget').val();
					opener.budget = $('#txtBudget').val();
					opener.cnt = $('#txtCnt').val();
					opener.cont = $('#txtCont').val();
					opener.effect = $('#txtEffect').val();
					opener.eval = $('#txtEval').val();

					self.close();
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

<form id="f" name="f" method="post">
<div id="lsTitle" class="title title_border"></div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>대분류(사업)</th>
			<td><div id="mstNm" class="left"></div></td>
		</tr>
		<tr>
			<th>중분류(프로그램)</th>
			<td><div id="proNm" class="left"></div></td>
		</tr>
		<tr>
			<th>소분류(서비스)</th>
			<td><div id="svcNm" class="left"></div></td>
		</tr>
		<tr style="display:none;">
			<th>상세서비스명</th>
			<td><div id="subNm" class="left"></div></td>
		</tr>
		<tr>
			<th>계획 목표</th>
			<td>
				<input id="txtTarget" name="txt" type="text" class="number" value="0" style="width:70px; margin-right:0;">
				<span id="lblUnitGbn"></span>
			</td>
		</tr>
		<tr>
			<th>계획 예산</th>
			<td>
				<input id="txtBudget" name="txt" type="text" class="number" value="0" style="width:70px;">
			</td>
		</tr>
		<tr>
			<th>계획 횟수</th>
			<td>
				<input id="txtCnt" name="txt" type="text" value="" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>사업내용</th>
			<td>
				<textarea id="txtCont" name="txts" style="width:100%; height:100px;"></textarea>
			</td>
		</tr>
		<tr>
			<th>기대효과</th>
			<td>
				<textarea id="txtEffect" name="txts" style="width:100%; height:80px;"></textarea>
			</td>
		</tr>
		<tr>
			<th>수행 및 평가도구</th>
			<td>
				<textarea id="txtEval" name="txts" style="width:100%; height:80px;"></textarea>
			</td>
		</tr>
		<tr>
			<td class="center bottom" colspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span>
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
</form>

<?
	include_once('../inc/_footer.php');
?>