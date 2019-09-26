<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo = $_SESSION['userCenterCode'];

	if ($type == 'INTERVIEW_LIST'){
		$regType = 'INTERVIEW_REG';
	}else{
		$regType = 'INTERVIEW_REG_N';
	}
?>
<script type="text/javascript">
	function lfReg(IPIN){
		var f = document.f;

		f.IPIN.value = IPIN;
		f.action = '../care/care.php?sr=<?=$sr;?>&type=<?=$regType;?>';
		f.submit();
	}

	function lfDel(IPIN){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		var f = document.f;

		f.IPIN.value = IPIN;
		f.action = '../care/care.php?sr=<?=$sr;?>&type=INTERVIEW_DEL';
		f.submit();
	}

	function lfPDF(type,subId,idx,ipin){
		var dir = 'P';
		var file = 'hce_print';

		if (type == '1'){
			dir = 'L';
		}

		if (!subId) subId = '';
		if (!idx) idx = '';

		var arguments	= 'root=hce'
						+ '&dir='+dir
						+ '&fileName='+file
						+ '&fileType=pdf'
						+ '&target=show.php'
						+ '&mode='+type
						+ '&gbn=care'
						+ '&wrkType=INTERVIEW_REG'
						+ '&sr=<?=$sr;?>'
						+ '&subId='+subId
						+ '&idx='+idx
						+ '&key='+ipin
						+ '&showForm=HCE';

		__printPDF(arguments);
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">초기상담기록지(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="add"></span><button type="button" class="bold" onclick="lfReg();">작성</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자명</th>
			<th class="head">생년월일</th>
			<th class="head">작성일</th>
			<th class="head">작성자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		if ($type == 'INTERVIEW_LIST' || $type == 'INTERVIEW_DEL'){
			$sql = 'SELECT	iv.IPIN
					,		iv.iver_nm
					,		iv.iver_dt
					,		m03_name AS name
					,		jumin.jumin
					FROM	hce_interview AS iv
					INNER	JOIN m03sugupja
							ON m03_ccode	= iv.org_no
							AND m03_mkind	= \'6\'
							AND m03_key		= iv.IPIN
					INNER	JOIN mst_jumin AS jumin
							ON jumin.org_no	= m03_ccode
							AND jumin.gbn	= \'1\'
							AND jumin.code	= m03_jumin
					WHERE	iv.org_no	= \''.$orgNo.'\'
					AND		iv.org_type	= \''.$sr.'\'
					AND		iv.rcpt_seq	= \'0\'
					ORDER	BY name';
		}else{
			$sql = 'SELECT	iv.IPIN
					,		iv.iver_nm
					,		iv.iver_dt
					,		cn.name
					,		cn.jumin
					FROM	hce_interview AS iv
					INNER	JOIN care_client_normal AS cn
							ON cn.org_no = iv.org_no
							AND cn.normal_sr = iv.org_type
							AND cn.normal_seq = iv.IPIN
					WHERE	iv.org_no	= \''.$orgNo.'\'
					AND		iv.org_type = \''.$sr.'\'
					AND		iv.rcpt_seq = \'-1\'
					ORDER	BY name';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			
			$sql = 'SELECT  count(*)
					FROM    hce_receipt
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$row['IPIN'].'\'
					AND     del_flag = \'N\'';
			
			$hceReceipt = $conn->get_data($sql);

			
			$sql = 'SELECT	COUNT(*)
					FROM	hce_interview
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$sr.'\'
					AND		IPIN	= \''.$row['IPIN'].'\'
					AND		rcpt_seq != \'0\'';
			$hceInterviewCnt = $conn->get_data($sql);
		
			
			?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="center"><?=$row['name'];?></td>
				<td class="center"><?=$myF->issToBirthDay($row['jumin'],'.');?></td>
				<td class="center"><?=$myF->dateStyle($row['iver_dt'],'.');?></td>
				<td class="center"><?=$row['iver_nm'];?></td>
				<td class="left last"><?
					if($hceReceipt > 0 && $hceInterviewCnt > 0){ ?>
						<span class="btn_pack m"><button onclick="lfReg('<?=$row['IPIN'];?>');">보기</button></span><?
					}else {?>
						<span class="btn_pack m"><button onclick="lfReg('<?=$row['IPIN'];?>');">수정</button></span><?
					} 
					if ($debug){?>
						<span class="btn_pack m"><button onclick="lfDel('<?=$row['IPIN'];?>');">삭제</button></span>
						<?
					}?>
					<span class="btn_pack m"><button type="button" onclick="lfPDF('21','','','<?=$row['IPIN'];?>');">출력</button></span>
				</td>
			</tr><?

			$no ++;
		}

		$conn->row_free();?>
	</tbody>
</table>
<input id="jumin" type="hidden" value="">
<input name="IPIN" type="hidden" value="">
<?
	include_once('../inc/_db_close.php');
?>