<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	$IsAll = true;

	//CMS 정보 찾기
	$sql = 'SELECT	cms_no, cms_dt, seq
			FROM	cv_cms_reg
			WHERE	org_no		 = \''.$orgNo.'\'
			AND		link_stat	!= \'1\'
			AND		del_flag	 = \'N\'
			ORDER	BY cms_dt';

	$CMSRowList = $conn->_fetch_array($sql);

	if (is_array($CMSRowList)){
		foreach($CMSRowList as $tmpIdx => $R){
			$_POST['CMS']	= $R['cms_no'];
			$_POST['date']	= $R['cms_dt'];
			$_POST['seq']	= $R['seq'];

			include('./center_acct_cms_link_set.php');

			if (!$IsLoop) break;
		}
	}

	include_once('../inc/_db_close.php');
?>