<?
	$sql = "select salary_type
			,      salary_index
			,      salary_subject
			,      salary_pay
			  from salary_addon
			 where org_no = '$code'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$index[1] = -1;
	$index[2] = -1;

	if ($is_preview == true){
		$addon_pay[1] = $rank_pay;
	}else{
		$addon_pay[1] = $salary['rank_pay'];
	}

	$addon_pay[2] = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$index[intval($row['salary_type'])] ++;

		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['index']	= $row['salary_index'];
		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['subject']	= $row['salary_subject'];
		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['pay']		= $row['salary_pay'];

		if ($is_preview == true){
			$addon_pay[$row['salary_type']] += $row['salary_pay'];
		}
	}

	$conn->row_free();

	$addon_1 = sizeof($salary_addon[1]);
	$addon_2 = sizeof($salary_addon[2]);
?>