<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo $myF->header_script();

	$wrt_mode   = $_POST[wrt_mode];
	$branch		= $_POST[branch];
	$personCode = $_POST[personCode];
	$personName = $_POST[personName];

	if (empty($personCode)){
		$sql = 'select ifnull(right(concat(\'00\', cast(cast(max(b01_code) as unsigned) + 1 as char)), 3), \'001\')
				  from b01person
				 where b01_branch = \''.$branch.'\'';

		$personCode = $conn->get_data($sql);
	}

	if ($wrt_mode == 1){
		$id  = $_POST[id];
	}else{
		$id  = $ed->de($_POST[id]);
	}

	$pwd        = $_POST[pwd];
	$position	= $_POST[position];
	$phone		= str_replace('-', '', $_POST[phone]);
	$postno		= $_POST[postno1].$_POST[postno2];
	$addr1		= $_POST[addr1];
	$addr2		= $_POST[addr2];
	$joinDate	= str_replace('-', '', $_POST[joinDate]);
	$quitDate	= str_replace('-', '', $_POST[quitDate]);
	$stat		= $_POST[stat];
	$other		= addSlashes($_POST[other]);
	$type		= $_POST[type];

	switch($type){
	case 'reg':
		$menuSeq = '3';
		break;
	case 'list':
		$menuSeq = '4';
		break;
	}

	$conn->begin();

	$sql = "replace into b01person values (
			 '$branch'
			,'$personCode'
			,'$personName'
			,'$id'
			,'$pwd'
			,'$position'
			,'$phone'
			,'$postno'
			,'$addr1'
			,'$addr2'
			,'$joinDate'
			,'$quitDate'
			,'$stat'
			,'$other'
			,'$gDomainID')";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo "
			<script>
				alert('담당자 저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
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
	location.replace('../branch/inchange_reg.php?menuIndex=13&menuSeq=<?=$menuSeq;?>&branch=<?=$branch;?>&person=<?=$personCode;?>&type=<?=$type;?>');
</script>