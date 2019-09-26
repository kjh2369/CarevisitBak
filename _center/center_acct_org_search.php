<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	$sql = 'SELECT	a.*, b.cms_no
			FROM	(
					SELECT	a.org_no, a.org_nm, a.manager, SUM(a.acct_amt) AS acct_amt
					FROM	(
							SELECT	DISTINCT a.org_no, a.svc_gbn, a.svc_cd, a.acct_amt, m00_store_nm AS org_nm, m00_mname AS manager
							FROM	cv_svc_acct_list AS a
							INNER	JOIN	m00center
									ON		m00_mcode	= a.org_no
									AND		m00_domain	= \''.$company.'\'
							WHERE	a.yymm = \''.$yymm.'\'
							) AS a
					GROUP	BY a.org_no
					) AS a
			LEFT	JOIN	cv_reg_info AS b
					ON		b.org_no = a.org_no
					AND		LEFT(b.from_dt, 6)	<= \''.$yymm.'\'
					AND		LEFT(b.to_dt, 6)	>= \''.$yymm.'\'
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$sql = 'SELECT	SUM(link_amt) AS amt
				FROM	cv_cms_link
				WHERE	org_no	= \''.$row['org_no'].'\'
				AND		yymm	= \''.$yymm.'\'
				AND		del_flag= \'N\'
				AND		IFNULL(link_stat, \'1\') = \'1\'';

		$inAmt = $conn->get_data($sql);
		$nonpay = $row['acct_amt'] - $inAmt;
		?>
		<div id="ID_CENTER_<?=$i;?>" onclick="lfSetOrg(this);" selYn="N" style="cursor:default; line-height:1.5em; border-bottom:1px solid #CCCCCC; border-right:1px solid #CCCCCC;">
			<div style="width:30px; float:left; text-align:right; padding-right:5px; "><?=$no;?>.</div>
			<div style="width:170px; float:left; padding-left:5px; border-left:1px dotted #CCCCCC;">
				<div id="ID_CELL_NO" class="nowrap" style=""><?=$row['org_no'];?></div>
				<div id="ID_CELL_CMS" style="display:none;"><?=$row['cms_no'];?></div>
				<div class="nowrap" style=""><?=$row['org_nm'];?></div>
				<div class="nowrap" style="">청구금액 : <span style="color:BLUE;"><?=number_format($row['acct_amt']);?></span></div>
				<div class="nowrap" style="">미납금액 : <span id="ID_CELL_NONAMT" style="color:RED;"><?=number_format($nonpay);?></span></div>
			</div>
		</div><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>