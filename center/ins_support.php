<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];

	$sql = 'SELECT	COUNT(*)
			FROM	center_ins_support
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	< \'201601\'
			AND		CASE WHEN support_yn = \'Y\' THEN 1
						 WHEN emp_yn = \'Y\' THEN 1 ELSE 0 END = 1';

	$newYn = $conn->get_data($sql);
	
	if ($newYn > 0){
		$newYn = 'N';
	}else{
		$newYn = 'Y';
	}

	$sql = 'SELECT	new_yn
			FROm	center_ins_spt_option
			WHERE	org_no = \''.$orgNo.'\'';

	$newFlag = $conn->get_data($sql);

?>
<div style="padding-left:10px;">※ <span style="color:blue;">10인미만 사업장</span> 국민연금 지원설정(두루누리 사회보험)</div>
<div style="padding-left:10px;">※ 월평균 보수 <span style="color:red;">130만원 이하</span> 근로자는 <span style="color:blue;">국민연금 1/2</span> 지원(2015년 이전)</div>
<div style="padding-left:28px;">2015년 1월부터 140만원미만</div>
<div style="padding-left:28px;">2018년 1월부터 190만원미만</div>
<div style="padding-left:28px;">2019년 1월부터 210만원미만</div>
<div style="padding-left:28px;">위 적용은 <span style="color:blue;">2013년 4월</span>부터 적용가능합니다.</div>
<div style="padding-left:10px;">※ 2016년 이전까지는<span style="color:blue;"> 50%</span>지원 2016년 부터 <span style="color:blue;">신규가입자는 60%</span>지원 <span style="color:blue;">기존가입자는 40%</span>지원 됩니다.</div>
<div style="padding-left:28px;">2018년부터 <span style="color:blue;">신규가입자</span>(5인미만 <span style="color:blue;">90%</span>, 5인이상 10인미만은 <span style="color:blue;">80%</span>), 기존기관은 40%지원됩니다.</br> ( 2018년부터 <span style="color:blue;">신규가입여부</span> 는 <span style="color:blue;">개별 직원정보</span>에서 설정할 수 있습니다. )</div>
<div style="padding-left:10px;">※ 일자리안정자금 지원금 신청기관 ( 2019년 <span style="color:blue;">신규입사자 건강보험료 50%</span> 경감, <span style="color:blue;">이전입사자 건강보험료 30% 경감</span> 적용 )</div>

