<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//id,name,seq,url,link_gbn,permit,debug,use_yn,demo_yn

	$gbn = $_POST['gbn'];
	$top = $_POST['top'];
	$lft = $_POST['left'];

	if (!$top) $top = 'A';
	if (!$lft) $lft = '1';?>

	<table class="my_table" style="width:100%; margin-top:-1px;">
		<colgroup>
			<col width="15px">
			<col>
		</colgroup>
		<tbody><?
			$sql = 'SELECT	*
					FROM	';

			if ($gbn == '1'){
				$sql .= '	menu_top';
			}else if ($gbn == '2'){
				$sql .= '	menu_left';
			}else{
				$sql .= '	menu_list';
			}

			if ($gbn == '1'){
			}else if ($gbn == '2'){
				$sql .= '
					WHERE	m_top	= \''.$top.'\'';
			}else{
				$sql .= '
					WHERE	m_top	= \''.$top.'\'
					AND		m_left	= \''.$lft.'\'';
			}

			$sql .= '
					ORDER	BY seq,id';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<tr top="<?=$row['m_top'];?>"
					left="<?=$row['m_left'];?>"
					id="<?=$row['id'];?>">
					<td class="right last"></td>
					<td class="last" onclick="lfSelMenu($(this).parent());"><a href="#" onclick="return false"><?=$row['name'];?></a></td>
				</tr><?
			}

			$conn->row_free();?>
		</tbody>
	</table><?
	include_once('../inc/_db_close.php');
?>