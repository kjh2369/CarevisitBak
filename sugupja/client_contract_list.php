<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');


	$code = $_POST['code'];
	$ssn  = $ed->de($_POST['ssn']);
	$kind = $_POST['kind'];

?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<!--col width="100px"-->
		<!--col width="100px"-->
		<col width="135px">
		<col width="*">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">작성일자</th>
			<!--th class="head">서비스구분</th-->
			<!--th class="head">케어구분</th-->
			<th class="head">계약기간</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = 'select svc_cd
				,	   seq
				,	   reg_dt
				,	   svc_seq
				,	   from_dt
				,	   to_dt
				,	   from_time3
				  from client_contract
				 where org_no   = \''.$code.'\'
				   and svc_cd   = \''.$kind.'\'
				   and jumin    = \''.$ssn.'\'
				   and del_flag = \'N\'';


		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$sql =  ' select from_dt
					  ,		 to_dt
						from client_his_svc
					   where org_no = \''.$code.'\'
						 and jumin  = \''.$ssn.'\'
						 and seq    = \''.$row['svc_seq'].'\'';
			$svc = $conn->get_array($sql);

			//계약기간
			$svc_dt = ($row['from_dt']!=''? ($myF->dateStyle($row['from_dt'],'.').'~'.$myF->dateStyle($row['to_dt'],'.')) : ($myF->dateStyle($svc['from_dt'],'.').'~'.$myF->dateStyle($svc['to_dt'],'.')));

			echo '<tr>';
			echo '<td class=\'center\'>'.($i+1).'</td>';
			echo '<td class=\'center\'>'.$myF->dateStyle($row['reg_dt'],'.').'</td>';
			/*
			if($row['from_time3'] != ''){
				echo '<td class=\'center\'>주야간보호</td>';
			}else {
				echo '<td class=\'center\'>'.$conn->kind_name_svc($row['svc_cd']).'</td>';
			}
			*/
			//echo '<td class=\'center\'>'.$row['svc_name'].'</td>';
			echo '<td class=\'center\'>'.$svc_dt.'</td>';
			echo '<td class=\'left\'>';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_reg("'.$row['seq'].'", "'.$row['svc_kind'].'");\'>수정</button></span> ';
			if($code == '34873000011'){ //보현재가(경남칠원)
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_show("'.$row['seq'].'","'.$row['svc_seq'].'", "200_test");\'>요양</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_show("'.$row['seq'].'","'.$row['svc_seq'].'", "500_test");\'>목욕</button></span> ';
			}else {
				//if ($debug){
					echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfShowCont("'.$row['seq'].'","'.$row['svc_seq'].'", "200");\'>요양(HTML)</button></span> ';
					echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_show("'.$row['seq'].'","'.$row['svc_seq'].'", "200");\'>요양</button></span> ';
				//}else{
				//	echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_show("'.$row['seq'].'","'.$row['svc_seq'].'", "200");\'>요양</button></span> ';
				//}
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_show("'.$row['seq'].'","'.$row['svc_seq'].'", "500");\'>목욕</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_show("'.$row['seq'].'","'.$row['svc_seq'].'", "800");\'>간호</button></span> ';
				if($gDayAndNight){
					echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_show("'.$row['seq'].'","'.$row['svc_seq'].'", "900");\'>주야간보호</button></span> ';
				}
				
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfPrtHwp("'.$row['seq'].'");\'>입소신청서</button></span> ';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'lfExcel("'.$row['seq'].'","'.$row['svc_seq'].'");\'>이용내역서</button></span> ';
			}
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'go_contract_delete("'.$row['seq'].'", "'.$row['svc_kind'].'");\'>삭제</button></span> ';
			echo '</td>';
			echo '</tr>';
		}

		$conn->row_free();
	?>
	</tbody>
	<tfoot>
		<tr>
		<?
			if ($row_count > 0){
				echo '<td class=\'left\' colspan=\'6\'>'.$myF->message($row_count, 'N').'</td>';
			}else{
				echo '<td class=\'center\' colspan=\'6\'>'.$myF->message('nodata', 'N').'</td>';
			}
		?>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");

	echo '<input name=\'ssn\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'seq\'  type=\'hidden\' value=\'\'>';
	echo '<input name=\'svc_seq\'  type=\'hidden\' value=\'\'>';
?>