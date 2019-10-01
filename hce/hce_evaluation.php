<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사례평가서
	 *********************************************************/
	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
			FROM	m03sugupja
			LEFT	JOIN	mst_jumin AS jumin
					ON		jumin.org_no= m03_ccode
					AND		jumin.gbn	= \'1\'
					AND		jumin.code	= m03_jumin
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= SubStr($row['real_jumin'].'0000000',0,13);
	$jumin	= $myF->issStyle($jumin);

	Unset($row);
	
	$rcpt = ($_GET['r_seq'] != '' ? $_GET['r_seq'] : $hce->rcpt);
	
	$sql = 'SELECT	*
			FROM	hce_evaluation
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$rcpt.'\'
			AND     del_flag= \'N\'';

	$row = $conn->get_array($sql);

	$evDt		= $myF->dateStyle($row['ev_dt']);
	$evHm		= $myF->timeStyle($row['ev_hm']);
	$ever		= $row['ever'];
	$everJumin	= $ed->en($row['ever_jumin']);
	$quest1		= $row['quest_1'];
	$quest2		= $row['quest_2'];
	$quest3		= $row['quest_3'];
	$quest4		= $row['quest_4'];
	$quest5		= $row['quest_5'];
	$quest6		= $row['quest_6'];
	$quest7		= $row['quest_7'];
	$quest8		= $row['quest_8'];
	$quest9		= $row['quest_9'];
	$quest10	= $row['quest_10'];
	$quest11	= $row['quest_11'];
	$quest12	= $row['quest_12'];
	$quest13	= $row['quest_13'];
	$quest14	= $row['quest_14'];
	$text1		= StripSlashes($row['text_1']);
	$text2		= StripSlashes($row['text_2']);
	$text3		= StripSlashes($row['text_3']);
	$text4		= StripSlashes($row['text_4']);
	$text5		= StripSlashes($row['text_5']);

	Unset($row);

	if($_GET['r_seq'] != '') $evDt = ''; 
	
	if (!$evDt) $evDt = Date('Y-m-d');
	if (!$evHm) $evHm = Date('H:00');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('span[id^="lblQ"]').css('cursor','default').css('font-weight','bold').css('color','blue').unbind('mouseover').bind('mouseover',function(){
			$(this).parent().css('background-color','#efefef');
		}).unbind('mouseout').bind('mouseout',function(){
			$(this).parent().css('background-color','#ffffff');
		});
		$('span[id^="lblQ"]').parent().unbind('click').bind('click',function(){
			var obj = $('span',this);
			var val = $(obj).attr('id').split('_');

			$('span[id^="'+val[0]+'"]').text('');
			$(obj).text('V');
		});
	});

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtEver').attr('jumin',obj['jumin']).val(obj['name']);
	}

	//저장
	function lfSave(){
		if (!$('#txtEvDt').val()){
			alert('평가일자를 입력하여 주십시오.');
			$('#txtEvDt').focus();
			return;
		}

		if (!$('#txtEvHm').val()){
			alert('평가시간를 입력하여 주십시오.');
			$('#txtEvHm').focus();
			return;
		}

		if (!$('#txtEver').val()){
			alert('평가자를 입력하여 주십시오.');
			lfMemFind();
			return;
		}

		var data = {};

		data['everJumin'] = $('#txtEver').attr('jumin');

		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('textarea').each(function(){
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

		$('span[id^="lblQ"]').each(function(){
			if ($(this).text()){
				var val = $(this).attr('id').split('_');

				data[val[0]] = val[1];
			}
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_evaluation_save.php'
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

	function lfDel(){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		location.reload();

		$.ajax({
			type:'POST'
		,	url:'./hce_evaluation_delete.php'
		,	data:{
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					location.href="../hce/hce_body.php?sr=<?=$sr;?>&type=131";
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


	function lfCopy(){
		$.ajax({
			type:'POST'
		,	url:'./hce_copy_list.php'
		,	data:{
			'orgNo':'<?=$orgNo?>',
			'type' :'131',
			'SR'   :'<?=$hce->SR;?>',
			'IPIN' :'<?=$hce->IPIN;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				
				if (!result) return;

				var arr = result.split('&');
				var val = new Array();
				
				for(var i=0; i<arr.length; i++){
					var tmp = arr[i].split('=');

					val[tmp[0]] = tmp[1];
				}

				location.href = '../hce/hce_body.php?sr=S&type=131&seq='+val['seq']+'&r_seq='+val['r_seq']+'&copyYn=Y';
				
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;

		
		

		//$('#strCname').text(val['name']);
		//$('#param').attr('value', 'jumin='+val['jumin']);
	}
</script>

<div class="title title_border">
	<div style="float:left; width:auto;">사례평가서</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<? if ($copyYn != 'Y'){?>
		<span class="btn_pack m wa"><button type="button" onclick="lfCopy();">이전내용불러오기</button></span><?
			}
		?>
		<span class="btn_pack m"><span class="add"></span><button type="button" class="bold" onclick="lfSave(); return false;">저장</button></span>
		<span class="btn_pack m"><span class="delete"></span><button onclick="lfDel();">삭제</button></span>
		<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>');">PDF</button></span>
	</div>
</div>
 
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="120px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">대상자명</th>
			<td class="left"><?=$name;?></td>
			<th class="head">주민번호</th>
			<td class="left last"><?=$jumin;?></td>
		</tr>
		<tr>
			<th class="head">평가일시</th>
			<td>
				<input id="txtEvDt" type="text" value="<?=$evDt;?>" class="date" style="margin-right:0;">
				<input id="txtEvHm" type="text" value="<?=$evHm;?>" class="no_string" alt="time" style="margin-left:0;">
			</td>
			<th class="head">평가자</th>
			<td class="last">
				<div style="float:left; width:auto; height:25px; padding:1px 0 0 5px;"><span class="btn_pack find" onclick="lfMemFind();"></span></div>
				<div style="float:left; width:auto; padding-top:2px;"><input id="txtEver" type="text" value="<?=$ever;?>" jumin="<?=$everJumin;?>" style="margin-left:0;" alt="not" readonly></div>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
		<col width="90px" span="5">
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="7">1. 서비스 목표달성 평가</th>
		</tr>
		<tr>
			<th class="head" rowspan="2">단계</th>
			<th class="head" rowspan="2">평가항목</th>
			<th class="head last" colspan="5">평가</th>
		</tr>
		<tr>
			<th class="head">매우그렇다</th>
			<th class="head">그렇다</th>
			<th class="head">보통</th>
			<th class="head">그렇지않다</th>
			<th class="head last">매우그렇지않다</th>
		</tr>
		<tr>
			<th class="center" rowspan="2">아웃리치</th>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">의뢰이유 확인 및 초기면접 진행여부가 적절했는가?</p></td>
			<td class="center"><span id="lblQ1_1" style="width:100%;"><?=$quest1 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ1_2" style="width:100%;"><?=$quest1 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ1_3" style="width:100%;"><?=$quest1 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ1_4" style="width:100%;"><?=$quest1 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ1_5" style="width:100%;"><?=$quest1 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">재가노인 신상 관련 기본정보파악이 적절했는가?</p></td>
			<td class="center"><span id="lblQ2_1" style="width:100%;"><?=$quest2 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ2_2" style="width:100%;"><?=$quest2 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ2_3" style="width:100%;"><?=$quest2 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ2_4" style="width:100%;"><?=$quest2 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ2_5" style="width:100%;"><?=$quest2 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<th class="center" rowspan="2">사정</th>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">재가노인의 욕구사정에 의거한 욕구사정이 적절했는가?</p></td>
			<td class="center"><span id="lblQ3_1" style="width:100%;"><?=$quest3 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ3_2" style="width:100%;"><?=$quest3 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ3_3" style="width:100%;"><?=$quest3 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ3_4" style="width:100%;"><?=$quest3 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ3_5" style="width:100%;"><?=$quest3 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">재가노인서비스제공여부에 대한 사례회의 진행이 적절했는가?</p></td>
			<td class="center"><span id="lblQ4_1" style="width:100%;"><?=$quest4 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ4_2" style="width:100%;"><?=$quest4 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ4_3" style="width:100%;"><?=$quest4 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ4_4" style="width:100%;"><?=$quest4 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ4_5" style="width:100%;"><?=$quest4 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<th class="center" rowspan="4">서비스<br>계획</th>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">서비스계획수립이 적절했는가?</p></td>
			<td class="center"><span id="lblQ5_1" style="width:100%;"><?=$quest5 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ5_2" style="width:100%;"><?=$quest5 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ5_3" style="width:100%;"><?=$quest5 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ5_4" style="width:100%;"><?=$quest5 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ5_5" style="width:100%;"><?=$quest5 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">서비스이용안내 및 계약이 적절했는가?</p></td>
			<td class="center"><span id="lblQ6_1" style="width:100%;"><?=$quest6 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ6_2" style="width:100%;"><?=$quest6 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ6_3" style="width:100%;"><?=$quest6 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ6_4" style="width:100%;"><?=$quest6 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ6_5" style="width:100%;"><?=$quest6 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">계약서에 명시된 내용에 따라 서비스 계획대로 실행되었는가?</p></td>
			<td class="center"><span id="lblQ7_1" style="width:100%;"><?=$quest7 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ7_2" style="width:100%;"><?=$quest7 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ7_3" style="width:100%;"><?=$quest7 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ7_4" style="width:100%;"><?=$quest7 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ7_5" style="width:100%;"><?=$quest7 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">계획에 맞는 자원이 어느 정도 확보되었는가?</p></td>
			<td class="center"><span id="lblQ8_1" style="width:100%;"><?=$quest8 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ8_2" style="width:100%;"><?=$quest8 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ8_3" style="width:100%;"><?=$quest8 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ8_4" style="width:100%;"><?=$quest8 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ8_5" style="width:100%;"><?=$quest8 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<th class="center" rowspan="2">서비스<br>제공<br> 및 점검</th>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">사례점검회의가 적절히 실행되었는가?</p></td>
			<td class="center"><span id="lblQ9_1" style="width:100%;"><?=$quest9 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ9_2" style="width:100%;"><?=$quest9 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ9_3" style="width:100%;"><?=$quest9 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ9_4" style="width:100%;"><?=$quest9 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ9_5" style="width:100%;"><?=$quest9 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">서비스제공과정에서 서비스제공과정 기록이 적절했는가?</p></td>
			<td class="center"><span id="lblQ10_1" style="width:100%;"><?=$quest10 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ10_2" style="width:100%;"><?=$quest10 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ10_3" style="width:100%;"><?=$quest10 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ10_4" style="width:100%;"><?=$quest10 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ10_5" style="width:100%;"><?=$quest10 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<th class="center" rowspan="3">재사정<br>및 종결</th>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">서비스연계 및 의뢰가 적절했는가?</p></td>
			<td class="center"><span id="lblQ11_1" style="width:100%;"><?=$quest11 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ11_2" style="width:100%;"><?=$quest11 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ11_3" style="width:100%;"><?=$quest11 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ11_4" style="width:100%;"><?=$quest11 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ11_5" style="width:100%;"><?=$quest11 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">재사정 기록지가 적절하게 작성되었는가?</p></td>
			<td class="center"><span id="lblQ12_1" style="width:100%;"><?=$quest12 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ12_2" style="width:100%;"><?=$quest12 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ12_3" style="width:100%;"><?=$quest12 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ12_4" style="width:100%;"><?=$quest12 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ12_5" style="width:100%;"><?=$quest12 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">서비스 종결 및 서비스 해지 안내가 적절했는가?</p></td>
			<td class="center"><span id="lblQ13_1" style="width:100%;"><?=$quest13 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ13_2" style="width:100%;"><?=$quest13 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ13_3" style="width:100%;"><?=$quest13 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ13_4" style="width:100%;"><?=$quest13 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ13_5" style="width:100%;"><?=$quest13 == '5' ? 'V' : '';?></span></td>
		</tr>
		<tr>
			<th class="center">기타</th>
			<td class="center"><p style="margin:3px; text-align:justify; line-height:1.3em;">전반적인 서비스 만족은 어떠한가?</p></td>
			<td class="center"><span id="lblQ14_1" style="width:100%;"><?=$quest14 == '1' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ14_2" style="width:100%;"><?=$quest14 == '2' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ14_3" style="width:100%;"><?=$quest14 == '3' ? 'V' : '';?></span></td>
			<td class="center"><span id="lblQ14_4" style="width:100%;"><?=$quest14 == '4' ? 'V' : '';?></span></td>
			<td class="center last"><span id="lblQ14_5" style="width:100%;"><?=$quest14 == '5' ? 'V' : '';?></span></td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">2. 재가노인에게 미친 영향</th>
		</tr>
		<tr>
			<th class="head">구분</th>
			<td class="last"><textarea id="txtEff1" style="width:100%;"><?=$text1;?></textarea></td>
		</tr>
		<tr>
			<th class="head">긍정적</th>
			<td class="last"><textarea id="txtEff2" style="width:100%;"><?=$text2;?></textarea></td>
		</tr>
		<tr>
			<th class="head">부정적</th>
			<td class="last"><textarea id="txtEff3" style="width:100%;"><?=$text3;?></textarea></td>
		</tr>
		<tr>
			<th class="head">변화된<br>욕구</th>
			<td class="last"><textarea id="txtEff4" style="width:100%;"><?=$text4;?></textarea></td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">3. 종합소견(향후계획)</th>
		</tr>
		<tr>
			<td class="last" colspan="2"><textarea id="txtFeel" style="width:100%;"><?=$text5;?></textarea></td>
		</tr>
	</tbody>
</table>

<?
	include_once('../inc/_db_close.php');
?>