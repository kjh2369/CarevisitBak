<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code   = $_POST['code'];
	$kind   = $_POST['kind'];
	$jumin  = $ed->de($_POST['jumin']);
	$result = $_POST['result'];
	$date   = $_POST['date'];

	if (empty($date)) $date = date('Ymd', mktime());

	$col = explode(',', $result);

	if (is_array($col)){
		$sql = '';

		foreach($col as $i => $column){
			$tmp = explode('_', $column);

			if ($tmp[0] == 'iljung'){
				switch($tmp[1]){
					case 'date':
						$sl = 'select min(t01_sugup_date) as min_dt
							   ,      max(t01_sugup_date) as max_dt
							     from t01iljung
								where t01_ccode  = \''.$code.'\'
								  and t01_mkind  = \''.$kind.'\'
								  and t01_jumin  = \''.$jumin.'\'
								  and t01_del_yn = \'N\'
								  and left(t01_sugup_date, 6) = \''.substr($date,0,6).'\'';

						$dt = $conn->get_array($sl);
						$min_dt = $myF->dateStyle($dt['min_dt'],'.');
						$max_dt = $myF->dateStyle($dt['max_dt'],'.');
						unset($dt);
						break;
				}
			}else{
				$sql .= (!empty($sql) ? ',' : '');
				$sql .= 'm03_'.$column.' as \''.$column.'\'';
			}
		}


		if (!empty($sql)){
			$sql = 'select '.$sql.'
					  from m03sugupja
					 where m03_ccode = \''.$code.'\'
					   and m03_mkind = \''.$kind.'\'
					   and m03_jumin = \''.$jumin.'\'';

			$cIf = $conn->get_array($sql);

			foreach($col as $i => $column){
				if ($column == 'jumin'){
					$cIf[$column] = $myF->issStyle($cIf[$column]);
				}
				echo '<div class=\'find_'.$column.'\'>'.$cIf[$column].'</div>';
			}

			echo '<div class=\'find_min_dt\'>'.$min_dt.'</div>';
			echo '<div class=\'find_max_dt\'>'.$max_dt.'</div>';
		}
	}

	include_once('../inc/_db_close.php');
?>