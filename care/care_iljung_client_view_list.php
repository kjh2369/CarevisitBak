<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$time	= str_replace(':','',$_POST['time']);
	$date	= str_replace('-','',$_POST['date']);
	$suga	= $_POST['suga'];
	$res	= $_POST['res'];
	$mem	= $ed->de($_POST['mem']);
	$rqst	= $_POST['rqst'];

	if ($rqst == 'PERSON'){
	}else if ($rqst == 'SERVICE'){
	}else{
		$rqst = 'CATEGORY';
	}

	$sql = 'SELECT	DISTINCT t01_jumin AS jumin
			,		t01_sugup_seq AS seq
			,		m03_name AS name
			,		IFNULL(a.jumin,t01_jumin) AS real_jumin
			FROM	t01iljung
			INNER	JOIN	m03sugupja
					ON		m03_ccode = t01_ccode
					AND		m03_mkind = \'6\'
					AND		m03_jumin = t01_jumin
			LEFT	JOIN	mst_jumin AS a
					ON		a.org_no= t01_ccode
					AND		a.gbn	= \'1\'
					AND		a.code	= t01_jumin
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \''.$SR.'\'
			AND		t01_sugup_date	= \''.$date.'\'
			AND		t01_sugup_fmtime= \''.$time.'\'
			AND		t01_suga_code1	= \''.$suga.'\'
			AND		t01_yoyangsa_id1= \''.$res.'\'
			AND		t01_yoyangsa_id2= \''.$mem.'\'';

	if ($rqst == 'PERSON'){
		$sql .= '
			AND		t01_request		= \'PERSON\'';
	}else if ($rqst == 'SERVICE'){
		$sql .= '
			AND		t01_request		= \'SERVICE\'';
	}else if ($rqst = 'CATEGORY'){
		$sql .= '
			AND		LENGTH(t01_request) = 10';
	}

	$sql .= '
			AND		t01_del_yn		= \'N\'
			ORDER	BY name';

	#echo '<tr><td colspan="5">';
	#echo nl2br($sql);
	#echo '</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);
		$jumin	= SubStr($row['real_jumin'].'0000000',0,13);
		$gender	= $myF->issToGender($jumin);
		$birth	= $myF->issToBirthday($jumin,'.');

		if ($gender == '남'){
			$gender = '<span style="color:BLUE;">남</span>';
		}else if ($gender == '여'){
			$gender = '<span style="color:RED;">여</span>';
		}

		$para = 'jumin='.$ed->en($row['jumin']).'&seq='.$row['seq'];

		//업무로그 작성여부
		$sql = 'SELECT	COUNT(*) AS cnt, contents AS str
				FROM	care_works_log
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		date		= \''.$date.'\'
				AND		jumin		= \''.$row['jumin'].'\'
				AND		suga_cd		= \''.$suga.'\'
				AND		resource_cd = \''.$res.'\'
				AND		mem_cd		= \''.$mem.'\'';

		$R = $conn->get_array($sql);

		$IsWorkLog = $R['cnt'];

		if ($IsWorkLog > 0){
			$IsWorkLog = 'Y';
			$workCont = stripslashes($R['str']);
		}else{
			$sql = 'SELECT	COUNT(*)
					FROM	care_result
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		jumin	= \''.$row['jumin'].'\'
					AND		date	= \''.$date.'\'
					AND		time	= \''.$time.'\'
					AND		seq		= \''.$row['seq'].'\'
					AND		no		= \'1\'';

			$IsWorkLog = $conn->get_data($sql);

			if ($IsWorkLog > 0){
				$IsWorkLog = 'Y';
			}else{
				$IsWorkLog = 'N';
			}

			$workCont = '';
		}
		
		
		
		?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$row['name'];?></td>
			<td class="center"><?=$gender;?></td>
			<td class="center"><?=$birth;?></td>
			<td class="center"><div class="left"><?=$workCont;?></div></td>
			<td class="center"><?
				if ($SR == 'S'){?>
					<div style="float:left; width:auto; margin-left:5px; padding-top:2px;"><span class="btn_pack small"><button onclick="lfWorkLogReg(this, '<?=$para;?>');">업무일지</button></span></div><?
				}
				if ($rqst == 'SERVICE'){?>
					<div style="float:left; width:auto; margin-left:5px; padding-top:2px;"><span class="btn_pack small"><button onclick="lfRemoe(this, '<?=$para;?>');">삭제</button></span></div><?
				}
				if ($SR == 'S' && $IsWorkLog == 'Y'){?>
					<div style="float:left; width:auto; margin-left:5px;"><span style="color:BLUE; font-weight:bold;">-업무일지작성</span></div><?
				}?>
			</td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
