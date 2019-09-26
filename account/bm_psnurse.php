<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		$('.CLS_ORG_NO').unbind('mouseover').bind('mouseover',function(){
			if ($(this).attr('selYn') == 'Y') return;
			$(this).css('background-color','D9E5FF');
		}).unbind('mouseout').bind('mouseout',function(){
			if ($(this).attr('selYn') == 'Y') return;
			$(this).css('background-color','FFFFFF');
		}).unbind('click').bind('click',function(){
			$('.CLS_ORG_NO').attr('selYn','N').css('background-color','FFFFFF');
			$(this).attr('selYn','Y').css('background-color','FAF4C0');
			lfSearch($(this).attr('code'));
		});
	});

	function lfSearch(orgNo){
		$.ajax({
			type :'POST'
		,	url  :'./bm_psnurse_search.php'
		,	data :{
				'orgNo':orgNo
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfApply(){
		var orgNo = $('.CLS_ORG_NO[selYn="Y"]').attr('code');

		$.ajax({
			type :'POST'
		,	url  :'./bm_psnurse_save.php'
		,	data :{
				'orgNo'	:orgNo
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			,	'cnt'	:$('#txtCnt').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSearch(orgNo);
				}else if (result == 7){
					alert('적용기간이 중복됩니다. 확인 후 다시 입력하여 주십시오.');
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfRemove(seq){
		var orgNo = $('.CLS_ORG_NO[selYn="Y"]').attr('code');

		$.ajax({
			type :'POST'
		,	url  :'./bm_psnurse_save.php'
		,	data :{
				'orgNo'	:orgNo
			,	'seq'	:seq
			,	'cnt'	:'NULL'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSearch(orgNo);
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">개인간병 인원수 설정</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="250px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">기관명</th>
			<th class="head last">설정내역</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top">
				<div id="ID_CENTER_LIST"><?
					$sql = 'SELECT	DISTINCT m00_mcode AS org_no
							,		m00_store_nm AS org_nm
							FROM	m00center
							INNER	JOIN	b02center
									ON		b02_center = m00_mcode
							WHERE	m00_domain = \''.$gDomain.'\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<div code="<?=$row['org_no'];?>" class="CLS_ORG_NO left" selYn="N" style="cursor:default; <?=$i > 0 ? 'border-top:1px dashed #CCCCCC;' : '';?>"><?=$row['org_nm'];?></div><?
					}

					$conn->row_free();?>
				</div>
			</td>
			<td class="center top last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col width="50px">
						<col width="70px">
						<col width="50px">
						<col width="50px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">적용년월</th>
							<td><input id="txtFromDt" type="text" value="" class="yymm"></td>
							<th class="center">종료년월</th>
							<td><input id="txtToDt" type="text" value="" class="yymm"></td>
							<th class="center">가구수</th>
							<td><input id="txtCnt" type="text" value="0" class="number" style="width:100%;"></td>
							<td class="left last">
								<span class="btn_pack m"><button onclick="lfApply();">적용</button></span>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40px">
						<col width="130px">
						<col width="70px">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">적용기간</th>
							<th class="head">인원수</th>
							<th class="head last">비고</th>
						</tr>
					</thead>
					<tbody id="ID_LIST"></tbody>
				</table>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>