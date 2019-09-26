<?
	include("../inc/_db_open.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$code  = $_SESSION["userCenterCode"];
	$ssn   = $ed->de($_POST['c_cd']);
	$kind  = $_POST['kind'];
	$svcCd = $_POST['svcCd'];
	
	$lsReportId1 = 'CLTSVCCTC';	
	$lsReportId2 = 'SWCONT';
	$lsReportId3 = 'SWCONT2';

	//계약기간, 상태, 서비스 조회
	$sql = "   select from_dt as s_from_dt
			   ,      to_dt as s_to_dt
			   ,	  seq
			   ,	  svc_stat
			   ,	  svc_cd
				 from client_his_svc
				where org_no = '".$code."'
				  and jumin  = '".$ssn."'";

	if ($svcCd != 'all'){
		$sql .= " AND svc_cd = '".$svcCd."'";
	}

	$sql .=" order by from_dt desc";
	
	$conn -> query($sql);
	$conn -> fetch();

	$row_count = $conn -> row_count();
?>

<table class="my_table my_green" style="width:480px;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="170px">
		<col width="50px">
		<col >
	</colgroup>
	<tr>
		<th class="center">No</th>
		<th class="center">서비스명</th>
		<th class="center">서비스기간</th>
		<th class="center">상태</th>
		<th class="center">
			<div style="float:center; width:auto;">비고</div>
		</th>
	</tr>
	<?

	$no = 1;

	if($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn -> select_row($i);

			if ($row['svc_stat'] == '1'){
				$lsStat   = '이용';
				$lsReason = '';
			}else{
				if ($row['code'] == '0')
					$liReason = 1;
				else
					$liReason = 2;

				$lsStat   = '중지';
			}

			$s_from_dt = str_replace("-", "", $row['s_from_dt']);
			$s_to_dt   = str_replace("-", "", $row['s_to_dt']);
			
			$sql = 'select jumin
					,	   svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,      use_yoil1
					,      from_time1
					,      to_time1
					,      use_yoil2
					,      from_time2
					,      to_time2      
					  from client_contract
					 where org_no   = \''.$code.'\'
					   and svc_cd   = \''.$kind.'\'
					   and jumin    = \''.$ssn.'\'
					   and svc_seq  = \''.$row['seq'].'\'
					   and del_flag = \'N\'';
			
			$ct = $conn -> get_array($sql);
			
			$svc_seq = $ct['svc_seq'] != '' ? $ct['svc_seq'] : $svc_seq;

	
			if ($row['svc_cd'] == '0'){
				
				if($row['seq'] == $ct['svc_seq']){
					$svc_from_dt = str_replace('-','',$row['s_from_dt']);
				}

				if(($svc_from_dt != '') and ($s_from_dt >= $svc_from_dt)){
					$report_id = 'CONTRACT';
					$seq    = $row['seq'];
					$html = '<td class="center">
					<span class="btn_pack m"><button type="button" onclick="_svc_contract_report_show(\''.$report_id.'\',\''.$kind.'\',\''.$ed->en($ssn).'\', \''.$svc_seq.'\', \'200\');">요양</button></span>
					<span class="btn_pack m"><button type="button" onclick="_svc_contract_report_show(\''.$report_id.'\',\''.$kind.'\',\''.$ed->en($ssn).'\', \''.$svc_seq.'\', \'500\');">목욕</button></span>
					</td>';
				}else {
					$report_id = $lsReportId1;
					$svc_dt    = $s_from_dt.'/'.$s_to_dt;
					$html = '<td class="center"><span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="_svc_contract_report_show(\''.$report_id.'\',\''.$kind.'\',\''.$ed->en($ssn).'\', \''.$svc_dt.'\');">출력</button></span></td>';
				}
			}else if ($row['svc_cd'] == '1'){
				if($s_from_dt > '20141231'){
					$report_id = $lsReportId3;
					$svc_dt    = $s_from_dt.'/'.$s_to_dt;
					$html = '<td class="center"><span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="_svc_contract_report_show(\''.$report_id.'\',\''.$kind.'\',\''.$ed->en($ssn).'\', \''.$svc_dt.'\');">출력</button></span></td>';					
				}else {
					$report_id = $lsReportId2;
					$svc_dt    = $s_from_dt.'/'.$s_to_dt;
					$html = '<td class="center"><span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="_svc_contract_report_show(\''.$report_id.'\',\''.$kind.'\',\''.$ed->en($ssn).'\', \''.$svc_dt.'\');">출력</button></span></td>';
				}
			}else{
				$report_id = $lsReportId2;
				$svc_dt    = $s_from_dt.'/'.$s_to_dt;
				$html = '<td class="center"><span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="_svc_contract_report_show(\''.$report_id.'\',\''.$kind.'\',\''.$ed->en($ssn).'\', \''.$svc_dt.'\');">출력</button></span></td>';
			}

			echo '<tr>
					<td class="center">'.$no.'</td>
					<td class="left">'.$conn->_svcNm($row['svc_cd']).'</td>
					<td class="center" style=\'font-weight:bold;\'>'.$myF->dateStyle($s_from_dt,'.').'~'.$myF->dateStyle($s_to_dt,'.').'</td>
					<td class="center">'.$lsStat.'</td>
					'.$html.'
				  </tr>';
			$no ++;
			
			$svc_seq = 0;
			unset($svc_from_dt);
		}
	}else {
		$report_id = $lsReportId1;
		echo '<tr>
				<td class="center" colspan="4" style=\'font-weight:bold;\'>계약기간이 없습니다.</td>
				<td><span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="_svc_contract_report_show(\''.$report_id.'\',\''.$kind.'\',\''.$ed->en($ssn).'\', \''.$from_dt.'/'.$to_dt.'\');">출력</button></span></td>
			  </tr>';
	}

	$conn->row_free();?>
</table>
