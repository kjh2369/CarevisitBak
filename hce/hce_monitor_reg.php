<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'EV5\'
			AND		use_yn	= \'Y\'';

	$evaluation = $conn->_fetch_array($sql,'code');
	
	$wrtDt	= Date('Y-m-d');
	$mntrSeq= $_GET['seq'];
	$copyYn = $_GET['copyYn'];
	
	
	if (Empty($mntrSeq)){
		$sql = 'SELECT	IFNULL(MAX(mntr_seq),0)+1
				FROM	hce_monitor
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$mntrSeq = $conn->get_data($sql);
	}

	$sql = 'SELECT  count(*)
			  FROM	hce_monitor
			 WHERE	org_no	= \''.$orgNo.'\'
			   AND	org_type= \''.$hce->SR.'\'
			   AND	IPIN	= \''.$hce->IPIN.'\'
			   AND	rcpt_seq= \''.$hce->rcpt.'\'
			   AND  del_flag= \'N\'';
	$Cnt = $conn->get_data($sql);
?>
<script type="text/javascript">
	var returnObj;

	$(document).ready(function(){
		setTimeout('lfSearch()',200);
	});

	function lfMemPer(){
		if (!returnObj) return;

		var obj = __parseVal(returnObj);

		$('#txtPer').attr('jumin',obj['jumin']).val(obj['name']);
	}

	function lfMemIpt(){
		if (!returnObj) return;

		var obj = __parseVal(returnObj);

		$('#txtIpt').attr('jumin',obj['jumin']).val(obj['name']);
	}

	//조회
	function lfSearch(){
		
		$.ajax({
			type:'POST'
		,	url:'./hce_monitor_reg_search.php'
		,	data:{
				'seq':$('#mntrSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				/*
				if (!data){
					if ('<?=$Cnt;?>' == 0){
						$('#lblMntrType1').text('■');
						$('#lblMntrType2').text('□');
					}else{
						$('#lblMntrType1').text('□');
						$('#lblMntrType2').text('■');
					}
					return;
				}
				*/

				var col = __parseVal(data);

				if (!data){
					if ('<?=$Cnt;?>' == 0){
						if (!col['type']) col['type'] = '1';
					}else{
						if (!col['type']) col['type'] = '2';
					}
					
					col['type']				= '';
					col['gbn']				= '1';
					col['scheduleGbn']		= '1';
					col['fullnessGbn']		= '1';
					col['perinchargeGbn']	= '1';
					col['monitorRst']		= '1';
				}else{
					$('#txtWrtDt').val(__getDate(col['date']));
				}

				/*
				$('#lblMntrType1').text('□');
				$('#lblMntrType2').text('□');
				$('#lblMntrType'+col['type']).text('■');
				*/

				$('#optMntrType'+col['type']).attr('checked',true);
				$('#optRec'+col['gbn']).attr('checked',true);
				$('#txtPer').attr('jumin',col['perJumin']).val(col['perNm']);
				$('#txtIpt').attr('jumin',col['ispJumin']).val(col['ispNm']);
				$('#optSchedule'+col['scheduleGbn']).attr('checked',true);
				$('#txtSchedule').val(col['scheduleStr']);
				$('#optFullness'+col['fullnessGbn']).attr('checked',true);
				$('#txtFullness').val(col['fullnessStr']);
				$('#otpPerincharge'+col['perinchargeGbn']).attr('checked',true);
				$('#txtPerincharge').val(col['perinchargeStr']);
				$('#txtAbility').val(col['abilityStr']);
				$('#txtLifeEnv').val(col['lifeEnvStr']);
				$('#txtExtDiscomfort').val(col['extDiscomfortStr']);
				$('#optRst'+col['monitorRst']).attr('checked',true);
				$('#txtExtDetail').val(col['extDetailStr']);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//저장
	function lfSaveSub(){
		
		if (!$('input:radio[name="optMntrType"]:checked').val()){
			alert('모니터링구분을 선택하여 주십시오.');
			return;
		}

		if (!$('input:radio[name="optRec"]:checked').val()){
			alert('작성구분을 선택하여 주십시오.');
			return;
		}

		if (!$('#txtWrtDt').val()){
			alert('작성일자를 입력하여 주십시오.');
			$('#txtWrtDt').focus();
			return;
		}

		if (!$('#txtPer').val()){
			alert('전담담당자를 입력하여 주십시오.');
			lfMemFind('lfMemPer');
			return;
		}

		if (!$('#txtIpt').val()){
			alert('조사자를 입력하여 주십시오.');
			lfMemFind('lfMemIpt');
			return;
		}

		var data = {};

		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('input:radio').each(function(){
			var name= $(this).attr('name');
			var val	= $('input:radio[name="'+name+'"]:checked').val();

			if (!val) val = '';

			data[name] = val;
		});

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});
		
		if ('<?=$copyYn;?>' == 'Y') data['mntrSeq'] = '';

		$.ajax({
			type:'POST'
		,	url:'./hce_monitor_reg_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(mntrSeq){
		//if ('<?=$debug;?>' != '1') return;

		if (!confirm('삭제후 복구가 불가능합니다. 정말로 삭제하시곘습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./hce_monitor_reg_delete.php'
		,	data:{
				'mntrSeq':mntrSeq
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();

					var form = document.createElement('form');
						form.setAttribute('target', 'frmBody');
						form.setAttribute('method', 'post');
						form.setAttribute('action', '../hce/hce_body.php?sr=<?=$sr;?>&type=101');

					document.body.appendChild(form);

					form.submit();
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}


	function lfCopy(){
		var objModal = new Object();
		var url      = './hce_copy.php?type=102';
		var style    = 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		//objModal.code  = $('#code').val();
		objModal.type  = '102';


		window.showModalDialog(url, objModal, style);

		var result = objModal.para;

		if (!result) return;

		var arr = result.split('&');
		var val = new Array();

		for(var i=0; i<arr.length; i++){
			var tmp = arr[i].split('=');

			val[tmp[0]] = tmp[1];
		}


		location.href = '../hce/hce_body.php?sr=S&type=102&seq='+val['seq']+'&r_seq='+val['r_seq']+'&copyYn=Y';

		//$('#strCname').text(val['name']);
		//$('#param').attr('value', 'jumin='+val['jumin']);
	}

</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right bottom last">
				<? 
				
				if ($copyYn != 'Y' && !$_GET['seq']){?>
						<span class="btn_pack m"><button type="button" onclick="lfCopy();">복사</button></span><?
					}
				?>
				<span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfSaveSub(); return false;">저장</a></span>
				<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="lfDelete('<?=$mntrSeq;?>');">삭제</a></span>
				<span class="btn_pack m"><span class="list"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=101" target="frmBody">리스트</a></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','','<?=$mntrSeq;?>');">출력</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue" style="border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="90px">
			<col width="90px">
			<col width="70px">
			<col>
		</colgroup>
		<tbody>
			<tr>

				<th class="head">모니터링구분</th>
				<td class="left">
					<label><input id="optMntrType1" name="optMntrType" type="radio" value="1" class="radio">최초</label>
					<label><input id="optMntrType2" name="optMntrType" type="radio" value="2" class="radio">정기</label>
					
					<!--span id="lblMntrType1">□</span>
					<span>최초</span>
					<span id="lblMntrType2">■</span>
					<span>정기</span-->
				</td>
				<th class="head">작성구분</th>
				<td class="last"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type	= \'CT\'
							AND		use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<label><input id="optRec<?=$row['code'];?>" name="optRec" type="radio" value="<?=$row['code'];?>" class="radio"><?=$row['name'];?></label><?
					}

					$conn->row_free();?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="50px">
			<col width="80px">
			<col width="135px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">작성일자</th>
				<td><input id="txtWrtDt" type="text" value="<?=$wrtDt;?>" class="date"></td>
				<th class="head">전담담당자</th>
				<td>
					<div style="float:left; width:auto; height:23px; margin-left:3px;"><span class="btn_pack find" onclick="lfMemFind('lfMemPer');"></span></div>
					<div style="float:left; width:auto; margin-top:1px;"><input id="txtPer" type="text" jumin="" value="" alt="not" readonly style="margin-left:0;"></div>
				</td>
				<th class="head">조사자</th>
				<td class="last">
					<div style="float:left; width:auto; height:23px; margin-left:3px;"><span class="btn_pack find" onclick="lfMemFind('lfMemIpt');"></span></div>
					<div style="float:left; width:auto; margin-top:1px;"><input id="txtIpt" type="text" jumin="" value="" alt="not" readonly style="margin-left:0;"></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="divBody" class="my_border_blue" style="height:200px; overflow-x:hidden; overflow-y:auto; border-top:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="130px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="bold last" colspan="2">1. 이용자 의견조사</th>
			</tr>
			<tr>
				<th class="last" colspan="2" style="padding-left:20px;">1) 서비스 모니터링</th>
			</tr>
			<tr>
				<th class="head">문항</th>
				<th class="head last">구체적인 내용</th>
			</tr>
			<tr>
				<th class="left" rowspan="2">서비스 제공 일정<br>지키는 정도</th>
				<td class="last"><?
					foreach($evaluation as $ev){?>
						<label><input id="optSchedule<?=$ev['code'];?>" name="optSchedule" type="radio" value="<?=$ev['code'];?>" class="radio"><?=$ev['name'];?></label><?
					}?>
				</td>
			</tr>
			<tr>
				<td class="last"><textarea id="txtSchedule" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="left" rowspan="2">서비스 내용<br>정확성 및 충실도</th>
				<td class="last"><?
					foreach($evaluation as $ev){?>
						<label><input id="optFullness<?=$ev['code'];?>" name="optFullness" type="radio" value="<?=$ev['code'];?>" class="radio"><?=$ev['name'];?></label><?
					}?>
				</td>
			</tr>
			<tr>
				<td class="last"><textarea id="txtFullness" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="left" rowspan="2">수발 담당자에<br>대한 만족도</th>
				<td class="last"><?
					foreach($evaluation as $ev){?>
						<label><input id="otpPerincharge<?=$ev['code'];?>" name="otpPerincharge" type="radio" value="<?=$ev['code'];?>" class="radio"><?=$ev['name'];?></label><?
					}?>
				</td>
			</tr>
			<tr>
				<td class="last"><textarea id="txtPerincharge" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="last" colspan="2" style="padding-left:20px;">2) 욕구변화 모니터링</th>
			</tr>
			<tr>
				<th class="head">문항</th>
				<th class="head last">구체적인 내용</th>
			</tr>
			<tr>
				<th class="left">기능상태 변화</th>
				<td class="last"><textarea id="txtAbility" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="left">생활환경 및<br>수발능력변화</th>
				<td class="last"><textarea id="txtLifeEnv" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="last" colspan="2" style="padding-left:20px;">3) 기타 불편사항 모니터링</th>
			</tr>
			<tr>
				<td class="last" colspan="2"><textarea id="txtExtDiscomfort" style="width:100%; height:50px;"></textarea></td>
			</tr>
			<tr>
				<th class="bold last" colspan="2">2. 서비스 종합계획</th>
			</tr>
			<tr>
				<th class="left">모니터링 결과 변영</th>
				<td class="last">
					<label><input id="optRst1" name="optRst" type="radio" class="radio" value="1">서비스 유지</label>
					<label><input id="optRst2" name="optRst" type="radio" class="radio" value="2">서비스 계획 변경</label>
					<label><input id="optRst3" name="optRst" type="radio" class="radio" value="3">재 방문 면담</label>
				</td>
			</tr>
			<tr>
				<th class="left">기타 구체적인 기록</th>
				<td class="last"><textarea id="txtExtDetail" style="width:100%; height:50px;"></textarea></td>
			</tr>
		</tbody>
	</table>
</div>
<input id="Cnt" type="hidden" value="<?=$Cnt;?>">
<input id="mntrSeq" type="hidden" value="<?=$mntrSeq;?>">
<?
	Unset($evaluation);
	include_once('../inc/_db_close.php');
?>