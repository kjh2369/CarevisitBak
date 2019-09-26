<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code		= $_POST[mark].$_POST[code];
	$pass		= $_POST[pass];
	$name		= $_POST[name];
	$manager	= $_POST[manager];
	$phone		= str_replace('-', '', $_POST[phone]);
	$mobile		= str_replace('-', '', $_POST[mobile]);
	$fax		= str_replace('-', '', $_POST[fax]);
	$tel		= str_replace('-', '', $_POST[tel]);
	$email      = $_POST[email];
	$homepage	= $_POST[homepage];
	$postno		= $_POST[postno1].$_POST[postno2];
	$addr1		= $_POST[addr1];
	$addr2		= $_POST[addr2];
	$joinDate	= str_replace('-', '', $_POST[joinDate]);
	$quitDate	= str_replace('-', '', $_POST[quitDate]);
	$stat		= $_POST[stat];
	$banknm		= $_POST[banknm];
	$bankno		= $_POST[bankno];
	$bankacct	= $_POST[bankacct];
	$regno		= str_replace('-', '', $_POST[regNo]);
	$regoftype	= $_POST[regoftype];
	$regofitem	= $_POST[regofitem];
	$regnum		= str_replace('-', '', $_POST[regnum]);
	$regdt		= $_POST[regdt];
	$regchar	= $_POST[regchar];
	$other		= addSlashes($_POST[other]); //stripSlashes
	$com_yn     = $_POST[com_yn];
	$type		= $_POST[type];
	$mode       = $_POST[mode];
	$domain     = $myF->_get_domain();

	switch($type){
	case 'reg':
		$menuSeq = '1';
		break;
	case 'list':
		$menuSeq = '2';
		break;
	}

	$conn->begin();

	$sql = "replace into b00branch values (
			 '$code'
			,'$pass'
			,'$name'
			,'$manager'
			,'$tel'
			,'$fax'
			,'$phone'
			,'$mobile'
			,'$email'
			,'$homepage'
			,'$postno'
			,'$addr1'
			,'$addr2'
			,'$joinDate'
			,'$quitDate'
			,'$stat'
			,'$banknm'
			,'$bankno'
			,'$bankacct'
			,'$regno'
			,'$regoftype'
			,'$regofitem'
			,'$regnum'
			,'$regdt'
			,'$regchar'
			,'$other'
			,'$com_yn'
			,'$domain')";
	if (!$conn->execute($sql)){
		$conn->rollback();
		echo "
			<script>
				alert('지사 저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				history.back();
			</script>
			 ";
		exit;
	}
	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('branch_reg.php?menuIndex=13&menuSeq=<?=$menuSeq;?>&code=<?=$code;?>&type=<?=$type;?>&mode=<?=$mode;?>');
</script>