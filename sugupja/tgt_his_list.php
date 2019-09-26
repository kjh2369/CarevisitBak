<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30%" span="3">
	</colgroup>
	<thead>
		<tr>
			<th class="bold">-계약이력</th>
			<th class="bold">-등급이력</th>
			<th class="bold">-구분이력</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top"><?
				$sql = 'SELECT	from_dt
						,		to_dt
						,		svc_stat
						,		svc_reason
						FROM	client_his_svc
						WHERE	org_no	= \''.$orgNo.'\'
						AND		svc_cd	= \'0\'
						AND		jumin	= \''.$jumin.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<div class="left" style="border-top:<?=$i > 0 ? '1px solid #CCCCCC;' : '';?>;"><?=$myF->dateStyle($row['from_dt'],'.');?>~<?=$myF->dateStyle($row['to_dt'],'.');?>[<?=$row['svc_stat'] == '1' ? '이용' : '중지';?>]</div><?
				}

				$conn->row_free();?>
			</td>
			<td class="top"><?
				$sql = 'SELECT	from_dt
						,		to_dt
						,		level
						,		app_no
						FROM	client_his_lvl
						WHERE	org_no	= \''.$orgNo.'\'
						AND		svc_cd	= \'0\'
						AND		jumin	= \''.$jumin.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);
					$row['level'] = ($row['level'] ? $row['level'].'등급' : '등급없음');?>
					<div class="left" style="border-top:<?=$i > 0 ? '1px solid #CCCCCC;' : '';?>;"><?=$myF->dateStyle($row['from_dt'],'.');?>~<?=$myF->dateStyle($row['to_dt'],'.');?>[<?=$row['level'];?>]</div><?
				}

				$conn->row_free();?>
			</td>
			<td class="top"><?
				$sql = 'SELECT	from_dt
						,		to_dt
						,		kind
						,		rate
						FROM	client_his_kind
						WHERE	org_no	= \''.$orgNo.'\'
						AND		jumin	= \''.$jumin.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);

					switch($row['kind']){
						case '3':
							$row['kind'] = '기초';
							break;

						case '2':
							$row['kind'] = '의료';
							break;

						case '4':
							$row['kind'] = '경감';
							break;

						case '1':
							$row['kind'] = '일반';
							break;

						default:
							$row['kind'] = '구분없음';
					}?>
					<div class="left" style="border-top:<?=$i > 0 ? '1px solid #CCCCCC;' : '';?>;"><?=$myF->dateStyle($row['from_dt'],'.');?>~<?=$myF->dateStyle($row['to_dt'],'.');?>[<?=$row['kind'];?>]</div><?
				}

				$conn->row_free();?>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>