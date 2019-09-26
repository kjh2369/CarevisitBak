<?
	include_once('../inc/_header.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사례접수일지
	 *********************************************************/

	$strName	= $_POST['txtName'];
	$strFrom	= str_replace('-', '', $_POST['txtFrom']);
	$strTo		= str_replace('-', '', $_POST['txtTo']);
	$strEndYn	= $_POST['cboEndYn'];

	if (!$strFrom) $strFrom = $strTo;
	if (!$strTo) $strTo = $strFrom;

	$orgNo = $_SESSION['userCenterCode'];
	$sr = $_GET['sr'];

	//접수방법
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CT\'';

	$rctGbn = $conn->_fetch_array($sql,'code');

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_REQUEST['page'];

	if (Empty($page)){
		$page = 1;
	}

	$sql = 'SELECT	COUNT(DISTINCT IPIN)
			FROM	hce_receipt
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= org_no
					AND		m03_mkind	= \'6\'
					AND		m03_key		= IPIN';

	if ($strName){
		$sql .= '	AND		m03_name >= \''.$strName.'\'';
	}

	$sql .= '
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'
			AND		del_flag= \'N\'';

	if ($strFrom && $strTo){
		$sql .= '
			AND		rcpt_dt >= \''.$strFrom.'\'
			AND		rcpt_dt <= \''.$strTo.'\'';
	}

	if ($strEndYn){
		$sql .= '
			AND		end_flag = \''.$strEndYn.'\'';
	}

	$totCnt = $conn->get_data($sql);

	// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
	if ($totCnt < (IntVal($page) - 1) * $itemCnt){
		$page = 1;
	}

	$params = array(
		'curMethod'		=> 'post',
		'curPage'		=> 'javascript:lfSearch',
		'curPageNum'	=> $page,
		'pageVar'		=> 'page',
		'extraVar'		=> '',
		'totalItem'		=> $totCnt,
		'perPage'		=> $pageCnt,
		'perItem'		=> $itemCnt,
		'prevPage'		=> '[이전]',
		'nextPage'		=> '[다음]',
		'prevPerPage'	=> '[이전'.$pageCnt.'페이지]',
		'nextPerPage'	=> '[다음'.$pageCnt.'페이지]',
		'firstPage'		=> '[처음]',
		'lastPage'		=> '[끝]',
		'pageCss'		=> 'page_list_1',
		'curPageCss'	=> 'page_list_2'
	);

	$pageCount = (intVal($page) - 1) * $itemCnt;
?>
<script type="text/javascript">
	function lfSeqList(obj,IPIN){
		/*
		$.ajax({
			type:'POST'
		,	url:'./hce_receipt_list_sub_seqlist.php'
		,	data:{
				'SR'	:'<?=$sr;?>'
			,	'IPIN'	:IPIN
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						html += '<tr style="cursor:pointer;" onmouseover="this.style.backgroundColor=\'#B2CCFF\';" onmouseout="this.style.backgroundColor=\'#D9E5FF\';" onclick="top.frames[\'frmTop\'].lfTarget(\''+IPIN+'\',\''+col['seq']+'\'); $(\'#divSeqList\').hide();">';
						html += '<td class="center">'+col['no']+'</td>';
						html += '<td class="center">'+col['type']+'</td>';
						html += '<td class="center">'+__getDate(col['date'],'.')+'</td>';
						html += '<td class="center">'+col['reqorNm']+'</td>';
						html += '<td class="center">'+col['reqorTel']+'</td>';
						html += '<td class="center">'+col['rcverNm']+'</td>';
						html += '<td class="center last">'+(col['endYn'] == 'Y' ? '<span style="color:red;">종결</span>' : '미결')+'</td>';
						html += '</tr>';
					}
				}

				$('#tbodySeqList').html(html);
				$('#divSeqList').css('left',$(obj).offset().left).css('top',$(obj).offset().top+$(obj).height()).show();
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
		*/

		$('tr[id^="row_"]').hide();
		$('tr[id="row_'+IPIN+'"]').show();
	}

	function lfDelete(IPIN,seq){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./hce_receipt_list_sub_delete.php'
		,	data:{
				'SR'	:'<?=$sr;?>'
			,	'IPIN'	:IPIN
			,	'seq'	:seq
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget('','');
					top.frames['frmLeft'].lfHideMenu();
					parent.lfPage('<?=$page;?>');
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
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
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="90px">
		<col width="40px">
		<col width="50px">
		<col width="70px">
		<col width="130px">
		<col width="90px">
		<col width="60px">
		<col width="40px">
		<col>
	</colgroup>
	<tbody><?
		$sql = 'SELECT	DISTINCT
						m03_name AS name
				,		rcpt.IPIN
				,		rcpt.rcpt_seq
				,		rcpt.rcpt_dt
				,		rcpt.phone
				,		rcpt.mobile
				,		rcpt.hce_seq
				,		rcpt.counsel_type AS rcpt_type
				,		rcpt.reqor_nm
				,		rcpt.reqor_telno
				,		rcpt.rcver_nm
				,		rcpt.end_flag
				FROM	hce_receipt AS rcpt
				INNER	JOIN	m03sugupja AS mst
						ON		mst.m03_ccode	= rcpt.org_no
						AND		mst.m03_mkind	= \'6\'
						AND		mst.m03_key		= rcpt.IPIN';

		if ($strName){
			$sql .= '	AND		m03_name >= \''.$strName.'\'';
		}

		$sql .= '
				LEFT	JOIN	client_his_svc AS svc
						ON		svc.org_no	= rcpt.org_no
						AND		svc.jumin	= mst.m03_jumin
						AND		svc.svc_cd	= rcpt.org_type
				WHERE	rcpt.org_no		= \''.$orgNo.'\'
				AND		rcpt.org_type	= \''.$sr.'\'
				AND		rcpt.rcpt_seq	= (SELECT MAX(rcpt_seq) FROM hce_receipt WHERE org_no = rcpt.org_no AND org_type = rcpt.org_type AND IPIN = rcpt.IPIN AND del_flag = \'N\')
				AND		rcpt.del_flag	= \'N\'';

		if ($strFrom && $strTo){
			$sql .= '
				AND		rcpt.rcpt_dt >= \''.$strFrom.'\'
				AND		rcpt.rcpt_dt <= \''.$strTo.'\'';
		}

		if ($strEndYn){
			$sql .= '
				AND		rcpt.end_flag = \''.$strEndYn.'\'';
		}

		$sql .= '
				ORDER	BY name
				LIMIT	'.$pageCount.','.$itemCnt;

		$r = $conn->_fetch_array($sql);
		$rCnt = SizeOf($r);

		for($i=0; $i<$rCnt; $i++){
			$row = $r[$i];

			if ($row['end_flag'] == 'Y'){
				$endStr = '<span style="color:red;">종결</span>';
			}else{
				$endStr = '미결';
			}?>
			<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="center"><a href="#" onclick="top.frames['frmTop'].lfTarget('<?=$row['IPIN'];?>','<?=$row['rcpt_seq'];?>'); $('#divSeqList').hide(); return false;"><?=$row['name'];?></a></td>
				<td class="center"><?=$myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');?></td><?
				if ($row['hce_seq'] > 1){?>
					<td class="center"><a href="#" onclick="lfSeqList($(this).parent(),'<?=$row['IPIN'];?>');"><span class="bold" style="color:blue;"><?=$row['hce_seq'];?></span></a></td><?
				}else{?>
					<td class="center"><?=$row['hce_seq'];?></td><?
				}?>
				<td class="center"><?=$rctGbn[$row['rcpt_type']]['name'];?></td>
				<td class="center"><?=$myF->dateStyle($row['rcpt_dt'],'.');?></td>
				<td class="center"><div class="left nowrap" style="width:125px;"><?=$row['reqor_nm'];?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['reqor_telno'],'.');?></td>
				<td class="center"><?=$row['rcver_nm'];?></td>
				<td class="center"><?=$endStr;?></td>
				<td class="center last">
					<div class="left"><span class="btn_pack small"><button type="button" onclick="lfDelete('<?=$row['IPIN'];?>','<?=$row['rcpt_seq'];?>');">삭제</button></span></div>
				</td>
			</tr><?

			if ($row['hce_seq'] > 1){
				$sql = 'SELECT	m03_name AS name
						,		rcpt.IPIN
						,		rcpt.rcpt_seq
						,		rcpt.rcpt_dt
						,		rcpt.phone
						,		rcpt.hce_seq
						,		rcpt.counsel_type AS rcpt_type
						,		rcpt.reqor_nm
						,		rcpt.reqor_telno
						,		rcpt.rcver_nm
						,		rcpt.end_flag
						FROM	hce_receipt AS rcpt
						INNER	JOIN	m03sugupja AS mst
						ON		mst.m03_ccode	= rcpt.org_no
						AND		mst.m03_mkind	= \'6\'
						AND		mst.m03_key		= rcpt.IPIN
						WHERE	rcpt.org_no		= \''.$orgNo.'\'
						AND		rcpt.org_type	= \''.$sr.'\'
						AND		rcpt.IPIN		= \''.$row['IPIN'].'\'
						AND		rcpt.del_flag	= \'N\'
						ORDER	BY rcpt_seq DESC
						LIMIT	1,'.($row['hce_seq'] - 1);

				$rs = $conn->_fetch_array($sql);
				$rsCnt = SizeOf($rs);

				for($j=0; $j<$rsCnt; $j++){
					$row = $rs[$j];

					if ($row['end_flag'] == 'Y'){
						$endStr = '<span style="color:red;">종결</span>';
					}else{
						$endStr = '미결';
					}?>
					<tr id="row_<?=$row['IPIN'];?>" style="cursor:default; background-color:#EAEAEA; display:none;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#EAEAEA';">
						<td class="center <?=($j < $rsCnt - 1 ? 'bottom' : '');?>"></td>
						<td class="center"><a href="#" onclick="top.frames['frmTop'].lfTarget('<?=$row['IPIN'];?>','<?=$row['rcpt_seq'];?>'); $('#divSeqList').hide(); return false;"><?=$row['name'];?></a></td>
						<td class="center"><?=$myF->phoneStyle($row['phone'],'.');?></td>
						<td class="center"><?=$row['hce_seq'];?></td>
						<td class="center"><?=$rctGbn[$row['rcpt_type']]['name'];?></td>
						<td class="center"><?=$myF->dateStyle($row['rcpt_dt'],'.');?></td>
						<td class="center"><div class="left nowrap" style="width:125px;"><?=$row['reqor_nm'];?></div></td>
						<td class="center"><?=$myF->phoneStyle($row['reqor_telno'],'.');?></td>
						<td class="center"><?=$row['rcver_nm'];?></td>
						<td class="center"><?=$endStr;?></td>
						<td class="center last"></td>
					</tr><?
				}

				Unset($rs);
			}
		}

		Unset($r);

		if ($rCnt == 0){?>
			<tr>
				<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}else{
			$paging = new YsPaging($params);
			$pageList = $paging->returnPaging();?>
			<script type="text/javascript">
				$('#pageList',parent.document).html('<?=$pageList;?>');
			</script><?
		}?>
	</tbody>
</table>
<div id="divSeqList" style="position:absolute; width:auto; background-color:#D9E5FF; border:1px solid #cccccc; border-bottom:none; display:none;">
	<table class="my_table" style="width:auto;">
		<colgroup>
			<col width="40px">
			<col width="50px">
			<col width="70px">
			<col width="130px">
			<col width="90px">
			<col width="60px">
			<col width="40px">
		</colgroup>
		<tbody id="tbodySeqList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>