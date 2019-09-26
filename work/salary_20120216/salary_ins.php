<?
	include_once("../inc/_myFun.php");

	$temp_from_date = $year.'-'.$month.'-01';
	$temp_to_date   = $myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $temp_from_date, 'Y-m-d'), 'Y-m-d');

	// 고용보험 설정 ---------------------------------------------------------------------------//
	$ins_employ[0]['from']   = '2010-01-01';
	$ins_employ[0]['to']     = '2011-03-31';
	$ins_employ[0]['worker'] = 0.45;
	$ins_employ[0]['center'] = 0.45;

	$ins_employ[1]['from']   = '2011-04-01';
	$ins_employ[1]['to']     = '9999-12-31';
	$ins_employ[1]['worker'] = 0.55;
	$ins_employ[1]['center'] = 0.55;

	$ins_employ_cnt = sizeof($ins_employ);

	for($i=0; $i<$ins_employ_cnt; $i++){
		if ($temp_from_date >= $ins_employ[$i]['from'] &&
			$temp_to_date   <= $ins_employ[$i]['to']){
			$ins_rate['worker_employ'] = $ins_employ[$i]['worker'];
			$ins_rate['center_employ'] = $ins_employ[$i]['center'];
			break;
		}
	}
	//------------------------------------------------------------------------------------------//

	// 건강보험 설정 ---------------------------------------------------------------------------//
	$ins_health[0]['from']   = '2010-01-01';
	$ins_health[0]['to']     = '2010-12-31';
	$ins_health[0]['worker'] = 2.665;
	$ins_health[0]['center'] = 2.665;

	$ins_health[1]['from']   = '2011-01-01';
	$ins_health[1]['to']     = '9999-12-31';
	$ins_health[1]['worker'] = 2.82;
	$ins_health[1]['center'] = 2.82;

	$ins_health_cnt = sizeof($ins_health);

	for($i=0; $i<$ins_health_cnt; $i++){
		if ($temp_from_date >= $ins_health[$i]['from'] &&
			$temp_to_date   <= $ins_health[$i]['to']){
			$ins_rate['worker_health'] = $ins_health[$i]['worker'];
			$ins_rate['center_health'] = $ins_health[$i]['center'];
			break;
		}
	}
	//------------------------------------------------------------------------------------------//

	// 장기요양 --------------------------------------------------------------------------------//
	$ins_oldcare[0]['from']   = '2010-01-01';
	$ins_oldcare[0]['to']     = '9999-12-31';
	$ins_oldcare[0]['worker'] = 6.55;
	$ins_oldcare[0]['center'] = 6.55;

	$ins_oldcare_cnt = sizeof($ins_oldcare);

	for($i=0; $i<$ins_oldcare_cnt; $i++){
		if ($temp_from_date >= $ins_oldcare[$i]['from'] &&
			$temp_to_date   <= $ins_oldcare[$i]['to']){
			$ins_rate['worker_oldcare'] = $ins_oldcare[$i]['worker'];
			$ins_rate['center_oldcare'] = $ins_oldcare[$i]['center'];
			break;
		}
	}
	//------------------------------------------------------------------------------------------//




	// 국민연금 --------------------------------------------------------------------------------//
	$ins_annuity[0]['from']   = '2010-01-01';
	$ins_annuity[0]['to']     = '9999-12-31';
	$ins_annuity[0]['worker'] = 4.5;
	$ins_annuity[0]['center'] = 4.5;

	$ins_annuity_cnt = sizeof($ins_annuity);

	for($i=0; $i<$ins_annuity_cnt; $i++){
		if ($temp_from_date >= $ins_annuity[$i]['from'] &&
			$temp_to_date   <= $ins_annuity[$i]['to']){
			$ins_rate['worker_annuity'] = $ins_annuity[$i]['worker'];
			$ins_rate['center_annuity'] = $ins_annuity[$i]['center'];
			break;
		}
	}
	//------------------------------------------------------------------------------------------//



	// 산제보험 --------------------------------------------------------------------------------//
	$ins_sanje[0]['from']   = '2010-01-01';
	$ins_sanje[0]['to']     = '9999-12-31';
	$ins_sanje[0]['worker'] = 0;
	$ins_sanje[0]['center'] = 0.7;

	$ins_sanje_cnt = sizeof($ins_sanje);

	for($i=0; $i<$ins_sanje_cnt; $i++){
		if ($temp_from_date >= $ins_sanje[$i]['from'] &&
			$temp_to_date   <= $ins_sanje[$i]['to']){
			$ins_rate['worker_sanje'] = $ins_sanje[$i]['worker'];
			$ins_rate['center_sanje'] = $ins_sanje[$i]['center'];
			break;
		}
	}
	//------------------------------------------------------------------------------------------//
?>