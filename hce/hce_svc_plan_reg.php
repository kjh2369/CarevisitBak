<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	서비스계획서
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$orgType = '40';

	$planSeq= $_GET['seq'];
	$planDt = Date('Y-m-d');
	$copyYn = $_GET['copyYn'];
	$rcptSeq = $_GET['r_seq'];

	if (!$copyYn) $copyYn = 'N';
	

	if (!$planSeq){
		$sql = 'SELECT	IFNULL(MAX(plan_seq),0)+1
				FROM	hce_plan_sheet
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$planSeq = $conn->get_data($sql);
	}

	$sql = 'SELECT	meet_seq
			FROM	hce_plan_sheet
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		plan_seq= \''.$planSeq.'\'';

	$meetSeq = $conn->get_data($sql);

	if (!$meetSeq){
		$sql = 'SELECT	meet_seq
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		meet_gbn= \''.$infoDt['meet_gbn'].'\'
				ORDER	BY meet_dt DESC
				LIMIT	1';

		$meetSeq = $conn->get_data($sql);
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();
		setTimeout('lfSearch()',100);
	});

	function lfResize(){
		var top = $('#divBody').offset().top;
		var height = $(document).height();

		var h = height - top - 10;

		$('#divBody').height(h);
	}

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtPlaner').attr('jumin',obj['jumin']).val(obj['name']);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_find.php'
		,	data:{
				'type':'<?=$type;?>'
			,	'seq':$('#planSeq').val()
			,	'r_seq':'<?=$rcptSeq;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseVal(data);

				if(!col['seq']) return;

				$('#meetSeq').val(col['seq']);
				$('#txtPlaner').attr('planerJumin',col['erJumin']).val(col['er']);
				$('#txtNeeds').val(col['needs']);
				$('#txtProblem').val(col['problem']);
				$('#txtGoal').val(col['goal']);

				if ('<?=$copyYn;?>' == 'Y'){
					$('#ID_MSG').text(__getDate(col['dt'], '.')+'에 작성된 서비스계획서를 복사하였습니다.');
				}else{
					$('#txtPlanDt').val(__getDate(col['dt']));
				}

				if ('<?=$hce->SR;?>' == 'S'){
					$('#txtContents',$('#ID_ROW_ITEM_OLD')).val(col['svcContent']);
					$('#txtPeriod',$('#ID_ROW_ITEM_OLD')).val(col['svcPeriod']);
					$('#txtTimes',$('#ID_ROW_ITEM_OLD')).val('');
					$('#txtMethod',$('#ID_ROW_ITEM_OLD')).val(col['svcMethod']);
				}else{
					$('#txtSvcPeriod').val(col['svcPeriod']);
					$('#txtSvcContent').val(col['svcContent']);
					$('#txtSvcMethod').val(col['svcMethod']);
				}

				$('#txtRemark').val(col['remark']);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//저장
	function lfSave(){
		if (!$('#txtPlanDt').val()){
			alert('작성일자를 입력하여 주십시오.');
			$('#txtPlanDt').focus();
			return;
		}

		if (!$('#txtPlaner').val()){
			alert('작성자를 입력하여 주십시오.');
			return;
		}

		var data = {};

		data['planerJumin']= $('#txtPlaner').attr('jumin');

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

		if ('<?=$hce->SR;?>' == 'S'){
			data['txtNeeds']	= $('#txtNeeds').val();
			data['txtProblem']	= $('#txtProblem').val();
			data['txtGoal']		= $('#txtGoal').val();
			data['txtRemark']	= $('#txtRemark').val();
			data['itemList']	= '';

			$('tr[id^="ID_ROW_ITEM_"]',$('#ID_LIST')).each(function(){
				data['itemList'] += (data['itemList'] ? '?' : '')
								 +	('idx='+($(this).attr('idx') ? $(this).attr('idx') : '0'))
								 +	('&contents='+$('#txtContents',this).val())
								 +	('&period='+$('#txtPeriod',this).val())
								 +	('&times='+$('#txtTimes',this).val())
								 +	('&method='+$('#txtMethod',this).val());
			});
		}else{
			$('textarea').each(function(){
				var id	= $(this).attr('id');
				var val	= $(this).val();

				data[id] = val;
			});
		}

		if ('<?=$copyYn;?>' == 'Y') data['planSeq'] = '';


		$.ajax({
			type:'POST'
		,	url:'./hce_apply.php'
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

	function flDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./hce_svc_plan_delete.php'
		,	data:{
				'planSeq':$('#planSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();
					location.href = '../hce/hce_body.php?sr=<?=$sr;?>&type=61';
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

	//제공계획 행추가
	function lfRowAdd(){
		var html = '';

		html += '<tr id="ID_ROW_ITEM_NEW">'
			 +	'<td class="center">-</td>'
			 +	'<td><textarea id="txtContents" style="width:100%;"></textarea></td>'
			 +	'<td><textarea id="txtPeriod" style="width:100%;"></textarea></td>'
			 +	'<td><textarea id="txtTimes" style="width:100%;"></textarea></td>'
			 +	'<td><textarea id="txtMethod" style="width:100%;"></textarea></td>'
			 +	'<td><div class="left"><span class="btn_pack small"><button onclick="lfRemove(this);">취소</button></span></div></td>'
			 +	'</tr>';

		if ($('tr',$('#ID_LIST')).length > 0){
			$('tr:first',$('#ID_LIST')).before(html);
		}else{
			$('#ID_LIST').html(html);
		}

		$('textarea',$('tr:first',$('#ID_LIST'))).each(function(){
			__init_object(this);
		});
	}

	function lfRemove(obj){
		var obj = __GetTagObject($(obj),'TR');
		$(obj).remove();
	}

	function lfCopy(){
		var objModal = new Object();
		var url      = './hce_copy.php';
		var style    = 'dialogWidth:300px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		//objModal.code  = $('#code').val();
		objModal.type  = '62';
		

		window.showModalDialog(url, objModal, style);

		var result = objModal.para;	

		if (!result) return;

		var arr = result.split('&'); 
		var val = new Array();

		for(var i=0; i<arr.length; i++){
			var tmp = arr[i].split('=');

			val[tmp[0]] = tmp[1];
		}
		
		
		location.href = '../hce/hce_body.php?sr=S&type=62&seq='+val['seq']+'&r_seq='+val['r_seq']+'&copyYn=Y';
		
		//$('#strCname').text(val['name']);
		//$('#param').attr('value', 'jumin='+val['jumin']);
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<td class="left bottom last" id="ID_MSG"></td>
			<td class="right bottom last"><?
				if ($copyYn != 'Y'){?>
					<span class="btn_pack m"><a href="#" onclick="lfCopy();">복사</a></span><?
				}?>
				<span class="btn_pack m"><span class="save"></span><a href="#" onclick="lfSave(); return false;">저장</a></span>
				<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="flDelete(); return false;">삭제</a></span>
				<span class="btn_pack m"><span class="list"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=61" target="frmBody">리스트</a></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','',$('#planSeq').val());">출력</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue" style="border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="50px">
			<col width="50px">
			<col width="10px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">일자</th>
				<td><input id="txtPlanDt" name="txt" type="text" value="<?=$planDt;?>" class="date"></td>
				<th class="head">작성자</th>
				<td class="" style="padding:1px 1px 0 2px;"><span class="btn_pack find" onclick="lfMemFind();"></span></td>
				<td class="last"><input id="txtPlaner" name="txt" type="text" value="" jumin="" alt="not" readonly></td>
			</tr>
		</tbody>
	</table>
</div>
<div id="divBody" class="my_border_blue" style="height:200px; overflow-x:hidden; overflow-y:auto; border-top:none;"><?
	if ($hce->SR == 'S'){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="bold last">- 재가노인의 욕구</th>
				</tr>
				<tr>
					<td><textarea id="txtNeeds" name="multi" style="width:100%; height:50px;"></textarea></td>
				</tr>
				<tr>
					<th class="bold last">- 재가노인의 문제</th>
				</tr>
				<tr>
					<td><textarea id="txtProblem" name="multi" style="width:100%; height:75px;"></textarea></td>
				</tr>
				<tr>
					<th class="bold last">- 목표</th>
				</tr>
				<tr>
					<td><textarea id="txtGoal" name="multi" style="width:100%; height:100px;"></textarea></td>
				</tr>
			</tbody>
		</table>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="200px">
				<col width="150px">
				<col width="100px">
				<col width="200px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head bold last" colspan="6"><div style="text-align:left; padding-left:5px;">- 서비스 제공계획</div></th>
				</tr>
				<tr>
					<th class="head">No</th>
					<th class="head">서비스내용</th>
					<th class="head">기간</th>
					<th class="head">횟수</th>
					<th class="head">방법</th>
					<th class="head">
						<div style="float:right; width:auto; padding-right:3px;">
							<span class="btn_pack small"><button onclick="lfRowAdd();">추가</button></span>
						</div>
						<div style="float:center; width:auto;">비고</div>
					</th>
				</tr>
			</thead>
			<tbody id="ID_LIST"><?
				$sql = 'SELECT	plan_idx
						,		contents
						,		period
						,		times
						,		method
						FROM	hce_plan_sheet_item
						WHERE	org_no	 = \''.$orgNo.'\'
						AND		org_type = \''.$hce->SR.'\'
						AND		IPIN	 = \''.$hce->IPIN.'\'
						AND		rcpt_seq = \''.$hce->rcpt.'\'
						AND		plan_seq = \''.$planSeq.'\'
						AND		del_flag = \'N\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();
				$no = 1;

				if ($rowCnt > 0){
					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<tr id="ID_ROW_ITEM_<?=$row['plan_idx'];?>" idx="<?=$row['plan_idx'];?>">
							<td class="center"><?=$no;?></td>
							<td class="center"><textarea id="txtContents" style="width:100%;"><?=StripSlashes($row['contents']);?></textarea></td>
							<td class="center"><textarea id="txtPeriod" style="width:100%;"><?=StripSlashes($row['period']);?></textarea></td>
							<td class="center"><textarea id="txtTimes" style="width:100%;"><?=StripSlashes($row['times']);?></textarea></td>
							<td class="center"><textarea id="txtMethod" style="width:100%;"><?=StripSlashes($row['method']);?></textarea></td>
							<td class="center">
								<div class="left"><span class="btn_pack small"><button onclick="lfRemove(this);" style="color:RED;">삭제</button></span></div>
							</td>
						</tr><?

						$no ++;
					}
				}else{?>
					<tr id="ID_ROW_ITEM_OLD">
						<td class="center">1</td>
						<td class="center"><textarea id="txtContents" style="width:100%;"></textarea></td>
						<td class="center"><textarea id="txtPeriod" style="width:100%;"></textarea></td>
						<td class="center"><textarea id="txtTimes" style="width:100%;"></textarea></td>
						<td class="center"><textarea id="txtMethod" style="width:100%;"></textarea></td>
						<td class="center">
							<div class="left"><span class="btn_pack small"><button onclick="lfRemove(this);" style="color:RED;">삭제</button></span></div>
						</td>
					</tr<?
				}

				$conn->row_free();
			?>
			</tbody>
		</table>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="bold last">- 비고</th>
				</tr>
				<tr>
					<td><textarea id="txtRemark" name="multi" style="width:100%; height:75px;"></textarea></td>
				</tr>
			</tbody>
		</table><?
	}else{?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="bold last" colspan="2">- 재가노인의 욕구</th>
				</tr>
				<tr>
					<td colspan="2"><textarea id="txtNeeds" name="multi" style="width:100%; height:50px;"></textarea></td>
				</tr>
				<tr>
					<th class="bold last" colspan="2">- 재가노인의 문제</th>
				</tr>
				<tr>
					<td colspan="2"><textarea id="txtProblem" name="multi" style="width:100%; height:75px;"></textarea></td>
				</tr>
				<tr>
					<th class="bold last" colspan="2">- 목표</th>
				</tr>
				<tr>
					<td colspan="2"><textarea id="txtGoal" name="multi" style="width:100%; height:100px;"></textarea></td>
				</tr>
				<tr>
					<th class="bold last" colspan="2">- 서비스 제공계획</th>
				</tr>
				<tr>
					<th class="head">서비스<br>기간</th>
					<td><textarea id="txtSvcPeriod" name="multi" style="width:100%; height:100px;"></textarea></td>
				</tr>
				<tr>
					<th class="head">서비스<br>내용</th>
					<td><textarea id="txtSvcContent" name="multi" style="width:100%; height:100px;"></textarea></td>
				</tr>
				<tr>
					<th class="head">서비스<br>방법</th>
					<td><textarea id="txtSvcMethod" name="multi" style="width:100%; height:100px;"></textarea></td>
				</tr>
				<tr>
					<th class="bold last" colspan="2">- 비고</th>
				</tr>
				<tr>
					<td colspan="2"><textarea id="txtRemark" name="multi" style="width:100%; height:75px;"></textarea></td>
				</tr>
			</tbody>
		</table><?
	}?>
	<input id="planSeq" type="hidden" value="<?=$planSeq;?>">
	<input id="meetSeq" type="hidden" value="<?=$meetSeq;?>">
</div>
<?
	include_once('../inc/_db_close.php');
?>