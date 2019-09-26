<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$deptCd	= $_POST['deptCd'];

	$sql = 'SELECT	a.jumin, b.name, a.mg_yn
			FROM	dept_attach_list AS a
			INNER	JOIN (
					SELECT	DISTINCT m02_yjumin AS jumin, m02_yname AS name
					FROM	m02yoyangsa
					WHERE	m02_ccode	= \''.$orgNo.'\'
					AND		m02_del_yn	= \'N\'
					) AS b
					ON		b.jumin = a.jumin
			WHERE	a.org_no = \''.$orgNo.'\'
			AND		a.dept_cd= \''.$deptCd.'\'
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$style = '';

		if ($i > 0){
			$style .= 'border-top:1px solid #CCCCCC;';
		}?>
		<tr jumin="<?=$ed->en64($row['jumin']);?>" mgYn="<?=$row['mg_yn'];?>">
			<td class="center bottom" style="<?=$style;?>"><?=$no;?></td>
			<td class="center bottom" style="<?=$style;?>"><div class="left"><?=$row['name'];?></div></td>
			<td class="center bottom" style="<?=$style;?>"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
			<td class="center bottom" style="<?=$style;?>"><?=$myF->issToGender($row['jumin']);?></td>
			<td class="center bottom last" style="<?=$style;?>"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>