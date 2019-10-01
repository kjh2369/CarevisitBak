<?
	if ($view_type == 'read'){?>
		<div style="float:left; width:100%; margin-left:10px; margin-top:10px; margin-right:10px; "><?
	}else{?>
		<div style="width:100%; margin-top:10px; float:left;"><?
	}

	$year = Date('Y') - 1;?>

	<script type="text/javascript">
		$(document).ready(function(){
			$('input:text[name="txtAvgMon"]').unbind('change').bind('change',function(){
				var mon = __str2num($(this).val());

				if (mon > 12){
					$(this).val('12');
				}
			});

			lfSalaryAvgSearch();
		});

		function lfMoveYear(aiPos){
			$('#lblYear').text(parseInt($('#lblYear').text()) + aiPos);

			lfSalaryAvgSearch();
		}

		function lfSalaryAvgSearch(){
			$.ajax({
				type:'POST'
			,	async:false
			,	url:'./mem_salary_avg_search.php'
			,	data:{
					'jumin':$('#memJumin').val()
				,	'year':$('#lblYear').text()
				}
			,	success:function(data){
					var val = data.split(String.fromCharCode(2));

					$('#txtAvgHMon').val(__num2str(val[0]));
					$('#txtAvgHPay').val(__num2str(val[1]));
					$('#txtAvgHAmt').val(__num2str(val[2]));
					$('#txtAvgSMon').val(__num2str(val[3]));
					$('#txtAvgSAmt').val(__num2str(val[4]));
					$('#txtAvgBCnt').val(__num2str(val[5]));
					$('#txtAvgBPay').val(__num2str(val[6]));
					$('#txtAvgBMon').val(__num2str(val[7]));
				}
			}).responseXML;
		}

		function lfSalaryAvgReg(){
			if ($('#memMode').val() == '0'){
				alert('데이타 저장 후 등록하여 주십시오.');
				return;
			}

			$.ajax({
				type:'POST'
			,	async:false
			,	url:'./mem_salary_avg_reg.php'
			,	data:{
					'jumin':$('#memJumin').val()
				,	'year':$('#lblYear').text()
				,	'hMon':__str2num($('#txtAvgHMon').val())
				,	'hPay':__str2num($('#txtAvgHPay').val())
				,	'hAmt':__str2num($('#txtAvgHAmt').val())
				,	'sMon':__str2num($('#txtAvgSMon').val())
				,	'sAmt':__str2num($('#txtAvgSAmt').val())
				,	'bCnt':__str2num($('#txtAvgBCnt').val())
				,	'bPay':__str2num($('#txtAvgBPay').val())
				,	'bMon':__str2num($('#txtAvgBMon').val())
				}
			,	success:function(result){
					if (result == 1){
						alert('정상적으로 처리되었습니다.');
					}else if (result == 9){
						alert('등록중 오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.');
					}else{
						alert(result);
					}
				}
			}).responseXML;
		}

		function lfSalaryAvgDel(){
			$.ajax({
				type:'POST'
			,	async:false
			,	url:'./mem_salary_avg_del.php'
			,	data:{
					'jumin':$('#memJumin').val()
				,	'year':$('#lblYear').text()
				}
			,	success:function(result){
					if (result == 1){
						alert('정상적으로 처리되었습니다.');
						lfSalaryAvgSearch();
					}else if (result == 9){
						alert('삭제중 오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.');
					}else{
						alert(result);
					}
				}
			}).responseXML;
		}
	</script>

	<!--table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="85px">
			<col width="70px" span="8">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="20">평균급여내역</th>
			</tr>
			<tr>
				<th class="head" rowspan="2">년도</th>
				<th class="head" rowspan="2">평균시급</th>
				<th class="head" colspan="2">시급</th>
				<th class="head" colspan="2">월급</th>
				<th class="head" colspan="3">목욕</th>
				<th class="head last" rowspan="2">비고</th>
			</tr>
			<tr>
				<th class="head">개월수</th>
				<th class="head">평균급여</th>
				<th class="head">개월수</th>
				<th class="head">평균급여</th>
				<th class="head">월목욕횟수</th>
				<th class="head">회당금액</th>
				<th class="head">개월수</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center">
					<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					</div>
				</td>
				<td class="center"><input id="txtAvgHPay" name="txtAvgPay" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtAvgHMon" name="txtAvgMon" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtAvgHAmt" name="txtAvgPay" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtAvgSMon" name="txtAvgMon" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtAvgSAmt" name="txtAvgPay" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtAvgBCnt" name="txtAvgCnt" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtAvgBPay" name="txtAvgPay" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtAvgBMon" name="txtAvgMon" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="left last">
					<span class="btn_pack m"><button type="button" onclick="lfSalaryAvgReg();">등록</button></span>
					<span class="btn_pack m"><button type="button" onclick="lfSalaryAvgDel();">삭제</button></span>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td class="left last" colspan="10">
					- 평균급여내역등록은 보수비교표 작성시 사용됩니다.<br>
					- 2012년의 급여계산내역이 없으신 기관에서는 직원별 2012년 급여내역을 등록해주십시오.
				</td>
			</tr>
		</tfoot>
	</table-->

	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold">특이사항(메모)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center"><?
				if ($view_type == 'read'){?>
					<div class="left top"><?=nl2br(stripslashes($mst[$basic_kind]['m02_memo']));?></div><?
				}else{?>
					<textarea name="mem_memo" style="width:100%; height:50px;"><?=stripslashes($mst[$basic_kind]['m02_memo']);?></textarea><?
				}?>
				</td>
			</tr>
		</tbody>
	</table>
</div>