<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$date	= $_POST['date'];
	$type	= $_POST['type'];

	$sql = 'SELECT	a.jumin
			,		b.name
			,		b.phone
			,		b.mobile
			,		c.lvl
			,		c.app_no
			,		d.kind
			,		b.cd_key
			FROM	(
					SELECT	DISTINCT jumin
					FROM	client_his_svc
					WHERE	org_no = \''.$orgNo.'\' '.($svcCd != '' ? 'AND svc_cd = \''.$svcCd.'\'' : '');

	if ($date){
		$sql .= '	AND		LEFT(REPLACE(from_dt,\'-\',\'\'),'.StrLen($date).') <= \''.$date.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),'.StrLen($date).')	>= \''.$date.'\'';
		$date = Date('Ymd');
	}

	$sql .= '		) AS a
			INNER	JOIN (
					SELECT	DISTInCT m03_jumin AS jumin, m03_name AS name, m03_tel AS phone, m03_hp AS mobile, m03_key AS cd_key
					FROM	m03sugupja
					WHERE	m03_ccode = \''.$orgNo.'\'
					) AS b
					ON		b.jumin = a.jumin
			LEFT	JOIN (
					SELECT	jumin, app_no, MIN(level) AS lvl
					FROM	client_his_lvl
					WHERE	org_no = \''.$orgNo.'\' '.($svcCd != '' ? 'AND svc_cd = \''.$svcCd.'\'' : '').'
					AND		LEFT(REPLACE(from_dt,\'-\',\'\'),'.StrLen($date).') <= \''.$date.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),'.StrLen($date).')	>= \''.$date.'\'
					GROUP	BY jumin, app_no
					) AS c
					ON		c.jumin = a.jumin
			LEFT	JOIN (
					SELECT	jumin, RIGHT(GROUP_CONCAT(kind),1) AS kind
					FROM	client_his_kind
					WHERE	org_no = \''.$orgNo.'\'
					AND		LEFT(REPLACE(from_dt,\'-\',\'\'),'.StrLen($date).') <= \''.$date.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),'.StrLen($date).')	>= \''.$date.'\'
					GROUP	BY jumin
					ORDER	BY from_dt, to_dt
					) AS d
					ON		d.jumin = a.jumin
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$style = 'padding:3px; line-height:1.5em; cursor:default;';

		if ($i > 0){
			$style .= 'border-top:1px solid #CCCCCC;';
		}

		if ($row['kind'] == '3'){
			$row['kind'] = '기초';
		}else if ($row['kind'] == '2'){
			$row['kind'] = '의료';
		}else if ($row['kind'] == '4'){
			$row['kind'] = '경감';
		}else if ($row['kind'] == '1'){
			$row['kind'] = '일반';
		}else{
			$row['kind'] = '구분없음';
		}

		$phone = $myF->phoneStyle($row['phone'],'.');

		if ($phone){
			$phone .= ($row['mobile'] ? '&nbsp;/&nbsp;'.$myF->phoneStyle($row['mobile'],'.') : '');
		}

		if ($myF->issToGender($row['jumin']) == '남'){
			$gender = '<span style="color:BLUE;">남</span>';
		}else{
			$gender = '<span style="color:RED;">여</span>';
		}?>
		<div id="ID_ROW_<?=$i;?>" jumin="<?=$ed->en($row['jumin']);?>" appNo="<?=$row['app_no'];?>" key="<?=$row['cd_key'];?>" style="<?=$style;?>"><?
			if ($type == 'SIMPLE'){?>
				<div><?=$row['name'];?>&nbsp;&nbsp;<?=$myF->issToBirthday($row['jumin'],'.');?>&nbsp;&nbsp;<?=$gender;?>&nbsp;&nbsp;<?=$row['kind'];?></div><?
			}else{?>
				<div><?=$row['name'];?>&nbsp;&nbsp;<?=$myF->issToBirthday($row['jumin'],'.');?>&nbsp;&nbsp;<?=$gender;?></div>
				<div><?=$row['app_no'] ? $row['app_no'] : '인정번호없음';?>&nbsp;&nbsp;<?=$row['kind'];?></div>
				<div><?=$phone;?></div><?
			}?>
		</div><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>