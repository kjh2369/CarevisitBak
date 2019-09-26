<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
	});

	function lfFindOrg(){
		$.ajax({
			type:'POST'
		,	url:'./center_find.php'
		,	data:{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LOCAL_POP_DATA').html(html);
				$('#ID_LOCAL_POP')
					.css('left','50px')
					.css('top','80px')
					.css('width','700px')
					.css('height','300px')
					.show();
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfFindOrgSet(obj){
		$('#ID_CELL_ORG').attr('orgNo', $(obj).attr('orgNo')).text($(obj).attr('orgNm')+'('+$(obj).attr('orgNo')+')');
		$('#ID_LOCAL_POP_DATA').html(html);
		$('#ID_CELL_CMS').text($(obj).attr('cmsNo'));
		$('#ID_LOCAL_POP').hide();
	}

	//입금등록
	function lfSave(){
		var inGbn = $('input:radio[name="optInGbn"]:checked').val();

		if (!$('#ID_CELL_ORG').attr('orgNo')){
			lfFindOrg();
			return;
		}

		if (inGbn == '1'){
			if (!$('#txtCmsNo').val()){
				alert('CMS 번호를 입력하여 주십시오.');
				$('#txtCmsNo').focus();
				return;
			}
		}

		if (!$('#txtClaimDt').val()){
			alert('청구일자를 입력하여 주십시오.');
			$('#txtClaimDt').focus();
			return;
		}

		if (!$('#txtInDt').val()){
			alert('입금일자를 입력하여 주십시오.');
			$('txtInDt').focus();
			return;
		}

		if (__str2num($('#txtInAmt').val()) == 0){
			alert('입금금액을 입력하여 주십시오.');
			$('#txtInAmt').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./center_acct_in_set.php'
		,	data:{
				'orgNo'	:$('#ID_CELL_ORG').attr('orgNo')
			,	'cmsNo'	:inGbn == '1' ? $('#txtCmsNo').val() : ''
			,	'cmsCom':inGbn == '1' ? $('input:radio[name="optCmsCom"]:checked').val() : ''
			,	'cmsDt'	:$('#txtClaimDt').val()
			,	'inDt'	:$('#txtInDt').val()
			,	'inAmt'	:$('#txtInAmt').val()
			,	'memNo'	:$('#txtMemNo').val()
			,	'memo'	:$('#txtMemo').val()
			,	'inGbn'	:inGbn
			,	'mode'	:'MANUAL'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}

				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfSetInGbn(){
		var inGbn = $('input:radio[name="optInGbn"]:checked').val();

		$('input:radio[name="optCmsCom"], #txtCmsNo, #txtMemNo').attr('disabled',inGbn == '1' ? false : true);
	}
</script>

<div class="title title_border">기관선택</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관찾기</th>
			<td class="last">
				<div id="ID_CELL_ORG" class="left" style="cursor:default; background-color:#FFFFD2;" orgNo="" onclick="lfFindOrg();">
					<span style="color:#CC723D;">-기관을 선택하여 주십시오.</span>
				</div>
			</td>
		</tr>
		<tr>
			<th class="center">CMS 번호</th>
			<td class="last">
				<div id="ID_CELL_CMS" class="left"></div>
			</td>
		</tr>
	</tbody>
</table>

<div class="title title_border">CMS 등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">입금구분</th>
			<td class="last">
				<label><input name="optInGbn" type="radio" class="radio" value="1" onclick="lfSetInGbn();" checked>CMS</label>
				<label><input name="optInGbn" type="radio" class="radio" value="2" onclick="lfSetInGbn();">무통장</label>
			</td>
		</tr>
		<tr>
			<th class="center">CMS기관</th>
			<td class="last"><?
				$sql = 'SELECT	gbn_cd,	gbn_nm
						FROM	cv_gbn
						WHERE	gbn_id = \'CCM\'
						ORDER	BY ob_seq, gbn_cd';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input name="optCmsCom" type="radio" class="radio" value="<?=$row['gbn_cd'];?>" <?=$i == 0 ? 'checked' : '';?>><?=$row['gbn_nm'];?></label><?
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="center">CMS번호</th>
			<td class="last">
				<input id="txtCmsNo" type="text" value="" class="no_string">
			</td>
		</tr>
		<tr>
			<th class="center">청구일자</th>
			<td class="last">
				<input id="txtClaimDt" type="text" value="" class="date">
			</td>
		</tr>
		<tr>
			<th class="center">입금일자</th>
			<td class="last">
				<input id="txtInDt" type="text" value="" class="date">
			</td>
		</tr>
		<tr>
			<th class="center">입금금액</th>
			<td class="last">
				<input id="txtInAmt" type="text" value="" class="number">
			</td>
		</tr>
		<tr>
			<th class="center">회원번호</th>
			<td class="last">
				<input id="txtMemNo" type="text" value="" class="no_string">
			</td>
		</tr>
		<tr>
			<th class="center">메모</th>
			<td class="last">
				<input id="txtMemo" type="text" value="" style="width:100%;">
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" style="padding-top:10px;" colspan="2">
				<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
				<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
			</td>
		</tr>
	</tfoot>
</table>

<div id="ID_LOCAL_POP" style="position:absolute; left:0; top:0; width:0; height:0; display:none; z-index:11; background:url('../image/tmp_bg.png'); border:2px solid #4374D9;">
	<div style="position:absolute; text-align:right; width:100%; top:-20px; left:-5px;">
		<a href="#" onclick="$('#ID_LOCAL_POP').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<div id="ID_LOCAL_POP_DATA" style="position:absolute; width:100%;"></div>
</div>
<?
	include_once('../inc/_footer.php');
?>