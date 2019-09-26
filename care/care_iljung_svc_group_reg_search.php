<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$category = $_POST['category'];

	if ($category == 'ALL') $category = '';

	$sql = 'SELECT	group_nm
			,		suga_cd
			,		res_cd
			,		mem_cd
			,		seq
			FROM	care_svc_group
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		del_flag= \'N\'
			AND		suga_cd = (	SELECT	CONCAT(suga_cd, suga_sub)
								FROM	care_suga
								WHERE	org_no	= care_svc_group.org_no
								AND		suga_sr = care_svc_group.org_type
								AND		CONCAT(suga_cd, suga_sub) = care_svc_group.suga_cd';

	if ($IsCareYoyAddon){
		$sql .= '				UNION	ALL
								SELECT	code
								FROM	care_suga_comm';
	}

	$sql .= '					LIMIT	1)';

	if ($category){
		$sql .= '
			AND		category = \''.$category.'\'';
	}

	$sql .= '
			ORDER	BY group_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<div onclick="lfSetGroup('<?=$row['suga_cd'];?>','<?=$row['res_cd'];?>','<?=$row['seq'];?>','<?=$ed->en($row['mem_cd']);?>');" onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';" style="cursor:pointer; line-height:1.3em; padding:5px; border-top:<?=($i > 0 ? '1px solid #CCCCCC;' : 'none');?>;">
			<div class="nowrap"><?=$row['group_nm'];?></div>
		</div><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>