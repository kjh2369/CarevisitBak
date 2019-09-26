<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	대상자 선정기준표
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$orgType = '40';
	$chicSeq = '1';

	//사정기록일자
	$sql = 'SELECT	ispt_dt
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$isptDt = $conn->get_data($sql);

	//사례관리자
	$sql = 'SELECT	rcver_nm
			,		rcver_ssn
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$manager		= $row['rcver_nm'];
	$managerJumin	= $ed->en($row['rcver_ssn']);

	Unset($row);

	//대상자정보
	$sql = 'SELECT	m03_name AS name
			,		phone
			,		mobile
			,		addr
			,		addr_dtl
			FROM	hce_receipt
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= org_no
					AND		m03_mkind	= \'6\'
					AND		m03_key		= IPIN
			WHERE	org_no	= \''.$orgNo.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$choiceNm	= $row['name'];
	$choiceTel	= $myF->phoneStyle($row['phone'],'.');
	if (!$choiceTel) $choiceTel	= $myF->phoneStyle($row['mobile'],'.');
	$choiceAddr	= $row['addr'].' '.$row['addr_dtl'];

	Unset($row);

	if (!$choiceDt)	$choiceDt	= Date('Y-m-d');
?>
<style>
	.divVal{
		font-weight:bold;
		color:blue;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('.divVal').parent().unbind('click').bind('click',function(){
			var obj = $('div:first',this);
			var id  = $(obj).attr('id').split('_');
			var val = $(obj).text();
			var point = 0;

			$('div[id^="'+id[0]+'"]').text('');

			if (val != 'V') $(obj).text('V');

			var tot = $('#'+id[0].substring(0,4));

			if (id[0].substring(4) == '1'){
				$(tot).attr('basePoint',($(obj).text() == 'V' ? $(obj).attr('value') : '0'));
			}else if (id[0].substring(4) == '2'){
				$(tot).attr('addPoint',($(obj).text() == 'V' ? $(obj).attr('value') : '0'));
			}else if (id[0].substring(4) == '3'){
				$(tot).attr('secPoint',($(obj).text() == 'V' ? $(obj).attr('value') : '0'));
			}

			point	= __str2num($(tot).attr('basePoint'))
					+ __str2num($(tot).attr('addPoint'))
					+ __str2num($(tot).attr('secPoint'));
			$(tot).text(point);

			lfPointSum();
		});

		setTimeout('lfLoad()',100);
	});

	function lfLoad(){
		$.ajax({
			type:'POST'
		,	url:'./hce_choice_search.php'
		,	data:{
				'seq':'1'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				for(var i in col){
					if (i == 'choiceDt'){
						$('#txtChoiceDt').val(__getDate(col[i]));
					}else if (i == 'total'){
						$('#lblTot').text(col[i]);
					}else if (i == 'rst'){
						$('#lblRst'+col[i]).text('V');
					}else if (i == 'comment'){
						$('#txtComment').val(col[i]);
					}else{
						$('#'+i+'_'+col[i]).parent().click();
					}
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPointSum(){
		var tot = 0;

		for(var i=0; i<10; i++){
			var chr = String.fromCharCode(65+i);

			tot += __str2num($('#lbl'+chr).text());
		}

		if (tot >= 25){
			$('#lblRst1').text('V');
			$('#lblRst2').text('');
		}else if (tot >= 20){
			$('#lblRst1').text('');
			$('#lblRst2').text('V');
		}else{
			$('#lblRst1').text('');
			$('#lblRst2').text('');
		}

		$('#lblTot').text(tot);
	}

	//저장
	function lfSave(){
		if (!$('#txtChoiceDt').val()){
			alert('작성일자를 입력하여 주십시오.');
			$('#txtChoiceDt').focus();
			return;
		}

		var data = {};

		data['choiceDt']= $('#txtChoiceDt').val();
		data['total']	= $('#lblTot').text();
		data['comment']	= $('#txtComment').val();

		$('.divVal').each(function(){
			if ($(this).text() == 'V'){
				var id = $(this).attr('id').split('_');

				data[id[0]] = id[1];
				data[id[0]+'Val'] = $(this).attr('value');
			}
		});

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

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
</script>
<div class="my_border_blue" style="border-bottom:none; margin-top:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="60px">
			<col width="70px">
			<col width="40px">
			<col width="90px">
			<col width="40px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">사례관리자</th>
				<td class="left last"><?=$manager;?></td>
				<td class="right last" colspan="6">
					<span class="btn_pack m"><span class="save"></span><a href="#" onclick="lfSave();">저장</a></span>
					<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','','<?=$chicSeq;?>');">출력</button></span>
				</td>
			</tr>
			<tr>
				<th class="head">작성일자</th>
				<td class="center"><input id="txtChoiceDt" name="txt" type="text" value="<?=$choiceDt;?>" class="date"></td>
				<th class="head">대상자명</th>
				<td class="left"><?=$choiceNm;?></td>
				<th class="head">연락처</th>
				<td class="center"><?=$choiceTel;?></td>
				<th class="head">주소</th>
				<td class="left last"><?=$choiceAddr;?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class="my_border_blue" style="border-top:none; border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="96px">
			<col width="245px">
			<col width="271px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">영역</th>
				<th class="head">기준</th>
				<th class="head">기본점수</th>
				<th class="head">가산점</th>
				<th class="head last">점수</th>
			</tr>
		</thead>
	</table>
</div>

<div id="divBody" class="my_border_blue" style="border-top:none; border-bottom:none; height:200px; overflow-x:hidden; overflow-y:auto;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="35px">
			<col width="60px">
			<col width="240px">
			<col width="110px">
			<col width="150px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center" rowspan="4">1&nbsp;&nbsp;영&nbsp;&nbsp;역<br>경제기능</th>
				<th class="center" colspan="2">수&nbsp;급&nbsp;형&nbsp;태</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblA1_1" class="divVal" value="5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">기초수급자</div>
							<div style="float:left; width:auto;">(5)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblA1_2" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">의료급여2종</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblA1_3" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">차상위계층</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblA1_4" class="divVal" value="0" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">일&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;반</div>
							<div style="float:left; width:auto;">(0)</div>
						</a>
					</div>
				</td>
				<th>경제활동<br>능력이없는 가족수</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblA2_1" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">1인</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblA2_2" class="divVal" value="1.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">2인</div>
							<div style="float:left; width:auto;">(1.5)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblA2_3" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">3인</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblA2_4" class="divVal" value="2.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:67px;">4인이상</div>
							<div style="float:left; width:auto;">(2.5)</div>
						</a>
					</div>
				</td>
				<td class="center bold last">
					<div id="lblA" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="center" colspan="2">주&nbsp;택&nbsp;형&nbsp;태</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblB1_1" class="divVal" value="0" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:80px;">자가</div>
							<div style="float:left; width:auto;">(0)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblB1_2" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:80px;">전세</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblB1_3" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:80px;">무료임대,의탁</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblB1_4" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:80px;">영구,국민임대</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblB1_5" class="divVal" value="4" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:80px;">월세</div>
							<div style="float:left; width:auto;">(4)</div>
						</a>
					</div>
				</td>
				<th>월세는<br>가산점 부여</th>
				<td class="left top">
					<div style="cursor:default;">
						<div id="lblB2_1" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:85px;">20만원 미만</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblB2_2" class="divVal" value="1.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:85px;">20만원~30만원</div>
							<div style="float:left; width:auto;">(1.5)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblB2_3" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:85px;">30만원이상</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
				</td>
				<td class="center bold last">
					<div id="lblB" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="" colspan="2">가족구성원 실수령액 및 총소득<br>(공적부조포함)</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblC1_1" class="divVal" value="6" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:120px;">50만원미만</div>
							<div style="float:left; width:auto;">(6)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblC1_2" class="divVal" value="5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:120px;">50만원~85만원미만</div>
							<div style="float:left; width:auto;">(5)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblC1_3" class="divVal" value="4" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:120px;">85만원~110만원미만</div>
							<div style="float:left; width:auto;">(4)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblC1_4" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:120px;">110만원~135만원미만</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblC1_5" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:120px;">135만원~160만원미만</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblC1_6" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:120px;">160만원~185만원미만</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblC1_7" class="divVal" value="0" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:120px;">185만원이상</div>
							<div style="float:left; width:auto;">(0)</div>
						</a>
					</div>
				</td>
				<td class="left top" colspan="2">
					<div style="cursor:default;">
						<div id="lblC2_1" class="divVal" value="1.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:210px;">공적부조 이외에 소득이 없으면 가산점</div>
							<div style="float:left; width:auto;">(1.5)</div>
						</a>
					</div>
				</td>
				<td class="center bold last">
					<div id="lblC" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="center" colspan="2">후원자의 수</th>
				<td class="bold"><p style="padding:0 5px 0 5px; text-align:justify; color:red;">사회복지기관, 종교단체, 친인척, 개인후원자로부터 금품후원제공시 해당영역별로 각각 (-1)점씩 부여</p></td>
				<td class="left" colspan="2">
					<div style="cursor:default;">
						<div id="lblD2_1" class="divVal" value="-0.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:140px;">후원금품 월 1만원~5만원</div>
							<div style="float:left; width:auto;">(-0.5)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblD2_2" class="divVal" value="-1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:140px;">후원금품 월 5만원이상</div>
							<div style="float:left; width:auto;">(-1)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblD2_3" class="divVal" value="-2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:140px;">후원금품 월 10만원이상</div>
							<div style="float:left; width:auto;">(-2)</div>
						</a>
					</div>
				</td>
				<td class="center bold last">
					<div id="lblD" basePoint="2" addPoint="0" secPoint="0" style="color:red;">2</div>
				</td>
			</tr>

			<tr>
				<th class="center" rowspan="5">2&nbsp;&nbsp;영&nbsp;&nbsp;역<br>건강기능</th>
				<th class="center" rowspan="2">건강<br>상태</th>
				<th class="center">신체적</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblE1_1" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">매우안좋음</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblE1_2" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">안좋음</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblE1_3" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">양호</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
				</td>
				<td class="left top" colspan="2">
					<div style="cursor:default;">
						<div id="lblE2_1" class="divVal" value="1.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:129px;">중환자 2인 이상인 경우</div>
							<div style="float:left; width:auto;">(1.5)</div>
						</a>
					</div>
				</td>
				<td class="center bold last">
					<div id="lblE" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="center">정서적</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblF1_1" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">매우안좋음</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblF1_2" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">안좋음</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblF1_3" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">양호</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
				</td>
				<td class="left top" colspan="2">
					<div style="cursor:default;">
						<div id="lblF2_1" class="divVal" value="1.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:129px;">중환자 2인 이상인 경우</div>
							<div style="float:left; width:auto;">(1.5)</div>
						</a>
					</div>
				</td>
				<td class="center bold last">
					<div id="lblF" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="center" colspan="2">장&nbsp;애&nbsp;등&nbsp;급</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblG1_1" class="divVal" value="6" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:33px;">1등급</div>
							<div style="float:left; width:auto;">(6)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblG1_2" class="divVal" value="5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:33px;">2등급</div>
							<div style="float:left; width:auto;">(5)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblG1_3" class="divVal" value="4" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:33px;">3등급</div>
							<div style="float:left; width:auto;">(4)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblG1_4" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:33px;">4등급</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblG1_5" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:33px;">5등급</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblG1_6" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:33px;">6등급</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
				</td>
				<td class="left top" colspan="2">
					<div style="cursor:default;">
						<div id="lblG2_1" class="divVal" value="1.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:140px;">중복장애</div>
							<div style="float:left; width:auto;">(1.5)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblG3_1" class="divVal" value="1.5" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:140px;">장애인이 2인 이상인 경우</div>
							<div style="float:left; width:auto;">(1.5)</div>
						</a>
					</div>
				</td>
				<td class="center bold last">
					<div id="lblG" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="center" colspan="2">A&nbsp;D&nbsp;L&nbsp;기&nbsp;능</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblH1_1" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:50px;">완전도움</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblH1_2" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:50px;">부분도움</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblH1_3" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:50px;">완전자립</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
				</td>
				<td colspan="2"></td>
				<td class="center bold last">
					<div id="lblH" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="center" colspan="2">요&nbsp;양&nbsp;등&nbsp;급</th>
				<td class="left">
					<div style="cursor:default;">
						<div id="lblI1_1" class="divVal" value="4" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">1등급</div>
							<div style="float:left; width:auto;">(4)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblI1_2" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">2등급</div>
							<div style="float:left; width:auto;">(3)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblI1_3" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">3등급</div>
							<div style="float:left; width:auto;">(2)</div>
						</a>
					</div>
					<div style="cursor:default;">
						<div id="lblI1_4" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#">
							<div style="float:left; width:63px;">등급외 A,B</div>
							<div style="float:left; width:auto;">(1)</div>
						</a>
					</div>
				</td>
				<td colspan="2"></td>
				<td class="center bold last">
					<div id="lblI" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>

			<tr>
				<th class="center bottom">사례관리자<br>재&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;량</th>
				<th class="center bottom" colspan="2">재&nbsp;&nbsp;량</th>
				<td class="left bottom">
					<div style="cursor:default;">
						<div id="lblJ1_1" class="divVal" value="1" style="float:left; width:10px;"></div>
						<a href="#"><div style="float:left; width:auto;">1점</div></a>
					</div>
					<div style="cursor:default;">
						<div id="lblJ1_2" class="divVal" value="2" style="float:left; width:10px;"></div>
						<a href="#"><div style="float:left; width:auto;">2점</div></a>
					</div>
					<div style="cursor:default;">
						<div id="lblJ1_3" class="divVal" value="3" style="float:left; width:10px;"></div>
						<a href="#"><div style="float:left; width:auto;">3점</div></a>
					</div>
					<div style="cursor:default;">
						<div id="lblJ1_4" class="divVal" value="4" style="float:left; width:10px;"></div>
						<a href="#"><div style="float:left; width:auto;">4점</div></a>
					</div>
					<div style="cursor:default;">
						<div id="lblJ1_5" class="divVal" value="5" style="float:left; width:10px;"></div>
						<a href="#"><div style="float:left; width:auto;">5점</div></a>
					</div>
				</td>
				<td class="left top bold bottom" colspan="2">
					<div style="cursor:default; color:red;">
						<div style="float:left; width:40px;">5점</div>
						<div style="float:left; width:auto;">:긴급지원이 필요한 대상자</div>
					</div>
					<div style="cursor:default; color:red;">
						<div style="float:left; width:40px;">4~3점</div>
						<div style="float:left; width:auto;">:다중적 서비스 필요한 대상자</div>
					</div>
					<div style="cursor:default; color:red;">
						<div style="float:left; width:40px;">2~1점</div>
						<div style="float:left; width:auto;">:단순 서비스가 필요한 대상자</div>
					</div>
				</td>
				<td class="center bold bottom last">
					<div id="lblJ" basePoint="0" addPoint="0" secPoint="0" style="color:red;">0</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div id="divFoot" class="my_border_blue" style="border-top:none;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="342px">
			<col width="266px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center" style="border-top:1px solid #a6c0f3;">총&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;점</th>
				<td class="center bold" style="border-top:1px solid #d4d4d4;">사례관리(<span id="lblRst1" style="margin-left:3px; margin-right:3px; color:red;"></span>) / 기본형(<span id="lblRst2" style="margin-left:3px; margin-right:3px; color:red;"></span>)</td>
				<td class="left bold" style="border-top:1px solid #d4d4d4;">
					<div style="cursor:default; color:red;">
						<div style="float:left; width:68px;">사례관리형</div>
						<div style="float:left; width:auto;">:25점이상</div>
					</div>
					<div style="cursor:default; color:red;">
						<div style="float:left; width:68px;">기본형</div>
						<div style="float:left; width:auto;">:20점이상 25점 미만</div>
					</div>
					<div style="cursor:default; color:red;">
						<div style="float:left; width:68px;">산정보류</div>
						<div style="float:left; width:auto;">:20점 미만</div>
					</div>
				</td>
				<td class="center bold last" style="border-top:1px solid #d4d4d4;">
					<div id="lblTot" style="color:red;">0</div>
				</td>
			</tr>
			<tr>
				<th class="center bottom">사례관리자<br>COMMENT</th>
				<td class="bottom last" colspan="3">
					<textarea id="txtComment" name="multi" style="width:100%; height:35px;"></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<input id="chicSeq" type="hidden" value="<?=$chicSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>