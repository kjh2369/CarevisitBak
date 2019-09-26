<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];

/*
SELECT a.org_no
,      a.yymm
,      a.svc_gbn
,      a.svc_cd
,      a.acct_amt
FROM   cv_svc_acct_list AS a
INNER  JOIN (
        SELECT DISTINCT m00_mcode AS org_no
        FROM   m00center
        WHERE  m00_domain = 'carevisit.net'
       ) AS b
       ON   b.org_no = a.org_no
WHERE  LEFT(a.yymm, 4) = '2015'


SELECT org_no
,      yymm
,      link_amt
FROM   cv_cms_link
WHERE  org_no = '1234'
AND    del_flag = 'N'
AND    LEFT(yymm,4) = '2015'
AND    CASE WHEN IFNULL(link_stat,'') = '' THEN '1' ELSE link_stat END = '1'
 */

	$sql = 'SELECT	a.org_no
			,		a.yymm
			,		a.svc_gbn
			,		a.svc_cd
			,		a.acct_amt
			FROM	cv_svc_acct_list AS a
			INNER	JOIN (
					SELECT	DISTINCT m00_mcode AS org_no
					FROM	m00center
					WHERE	m00_domain = \''.$company.'\'
					) AS b
					ON		b.org_no = a.org_no
			WHERE	LEFT(a.yymm, 4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$month = IntVal(SubStr($row['yymm'],4,2));

		if (!is_numeric(StrPos($data[$month]['CENTER_LIST'],'/'.$row['org_no']))){
			$data[$month]['CENTER_LIST'] .= '/'.$row['org_no'];
			$data[$month]['CENTER_CNT'] ++;
		}

		$data[$month]['ACCT_AMT'] += $row['acct_amt'];
		$data[$month]['ACCT_DTL'][$row['svc_gbn'].'_'.$row['svc_cd']] += $row['acct_amt'];
	}

	$conn->row_free();

	if (is_array($data)){
		$val = '';

		foreach($data as $month => $R1){
			$val .= ($val ? '?' : '');
			$val .= $month.'_1='.$R1['CENTER_CNT'];
			//$val .= '&'.$month.'_2='.$R1['ACCT_AMT'];
			$val .= '&'.$month.'_3='.$R1['ACCT_AMT'];

			foreach($R1['ACCT_DTL'] as $svcCd => $R2){
				$val .= '&'.$month.'_'.$svcCd.'='.$R2;
			}
		}

		echo $val;
	}

	include_once('../inc/_db_close.php');
?>