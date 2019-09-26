<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo  = $_POST['orgNo'];
	$IPIN   = $_POST['IPIN'];
	$SR     = $_POST['SR'];
	$type   = $_POST['type'];
	
	$sql = 'SELECT	DISTINCT m02_yjumin AS jumin, m02_yname AS name
			FROM	m02yoyangsa
			WHERE	m02_ccode = \''.$orgNo.'\'';

	$mem = $conn->_fetch_array($sql, 'jumin');

	
	if($type == '52'){ 
	
		$html = '<table class=\'my_table\' style=\'width:100%;\'>
					<colgroup>
						<col width="50px">
						<col width="70px">
						<col width="70px">
						<col width="70px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>';

		$sql = 'SELECT	*
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'';
		//if($debug) $html .= '<tr><td colspan="5">'.nl2br($sql).'</td></tr>'; 
		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			$attendeeCnt = SizeOf(Explode('&',$row['attendee']));
			if($row['meet_gbn'] == '1'){
				$meetGbn = '선정';
			}else if($row['meet_gbn'] == '2'){
				$meetGbn = '제공';
			}else if($row['meet_gbn'] == '3'){
				$meetGbn = '재사정';
			}else if($row['meet_gbn'] == '4'){
				$meetGbn = '종결';
			}else {
				$meetGbn = '기타';
			}
			
			$html .= '<tr>';
			$html .= '<td class="center">'.($i+1).'</td>';
			$html .= '<td class="center"><a href="#" onclick=\'setItem("sr='.$SR.'&type=52&r_seq='.$row['rcpt_seq'].'&seq='.$row['meet_seq'].'&copyYn=Y");\'>'.$myF->dateStyle($row['meet_dt'],'.').'</a></td>';
			$html .= '<td class="center">'.$meetGbn.'</td>';
			$html .= '<td class="center"><div class="left">'.$row['examiner'].'</div></td>';
			$html .= '<td class="center">'.$attendeeCnt.'명</td>';
			/*$html .= '<td class="center">'.($row['decision_gbn'] == '1' ? '제공' : '종결').'</td>';
			$html .= '<td class="center">'.$myF->dateStyle($row['decision_dt'],'.').'</td>';*/
			$html .= '<td class="last"></td>';
			$html .= '</tr>';
				
				
		}
	}else if($type == '102'){ 
	
		$html = '<table class=\'my_table\' style=\'width:100%;\'>
					<colgroup>
						<col width="50px">
						<col width="70px">
						<col width="70px">
						<col width="70px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>';

		$sql = 'SELECT	*
				FROM	hce_monitor
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'';
		
		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			$html .= '<tr>';
			$html .= '<td class="center">'.($i+1).'</td>';
			$html .= '<td class="center"><a href="#" onclick=\'setItem("sr='.$SR.'&type=102&r_seq='.$row['rcpt_seq'].'&seq='.$row['mntr_seq'].'&copyYn=Y");\'>'.$myF->dateStyle($row['mntr_dt'],'.').'</a></td>';
			$html .= '<td class="center">'.($row['mntr_gbn'] == '1' ? '최초' : '정기').'</td>';
			$html .= '<td class="center"><div class="left">'.$row['per_nm'].'</div></td>';
			$html .= '<td class="center"><div class="left">'.$row['inspector_nm'].'</div></td>';
			$html .= '<td class="last"></td>';
			$html .= '</tr>';
				
				
		}
	}else if($type == '142'){
		$html = '<table class=\'my_table\' style=\'width:100%;\'>
					<colgroup>
						<col width="50px">
						<col width="70px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>';

		$sql = 'SELECT	*
				FROM	hce_provide_evl
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND     del_flag= \'N\'';
		
		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			$html .= '<tr>';
			$html .= '<td class="center">'.($i+1).'</td>';
			$html .= '<td class="center"><a href="#" onclick=\'setItem("sr='.$SR.'&type=142&r_seq='.$row['rcpt_seq'].'&seq='.$row['evl_seq'].'&copyYn=Y");\'>'.$myF->dateStyle($row['evl_dt'],'.').'</a></td>';
			$html .= '<td class="center"><div class="left">'.$mem[$row['evl_cd']]['name'].'</div></td>';
			$html .= '<td class="last"></td>';
			$html .= '</tr>';
		}
	}else if($type == '131'){
		
		$sql = 'SELECT	*
				FROM	hce_evaluation
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND     del_flag= \'N\'
				ORDER   by ev_dt asc
				limit   1';
		
		$row = $conn -> get_array($sql); 
		
		$data = 'sr='.$SR.'&type=131&r_seq='.$row['rcpt_seq'].'&seq='.$row['ev_seq'].'&copyYn=Y';

	}else {

		$html = '<table class=\'my_table\' style=\'width:100%;\'>
					<colgroup>
						<col width="50px">
						<col width="70px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>';

		$sql = 'SELECT	*
				FROM	hce_plan_sheet
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'';
		
		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			$attendeeCnt = SizeOf(Explode('&',$row['attendee']));
			
			
			$html .= '<tr>';
			$html .= '<td class="center">'.($i+1).'</td>';
			$html .= '<td class="center"><a href="#" onclick=\'setItem("sr='.$SR.'&type=52&r_seq='.$row['rcpt_seq'].'&seq='.$row['plan_seq'].'&copyYn=Y");\'>'.$myF->dateStyle($row['plan_dt'],'.').'</a></td>';
			$html .= '<td class="center"><div class="left">'.$row['planer'].'</div></td>';
			$html .= '<td class="last"></td>';
			$html .= '</tr>';
				
				
		}
	
		
	}
	
	if($type == '131'){
		echo $data;
	}else {
		$conn->row_free();

		$html .= '	</tbody>
				  </table>';

		echo $html;

		include_once('../inc/_db_close.php');
	}
?>