<script type="text/javascript">
	$(document).ready(function(){
		lfNpsSptSet();

		if('<?=$year;?>' < 2018){
			$('#showNewYn').show();
		}else {
			$('#showNewYn').hide();
		}
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYear').text());

		year += pos;

		$('#lblYear').text(year);
		
		if(year < 2018){
			$('#showNewYn').show();
		}else {
			$('#showNewYn').hide();
		}

		lfNpsSptSet();
	}

	function lfNpsSptSet(){
		var year = __str2num($('#lblYear').text());

		if (year < 2013){
			$('input:checkbox[id^="chkNpsSpt"]').attr('disabled',true);
		}else if (year > 2013){
			$('input:checkbox[id^="chkNpsSpt"]').attr('disabled',false);
		}else{
			$('input:checkbox[id^="chkNpsSpt"]').attr('disabled',false);
			$('input:checkbox[id="chkNpsSpt1"]').attr('disabled',true);
			$('input:checkbox[id="chkNpsSpt2"]').attr('disabled',true);
			$('input:checkbox[id="chkNpsSpt3"]').attr('disabled',true);
		}
		
		

		lfNpsSptFind();
	}

	function lfNpsSptFind(){
		$.ajax({
			type: 'POST'
		,	url : './nps_support.php'
		,	data: {
				'type':'FIND'
			,	'year':$('#lblYear').text()
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				var col = __parseStr(data);

				for(var i=1; i<=12; i++){
					$('#chkNpsSpt'+i).attr('checked',(col['nps'+i] == 'Y' ? 'checked' : ''));
					$('#chkEmpSpt'+i).attr('checked',(col['emp'+i] == 'Y' ? 'checked' : ''));
					$('#chkHealthSpt'+i).attr('checked',(col['health'+i] == 'Y' ? 'checked' : ''));
					$('#chk5Under'+i).attr('checked', (col['per'+i] == 'Y' ? 'checked' : ''));
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfNpsSptApply(){
		
		$.ajax({
			type: 'POST'
		,	url : './nps_support.php'
		,	data: {
				'type':'APPLY'
			,	'year':$('#lblYear').text()
			,	'nps1':$('#chkNpsSpt1').attr('checked') ? 'Y' : 'N'
			,	'nps2':$('#chkNpsSpt2').attr('checked') ? 'Y' : 'N'
			,	'nps3':$('#chkNpsSpt3').attr('checked') ? 'Y' : 'N'
			,	'nps4':$('#chkNpsSpt4').attr('checked') ? 'Y' : 'N'
			,	'nps5':$('#chkNpsSpt5').attr('checked') ? 'Y' : 'N'
			,	'nps6':$('#chkNpsSpt6').attr('checked') ? 'Y' : 'N'
			,	'nps7':$('#chkNpsSpt7').attr('checked') ? 'Y' : 'N'
			,	'nps8':$('#chkNpsSpt8').attr('checked') ? 'Y' : 'N'
			,	'nps9':$('#chkNpsSpt9').attr('checked') ? 'Y' : 'N'
			,	'nps10':$('#chkNpsSpt10').attr('checked') ? 'Y' : 'N'
			,	'nps11':$('#chkNpsSpt11').attr('checked') ? 'Y' : 'N'
			,	'nps12':$('#chkNpsSpt12').attr('checked') ? 'Y' : 'N'
			,	'emp1':$('#chkEmpSpt1').attr('checked') ? 'Y' : 'N'
			,	'emp2':$('#chkEmpSpt2').attr('checked') ? 'Y' : 'N'
			,	'emp3':$('#chkEmpSpt3').attr('checked') ? 'Y' : 'N'
			,	'emp4':$('#chkEmpSpt4').attr('checked') ? 'Y' : 'N'
			,	'emp5':$('#chkEmpSpt5').attr('checked') ? 'Y' : 'N'
			,	'emp6':$('#chkEmpSpt6').attr('checked') ? 'Y' : 'N'
			,	'emp7':$('#chkEmpSpt7').attr('checked') ? 'Y' : 'N'
			,	'emp8':$('#chkEmpSpt8').attr('checked') ? 'Y' : 'N'
			,	'emp9':$('#chkEmpSpt9').attr('checked') ? 'Y' : 'N'
			,	'emp10':$('#chkEmpSpt10').attr('checked') ? 'Y' : 'N'
			,	'emp11':$('#chkEmpSpt11').attr('checked') ? 'Y' : 'N'
			,	'emp12':$('#chkEmpSpt12').attr('checked') ? 'Y' : 'N'
			,	'under1':$('#chk5Under1').attr('checked') ? 'Y' : 'N'
			,	'under2':$('#chk5Under2').attr('checked') ? 'Y' : 'N'
			,	'under3':$('#chk5Under3').attr('checked') ? 'Y' : 'N'
			,	'under4':$('#chk5Under4').attr('checked') ? 'Y' : 'N'
			,	'under5':$('#chk5Under5').attr('checked') ? 'Y' : 'N'
			,	'under6':$('#chk5Under6').attr('checked') ? 'Y' : 'N'
			,	'under7':$('#chk5Under7').attr('checked') ? 'Y' : 'N'
			,	'under8':$('#chk5Under8').attr('checked') ? 'Y' : 'N'
			,	'under9':$('#chk5Under9').attr('checked') ? 'Y' : 'N'
			,	'under10':$('#chk5Under10').attr('checked') ? 'Y' : 'N'
			,	'under11':$('#chk5Under11').attr('checked') ? 'Y' : 'N'
			,	'under12':$('#chk5Under12').attr('checked') ? 'Y' : 'N'
			,	'newYn':$('#chkNewYn').attr('checked') ? 'Y' : 'N'

			,	'health1':$('#chkHealthSpt1').attr('checked') ? 'Y' : 'N'
			,	'health2':$('#chkHealthSpt2').attr('checked') ? 'Y' : 'N'
			,	'health3':$('#chkHealthSpt3').attr('checked') ? 'Y' : 'N'
			,	'health4':$('#chkHealthSpt4').attr('checked') ? 'Y' : 'N'
			,	'health5':$('#chkHealthSpt5').attr('checked') ? 'Y' : 'N'
			,	'health6':$('#chkHealthSpt6').attr('checked') ? 'Y' : 'N'
			,	'health7':$('#chkHealthSpt7').attr('checked') ? 'Y' : 'N'
			,	'health8':$('#chkHealthSpt8').attr('checked') ? 'Y' : 'N'
			,	'health9':$('#chkHealthSpt9').attr('checked') ? 'Y' : 'N'
			,	'health10':$('#chkHealthSpt10').attr('checked') ? 'Y' : 'N'
			,	'health11':$('#chkHealthSpt11').attr('checked') ? 'Y' : 'N'
			,	'health12':$('#chkHealthSpt12').attr('checked') ? 'Y' : 'N'
			,	'newHealthYn':$('#chkNewHealthYn').attr('checked') ? 'Y' : 'N'
			}
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (!result){
					alert('정상적으로 자장되었습니다.');
				}else{
					alert(result);
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>

<table class="my_table my_border_blue" style="width:auto; margin-left:20px; margin-bottom:10px;">
	<colgroup>
		<col width="85px">
		<col width="70px">
		<col width="30px" span="12">
		<col width="50px">
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<th class="center">구분</th>
			<th class="center">1월</th>
			<th class="center">2월</th>
			<th class="center">3월</th>
			<th class="center">4월</th>
			<th class="center">5월</th>
			<th class="center">6월</th>
			<th class="center">7월</th>
			<th class="center">8월</th>
			<th class="center">9월</th>
			<th class="center">10월</th>
			<th class="center">11월</th>
			<th class="center">12월</th>
			<td class="center" rowspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfNpsSptApply();">적용</button></span>
			</td>
		</tr>
		<tr>
			<td rowspan="4">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="center">국민연금</td>
			<td class="center"><input id="chkNpsSpt1" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt2" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt3" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt4" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt5" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt6" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt7" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt8" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt9" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt10" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt11" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkNpsSpt12" type="checkbox" class="checkbox" value="Y"></td>
		</tr>
		<tr>
			<td class="center">고용보험</td>
			<td class="center"><input id="chkEmpSpt1" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt2" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt3" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt4" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt5" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt6" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt7" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt8" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt9" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt10" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt11" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkEmpSpt12" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center">
				<div id="showNewYn"><label><input id="chkNewYn" type="checkbox" class="checkbox" value="Y" <?=$newFlag == 'Y' ? 'checked' : '';?> <?=$newYn != 'Y' ? 'disabled="false"' : '';?>>신규</label></div>
			</td>
		</tr>
		<tr>
			<td class="center">건강보험</td>
			<td class="center"><input id="chkHealthSpt1" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt2" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt3" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt4" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt5" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt6" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt7" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt8" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt9" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt10" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt11" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chkHealthSpt12" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"></td>
		</tr>
		<tr>
			<td class="center">5인미만</td>
			<td class="center"><input id="chk5Under1" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under2" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under3" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under4" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under5" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under6" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under7" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under8" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under9" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under10" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under11" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center"><input id="chk5Under12" type="checkbox" class="checkbox" value="Y"></td>
			<td class="center">&nbsp;</td>
		</tr>
	</tbody>
</table>