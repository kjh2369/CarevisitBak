<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_page_list.php");

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$page	= $_POST['page'];

	$itemCnt = 20;

	$sql = 'SELECT	COUNT(DISTINCT org_no)
			FROM	cv_reg_info
			INNER	JOIN	m00center
					ON		m00_mcode	= org_no
					AND		m00_del_yn	= \'N\'
			WHERE	LEFT(from_dt,6) <= \''.$yymm.'\'
			AND		LEFT(to_dt,6)	>= \''.$yymm.'\'';

	$totCnt = $conn->get_data($sql);
	$pageCnt = (intVal($page) - 1) * $itemCnt;


	$sql = 'SELECT	DISTINCT a.org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm, m00_ctel AS phone
			,		CASE WHEN yn_1_01 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_01
			,		CASE WHEN yn_1_11 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_11
			,		CASE WHEN yn_1_14 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_14
			,		CASE WHEN yn_1_15 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_15
			,		CASE WHEN yn_1_21 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_21
			,		CASE WHEN yn_1_22 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_22
			,		CASE WHEN yn_1_23 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_23
			,		CASE WHEN yn_1_24 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_24
			,		CASE WHEN yn_1_41 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_41
			,		CASE WHEN yn_1_42 > 0 THEN \'Y\' ELSE \'\' END AS yn_1_42
			,		CASE WHEN yn_2_11 > 0 THEN \'Y\' ELSE \'\' END AS yn_2_11
			,		CASE WHEN yn_2_21 > 0 THEN \'Y\' ELSE \'\' END AS yn_2_21
			FROM	(
					SELECT	a.org_no
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'01\' THEN 1 ELSE 0 END) AS yn_1_01
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'11\' THEN 1 ELSE 0 END) AS yn_1_11
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'14\' THEN 1 ELSE 0 END) AS yn_1_14
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'15\' THEN 1 ELSE 0 END) AS yn_1_15
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'21\' THEN 1 ELSE 0 END) AS yn_1_21
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'22\' THEN 1 ELSE 0 END) AS yn_1_22
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'23\' THEN 1 ELSE 0 END) AS yn_1_23
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'24\' THEN 1 ELSE 0 END) AS yn_1_24
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'41\' THEN 1 ELSE 0 END) AS yn_1_41
					,		SUM(CASE WHEN b.svc_gbn = \'1\' AND b.svc_cd = \'42\' THEN 1 ELSE 0 END) AS yn_1_42
					,		SUM(CASE WHEN b.svc_gbn = \'2\' AND b.svc_cd = \'11\' THEN 1 ELSE 0 END) AS yn_2_11
					,		SUM(CASE WHEN b.svc_gbn = \'2\' AND b.svc_cd = \'21\' THEN 1 ELSE 0 END) AS yn_2_21
					FROM	cv_reg_info AS a
					LEFT	JOIN	cv_svc_fee AS b
							ON		b.org_no	= a.org_no
							AND		b.use_yn	= \'Y\'
							AND		b.del_flag	= \'N\'
							AND		LEFT(b.from_dt,6) <= \''.$yymm.'\'
							AND		LEFT(b.to_dt,6)	  >= \''.$yymm.'\'
					WHERE	LEFT(a.from_dt,6) <= \''.$yymm.'\'
					AND		LEFT(a.to_dt,6)	  >= \''.$yymm.'\'
					GROUP	BY a.org_no
					) AS a
			INNER	JOIN	m00center
					ON		m00_mcode	= a.org_no
					AND		m00_del_yn	= \'N\'
			ORDER	BY org_nm
			LIMIT	'.$pageCnt.','.$itemCnt;

	//echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$pageCnt + $no;?></td>
			<td class="center"><?=$row['org_no'];?></td>
			<td class="center"><div class="left nowrap" style="width:150px;"><?=$row['org_nm'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:70px;"><?=$row['mg_nm'];?></div></td>
			<td class="center"><?=$row['yn_1_01'];?></td>
			<td class="center"><?=$row['yn_1_11'];?></td>
			<td class="center"><?=$row['yn_1_14'];?></td>
			<td class="center"><?=$row['yn_1_15'];?></td>
			<td class="center"><?=$row['yn_1_21'];?></td>
			<td class="center"><?=$row['yn_1_22'];?></td>
			<td class="center"><?=$row['yn_1_23'];?></td>
			<td class="center"><?=$row['yn_1_24'];?></td>
			<td class="center"><?=$row['yn_1_41'];?></td>
			<td class="center"><?=$row['yn_1_42'];?></td>
			<td class="center"><?=$row['yn_2_11'];?></td>
			<td class="center"><?=$row['yn_2_21'];?></td>
			<td class="center last"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>