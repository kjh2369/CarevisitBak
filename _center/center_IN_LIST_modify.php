<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$no		= $_GET['no'];
	$orgNo	= $_GET['orgNo'];
	$cmsNo	= $_GET['cmsNo'];
	$cmsDt	= $_GET['cmsDt'];
	$cmsSeq	= $_GET['cmsSeq'];


	//기관정보
	$sql = 'SELECT	DISTINCT m00_store_nm AS org_nm, m00_mname AS mg_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$row = $conn->get_array($sql);

	$orgNm	= $row['org_nm'];
	$mgNm	= $row['mg_nm'];

	Unset($row);


	//CMS 정보
	$sql = 'SELECT	*
			FROM	cv_cms_reg
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$cmsNo.'\'
			AND		cms_dt	= \''.$cmsDt.'\'
			AND		seq		= \''.$cmsSeq.'\'';

	$row = $conn->get_array($sql);

	$inDt = $row['in_dt']; //입금일자
	$cmsCom = $row['cms_com']; //CMS 기관
	$cmsMemNo = $row['cms_mem_no']; //CMS 회원번호
	$inAmt = $row['in_amt']; //입금금액

	Unset($row);


	//적용금액
	$sql = 'SELECT	SUM(link_amt)
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$cmsNo.'\'
			AND		cms_dt	= \''.$cmsDt.'\'
			AND		cms_seq = \''.$cmsSeq.'\'
			AND		del_flag= \'N\'';

	$applyAmt = $conn->get_data($sql);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
	});

	function lfModify(mode){
		if (mode == '2'){
			if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;
		}

		$.ajax({
			type:'POST'
		,	url:'./center_IN_LIST_modify_save.php'
		,	data:{
				'orgNo'	:'<?=$orgNo;?>'
			,	'cmsNo'	:'<?=$cmsNo;?>'
			,	'cmsDt'	:'<?=$cmsDt;?>'
			,	'cmsSeq':'<?=$cmsSeq;?>'
			,	'inAmt'	:$('#txtInAmt').val()
			,	'mode'	:mode
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					if (mode == '1'){
						location.reload();
					}else{
						$('td',$('#ID_ROW_<?=$no;?>',$('#ID_BODY_LIST',opener.document))).css('color','RED').css('text-decoration','line-through');
						$('.CLS_BTN',$('#ID_ROW_<?=$no;?>',$('#ID_BODY_LIST',opener.document))).remove();
					}
				}else{
					alert(result);
				}
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

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="50px">
		<col width="200px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col width="70px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관명</th>
			<td class="center"><div class="left nowrap" style="width:200px;"><?=$orgNm;?></div></td>
			<th class="center">기관기호</th>
			<td class="left"><?=$orgNo;?></td>
			<th class="center">대표자</th>
			<td class="left"><div class="left nowrap" style="width:70px;"><?=$mgNm;?></div></td>
			<th class="center">CMS no</th>
			<td class="left last"><?=$cmsNo;?></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="60px">
		<col width="70px">
		<col width="60px">
		<col width="70px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">청구일자</th>
			<td class="left"><?=$myF->dateStyle($cmsDt,'.');?></td>
			<th class="center">입금일자</th>
			<td class="left"><?=$myF->dateStyle($inDt,'.');?></td>
			<th class="center">CMS기관</th>
			<td class="left"><?=$cmsCom;?></td>
			<th class="center">회원번호</th>
			<td class="left"><?=$cmsMemNo;?></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="80px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">입금금액</th>
			<td class="last">
				<input id="txtInAmt" type="text" value="<?=number_format($inAmt);?>" class="number" style="width:100%;">
			</td>
			<td class="">
				<span class="btn_pack small"><button onclick="lfModify('1');" style="color:BLUE;">변경</button></span>
				<span class="btn_pack small"><button onclick="lfModify('2');" style="color:RED;">삭제</button></span>
			</td>
			<th class="center">적용금액</th>
			<td class="left"><?=number_format($applyAmt);?></td>
			<th class="center">미적용금액</th>
			<td class="left last"><?=number_format($inAmt - $applyAmt);?></td>
		</tr>
	</tbody>
</table><?
$colgroup = '
	<col width="40px">
	<col width="70px">
	<col width="90px">
	<col width="90px">
	<col width="90px">
	<col>';?>
<div class="title title_border" style="background-color:WHITE;">적용내역</div>
<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">청구년월</th>
			<th class="head">청구금액</th>
			<th class="head">적용금액</th>
			<th class="head">미납금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
<div id="ID_APPLY_LIST" style="overflow-x:hidden; overflow-y:scroll; height:151px; background-color:WHITE;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody><?
			$sql = 'SELECT	yymm, seq, acct_ym, link_amt
					FROM	cv_cms_link
					WHERE	org_no	= \''.$orgNo.'\'
					AND		cms_no	= \''.$cmsNo.'\'
					AND		cms_dt	= \''.$cmsDt.'\'
					AND		cms_seq = \''.$cmsSeq.'\'
					AND		del_flag= \'N\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();
			$no = 1;

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['yymm'] > '201508'){
					$sql = 'SELECT	SUM(acct_amt) AS acct_amt
							FROM	cv_svc_acct_list
							WHERE	org_no	= \''.$orgNo.'\'
							AND		yymm	= \''.$row['yymm'].'\'';
				}else{
					$sql = 'SELECT	amt
							FROM	cv_svc_acct_amt
							WHERE	org_no	 = \''.$orgNo.'\'
							AND		yymm	<= \''.$row['yymm'].'\'
							ORDER	BY yymm
							LIMIT	1';
				}

				$acctAmt = $conn->get_data($sql);?>
				<tr>
					<td class="center"><?=$no;?></td>
					<td class="center"><?=$myF->_styleYYMM($row['acct_ym'],'.');?></td>
					<td class="center"><div class="right"><?=number_format($acctAmt);?></div></td>
					<td class="center"><div class="right"><?=number_format($row['link_amt']);?></div></td>
					<td class="center"><div class="right"><?=number_format($row['link_amt'] - $acctAmt);?></div></td>
					<td class="center last"></td>
				</tr><?

				$no ++;
			}

			$conn->row_free();?>
		</tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>