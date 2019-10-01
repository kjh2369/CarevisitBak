<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사정기록지
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];

	$sql = 'SELECT	ispt_dt
			,		ispt_from
			,		ispt_to
			,		counsel_type
			,		iver_nm
			,		iver_jumin
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \'1\'';

	$row = $conn->get_array($sql);

	$isptDt		= $myF->dateStyle($row['ispt_dt']);
	$isptFrom	= $myF->timeStyle($row['ispt_from']);
	$isptTo		= $myF->timeStyle($row['ispt_to']);
	$counselType= $row['counsel_type'];
	$iverNm		= $row['iver_nm'];
	$iverJumin	= $ed->en($row['iver_jumin']);

	Unset($row);

	if (!$isptDt)		$isptDt		= Date('Y-m-d');
	if (!$isptFrom)		$isptFrom	= '10:00';
	if (!$isptTo)		$isptTo		= '11:00';
	if (!$counselType)	$counselType= '1';
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoadBody(\'1\')',100);
	});

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtIVer').attr('jumin',obj['jumin']).val(obj['name']);
	}

	function lfSelMenu(a,obj){
		$('a').css('color','');
		$(a).css('color','blue');

		$('#divFoot').hide();

		setTimeout('lfLoadBody(\''+obj+'\')',100);
	}

	//기본사항 조회
	function lfLoadBody(bodyId, hcptSeq){
		//if ('<?=$debug;?>' == '1') return;

		$('#ID_MENU_SEL_CD').attr('menuId',bodyId);

		if (!hcptSeq) hcptSeq = '';

		$.ajax({
			type: 'POST'
		,	url : './hce_ispt.php'
		,	data: {
				'id':bodyId
			,	'sr':$('#sr').val()
			,	'hcptSeq':hcptSeq
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				$('#divBody').html(data).scrollTop(0);
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//저장
	function lfSave(){
		if (!$('#txtIsptDt').val()){
			alert('상담일자를 입력하여 주십시오.');
			$('#txtIsptDt').focus();
			return;
		}

		if (!$('#txtIsptFrom').val()){
			alert('상담 시작시간을 입력하여 주십시오.');
			$('#txtIsptFrom').focus();
			return;
		}

		if (!$('#txtIsptTo').val()){
			alert('상담 종료시간을 입력하여 주십시오.');
			$('#txtIsptTo').focus();
			return;
		}

		if (!$('#txtIVer').attr('jumin')){
			alert('담당자를 선택하여 주십시오.');
			lfMemFind();
			return;
		}

		lfSaveSub();
	}
</script>

<!-- <div class="my_border_blue" style="border-bottom:none; margin-top:10px;"> -->
<div class="title title_border">
	<div style="float:left; width:auto;">사정기록</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="if($('#bodyIdx').val()){lfPDF('<?=$type;?>',$('#bodyIdx').val(),$('#isptSeq').val());}">현재페이지 출력</button></span>
		<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','ALL');">전체 출력</button></span>
	</div>
</div>
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="180px">
			<col width="60px">
			<col width="70px">
			<col width="20px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center" >상담일시</th>
				<td class="">
					<input id="txtIsptDt" name="txt" type="text" value="<?=$isptDt;?>" class="date" style="margin-right:0;">
					<input id="txtIsptFrom" name="txt" type="text" value="<?=$isptFrom;?>" class="no_string" alt="time" style="margin-left:0; margin-right:0;"> ~
					<input id="txtIsptTo" name="txt" type="text" value="<?=$isptTo;?>" class="no_string" alt="time" style="margin-left:0; margin-right:0;">
				</td>
				<th class="center">담당자</th>
				<td class="center last" colspan="2">
					<input id="txtIVer" name="txt" type="text" value="<?=$iverNm;?>" jumin="<?=$iverJumin;?>" style="width:100%; border:none;" alt="not" readonly>
				</td>
				<td class="center" colspan="9">
					<span class="btn_pack find" onclick="lfMemFind();"></span>
				</td>
				
			</tr>
			<tr>
				<th class="center">상담방법</th>
				<td class="last" colspan="13" >
					<div style="float:left; width:auto;"><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'CT\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<label><input id="optCounselType_<?=$row['code'];?>" name="optCounselType" type="radio" class="radio" value="<?=$row['code'];?>" <?=($counselType == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
						}

						$conn->row_free();?>
					</div>
					<div class="right" style="float:right; width:auto;">
						<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'11'); return false;">약도</a></span>
						<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'10'); return false;">사진첨부</a></span>
					</div>
				</td>
			</tr>
			<tr>
				<th class="center">상담메뉴</th>
				<td class="left last" colspan="13">
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'1'); return false;" style="color:blue;">기본</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'2'); return false;">가계도</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'3'); return false;">일상생활동작정도</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'4'); return false;">도구적 일상생활동작</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'5'); return false;">정서적측면</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'6'); return false;">사회적측면</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'7'); return false;">욕구</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'8'); return false;">노인인지기능력평가</a></span>
					<span class="btn_pack small"><a href="#" onclick="lfSelMenu(this,'9'); return false;">노인우울척도</a></span>
				</td>
			</tr>
		</tbody>
	</table>

	<script type="text/javascript">
		function lfLoadContents(hcptSeq){
			if (!confirm('선택하신 차수의 내용을 불러오시겠습니까?')) return;

			var menuId = $('#ID_MENU_SEL_CD').attr('menuId');
			lfLoadBody(menuId, hcptSeq);
		}
	</script>
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="100px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center" colspan="2">이전내용불러오기</th>
				<td class="left last">
					<div id="ID_MENU_SEL_CD" style="display:none;" menuId=""></div><?
					//echo $orgNo.'/'.$hce->SR.'/'.$hce->IPIN.'/'.$hce->rcpt;

					$sql = 'SELECT	rcpt_seq
							,		hce_seq
							FROM	hce_receipt
							WHERE	org_no	= \''.$orgNo.'\'
							AND		org_type= \''.$hce->SR.'\'
							AND		IPIN	= \''.$hce->IPIN.'\'
							AND		del_flag= \'N\'
							ORDER	BY hce_seq';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<span class="btn_pack small"><button onclick="lfLoadContents('<?=$row['rcpt_seq'];?>');" style="<?=$hce->rcpt == $row['rcpt_seq'] ? 'color:BLUE; font-weight:bold;' : '';?>"><?=$row['hce_seq'];?>차</button></span><?
					}

					$conn->row_free();?>
				</td>
				<td class="right last">
					<span class="btn_pack m"><span class="save"></span><button type="button" onclick="lfSave();">저장</button></span>
					
				</td>
			</tr>
		</tbody>
	</table>
<!-- </div> -->
<div id="divBody" class="my_border_blue" style="border-top:none; height:200px; overflow-x:hidden; overflow-y:auto;"></div>
<?
	include_once('../inc/_db_close.php');
?>