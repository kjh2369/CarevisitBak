<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($lbLimitSet){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="70px" span="2">
				<col width="65px" span="4">
				<col>
			</colgroup>
			<tbody><?
				$sql = 'SELECT seq
						,      from_dt
						,      to_dt
						,      amt_care
						,      amt_bath
						,      amt_nurse
						,      amt
						  FROM client_his_limit
						 WHERE org_no = \''.$code.'\'
						   AND jumin  = \''.$jumin.'\'
						 ORDER BY from_dt desc, to_dt DESC';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);?>
					<tr>
						<td class="center"><?=$myF->dateStyle($row['from_dt'],'.');?></td>
						<td class="center"><?=$myF->dateStyle($row['to_dt'],'.');?></td>
						<td class="center"><div class="right"><?=number_format($row['amt_care']);?></div></td>
						<td class="center"><div class="right"><?=number_format($row['amt_bath']);?></div></td>
						<td class="center"><div class="right"><?=number_format($row['amt_nurse']);?></div></td>
						<td class="center"><div class="right"><?=number_format($row['amt']);?></div></td>
						<td class="center">
							<div class="left"><?
								if ($i == 0){?>
									<span class="btn_pack m"><button type="button" onclick="doDel('<?=$i;?>');">삭제</button></span><?
								}?>
								<span id="seq_<?=$i;?>" style="display:none;"><?=$row['seq'];?></span>
							</div>
						</td>
					</tr><?
				}

				$conn->row_free();?>
			</tbody>
		</table><?
	}else{?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="71px">
				<col width="71px">
				<col width="81px">
				<col>
			</colgroup>
			<tbody><?

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      amt
				  from client_his_limit
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				 order by from_dt desc, to_dt desc';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><span id=""><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
				<td class="center"><span id=""><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
				<td class="center"><div id="" class="right"><?=number_format($row['amt']);?></div></td>
				<td class="center">
					<div class="left"><?
						if ($i == 0){?>
							<span class="btn_pack m"><button type="button" onclick="doDel('<?=$i;?>');">삭제</button></span><?
						}?>
						<span id="seq_<?=$i;?>" style="display:none;"><?=$row['seq'];?></span>
					</div>
				</td>
			</tr><?
		}

		$conn->row_free();?>

		</tbody>
		</table><?
	}

	include_once('../inc/_db_close.php');
?>