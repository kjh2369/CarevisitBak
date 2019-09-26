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
	ob_start();

	$sql = "select r260_date"
		 . ",      r260_seq"
		 . "  from r260talk"
		 . " where r260_ccode = '".$code
		 . "'  and r260_mkind = '".$kind
		 . "'  and r260_yoyangsa = '".$jumin
		 . "'"
		 . " order by r260_date";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$talk = $conn->select_row($i);

			echo "<tr>";

			if ($date != subStr($talk['r260_date'], 4, 4)){
				$date  = subStr($talk['r260_date'], 4, 4);
				echo "<td style='text-align:center; font size:9pt;'>".subStr($talk["r260_date"], 0, 4).".".subStr($talk["r260_date"], 4, 2).".".subStr($talk["r260_date"], 6, 2)."</td>";
			}else{
				echo '<td style="border-top:1px solid #ffffff; font size:8pt;"></td>';
			}
			echo '<td style="text-align:right;">';

			echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','".$talk['r260_date']."','".$ed->en($jumin)."','".$talk['r260_seq']."','report','input','47','php','1','2')); idTalkPopup.style.display='none';\">수정</button></span> ";
			echo "<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"window.open('../report/report_show_47.php?mCode=$code&mKind=$kind&mDate=".$talk['r260_date']."&mYoyKey=".$ed->en($jumin)."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no'); idTalkPopup.style.display='none';\">출력</button></span>";
			echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr><td style="text-align:center;" colspan="2">::검색된 데이타가 없습니다.::</td></tr>';
	}
	echo "
		<tr>
			<td colspan='2' style='text-align:right'>
				<span class='btn_pack m icon'><span class='add'></span><button style='width:54px; type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$kind."','','".$ed->en($jumin)."','','report','input','47','php','1','2')); idTalkPopup.style.display='none';\">입력</button></span>
				<span class='btn_pack m'><button style='width:54px;' type='button' onFocus='this.blur();' onClick=\"idTalkPopup.style.display='none';\">닫기</button></span>
			</td>
		</tr>
		 ";

	$value = ob_get_contents();
	ob_end_clean();
	echo $value;
?>
</table>
<?
	include("../inc/_db_close.php");
?>