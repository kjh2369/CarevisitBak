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

	$sql = "select r200_wrt_date"
		 . "  from r200fsttalk"
		 . " where r200_ccode = '".$code
		 . "'  and r200_mkind = '".$kind
		 . "'  and r200_jumin = '".$jumin
		 . "'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
			if ($row_count > 0){
				for($i=0; $i<$row_count; $i++){
					$fsttalk = $conn->select_row($i);
			
					//$dateYMD = date('Ymd', mkTime());
					//$date1 = $myF->dateAdd('month', 2, $test['r270_date'], 'y-m-d');
					//$date2 = $myF->dateDiff('d', $date1, $dateYMD);
				
					echo "<tr>";
			
					if ($date != subStr($fsttalk['r200_wrt_date'], 4, 4)){
						$date  = subStr($fsttalk['r200_wrt_date'], 4, 4);
						echo "<td style='text-align:center; font size:9pt;'>".subStr($fsttalk["r200_wrt_date"], 0, 4).".".subStr($fsttalk["r200_wrt_date"], 4, 2).".".subStr($fsttalk["r200_wrt_date"], 6, 2)."</td>";
					}else{
						echo '<td style="border-top:1px solid #ffffff; font size:8pt;"></td>';
					}


						echo '<td style="text-align:right;">';
					
						echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','".$fsttalk['r200_wrt_date']."','".$ed->en($jumin)."','','report','input','31','php','2','1'));\">수정</button></span>";
						echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"window.open('../report/report_show_31.php?mCode=$code&mKind=$kind&mDate=".$fsttalk['r200_wrt_date']."&mSuKey=".$ed->en($jumin)."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></span>";
						echo '</td>';
						echo '</tr>';
				}
			}else{
				echo '<tr><td style="text-align:center;" colspan="8">::검색된 데이타가 없습니다.::</td></tr>';
			}
			
				echo "<tr><td colspan='2' style='text-align:right'><span class='btn_pack m icon'><span class='add'></span><button type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','','".$ed->en($jumin)."','','report','input','31','php','2','1'));\">입력</button></span>";
				echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"idPopup.style.display='none';\">닫기</button></span></td></tr>";
			

		include("../inc/_db_close.php");
	?>
	</table>