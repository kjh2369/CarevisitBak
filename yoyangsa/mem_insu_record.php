<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];

	if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

	//보험사 리시트
	$sql = 'SELECT g01_code AS cd
			,      g01_name AS nm
			  FROM g01ins';
	$insuList = $conn->_fetch_array($sql,'cd');

	//기관 보험 가입이력
	$sql = 'SELECT insu_cd AS cd
			,      from_dt AS f_dt
			,      to_dt AS t_dt
			  FROM insu_center
			 WHERE org_no = \''.$code.'\'';
	$insuCenter = $conn->_fetch_array($sql,'f_dt');
?>
<div class="title title_border">배상책임보험 가입이력</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="200px">
		<col width="70px" span="5">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">보험사명</th>
			<th class="head">입사일</th>
			<th class="head">퇴사일</th>
			<th class="head">가입일</th>
			<th class="head">해지일</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT seq
				,      join_dt
				,      quit_dt
				,      start_dt
				,      end_dt
				,      CASE stat WHEN \'1\' THEN \'가입신청\'
				                 WHEN \'3\' THEN \'가입\'
								 WHEN \'7\' THEN \'해지신청\'
								 WHEN \'9\' THEN \'해지\' ELSE \'-\' END AS stat
				  FROM insu
				 WHERE org_no = \''.$code.'\'
				   AND jumin  = \''.$jumin.'\'
				 ORDER BY seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			UnSet($insuDt);

			foreach($insuCenter as $r){
				if ($r['f_dt'] <= $row['start_dt'] && $r['t_dt'] > $row['start_dt']){
					$insuDt = $r['f_dt'];
					break;
				}
			}

			$insuNm = $insuList[$insuCenter[$insuDt]['cd']]['nm'];?>
			<tr>
				<td class="center"><?=$row['seq'];?></td>
				<td class="center"><div class="left nowrap" style="width:200px;"><?=$insuNm;?></div></td>
				<td class="center"><?=$myF->dateStyle($row['join_dt'],'.');?></td>
				<td class="center"><?=$myF->dateStyle($row['quit_dt'],'.');?></td>
				<td class="center"><?=$myF->dateStyle($row['start_dt'],'.');?></td>
				<td class="center"><?=$myF->dateStyle($row['end_dt'],'.');?></td>
				<td class="center"><?=$row['stat'];?></td>
				<td class="center last"></td>
			</tr><?
		}

		$conn->row_free();?>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>