<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	a.svc_gbn
			,		a.svc_cd AS main_cd
			,		a.svc_nm AS main_nm
			,		b.svc_cd AS sub_cd
			,		b.svc_nm AS sub_nm
			FROM	(
					SELECT	\'1\' AS svc_gbn
					,		svc_cd
					,		svc_nm
					FROM	cv_svc_main
					WHERE	parent_cd IS NULL
					UNION	ALL
					SELECT	\'2\'
					,		svc_cd
					,		svc_nm
					FROM	cv_svc_sub
					WHERE	parent_cd IS NULL
					) AS a
			INNER	JOIN (
					SELECT	\'1\' AS svc_gbn
					,		svc_cd
					,		svc_nm
					,		parent_cd
					FROM	cv_svc_main
					WHERE	parent_cd IS NOT NULL
					UNION	ALL
					SELECT	\'2\'
					,		svc_cd
					,		svc_nm
					,		parent_cd
					FROM	cv_svc_sub
					WHERE	parent_cd IS NOT NULL
					) AS b
					ON		b.svc_gbn	= a.svc_gbn
					AND		b.parent_cd = a.svc_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$width = 360;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$svc[$row['svc_gbn'].'_'.$row['main_cd']]){
			$svc[$row['svc_gbn'].'_'.$row['main_cd']]['name'] = $row['main_nm'];
		}
		$svc[$row['svc_gbn'].'_'.$row['main_cd']]['list'][] = Array('gbn'=>$row['svc_gbn'], 'cd'=>$row['sub_cd'], 'nm'=>$row['sub_nm']);
		$svc[$row['svc_gbn'].'_'.$row['main_cd']]['cnt'] ++;

		$width += 90;
	}

	$conn->row_free();
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_ACCT_DEPOSIT_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year':$('#yymm').attr('year')
			,	'month':$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col width="90px">
		<col width="80px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">CMS번호</th>
			<th class="head">대표자명</th>
			<th class="head">연락처</th>
			<th class="head">청구금액</th>
			<th class="head">입금금액</th>
			<th class="head">미납금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>