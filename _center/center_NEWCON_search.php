<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$column	= $_POST['column'];
	$order	= $_POST['orderBy'];
	$rsGbn	= $_POST['rsGbn'];

	$sql = 'SELECT	DISTINCT a.org_no, a.start_dt, a.rs_dtl_cd, a.insert_id, a.insert_dt
			,		m00_store_nm AS org_nm, m00_mname AS mg_nm
			,		b01_name AS pic_nm
			,		(SELECT b01_name FROM b01person WHERE b01_id = a.insert_id) AS reg_nm
			FROM	cv_reg_info AS a
			INNER	JOIN	m00center
					ON		m00_mcode = a.org_no
			INNER	JOIN	b01person
					ON		a.link_branch = b01_branch
					AND		a.link_person = b01_code
			WHERE	a.rs_cd = \'3\'
			AND		DATE_FORMAT(NOW(),\'%Y%m%d\') BETWEEN a.from_dt AND a.to_dt';

	if ($rsGbn == 'ALL'){
	}else if ($rsGbn == '04'){
		$sql .= ' AND a.rs_dtl_cd = \'04\'';
	}else{
		$sql .= ' AND a.rs_dtl_cd != \'04\'';
	}

	$sql .= '
			ORDER	BY '.$column.' '.$order;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;
	$dtlRs = Array('01'=>'신규연결','02'=>'기간연장','03'=>'재연결','04'=>'케어비지트');

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td><div class="left nowrap" style="width:150px;"><a href="#" onclick="lfSelOrg('<?=$row['org_no'];?>');"><?=$row['org_nm'];?></a></div></td>
			<td>&nbsp;<?=$row['org_no'];?></td>
			<td>&nbsp;<?=$row['mg_nm'];?></td>
			<td class="center"><?=$myF->dateStyle($row['start_dt'],'.');?></td>
			<td>&nbsp;<?=$row['pic_nm'];?></td>
			<td class="center"><?=$dtlRs[$row['rs_dtl_cd']];?></td>
			<td>&nbsp;<?=$row['reg_nm'];?></td>
			<td class="last"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>