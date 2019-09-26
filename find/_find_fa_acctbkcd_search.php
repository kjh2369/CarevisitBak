<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	gwan_cd as cd1, hang_cd as cd2, mog_cd as cd3, gwan_name as nm1, hang_name as nm2, mog_name as nm3, dtl_txt as other
			FROM	fa_item
			WHERE	re_gbn	 = \''.$_POST['gbn'].'\'
			AND		use_flag = \'Y\'
			ORDER	BY gwan_cd, hang_cd, mog_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$id = 0;

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($cd1 != $row['cd1']){
			$cd1  = $row['cd1'];
			$id1 = $i;
			$rowSpan[$id1][1] = 1;
		}else{
			$rowSpan[$id1][1] ++;
		}

		if ($cd2 != $row['cd1'].$row['cd2']){
			$cd2  = $row['cd1'].$row['cd2'];
			$id2 = $i;
			$rowSpan[$id2][2] = 1;
		}else{
			$rowSpan[$id2][2] ++;
		}
	}

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$cd1 = $row['cd1'];
		$cd2 = $row['cd2'];
		$cd3 = $row['cd3'];

		if ($tmpCD1 != $cd1){
			$tmpCD1  = $cd1;
			$nm1     = $row['nm1'];
		}

		if ($tmpCD2 != $cd1.$cd2){
			$tmpCD2  = $cd1.$cd2;
			$nm2     = $row['nm2'];
		}

		$nm3 = $row['nm3'];

		$data .= $cd1.chr(2)
			  .  $nm1.chr(2)
			  .  $rowSpan[$i][1].chr(2)
			  .  $cd2.chr(2)
			  .  $nm2.chr(2)
			  .  $rowSpan[$i][2].chr(2)
			  .  $cd3.chr(2)
			  .  $nm3.chr(2)
			  .  $row['other'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>