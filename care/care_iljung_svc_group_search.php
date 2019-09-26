<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$category = $_POST['category'];

	if ($category == 'ALL') $category = '';

	$sql = 'SELECT	DISTINCT
					iljung.grp_cd
			,		iljung.suga_cd
			,		iljung.seq
			,		grp.group_nm
			,		iljung.mem_cd
			,		iljung.date
			,		iljung.time
			,		iljung.conf_yn
			,		iljung.tg_cnt
			,		iljung.del_flag
			FROM	care_svc_iljung AS iljung
			INNER	JOIN	care_svc_group AS grp
					ON		grp.org_no	= iljung.org_no
					AND		grp.org_type= iljung.org_type
					AND		grp.suga_cd	= iljung.suga_cd
					AND		grp.seq		= iljung.seq';

	if ($category){
		$sql .= '	AND		grp.category = \''.$category.'\'';
	}

	/*$sql .= ' LEFT	JOIN	t01iljung
					ON		t01_ccode		= iljung.org_no
					AND		t01_mkind		= iljung.org_type
					AND		t01_suga_code1	= iljung.suga_cd
					AND		t01_request		= iljung.grp_cd
					AND		t01_del_yn		= \'N\'';*/

	$sql .= '
			WHERE	iljung.org_no	= \''.$orgNo.'\'
			AND		iljung.org_type = \''.$SR.'\'
			AND		LEFT(iljung.date,6) = \''.$year.$month.'\'';

	//$sql .= ' AND	CASE WHEN IFNULL(t01_request,\'\') != \'\' THEN \'N\' ELSE iljung.del_flag END = \'N\'';
	$sql .= ' AND iljung.del_flag = \'N\'';


	$sql .= '
			ORDER	BY group_nm, date';

	#if ($debug) echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$i+1;?></td>
			<td class="center"><div class="left"><?=$row['group_nm'];?> (<?=$row['grp_cd'];?>)</div></td>
			<td class="center"><?=$myF->dateStyle($row['date'],'.');?></td>
			<td class="center"><?=($row['time'] == '1' ? '오전' : '오후');?></td>
			<? if($sr=='R'){ ?><td class="center"><?=$row['conf_yn'];?></td><? } ?>
			<td class="center"><?=$row['tg_cnt'];?></td>
			<td class="center last">
				<div class="left"><?
					if ($row['del_flag'] == 'N'){?>
						<span class="btn_pack m"><button onclick="lfReg('<?=$row['grp_cd'];?>','<?=$row['suga_cd'];?>','<?=$row['seq'];?>');" style="color:BLUE;">수정</button></span><?
					}?>
					<span class="btn_pack m"><button onclick="lfDel('<?=$row['grp_cd'];?>');" style="color:RED;">삭제</button></span>
				</div>
			</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>