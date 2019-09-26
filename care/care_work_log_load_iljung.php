<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$jumin	= $ed->de($_POST['jumin']);
	$sugaCd	= $_POST['sugaCd'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;

	$weekly = Array(
		0=>'<span style="color:RED;">일</span>'
	,	1=>'월'
	,	2=>'화'
	,	3=>'수'
	,	4=>'목'
	,	5=>'금'
	,	6=>'<span style="color:BLUE;">일</span>'
	);

	$sql = 'SELECT	a.jumin
			,		m03_name AS name
			,		a.date
			,		CAST(RIGHT(a.date,2) AS unsigned) AS day
			,		CASE WHEN IFNULL(c.jumin,\'\') != \'\' THEN \'Y\' ELSE \'N\' END AS reg_yn
			FROM	(
						SELECT	DISTINCT t01_jumin AS jumin
						,		t01_sugup_date AS date
						FROM	t01iljung
						WHERE	t01_ccode		= \''.$orgNo.'\'
						AND		t01_mkind		= \''.$SR.'\'
						AND		t01_suga_code1	= \''.$sugaCd.'\'
						AND		t01_yoyangsa_id2= \''.$jumin.'\'
						AND		t01_del_yn		= \'N\'
						AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
					) AS a
			INNER	JOIN	m03sugupja
					ON		m03_ccode = \''.$orgNo.'\'
					AND		m03_mkind = \'6\'
					AND		m03_jumin = a.jumin
			LEFT	JOIN (
						SELECT	jumin
						,		date
						FROM	care_work_log
						WHERE	org_no		= \''.$orgNo.'\'
						AND		org_type	= \''.$SR.'\'
						AND		suga_cd		= \''.$sugaCd.'\'
						AND		mem_cd		= \''.$jumin.'\'
						AND		del_flag	= \'N\'
						AND		LEFT(date,6)= \''.$year.$month.'\'
					) AS c
					ON		c.jumin = a.jumin
					AND		c.date	= a.date
			ORDER	BY CASE WHEN reg_yn = \'N\' THEN 1 ELSE 2 END, name, date';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$weekday = $weekly[Date('w',StrToTime($row['date']))];?>
		<tr onclick="lfSelIljung(this);" jumin="<?=$ed->en($row['jumin']);?>" date="<?=$row['date'];?>" style="cursor:default;" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';">
			<td>&nbsp;<?=$row['name'];?></td>
			<td>&nbsp;<?=$row['day'];?>일(<?=$weekday;?>)</td>
			<td>&nbsp;<?=$row['reg_yn'] == 'Y' ? '<span style="color:BLUE;">작성' : '<span style="color:RED;">미작성';?></span></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>