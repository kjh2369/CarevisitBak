<?
	include("../inc/_header.php");
	include("../inc/_ed.php");
?>
<style>
body{
	margin:0px;
	padding:0px;
}
</style>
<script src="../js/work.js" type="text/javascript"></script>
<?
	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$sugupja = $ed->decode(urlDecode($_GET['sugupja']));
	$sugupDate = $_GET['sugupDate'];
	$sugupFmTime = $_GET['sugupFmTime'];
	$sugupSeq = $_GET['sugupSeq'];
	$yoyangsa = $ed->decode(urlDecode($_GET['yoyangsa']));
?>
<table class="view_type1" style="width:600px; height:500px; margin:0px;">
<tr>
<th style="height:24px; width:50px;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;">No.</th>
<th style="height:24px; width:60px;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;">시각</th>
<th style="height:24px; width:400px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">위치정보</th>
<th style="height:24px; width:90px;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;">구분</th>
</tr>
<tr>
<td style="height:476px; padding:0px; border-top:none; border-bottom:none; vertical-align:top;" colspan="4">
	<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100%;">
		<table style="width:100%; margin:0px;">
		<?
			$sql = "select concat(substring(m61_gps_time, 1, 2), ':', substring(m61_gps_time, 3, 2)) as m61_gps_time"
				 . ",      case when ifnull(m61_gps_address, '') != '' then m61_gps_address else concat(m61_gps_x, '/', m61_gps_y) end as m61_gps_address"
				 . ",      case m61_gps_status when 'S' then '실행'"
				 . "                           when 'R' then '재실행'"
				 . "                           when 'X' then '취소'"
				 . "                           when 'E' then '종료'
											   when 'C' then '<font color=''#ff000''>에러</font>'"
				 . "                           else '진행' end as m61_gps_status"
				 . "  from m61gps"
				 . " where m61_mcode       = '".$mCode
				 . "'  and m61_mkind       = '".$mKind
				 . "'  and m61_sugupja     = '".$sugupja
				 . "'  and m61_sugupdate   = '".$sugupDate
				 . "'  and m61_sugupfmtime = '".$sugupFmTime
				 . "'  and m61_sugupseq    = '".$sugupSeq
				 . "'  and m61_yoyangsa    = '".$yoyangsa
				 . "'"
				 . " order by m61_gps_date"
				 . ",         m61_gps_time"
				 . ",         case m61_gps_status when 'S' then 2"
				 . "                              when 'R' then 3"
				 . "                              when 'E' then 9"
				 . "                              when 'X' then 1 else 8 end";
			$conn->query($sql);
			$conn->fetch();
			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				echo '<tr>';
				echo '<td style="height:24px; width:50px;  padding:0px; text-align:center;">'.($i+1).'</td>';
				echo '<td style="height:24px; width:60px;  padding:0px; text-align:center;">'.$row['m61_gps_time'].'</td>';
				echo '<td style="height:24px; width:400px; padding:0px; text-align:left;">'.$row['m61_gps_address'].'</td>';
				echo '<td style="height:24px; padding:0px; text-align:left;">'.$row['m61_gps_status'].'</td>';
				echo '</tr>';
			}

			$conn->row_free();
		?>
		</table>
	</div>
</td>
</tr>
</table>
<?
	include("../inc/_footer.php");
?>
<script>self.focus();</script>