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
	
	$orderBy = $_POST['optOrder'];
	$dipoYn  = $_POST['cboDipoYn'];
	$cnt	 = $_POST['cboCnt'];
	$orgNo   = $_POST['txtOrgNo'];
	$orgNm   = $_POST['txtOrgNm'];
	
	
	
	$sql = 'SELECT sum(deposit_pay)
			  FROM seminar_request
			 WHERE deposit_yn = \'Y\'
			 AND   left(insert_dt, 4) = \'2018\'
			 AND   del_flag = \'N\'';
	

	$DIPO_TOT = $conn -> get_data($sql);
	
?>

<table border="1">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="110px">
		<col width="120px">
		<col width="180px">
		<col width="90px">
		<col width="90px">
		<col >
		<col width="70px">
		<col width="70px">
		<col width="120px">
		<col width="120px">
	</colgroup>
	<thead>
		<tr>
			<th >No</th>
			<th >신청일시</th>
			<th >기관기호</th>
			<th >기관명</th>
			<th >참석자</th>
			<th >직책</th>
			<th >연락처</th>
			<th >주소</th>
			<th >신청수</th>
			<th >입금액</th>
			<th >입금여부</th>
			<th >이메일</th>
		</tr>
	</thead>
	<tbody><?
		
		$sql = 'SELECT	sr.seq
				,		sr.org_no
				,		sr.org_nm
				,		sr.name
				,	    sr.rank+sr.rank2 as all_rank
				,		sr.deposit_pay
				,	    sr.deposit_yn
				,	    sr.pos
				,		left(sr.insert_dt, 10) as dt
				,		mst.addr
				,	    mst.tel
				FROM	seminar_request AS sr
				INNER	JOIN (
							SELECT	DISTINCT
									m00_mcode AS org_no
							,		m00_caddr1 AS addr
							,		m00_ctel AS tel
							FROM	m00center
						) AS mst
						ON		mst.org_no = sr.org_no';
		
		$sql .= ' AND del_flag = \'N\'
				  AND gbn = \'2\'';

		//입금유무
		if($dipoYn != 'all') $sql .= ' AND deposit_yn = \''.$dipoYn.'\'';
		
		$sql .= ' AND left(insert_dt, 4) = \''.date('Y').'\''; 
		
		if ($orderBy == '1'){
			$sql .= ' ORDER	BY insert_dt DESC';
		}else if ($orderBy == '2'){
			$sql .= ' ORDER	BY org_nm';
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
			
			$dipo_yn = $row['deposit_yn'] == 'Y' ? '입금' : '미입금';
		
			?>
			<tr style="background-color:#<?=$bgcolor;?>;">
				<td ><?=$i+1;?></td>
				<td ><?=str_replace('-','.',$row['dt']);?></td>
				<td style="mso-number-format:\@;" ><?=$row['org_no'];?></td>
				<td ><?=$row['org_nm'];?></td>
				<td ><?=$row['name'];?></td>
				<td ><?=$row['pos'];?></td>
				<td ><?=$myF->phoneStyle($row['tel'],'.');?></td>
				<td ><?=$row['addr'];?></td>
				<td ><?=$row['all_rank'];?>명</td>
				<td ><?=number_format($row['deposit_pay']);?></td>
				<td ><?=number_format($row['in_pay']);?></td>
				<td ><?=$dipo_yn;?></td>
				<td ><?=$row['email'];?></td>
			</tr><?

			if($row['deposit_yn'] == 'Y'){
				$DIPO_CNT += $row['all_rank'];
			}
			

			//$tot_rank += $row['all_rank'];
			//$tot_pay += $row['deposit_pay'];

		} ?>
	
	<tr style="background-color:#<?=$bgcolor;?>;">
		<td  colspan="5" style="font-size:10pt; font-weight:bold;">총 합계</td>
		<td style="font-size:10pt; font-weight:bold; text-align:right;" colspan="6">입금명수 : <?=number_format($DIPO_CNT);?>명 입금액 : <?=number_format($DIPO_TOT);?>원</td>
	</tr>
	<?
		$conn->row_free();
	?>
	</tbody>
</table>