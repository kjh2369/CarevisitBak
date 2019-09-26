<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$name = $_POST['name'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$SR = $_POST['SR'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$sql = 'SELECT	DISTINCT
					org_no
			,		svc_cd
			,		from_dt
			,		to_dt
			FROM	(
					SELECT	org_no
					,		jumin
					,		svc_cd
					,		from_dt
					,		to_dt
					FROM	client_his_svc
					WHERE	LEFT(jumin,7) = \''.SubStr($jumin,0,7).'\'
					AND		svc_cd != \''.$SR.'\'
					AND		svc_cd != \'3\'
					UNION	ALL
					SELECT	org_no
					,		jumin
					,		svc_cd
					,		from_dt
					,		to_dt
					FROM	care_svc_his
					WHERE	LEFT(jumin,7) = \''.SubStr($jumin,0,7).'\'
				) AS svc
			INNER	JOIN	m03sugupja
					ON		m03_ccode = org_no
					AND		m03_jumin = jumin
					AND		m03_name  = \''.$name.'\'
			ORDER	BY from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$duplicate = false;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$tmpF = str_replace('-','',$row['from_dt']);
		$tmpT = str_replace('-','',$row['to_dt']);

		if (($from >= $tmpF && $from <= $tmpT) ||
			($to >= $tmpF && $to <= $tmpT)){
			echo $tmpF.'/'.$tmpT.chr(13);
			$duplicate = true;
			break;
		}
	}

	$conn->row_free();

	if ($duplicate){
		$jumin = $ed->en($jumin);?>
		<script type="text/javascript">
			var parm = new Array();
				parm = {
					'jumin':'<?=$jumin;?>'
				,	'name':'<?=$name;?>'
				,	'from':'<?=$tmpF;?>'
				,	'to':'<?=$tmpT;?>'
				};

			var objModal = new Object();
				objModal.parm  = parm;

			window.showModalDialog('./care_svc_history.php', objModal, 'dialogWidth:500px;dialogHeight:600px;scroll:no;status:no;help:no');
		</script><?
	}

	include_once('../inc/_db_close.php');
?>