<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$CMSNo	= IntVal($_POST['CMSNo']);
	$orgNm	= $_POST['orgNm'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$link	= $_POST['link'];

	$sql = 'SELECT	org_no
			,		cms_no
			,		org_nm
			,		cms_dt
			,		seq
			,		in_amt
			,		in_stat
			,		link_stat
			FROM	cv_cms_reg
			WHERE	del_flag = \'N\'';

	if ($CMSNo){
		$sql .= '
			AND		CAST(cms_no AS unsigned) = \''.$CMSNo.'\'';
	}

	if ($orgNm){
		$sql .= '
			AND		org_nm LIKE \'%'.$orgNm.'%\'';
	}

	if ($fromDt && $toDt){
		$sql .= '
			AND		cms_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'';
	}else if ($fromDt){
		$sql .= '
			AND		cms_dt >= \''.$fromDt.'\'';
	}else if ($toDt){
		$sql .= '
			AND		cms_dt <= \''.$toDt.'\'';
	}

	if ($link){
		if ($link == '5'){
			$sql .= '
			AND		link_stat != \'1\'';
		}else{
			$sql .= '
			AND		link_stat = \''.$link.'\'';
		}
	}

	$sql .= '
			ORDER	BY CASE WHEN LENGTH(org_no) > 0 THEN 1 ELSE 2 END, org_nm, cms_no, cms_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt > 0){
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['link_stat'] == '1'){
				$row['link_stat'] = '연결완료';
			}else if ($row['link_stat'] == '3'){
				$row['link_stat'] = '<span style="color:BLUE;">일부연결</span>';
			}else if ($row['link_stat'] == '9'){
				$row['link_stat'] = '<span style="color:RED;">미연결</span>';
			}?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="center"><div class="left"><?=$row['org_no'];?></div></td>
				<td class="center"><?=$row['cms_no'];?></td>
				<td class="center"><div class="left"><?=$row['org_nm'];?></div></td>
				<td class="center"><?=$myF->dateStyle($row['cms_dt'],'.');?></td>
				<td class="center"><div class="right"><?=number_format($row['in_amt']);?></div></td>
				<td class="center"><div class="left"><?=$row['in_stat'];?></div></td>
				<td class="center"><div class="left"><?=$row['link_stat'];?></div></td>
				<td class="center last"><?
					if (!$row['org_no']){?>
						<div class="left"><span class="btn_pack small"><button onclick="lfOrgNoLink('<?=$row['cms_no'];?>','<?=$row['cms_dt'];?>','<?=$row['seq'];?>');">기관기호연결</button></span></div><?
					}?>
				</td>
			</tr><?

			$no ++;
		}
	}else{?>
		<tr>
			<td class="center last" colspan="8">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>