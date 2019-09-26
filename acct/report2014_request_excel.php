<?
	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	include_once("../inc/_db_open.php");

?>

<table border="1">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="180px">
		<col >
		<col width="70px">
		<col width="120px">
		<col width="120px">
	</colgroup>
	<thead>
		<tr>
			<th >No</th>
			<th >신청일시</th>
			<th >기관명</th>
			<th >주소</th>
			<th >입금액</th>
			<th >입금여부</th>
			<th >사용여부</th>
		</tr>
	</thead>
	<tbody><?
		$orderBy = $_POST['txtOrder'];
	
		$sql = 'SELECT	mst.addr
				,		mst.org_nm
				,		mst.org_no
				,		rp.seq
				,		rp.dipo_pay
				,		rp.dipo_yn
				,		rp.use_yn
				,		rp.insert_dt
				FROM	report2014_request AS rp
				INNER	JOIN (		
							SELECT	DISTINCT
									m00_mcode AS org_no
							,	    m00_store_nm AS org_nm
							,		m00_caddr1 as addr
							FROM	m00center
						) AS mst
						ON		mst.org_no = rp.org_no
				WHERE seq = (select max(seq) from report2014_request as tmp where tmp.org_no = rp.org_no)';
		
		if ($orderBy == '1'){
			$sql .= ' ORDER	BY rp.insert_dt DESC';
		}else if ($orderBy == '2'){
			$sql .= ' ORDER	BY mst.org_nm';
		}else {
			$sql .= ' ORDER	BY case rp.use_yn when \'N\' then 1 else 2 end, rp.insert_dt DESC';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($i % 2 == 0){
				$bgcolor = 'FFFFFF';
			}else{
				$bgcolor = 'EFEFEF';
			}
			
			$dipo_yn = $row['dipo_yn'] == 'Y' ? '입금' : '미입금';
			$use_yn  = $row['use_yn'] == 'Y' ? '사용' : '미사용';

			?>
			<tr style="background-color:#<?=$bgcolor;?>;">
				<td ><?=$i+1;?></td>
				<td ><?=str_replace('-','.',$row['insert_dt']);?></td>
				<td ><?=$row['org_nm'];?></td>
				<td ><?=$row['addr'];?></td>
				<td ><?=number_format($row['dipo_pay']);?></td>
				<td ><?=$dipo_yn;?></td>
				<td ><?=$use_yn;?></td>
			</tr><?

			$tot_pay += $row['dipo_pay'];

		} ?>
	
	<tr style="background-color:#<?=$bgcolor;?>;">
		<td  colspan="4" style="font-size:10pt; font-weight:bold;">총 합계</td>
		<td class="right" style="font-size:10pt; font-weight:bold;"><?=number_format($tot_pay);?></span></td>
		<td class="center ">&nbsp;</td>
		<td class="center last">&nbsp;</td>
	</tr>
	<?
		$conn->row_free();
	?>
	</tbody>
</table>