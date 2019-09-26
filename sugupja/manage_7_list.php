<?
	include("../inc/_db_open.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$code = $_POST['code'];
	$kind = $_POST['kind'];
	$jumin = $ed->de($_POST['jumin']);
	
	
?>
	<table class="view_type1" style="width:100%; background-color:#ffffff; border:2px solid #ccc;">
	
	<?
	$sql = "select r360_date"
		 . ",      r360_seq"
		 . "  from r360quest"
		 . " where r360_ccode = '".$code
		 . "'  and r360_mkind = '".$kind
		 . "'  and r360_sugupja = '".$jumin
		 . "'  and r360_service_gbn = '800'"
		 . " order by r360_date";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$quest = $conn->select_row($i);
		
			if ($date != subStr($quest['r360_date'], 4, 4)){
						$date  = subStr($quest['r360_date'], 4, 4);
						echo "<td style='text-align:center; font size:9pt;'>".subStr($quest["r360_date"], 0, 4).".".subStr($quest["r360_date"], 4, 2).".".subStr($quest["r360_date"], 6, 2)."</td>";
					}else{
						echo '<td style="border-top:1px solid #ffffff; font size:8pt;"></td>';
					}
						echo '<td style="text-align:right;">';
			
						echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','".$quest['r360_date']."','".$ed->en($jumin)."','".$quest['r360_seq']."','report','input','75','php','2','5'));\">수정</button></span>";
						echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"window.open('../report/report_show_75.php?mCode=$code&mKind=$kind&mDate=".$quest['r360_date']."&mSuKey=".$ed->en($jumin)."&mSeq=".$quest['r360_seq']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></span>";
						echo '</td>';
					echo '</tr>';
			
				}
			}else{
				echo '<tr><td style="text-align:center;" colspan="2">::검색된 데이타가 없습니다.::</td></tr>';
			}	
				echo "<tr><td colspan='2' style='text-align:right'><span class='btn_pack m icon'><span class='add'></span><button style='width:54px; type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','".$quest['r360_date']."','".$ed->en($jumin)."','','report','input','75','php','2','5'));\">입력</button></span>";	
				echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"idPopup.style.display='none';\">닫기</button></span></td></tr>";

			
	include("../inc/_db_close.php");
	
	?>
	</table>