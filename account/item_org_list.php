<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['USER_ORGNO'];
	$bodyid = $_POST['bodyid'];
	$re_gbn = $_POST['re_gbn'];


	$sql = 'SELECT	gwan_cd, hang_cd, mog_cd, gwan_name, hang_name, mog_name, dtl_txt
			FROM	fa_item
			WHERE	re_gbn	 = \''.$re_gbn.'\'
			AND		use_flag = \'Y\'
			ORDER	BY gwan_cd, hang_cd, mog_cd
			';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$data[$row['gwan_cd']])
			 $data[$row['gwan_cd']] = Array('name'=>$row['gwan_name'], 'rows'=>0, 'LIST'=>Array());
		if (!$data[$row['gwan_cd']]['LIST'][$row['hang_cd']])
			 $data[$row['gwan_cd']]['LIST'][$row['hang_cd']] = Array('name'=>$row['hang_name'], 'rows'=>0, 'LIST'=>Array());

		$data[$row['gwan_cd']]['rows'] ++;
		$data[$row['gwan_cd']]['LIST'][$row['hang_cd']]['rows'] ++;
		$data[$row['gwan_cd']]['LIST'][$row['hang_cd']]['LIST'][$row['mog_cd']] = Array('name'=>$row['mog_name'], 'dtl_txt'=>$row['dtl_txt']);
	}

	$conn->row_free();

	foreach($data as $gwan_cd => $gwan){
		foreach($gwan['LIST'] as $hang_cd => $hang){
			foreach($hang['LIST'] as $mog_cd => $mog){?>
				<tr><?
				if ($gwan['rows'] > 0){?>
					<td class="txt_center" style="vertical-align:top;" rowspan="<?=$gwan['rows'];?>"><?=$gwan_cd;?></td>
					<td style="vertical-align:top;" rowspan="<?=$gwan['rows'];?>"><?=$gwan['name'];?></td><?
				}
				if ($hang['rows'] > 0){?>
					<td class="txt_center" style="vertical-align:top;" rowspan="<?=$hang['rows'];?>"><?=$hang_cd;?></td>
					<td style="vertical-align:top;" rowspan="<?=$hang['rows'];?>"><?=$hang['name'];?></td><?
				}?>
				<td class="txt_center" style="vertical-align:top;"><?=$mog_cd;?></td>
				<td style="vertical-align:top;"><?=$mog['name'];?></td>
				<td class="last"><?=$mog['dtl_txt'];?></td>
				</tr><?

				$gwan['rows'] = 0;
				$hang['rows'] = 0;
			}
		}
	}?>
