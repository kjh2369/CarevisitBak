<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$re_gbn = $_POST['re_gbn'];


	$sql = 'SELECT	a.gwan_cd, a.hang_cd, a.mog_cd, a.gwan_name, a.hang_name, a.mog_name, b.amt, a.dtl_txt
			FROM	fa_item AS a
			LEFT	JOIN	fa_budget AS b
					ON		b.org_no	= \''.$orgNo.'\'
					AND		b.year		= \''.$year.'\'
					AND		b.gwan_cd	= a.gwan_cd
					AND		b.hang_cd	= a.hang_cd
					AND		b.mog_cd	= a.mog_cd
					AND		b.re_gbn	= a.re_gbn
			WHERE	a.re_gbn	= \''.$re_gbn.'\'
			AND		a.use_flag	= \'Y\'
			ORDER	BY gwan_cd, hang_cd, mog_cd
			';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$gwan_cd = $row['gwan_cd'];
		$hang_cd = $row['hang_cd'];
		$mog_cd = $row['mog_cd'];

		if (!$data[$gwan_cd])
			 $data[$gwan_cd] = Array('name'=>$row['gwan_name'], 'rows'=>0, 'LIST'=>Array());
		if (!$data[$gwan_cd]['LIST'][$hang_cd])
			 $data[$gwan_cd]['LIST'][$hang_cd] = Array('name'=>$row['hang_name'], 'rows'=>0, 'LIST'=>Array());

		$data[$gwan_cd]['LIST'][$hang_cd]['LIST'][$mog_cd] = Array('name'=>$row['mog_name'], 'amt'=>$row['amt'], 'dtl_txt'=>$row['dtl_txt']);
		$data[$gwan_cd]['rows'] ++;
		$data[$gwan_cd]['LIST'][$hang_cd]['rows'] ++;
	}

	$conn->row_free();

	foreach($data as $gwan_cd => $gwan){
		foreach($gwan['LIST'] as $hang_cd => $hang){
			foreach($hang['LIST'] as $mog_cd => $mog){?>
				<tr><?
				if ($gwan['rows'] > 0){?>
					<th id="COL_<?=$gwan_cd;?>" style="text-align:left; vertical-align:top; padding-top:8px" rowspan="<?=$gwan['rows'];?>"><?=$gwan['name'];?></th><?
				}
				if ($hang['rows'] > 0){?>
					<th id="COL_<?=$gwan_cd;?>_<?=$hang_cd;?>" style="text-align:left; vertical-align:top; padding-top:8px" rowspan="<?=$hang['rows'];?>"><?=$hang['name'];?></th><?
				}?>
				<td style="vertical-align:top;"><?=$mog['name'];?></td>
				<td style="vertical-align:top;"><input id="amt" type="text" value="<?=number_format($mog['amt']);?>" class="number" style="width:80%;" gwan_cd="<?=$gwan_cd;?>" hang_cd="<?=$hang_cd;?>" mog_cd="<?=$mog_cd;?>">&nbsp;원</td>
				<td class="last"><?=$mog['dtl_txt'];?></td>
				</tr><?

				$gwan['rows'] = 0;
				$hang['rows'] = 0;
			}
		}
	}

	unset($data);
