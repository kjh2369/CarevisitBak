<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
$(document).ready(function(){
	$.ajax({
		type: 'POST'
	,	url : './mem_basic_search.php'
	,	data: {
		}
	,	beforeSend: function (){
			$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center;\'>'+__get_loading()+'</div></center></div>').show();
		}
	,	success: function (data){
			var list = data.split(String.fromCharCode(1));
			var html = '';

			/*
			$data .= 0 $ed->en($row['jumin']).chr(2)
			  . 1 $myF->issStyle($row['jumin']).chr(2)
			  . 2 $row['name'].chr(2)
			  . 3 $row['join_dt'].chr(2)
			  . 4 $row['quit_dt'].chr(2)
			  . 5 $row['employ_type'].chr(2)
			  . 6 $row['employ_stat'].chr(2)
			  . 7 $row['weekly'].chr(2)
			  . 8 $row['bank_acct'].chr(2)
			  . 9 $row['bank_no'].chr(2)
			  . 10 $row['bank_nm'].chr(2)
			  . 11 $row['prolong_rate'].chr(2)
			  . 12 $row['holiday_rate_gbn'].chr(2)
			  . 13 $row['holiday_rate'].chr(2)
			  . 14 $row['annuity_yn'].chr(2)
			  . 15 $row['health_yn'].chr(2)
			  . 16 $row['sanje_yn'].chr(2)
			  . 17 $row['employ_yn'].chr(2)
			  . 18 $row['paye_yn'].chr(2)
			  . 19 $row['annuity_amt'].chr(1);
			*/
			var weekly = new Array('일','월','화','수','목','금','토');

			for(var i=0; i<list.length; i++){
				if (list[i]){
					var val = list[i].split(String.fromCharCode(2));
					var employType = '<div style="float:left; width:auto; margin-left:5px;">&nbsp;&nbsp;&nbsp;</div>'
					,	employStat = '<div style="float:left; width:auto; margin-left:5px;">&nbsp;&nbsp;&nbsp;</div>'
					,	weekday = '<div style="float:left; width:auto; margin-left:5px;">&nbsp;&nbsp;&nbsp;</div>'
					,	insu4 = ''
					,	insuStat = '';

					//고용형태
					switch(val[5]){
						case '1':
							employType = '<div style="float:left; width:auto; margin-left:5px;" title="정규직">정</div>';
							break;

						case '2':
							employType = '<div style="float:left; width:auto; margin-left:5px;" title="계약직">계</div>';
							break;

						case '3':
							employType = '<div style="float:left; width:auto; margin-left:5px; color:blue;" title="단시간(60시간이상)">단</div>';
							break;

						case '4':
							employType = '<div style="float:left; width:auto; margin-left:5px; color:red;" title="단시간(60시간미만)">단</div>';
							break;
					}

					//고용상태
					switch(val[6]){
						case '1':
							employStat = '<div style="float:left; width:auto; margin-left:5px;" title="재직">재</div>'; //
							break;

						case '2':
							employStat = '<div style="float:left; width:auto; margin-left:5px;" title="휴직">휴</div>'; //
							break;

						case '9':
							employStat = '<div style="float:left; width:auto; margin-left:5px; color:red;" title="퇴직">퇴</div>'; //
							break;
					}

					weekday = weekly[val[7]];

					//주휴희망요일
					switch(val[7]){
						case '6':
							weekday = '<div style="float:left; width:auto; margin-left:5px; color:blue;">'+weekday+'</div>';
							break;

						case '0':
							weekday = '<div style="float:left; width:auto; margin-left:5px; color:red;">'+weekday+'</div>';
							break;

						default:
							weekday = '<div style="float:left; width:auto; margin-left:5px;">'+weekday+'</div>';
					}

					//원천징수대상자
					if (val[18] == 'Y'){
						insu4 = '원천징수';
					}else{
						insu4 += '<div style="float:left; width:auto; margin-left:5px; color:#'+(val[14] == 'Y' ? '' : 'cccccc')+';">국</div>';
						insu4 += '<div style="float:left; width:auto; margin-left:5px; color:#'+(val[15] == 'Y' ? '' : 'cccccc')+';">건</div>';
						insu4 += '<div style="float:left; width:auto; margin-left:5px; color:#'+(val[17] == 'Y' ? '' : 'cccccc')+';">고</div>';
						insu4 += '<div style="float:left; width:auto; margin-left:5px; color:#'+(val[16] == 'Y' ? '' : 'cccccc')+';">산</div>';
					}

					//배상책임보험
					switch(val[20]){
						case '1':
							insuStat = '가입신청';
							break;

						case '3':
							insuStat = '가입';
							break;

						case '7':
							insuStat = '해지신청';
							break;

						case '9':
							insuStat = '해지';
							break;

						default:
							insuStat = '';
					}

					html += '<tr>'
						 +  '<td class="center">'+(i+1)+'</td>'
						 +  '<td class="center"><div class="left nowrap" style="width:70px;">'+val[2]+'</div></td>'
						 +  '<td class="center">'+val[1]+'</td>'
						 +  '<td class="center">'+employType+employStat+weekday+'</td>'
						 +  '<td class="center">'+insu4+'</td>'
						 +  '<td class="center">'+insuStat+'</td>'
						 +  '<td class="center"></td>'
						 +  '<td class="center"></td>'
						 +  '<td class="center"></td>'
						 +  '<td class="center"></td>'
						 +  '<td class="center last"></td>'
						 +  '</tr>';
				}
			}

			$('#list').html(html);
			$('#tempLodingBar').remove();
		}
	,	error: function (){
		}
	}).responseXML;
});
</script>
<div class="title title_border">기초자료등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="90px">
		<col width="57px">
		<col width="74px">
		<col width="60px">
		<col width="70px" span="4">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">직원명</th>
			<th class="head">주민번호</th>
			<th class="head">고용정보</th>
			<th class="head">4대보험</th>
			<th class="head">책임보험</th>
			<th class="head"></th>
			<th class="head"></th>
			<th class="head"></th>
			<th class="head"></th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>