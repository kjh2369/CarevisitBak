<?
	$sql = 'SELECT counsel.client_dt
			,      MAX(counsel.client_seq) AS client_seq
			,      counsel.client_nm
			,      counsel.client_ssn
			,      counsel.client_phone
			,	   counsel.client_mobile
			,      counsel.client_counsel
			,      mst.kind
			  FROM (
				   SELECT client_dt
				   ,      client_seq
				   ,      client_nm
				   ,      client_ssn
				   ,      client_phone
				   ,	  client_mobile
				   ,      client_counsel
					 FROM counsel_client '.$wsl.'
				   ) AS counsel
			  LEFT JOIN (
				   SELECT MIN(m03_mkind) AS kind
				   ,      m03_jumin AS jumin
					 FROM m03sugupja
					WHERE m03_ccode  = \''.$code.'\'
					  AND m03_del_yn = \'N\'
					GROUP BY m03_jumin
				  ) AS mst
			   ON counsel.client_ssn = mst.jumin
			 GROUP BY counsel.client_dt, counsel.client_seq, counsel.client_nm, counsel.client_ssn, counsel.client_phone, counsel.client_counsel
			 ORDER BY client_nm, client_dt DESC '.$limit_query;

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$no = $pageCount + ($i + 1); //$i + 1;
		
		//상담 재가/노인돌봄/가사간병/장애활동
		$sql = "select talker_dt
				  from counsel_client_normal
				 where org_no     = '".$code."'
				   and client_dt  = '".$row['client_dt']."'
				   and client_seq = '".$row['client_seq']."'";
		
		$normalDt = $conn->get_data($sql);
		
		//상담 신생아
		$sql = "select talker_dt
				  from counsel_client_baby
				 where org_no     = '".$code."'
				   and client_dt  = '".$row['client_dt']."'
				   and client_seq = '".$row['client_seq']."'";

		$babyDt = $conn->get_data($sql);
		
		$talkDt = ($normalDt != '' ? $normalDt : $babyDt);
		

		if($row['client_mobile'] != ''){
			$tel = $row['client_mobile'];
		}else {
			$tel = $row['client_phone'];
		}

		if ($counsel_list_mode == 1){
			echo '<tr style=\'cursor:default;\' onmouseover=\'this.style.backgroundColor="#f2f5ff";\' onmouseout=\'this.style.backgroundColor="#ffffff";\'>';
		}else{
			echo '<tr style=\'cursor:pointer;\' onmouseover=\'this.style.background="#efefef";\' onmouseout=\'this.style.background="#ffffff";\' onclick=\'counsel_view("'.$code.'","'.$row['client_dt'].'","'.$row['client_seq'].'","'.$row['client_counsel'].'");\'>';
		}

		echo '	<td class=\'center\'>'.$no.'</td>';

		if ($h_ref == 'report'){
			echo '	<td class=\'left\'>'.$row['client_nm'].'</td>';
		}else{
			echo '	<td class=\'left\'><a href=\'#\' onclick=\'counsel_reg("'.$row['client_dt'].'","'.$row['client_seq'].'")\'>'.$row['client_nm'].'</a></td>';
		}
		
		if($row['kind'] == 'A'){
			$row['kind'] = '';
		}

		echo '	<td class=\'left\'>'.$myF->phoneStyle($tel).'</td>
				<td class=\'left\'>'.$conn->kind_name_svc($row['client_counsel']).'</td>
				<td class=\'left\'>'.$conn->kind_name_svc($row['kind']).'</td>';
		
		echo ' <td class=\'center\'>'.$myF->dateStyle($talkDt).'</td>';

		if ($counsel_list_mode == 1){
			echo '<td class=\'left last\'>';

			if ($h_ref == 'report'){
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'counsel_reg("'.$row['client_dt'].'","'.$row['client_seq'].'")\'>수정</button></span>   ';
			}

			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'counsel_print("'.$code.'","'.$row['client_dt'].'","'.$row['client_seq'].'","'.$row['client_counsel'].'")\'>출력</button></span>   ';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'counsel_delete("'.$code.'","'.$row['client_dt'].'","'.$row['client_seq'].'");\'>삭제</button></span>';
			echo '</td>';
		}else{
			echo '<td class=\'left last\'>&nbsp;</td>';
		}

		echo '
			</tr>';
	}

	$conn->row_free();
?>