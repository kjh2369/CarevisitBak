<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');
?>
<script type="text/javascript">

</script>
<div class="title title_border">기관리스트</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<col width="130px">
		<col width="60px">
		<col width="130px">
		<col width="80px">
		<col width="50px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">지역</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head">재가</th>
			<th class="head">바우처</th>
			<th class="head">지원</th>
			<th class="head last">기타</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT	b02_center AS code
				,		CASE WHEN area.area_nm != \'\' THEN area.area_nm ELSE \'기타\' END AS area
				,		m00_store_nm AS name
				,		m00_mname AS manager
				,		m00_ctel AS phone
				,		CONCAT(m00_caddr1,\' \',m00_caddr2) AS addr
				,		m00_cdate AS date
				,		m00_email AS email
				,		(SELECT	COUNT(*)
						FROM	client_his_svc
						WHERE	org_no	= b02_center
						AND		svc_cd	= \'0\'
						AND		from_dt	<= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
						AND		to_dt	>= DATE_FORMAT(NOW(),\'%Y-%m-%d\')) AS cnt1
				,		(SELECT	COUNT(*)
						FROM	client_his_svc
						WHERE	org_no	= b02_center
						AND		svc_cd	>= \'1\'
						AND		svc_cd	<= \'4\'
						AND		from_dt	<= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
						AND		to_dt	>= DATE_FORMAT(NOW(),\'%Y-%m-%d\')) AS cnt2
				,		(SELECT	COUNT(*)
						FROM	client_his_svc
						WHERE	org_no	= b02_center
						AND		svc_cd	= \'6\'
						AND		from_dt	<= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
						AND		to_dt	>= DATE_FORMAT(NOW(),\'%Y-%m-%d\')) AS cnt3
				FROM	b02center AS b02
				INNER	JOIN m00center
						ON m00_mcode = b02_center
						AND m00_mkind = b02_kind
				LEFT	JOIN care_area AS area
						ON area.area_cd = b02.care_area
				WHERE	b02_caresvc = \'Y\'
				ORDER	BY CASE WHEN IFNULL(b02.care_area,\'\') = \'\' THEN \'99\' ELSE b02.care_area END, name';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="center"><?=$row['area'];?></td>
				<td class="center"><div class="left nowrap" style="width:130px;" title="<?=$row['name'];?>"><?=$row['name'];?></div></td>
				<td class="center"><?=$row['manager'];?></td>
				<td class="center"><div class="left nowrap" style="width:130px;" title="<?=$row['addr'];?>"><?=$row['addr'];?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['phone'],'.');?></td>
				<td class="center"><?=$row['cnt1'];?></td>
				<td class="center"><?=$row['cnt2'];?></td>
				<td class="center"><?=$row['cnt3'];?></td>
				<td class="center last"></td>
			</tr><?
			$no ++;
		}

		$conn->row_free();?>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>