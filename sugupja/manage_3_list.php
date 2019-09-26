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
	$sql = "select r250_seq"
		 . ",      r250_date"
		 . "  from r250risktoll"
		 . " where r250_ccode = '".$code
		 . "'  and r250_mkind = '".$kind
		 . "'  and r250_sugupja_jumin = '".$jumin
		 . "'"
		 . " order by r250_date";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

			if ($row_count > 0){
				for($i=0; $i<$row_count; $i++){
					$risktoll = $conn->select_row($i);
					
					//$dateYMD = date('Ymd', mkTime());
					//$date1 = $myF->dateAdd('month', 2, $talk['r260_date'], 'y-m-d');
					//$date2 = $myF->dateDiff('d', $date1, $dateYMD);
					//echo $date;
					echo "<tr>";
					
					if ($date != subStr($risktoll['r250_date'], 4, 4)){
						$date  = subStr($risktoll['r250_date'], 4, 4);
						echo "<td style='text-align:center; font size:9pt;'>".subStr($risktoll["r250_date"], 0, 4).".".subStr($risktoll["r250_date"], 4, 2).".".subStr($risktoll["r250_date"], 6, 2)."</td>";
					}else{
						echo '<td style="border-top:1px solid #ffffff; font size:8pt;"></td>';
					}
						echo '<td style="text-align:right;">';
					
						echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','".$risktoll['r250_date']."','','".$risktoll['r250_seq']."','report','input','81','php','2','1'));\">수정</button></span>";
						echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"window.open('../report/report_show_81.php?mCode=$code&mKind=$kind&mDate=".$risktoll['r250_date']."&mSuKey=".$ed->en($jumin)."&mSeq=".$risktoll['r250_seq']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></span>";
						echo '</td>';
						echo '</tr>';
				
				}
			}else{
				echo '<tr><td style="text-align:center;" colspan="2">::검색된 데이타가 없습니다.::</td></tr>';
			}			
				echo "<tr><td colspan='2' style='text-align:right'><span class='btn_pack m icon'><span class='add'></span><button style='width:54px; type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','".$risktoll['r250_date']."','".$ed->en($jumin)."','','report','input','81','php','2','1'));\">입력</button></span>";	
				echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"idPopup.style.display='none';\">닫기</button></span></td></tr>";

			
			
	
		include("../inc/_db_close.php");
	?>
	</table>