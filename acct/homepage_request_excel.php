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
	include_once("../inc/_myFun.php");
	
	$orderBy = $_POST['txtOrder'];
	$dipoYn  = $_POST['cboDipoYn'];
	$orgNo   = $_POST['txtOrgNo'];
	$orgNm   = $_POST['txtOrgNm'];
	
	
	$sql = 'SELECT sum(pay)
			  FROM homepage_request
			 WHERE dipo_yn = \'Y\'';


	$DIPO_TOT = $conn -> get_data($sql); 
?>

<table border="1">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="180px">
		<col width="90px">
		<col width="90px">
		<col >
		<col width="70px">
		<col width="120px">
	</colgroup>
	<thead>
		<tr>
			<th >No</th>
			<th >신청일시</th>
			<th >기관명</th>
			<th >대표자</th>
			<th >연락처</th>
			<th >주소</th>
			<th >입금액</th>
			<th >입금여부</th>
		</tr>
	</thead>
	<tbody><?
		
		$sql = 'SELECT	mst.addr
			,		mst.org_nm
			,	    mst.org_tel
			,		mst.org_mname
			,		tb.org_no
			,		tb.seq
			,		tb.pay
			,		tb.dipo_yn
			,		tb.insert_dt
			FROM	homepage_request AS tb
			INNER	JOIN (		
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm AS org_nm
						,		m00_ctel AS org_tel
						,		m00_mname AS org_mname
						,		concat(m00_caddr1,\' \',m00_caddr2) as addr
						FROM	m00center
					) AS mst
					ON		mst.org_no = tb.org_no
			WHERE seq = (select max(seq) from homepage_request as tmp where tmp.org_no = tb.org_no)
			AND del_flag = \'N\'';
	
		//기관코드조회
		if($orgNo != ''){
			$sql .= ' AND org_no like \'%'.$orgNo.'%\'';
		}	
		
		//기관명조회
		if($orgNm != ''){
			$sql .= ' AND org_nm like \'%'.$orgNm.'%\'';
		}
		
		
		//입금유무
		if($dipoYn == 'Y'){
			$sql .= ' AND dipo_yn = \'Y\'';
		}else if($dipoYn == 'N'){
			$sql .= ' AND dipo_yn = \'N\'';
		}
		

		//정렬
		if ($orderBy == '1'){
			$sql .= ' ORDER	BY tb.insert_dt DESC';
		}else if ($orderBy == '2'){
			$sql .= ' ORDER	BY mst.org_nm';
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
		
			?>
			<tr style="background-color:#<?=$bgcolor;?>;">
				<td ><?=$i+1;?></td>
				<td ><?=str_replace('-','.',$row['insert_dt']);?></td>
				<td ><?=$row['org_nm'];?></td>
				<td ><?=$myF->phoneStyle($row['org_tel'],'.');?></td>
				<td ><?=$row['org_mname'];?></td>
				<td ><?=$row['addr'];?></td>
				<td ><?=number_format($row['pay']);?></td>
				<td ><?=$dipo_yn;?></td>
			</tr><?

			if($row['dipo_yn'] == 'Y'){
				$DIPO_CNT += $row['rank'];
			}
			


		} ?>
	
	<tr style="background-color:#<?=$bgcolor;?>;">
		<td  colspan="4" style="font-size:10pt; font-weight:bold;">총 합계</td>
		<td style="font-size:10pt; font-weight:bold; text-align:right;" colspan="4">입금건수 : <?=number_format($DIPO_CNT);?>대 입금액 : <?=number_format($DIPO_TOT);?>원</td>
	</tr>
	<?
		$conn->row_free();
	?>
	</tbody>
</table>