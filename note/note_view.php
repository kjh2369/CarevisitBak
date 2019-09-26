<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");

	$mode = $_POST['mode'];
	$code = $_POST['code'];
	$yymm = $_POST['yymm'];
	$seq  = $_POST['seq'];
	$fcd  = $_POST['fcd'];
	$mem  = $_SESSION['userNo'];

	if ($mode == 'to')
		$view_cd = $fcd;
	else
		$view_cd = $code;

	$sql = 'select case msg_send.msg_send_type when \'all\'    then \'전체발송\'
											   when \'branch\' then \'지사별발송\'
											   when \'center\' then \'가맹점별발송\'
											   when \'dept\'   then \'부서별발송\' else \'개인별발송\' end as send_type
			,      msg_send_id as send_id
			,      msg_send_nm as send_nm
			,      msg_send_dt as send_dt
			,      msg_subject as subject
			,      msg_content as content
			  from msg_send
			 where org_no   = \''.$view_cd.'\'
			   and msg_yymm = \''.$yymm.'\'
			   and msg_seq  = \''.$seq.'\'';

	$send = $conn->get_array($sql);

	if ($mode == 'to'){
		$sql = 'update msg_receipt
				   set msg_open_flag = \'Y\'
				,      msg_open_dt   = now()
				 where from_no       = \''.$fcd.'\'
				   and org_no        = \''.$code.'\'
				   and msg_yymm      = \''.$yymm.'\'
				   and msg_seq       = \''.$seq.'\'
				   and msg_mem       = \''.$mem.'\'';

		$conn->begin();
		$conn->execute($sql);
		$conn->commit();
	}
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col>
		<col width="100px">
	</colgroup>
	<tbody>
		<tr>
			<th class="border_g">전송구분</th>
			<td class="left last"><?=$send['send_type'];?></td>
			<td class="right"><a href="#" onclick="msg_hidden(); return false;">닫기</a></td>
		</tr>
		<tr>
			<th class="border_g">보낸사람</th>
			<td class="left" colspan="2"><?=$send['send_nm'];?></td>
		</tr>
		<tr>
			<th class="border_g">보낸시간</th>
			<td class="left" colspan="2"><?=$send['send_dt'];?></td>
		</tr>
		<tr>
			<th class="border_g">받는사람</th>
			<td class="left" colspan="2">
			<?
				$sql = 'select msg_mem as mem_id
						,      msg_mem_nm as mem_nm
						,      msg_open_flag as open_yn
						  from msg_receipt
						 where from_no      = \''.$view_cd.'\'
						   and msg_yymm     = \''.$yymm.'\'
						   and msg_seq      = \''.$seq.'\'
						 order by mem_nm';

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				echo '<table style=\'width:100%;\'>';
				echo '<colgroup>
						<col width=\'20%\' span=\'5\'>
					  </colgroup>
					  <tbody>';

				$tr = false;

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					if ($i % 5 == 0){
						if ($tr) echo '</tr>';
						$tr = true;
						echo '<tr>';
					}

					echo '<td style=\'border:none;\'><img src=\'../image/msg'.($row['open_yn'] == 'Y' ? '2' : '1').'.gif\'> '.$row['mem_nm'].'['.$myF->formatString($row['mem_id'],'####-####').']</td>';
				}

				for($j=$i+1; $j<=5; $j++){
					echo '<td style=\'border:none;\'></td>';
				}

				if ($tr) echo '</tr>';

				echo '</tbody></table>';

				$conn->row_free();
			?>
			</td>
		</tr>
		<tr>
			<th class="border_g">제목</th>
			<td class="left" colspan="2"><?=stripslashes($send['subject']);?></td>
		</tr>
		<tr>
			<td colspan="3" style="padding:5px;"><?=stripslashes($send['content']);?></td>
		</tr>
	</tbody>
</table>
<?
	include_once("../inc/_footer.php");
?>