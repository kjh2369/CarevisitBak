<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];

	$sql = 'SELECT	DISTINCT
					m02_yjumin AS jumin
			,		m02_yname AS name
			FROM	m02yoyangsa
			WHERE	m02_ccode = \''.$code.'\'
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($no == $rowCnt){
			$class = 'bottom';
		}else{
			$class = '';
		}?>
		<tr>
			<td class="center <?=$class;?>"><?=$no;?></td>
			<td class="center <?=$class;?> last"><div style="float:left; width:auto; margin-left:5px;"><a href="#" class="clsA" onclick="lfMenuGet(this,'<?=$ed->en($row['jumin']);?>'); return false;"><?=$row['name'];?></a></div></td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>