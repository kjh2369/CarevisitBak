<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$code	= $_POST['category'];
	$IsPopup= $_POST['IsPopup'];

	if ($code == 'ALL') $code = '';

	$sql = 'SELECT	grp.suga_cd
			,		suga.suga_nm
			,		grp.seq
			,		cust.cust_nm
			,		grp.group_nm
			,		LENGTH(grp.target) - LENGTH(REPLACE(grp.target,\'/\',\'\')) + 1 AS cnt
			FROM	care_svc_group AS grp';

	if ($IsCareYoyAddon){
		//공통수가
		$sql .= '
			INNER	JOIN (
						SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm
						FROM	care_suga
						WHERE	org_no	= \''.$orgNo.'\'
						AND		suga_sr	= \''.$SR.'\'
						UNION	ALL
						SELECT	\''.$orgNo.'\' AS org_no, \''.$SR.'\' AS suga_sr, LEFT(code,5) AS suga_cd, MID(code,6) AS suga_sub, name
						FROM	care_suga_comm
					) AS suga
					ON		suga.org_no	= grp.org_no
					AND		suga.suga_sr= grp.org_type
					AND		CONCAT(suga.suga_cd,suga.suga_sub) = grp.suga_cd';
	}else{
		$sql .= '
			INNER	JOIN	care_suga AS suga
					ON		suga.org_no	= grp.org_no
					AND		suga.suga_sr= grp.org_type
					AND		CONCAT(suga.suga_cd,suga.suga_sub) = grp.suga_cd';
	}

	$sql .= '
			INNER	JOIN	care_cust AS cust
					ON		cust.org_no	= grp.org_no
					AND		cust.cust_cd= grp.res_cd
			WHERE	grp.org_no	= \''.$orgNo.'\'
			AND		grp.org_type= \''.$SR.'\'';

	if ($code){
		$sql .= '
			AND		grp.category= \''.$code.'\'';
	}

	$sql .= '
			AND		grp.del_flag= \'N\'
			ORDER	BY group_nm';

	//if ($debug) echo '<tr><td colspan="10">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr id="rowId_<?=$row['suga_cd'];?>_<?=$row['seq'];?>">
			<td class="center"><?=$i+1;?></td>
			<td class="left"><?=$row['group_nm'];?></td>
			<td class="left"><?=$row['cust_nm'];?></td>
			<td class="left"><?=$row['suga_nm'];?></td>
			<td class="right"><?=$row['cnt'];?></td>
			<td class="left last"><?
				if ($IsPopup == 'Y'){?>
					<span class="btn_pack small"><button onclick="lfSetGroup('<?=$row['suga_cd'];?>','<?=$row['seq'];?>','<?=$row['suga_nm'];?>');">선택</button></span><?
				}else{?>
					<span class="btn_pack m"><button onclick="lfReg('<?=$row['suga_cd'];?>','<?=$row['seq'];?>');" style="color:BLUE;">수정</button></span>
					<span class="btn_pack m"><button onclick="lfDel('<?=$row['suga_cd'];?>','<?=$row['seq'];?>');" style="color:RED;">삭제</button></span><?
				}?>
			</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>