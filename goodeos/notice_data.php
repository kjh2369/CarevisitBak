<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");

	$orderBy = $_POST['orderBy'];
	$descBy  = $_POST['descBy'];
?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="70px">
			<col width="270px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
		<?
			$sql = 'select pop.id as id
					,      tbl.subject as subject
					,	   tbl.content as content
					,      tbl.reg_dt as reg_dt
					,      case when pop.read_yn = \'Y\' then \'Y\' else \'\' end as read_yn
					  from (
						   select notice_id as id
						   ,      read_yn as read_yn
							 from popup_notice
							where org_no   = \''.$_SESSION['userCenterCode'].'\'
							  and from_dt <= \''.date('Y-m-d').'\'
							  and to_dt   >= \''.date('Y-m-d').'\'
						   ) as pop
					 inner join tbl_goodeos_notice as tbl
						on tbl.id = pop.id
					 order by ';

			switch($orderBy){
				case 2:
					$sql .= ' case when pop.read_yn = \'Y\' then 1 else 2 end, ';
					break;

				case 3:
					$sql .= ' case when pop.read_yn != \'Y\' then 1 else 2 end, ';
					break;
			}

			$sql .= ' reg_dt';

			if ($descBy == 1){
				$sql .= ' desc';
			}

			//if ($debug) echo '<tr><td>'.nl2br($sql).'</td></tr>';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if (date('Y.m.d') == date('Y.m.d',$row['reg_dt'])){
					$icon = '<div style=\'float:left; width:auto; margin-left:3px; margin-top:6px;\'><img src=\'../image/icon_new.gif\'></div>';
				}else{
					$icon = '';
				}?>
				<tr>
					<td class="center"><?=$i+1;?></td>
					<td class="center"><?=date('Y.m.d',$row['reg_dt']);?></td>
					<td class="center">
						<?=$icon;?><div style="float:left; width:auto; margin-left:3px;"><a href="#" onclick="showNotice('<?=$row['id'];?>');"><?=$row['subject'];?></a></div>
					</td>
					<td class="center"><div id="readYn<?=$row['id'];?>"><?=$row['read_yn'];?></div></td>
					<td class="center">&nbsp;</td>
				</tr><?
			}

			$conn->row_free();
		?>
		</tbody>
	</table>
<?
	include_once("../inc/_db_close.php");
?>