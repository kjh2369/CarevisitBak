<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$page = IntVal($_POST['page']);
	$type = $_POST['type'];

	$svcCd = $type;

	$itemCount = 20;
	$pageCount = 10;

	$pageCnt = $page;

	if (Empty($pageCount)){
		$pageCount = 1;
	}

	$pageCnt = (intVal($pageCnt) - 1) * $itemCount;

	//MAX COUNT
	$sql = 'SELECT	COUNT(*)
			FROM	(
					SELECT	DISTINCT
							m00_mcode AS code
					,		m00_store_nm AS name
					FROM	m00center
					WHERE	m00_domain = \''.$gDomain.'\'
					) AS mst
			INNER	JOIN	sub_svc AS svc
					ON		svc.org_no = mst.code
					AND		svc.svc_cd = \''.$svcCd.'\'';

	$maxCnt = $conn->get_data($sql);

	$sql = 'SELECT	mst.code
			,		mst.name
			,		svc.svc_cd
			,		svc.seq
			,		svc.from_dt
			,		svc.to_dt
			,		svc.acct_yn
			FROM	(
					SELECT	DISTINCT
							m00_mcode AS code
					,		m00_store_nm AS name
					FROM	m00center
					WHERE	m00_domain = \''.$gDomain.'\'
					) AS mst
			INNER	JOIN	sub_svc AS svc
					ON		svc.org_no = mst.code
					AND		svc.svc_cd = \''.$svcCd.'\'
			ORDER	BY name
			LIMIT '.$pageCnt.','.$itemCount;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = $pageCnt + 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$svcNm = '';

		switch($row['svc_cd']){
			case '5':
				$svcNm = '주야간보호';
				break;

			case '7':
				$svcNm = '복지용구';
				break;
		}?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left nowarp" style="width:90px;"><?=$row['code'];?></div></td>
			<td class="center"><div class="left nowarp" style="width:200px;"><?=$row['name'];?></div></td>
			<td class="center"><div class="left nowarp" style="width:100px;"><?=$svcNm;?></div></td>
			<td class="center"><?=$myF->dateStyle($row['from_dt'],'.');?> ~ <?=$myF->dateStyle($row['to_dt'],'.');?></td>
			<td class="center"><?=$row['acct_yn'];?></td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack small"><button onclick="lfReg('<?=$row['code'];?>','<?=$row['svc_cd'];?>','<?=$row['seq'];?>');" style="color:BLUE;">수정</button></span>
					<span class="btn_pack small"><button onclick="lfDelete('<?=$row['code'];?>','<?=$row['svc_cd'];?>','<?=$row['seq'];?>');" style="color:RED;">삭제</button></span>
				</div>
			</td>
		</tr><?
		$no ++;
	}

	$conn->row_free();?>
	<script type="text/javascript">
		_lfSetPageList(__str2num('<?=$maxCnt;?>'),__str2num('<?=$page;?>'));
	</script><?

	include_once('../inc/_db_close.php');
?>