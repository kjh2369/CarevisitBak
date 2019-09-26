<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	a.org_no
			,		a.org_nm
			,		a.manager
			,		a.phone
			,		b.cms_no
			,		SUM(a.acct_amt) AS acct_amt
			FROM	(
					SELECT	DISTINCT
							a.org_no
					,		m00_store_nm AS org_nm
					,		m00_mname AS manager
					,		m00_ctel AS phone
					,		a.svc_gbn
					,		a.svc_cd
					,		a.acct_amt
					FROM	cv_svc_acct_list AS a
					INNER	JOIN	m00center
							ON		m00_mcode = a.org_no
							AND		m00_domain= \''.$company.'\'
					WHERE	a.yymm = \''.$yymm.'\'
					) AS a
			INNER	JOIN	cv_reg_info AS b
					ON		b.org_no = a.org_no
			GROUP	BY a.org_no
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['org_nm'];?></div></td>
			<td class="center"><div class="left"><?=$row['cms_no'];?></div></td>
			<td class="center"><div class="left"><?=$row['manager'];?></div></td>
			<td class="center"><div class="left"><?=$myF->phoneStyle($row['phone'],'.');?></div></td>
			<td class="center"><div class="right"><?=number_format($row['acct_amt']);?></div></td>
			<td class="center"><div class="right"></div></td>
			<td class="center"><div class="right"></div></td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack small"><button>입금등록</button></span>
				</div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>