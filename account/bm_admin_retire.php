<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
	});

	function lfSearch(){
		if (!$('#cboOrg').val()){
			alert('지점을 선택하여 주십시오.');
			$('#cboOrg').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'../__management/retire/retire_search.php'
		,	data:{
				'orgNo'	:$('#cboOrg').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			,	'type'	:'ADMIN'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				html = html.split('<!--CUT_LINE-->');
				$('#ID_LIST').html(html[0]);
				$('#ID_SUM').html(html[1]);
			}
		,	complete:function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfExcel(){
		if (!$('#cboOrg').val()) return;

		var parm = new Array();
			parm = {
				'orgNo'	:$('#cboOrg').val()
			,	'fromDt':$('#txtFromDt').val().replace('-','')
			,	'toDt'	:$('#txtToDt').val().replace('-','')
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('method', 'post');
		form.setAttribute('action', '../__management/retire_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">퇴직적립금 현황</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>지점</th>
			<td>
				<select id="cboOrg" style="width:auto;">
					<option value="">-지점을 선택하여 주십시오.-</option><?
					$sql = 'SELECT	DISTINCT
									m00_mcode AS org_no
							,		m00_store_nm AS org_nm
							,		m00_mname AS manager
							FROM	m00center
							INNER	JOIN	b02center
									ON		b02_center = m00_mcode
							WHERE	m00_domain = \''.$gDomain.'\'
							ORDER	BY org_no';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['org_no'];?>"><?=$row['org_nm'];?>[<?=$row['manager'];?>]</option><?
					}

					$conn->row_free();?>
				</select>
			</td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><button onclick="lfExcel();">Excel</button></span>
			</td>
		</tr>
		<tr>
			<th>기간</th>
			<td>
				<input id="txtFromDt" type="text" value="<?=$year;?>-<?=$month;?>" class="yymm"> ~
				<input id="txtToDt" type="text" value="<?=$year;?>-<?=$month;?>" class="yymm">
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px" span="3">
		<col width="100px">
		<col width="80px" span="2">
		<col width="100px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">입사일자</th>
			<th class="head">퇴사일자</th>
			<th class="head">고용형태</th>
			<th class="head">근무일수</th>
			<th class="head">근무시간</th>
			<th class="head">급여</th>
			<th class="head">퇴직적립금</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
	<tbody id="ID_SUM"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>