<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$html  = '';
	
	
	$svcGbn		= Explode(chr(1),$_POST['chkSvc']);
	$code		= $_POST['code'];
	$mode		= $_POST['mode'];
	$year       = $_POST['year'];
	$month      = number_format($_POST['month'])<10? '0'.number_format($_POST['month']) : number_format($_POST['month']);
	$yymm       = $year.$month; 	
	
	
	$width = 'width:100%;';

	$html .= '<table class=\'my_table\' style=\''.$width.'\' '.$tableBorderStyle.'>
				<colgroup>
					<col width=\'40px\'>
					<col width=\'70px\'>
					<col width=\'70px\'>
					<col width=\'70px\'>
					<col width=\'80px\'>
					<col>
				</colgroup>';

	

	$html .= '	<thead>
					<tr>
						<th class=\'head\' ><input id="chkAll" name="chk" type="checkbox" class="checkbox" onclick="lfChkAll();"></th>
						<th class=\'head\' >성명</th>
					    <th class=\'head\' >생년월일</th>	
					    <th class=\'head\' >등급</th>
						<th class=\'head\'>
							<div style=\'text-align:left; padding-left:5px;\'>
								<span class=\'btn_pack small\'><button type=\'button\' style=\'width:52px;\' onclick=\'lfPrint("sel");\'>선택출력</button></span>
							</div>
						</th>
					    <th class=\'head\' >비고</th>
					</tr>';
				
		$html .= '</thead>';

	
	
	if (!Empty($sl)){
		$sl .= ' UNION ALL ';
	}
	
	$sl .= 'SELECT t01_jumin AS jumin
			  FROM t01iljung
			 WHERE t01_ccode = \''.$code.'\'
			   AND t01_mkind = \'0\'
			   AND t01_del_yn = \'N\'
			   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'';

	
	$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
			,      mst.name AS c_nm
			,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
			  FROM ( '.$sl.' ) AS iljung
			 INNER JOIN (
				   SELECT m03_jumin AS jumin
				   ,      m03_name AS name
				   ,      m03_tel AS tel
					 FROM m03sugupja
					WHERE m03_ccode = \''.$code.'\'
					  AND m03_mkind = \'0\'
				   ) AS mst
				ON mst.jumin = iljung.jumin
			  LEFT JOIN (
				   SELECT jumin
				   ,      level
					 FROM client_his_lvl
					WHERE org_no = \''.$code.'\'
					  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
					  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
				   ) AS lvl
				ON lvl.jumin = iljung.jumin
			  LEFT JOIN (
				   SELECT jumin
				   ,      kind
					 FROM client_his_kind
					WHERE org_no = \''.$code.'\'
					  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
					  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
				   ) AS kind
				ON kind.jumin = iljung.jumin
			 ORDER BY name';


	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$html .= '<tr>
					<td class=\'center\'><input id="chkIn'.$i.'" name="chkIn" type="checkbox" class="checkbox" cltCd="'.$ed->en($row['c_cd']).'"></td>
					<td class=\'left\'>'.$row['c_nm'].'</td>
					<td class=\'center\'>'.$myF->issToBirthday($row['c_cd'],'.').'</td>
					<td class=\'center\'>'.$row['l_nm'].'</td>
					<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfPrint("'.$ed->en($row['c_cd']).'");\'>출력</button></span></td>
				  </tr>';

		if($excel_yn != 'Y'){
			$html .= '	';
		}

	}

	$conn->row_free();

	if ($rowCount > 0){
		$html .= '<tr>
					<td class=\'center bottom last\' colspan=\'9\'>&nbsp;</td>
				  </tr>';
	}else{

		$html .= '<tr>
					<td class=\'center last\' style="text-align:center;" colspan=\'9\'>'.$myF->message('nodata', 'N').'</td>
				  </tr>';

	}

	$html .= '</table>';


	echo $html;


	include_once('../inc/_db_close.php');
?>