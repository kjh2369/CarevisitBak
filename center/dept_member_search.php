<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];

	$sql = 'SELECT	a.jumin, a.name
			FROM	(
					SELECT	DISTINCT m02_ccode AS org_no, m02_yjumin AS jumin, m02_yname AS name
					FROM	m02yoyangsa
					WHERE	m02_ccode	= \''.$orgNo.'\'
					AND		m02_del_yn	= \'N\'
					) AS a
			LEFT	JOIN	dept_attach_list AS b
					ON		b.org_no= a.org_no
					AND		b.jumin	= a.jumin
			WHERE	b.jumin IS NULL
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
		<tr jumin="<?=$ed->en64($row['jumin']);?>">
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