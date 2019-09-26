<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_body_header.php');

	$code = $_SESSION['userCenterCode'];
	$mode = $_GET['mode'];

	if ($mode == '1'){
		$lsTitle = '배상책임보험 신청';
	}else if ($mode == '3'){
		$lsTitle = '배상책임보험 진행';
	}else if ($mode == '5'){
		$lsTitle = '배상책임보험 완료';
	}
?>
<form id="f" name="f">
<div class="title title_border"><?=$lsTitle;?></div>
<table class="my_table" style="width:100%;">
	<colgroup><?
		if ($mode == '5'){?>
			<col width="60px">
			<col width="90px"><?
		}?>
		<col width="40px">
		<col width="90px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr><?
			if ($mode == '5'){?>
				<th class="center">가입기간</th>
				<td class="center">
					<select id="cboPeriod" name="cbo" style="width:auto;"><?
					$sql = 'SELECT	seq
							,		from_dt
							,		to_dt
							FROM	insu_center
							WHERE	org_no = \''.$code.'\'
							AND		svc_cd = \'0\'
							ORDER	BY seq DESC';

					$conn->query($sql);
					$conn->fetch();

					$rowCount = $conn->row_count();

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['seq'];?>"><?=$myF->dateStyle($row['from_dt'],'.');?>~<?=$myF->dateStyle($row['to_dt'],'.');?></option><?
					}

					$conn->row_free();?>
					</select>
				</td><?
			}?>
			<th class="center">성명</th>
			<td class="center"><input id="name" name="txt" type="text" style="width:100%;"></td>
			<td class="left last"><span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span></td>
			<td class="right last"><?
				if ($mode == '5'){?>
					<span class="btn_pack m"><button type="button" onclick="lfShow();">출력</button></span><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="70px">
		<col width="100px">
		<col width="80px" span="4">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">보험사명</th>
			<th class="head">성명</th>
			<th class="head">주민번호</th>
			<th class="head">입사일자</th>
			<th class="head">퇴사일자</th>
			<th class="head">가입일자</th>
			<th class="head">해지일자</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot><td class="bottom last"></td></tfoot>
</table>
</form>
<script type="text/javascript">
$(document).ready(function(){
	__init_form(document.f);
	setTimeout('lfSearch()',200);
});

function lfSearch(){
	$.ajax({
		type :'POST'
	,	url  :'./mem_insu_search.php'
	,	data :{
			'name'	:$('#name').val()
		,	'kind'	:$('#kind').val()
		,	'seq'	:$('#cboPeriod').val()
		,	'mode'	:'<?=$mode;?>'
		}
	,	beforeSend: function(){
		}
	,	success: function(html){
			$('#tbodyList').html(html);

			/*
			var html = '';
			var list = data.split(String.fromCharCode(1));
			var idx  = 1;

			for(var i=0; i<list.length; i++){
				if (list[i]){
					var val = list[i].split(String.fromCharCode(2));
					var lsStat = '';
					var link = '&nbsp;';

					if (val[6] == '0' && val[3] != ''){
						//미가입 퇴사
					}else if (val[6] == '0' && val[3] == ''){
						//가입가능
						lsStat = ' ';
						link = '<span class="btn_pack m"><button type="button" onclick="lfRegInsu(\''+idx+'\',\''+val[7]+'\',\''+val[2]+'\',\''+val[4]+'\');">배상책임보험 가입</button></span>';
					}else if (val[6] == '1'){
						lsStat = '가입신청';
					}else if (val[6] == '3'){
						lsStat = '가입완료';
					}else if (val[6] == '7'){
						lsStat = '해지신청';
					}else if (val[6] == '9'){
						lsStat = '해지완료';
					}

					if (val[2] == val[4]){
						var clr = '#000000';
					}else{
						var clr = '#ff0000';
					}



					if ('<?=$mode;?>' == '3'){
						link = '<span class="btn_pack m"><button type="button" onclick="lfCancel(\''+idx+'\',\''+val[7]+'\',\''+val[2]+'\',\''+val[6]+'\');">신청취소</button></span>';
					}

					if (lsStat != ''){
						html += '<tr id="tr_'+idx+'">'
							 +  '<td class="center">'+idx+'</td>'
							 +  '<td class="left">'+val[1]+'</td>'
							 +  '<td class="center">'+val[0]+'</td>'
							 +  '<td class="center">'+val[2]+'</td>'
							 +  '<td class="center">'+val[3]+'</td>'
							 +  '<td class="center" style="color:'+clr+';">'+val[4]+'</td>'
							 +  '<td class="center">'+val[5]+'</td>'
							 +  '<td class="center">'+lsStat+'</td>'
							 +  '<td class="left last">'+link+'</td>'
							 +  '</tr>';
						idx ++;
					}
				}
			}

			if (!html){
				html = '<tr>'
					 + '<td class="center last" colspan="9">::검색된 데이타가 없습니다.::</td>'
					 + '</tr>';
			}

			$('#list').html(html);
			*/
		}
	});
}

function lfRegInsu(aiIdx, asJumin, asJoinDt, asStartDt){
	$.ajax({
		type :'POST'
	,	url  :'./mem_insu_fun.php'
	,	data :{
			'jumin':asJumin
		,	'join':asJoinDt
		,	'start':asStartDt
		,	'type':'REG'
		}
	,	beforeSend: function(){
		}
	,	success: function(result){
			if (result == 1){
				//$('td', $('#tr_'+aiIdx)).eq(5).text('가입신청');
				lfSearch();
			}else if (result == 9){
				alert('배상책입가입신청중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
			}else{
				alert(result);
			}
		}
	});
}

	function lfCancel(insuCd,jumin,seq){
		$.ajax({
			type :'POST'
		,	url  :'./mem_insu_fun.php'
		,	data :{
				'insuCd':insuCd
			,	'jumin'	:jumin
			,	'seq'	:seq
			,	'type'	:'CANCEL'
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				if (result == 1){
					//$('td', $('#tr_'+aiIdx)).eq(5).text('가입신청');
					lfSearch();
				}else if (result == 9){
					alert('잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
	/*
	function lfCancel(aiIdx, asJumin, asJoinDt, asStat){
		$.ajax({
			type :'POST'
		,	url  :'./mem_insu_fun.php'
		,	data :{
				'jumin':asJumin
			,	'join':asJoinDt
			,	'stat':asStat
			,	'type':'CANCEL'
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				if (result == 1){
					//$('td', $('#tr_'+aiIdx)).eq(5).text('가입신청');
					lfSearch();
				}else if (result == 9){
					alert('잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
	*/

function lfShow(){
	var lsCode  = '<?=$_SESSION["userCenterCode"];?>';

	$arguments = 'root=insu/main/'
		  + '&dir=P'
		  + '&fileName=reg_list'
		  + '&fileType=pdf'
		  + '&target=show.php'
		  + '&showForm=REPORT_INSU'
		  + '&code='+lsCode
		  + '&svcCd=0'
		  + '&seq='+$('#cboPeriod').val();

	__printPDF($arguments);
}
</script>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>