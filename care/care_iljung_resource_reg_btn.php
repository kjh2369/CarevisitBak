<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['suga'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$from	= $_POST['from'];
	$to		= $_POST['to'];
?>
<style>
	.thStyle{
		border-bottom:2px solid #a6c0f3;
	}
</style>
<script type="text/javascript">
	function lfSave(){
		var para = '';
		$('.clsCal').each(function(){
			if ($(this).attr('resourceCd')){
				para += 'day='+$(this).attr('day');
				para += '&cnt='+$(this).attr('cnt');
				para += '&week='+$(this).attr('week');
				para += '&svcKind='+$(this).attr('svcKind');
				para += '&from='+$(this).attr('from');
				para += '&resourceCd='+$(this).attr('resourceCd');
				para += '&resourceNm='+$(this).attr('resourceNm');
				para += '&memCd='+$(this).attr('memCd');
				para += '&memNm='+$(this).attr('memNm');
				para += '&cost='+$(this).attr('cost');
				para += '&client='+$(this).attr('client');
				para += '&stat='+$(this).attr('stat');
				para += '&seq='+$(this).attr('svcSeq');
				para += ';';
			}
		});
		
		/*
		if (!$('#lblResource').attr('code')){
			alert('자원을 선택하여 주십시오.');
			lfResourceFind();
			return;
		}
		
		if (!$('#lblMemName').attr('jumin')){
			alert('담당직원을 선택하여 주십시오.');
			lfMemberFind();
			return;
		}

		if (!$('#txtFromH').val() || !$('#txtFromM').val()){
			alert('시작시간을 입력하여 주심시오.');
			if (!$('#txtFromH').val()){
				$('#txtFromH').focus();
				return;
			}
			if (!$('#txtFromM').val()){
				$('#txtFromM').focus();
				return;
			}
		}

		var liDayCnt = $('div[id^="txtDay_"][value="Y"]').length;

		if (liDayCnt == 0){
			alert('배정할 일자를 선택하여 주십시오.');
			return;
		}

		var liClientCnt = $('input:checkbox[name="chk"]:checked').length;

		if (liClientCnt == 0){
			alert('대상자를 선택하여 주십시오.');
			return;
		}
		*/

		if (!para){
			alert('일정을 입력하여 주십시오.');
			return;
		}

		
		$.ajax({
			type : 'POST'
		,	url  : './care_iljung_resource_reg_save.php'
		,	data : {
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			,	'para'	:para
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					setTimeout('lfLoadIljung()',100);
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	
	}

	function lfDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type : 'POST'
		,	url  : './care_iljung_resource_reg_delete.php'
		,	data : {
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					setTimeout('lfLoadIljung()',100);
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function lfCalClean(){
		$('.clsCal[stat="9"][ynSave="N"]').remove();
	}
</script>
<table class="my_table" style="width:100%; <?=($type == 'CONF' ? 'border-top:2px solid #0e69b0; margin-top:-2px;' : '');?>">
	<colgroup>
		<col width="110px">
		<col width="200px">
		<col>
		<col width="500px">
	</colgroup>
	<tbody>
		<tr>
			<td class="left bold last"><div class="title"><?=intval($year);?>년 <?=intval($month);?>월</div></td>
			<td class="right last">
				<img src="../iljung/img/btn_calen8.gif" onclick="lfCalClean();" alt="미저장일정지우기">
				<img src="../image/btn_save_2.png" onclick="lfSave();" alt="일정저장">
				<img src="../image/btn11.gif" onclick="lfDelete();" alt="일적삭제">
				<!--img src="../image/btn_print_1.png" onclick="lfShowCaln('Y');" alt="금액표시된 출력물입니다."-->
				<!--img src="../image/btn_print_2.png" onclick="lfShowCaln('N');" alt="금액 미표시된 출력물입니다."-->
			</td>
		</tr>
	</tbody>
</table>
<table id="tblCalBody" ynLoad="N" class="my_table" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="15%">
		<col width="14%">
		<col width="14%">
		<col width="14%">
		<col width="14%">
		<col width="14%">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold thStyle clsCalCol0"><div style="cursor:default; color:ff0000;">일</div></th>
			<th class="head bold thStyle clsCalCol1"><div style="cursor:default; color:000000;">월</div></th>
			<th class="head bold thStyle clsCalCol2"><div style="cursor:default; color:000000;">화</div></th>
			<th class="head bold thStyle clsCalCol3"><div style="cursor:default; color:000000;">수</div></th>
			<th class="head bold thStyle clsCalCol4"><div style="cursor:default; color:000000;">목</div></th>
			<th class="head bold thStyle clsCalCol5"><div style="cursor:default; color:000000;">금</div></th>
			<th class="head bold thStyle clsCalCol6 last"><div style="cursor:default; color:0000ff;">토</div></th>
		</tr>
	</thead>
</table>
<?
	include_once('../inc/_db_close.php');
?>