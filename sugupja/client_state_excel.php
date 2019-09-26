<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	
	$year	= $_GET['year'];
	$month	= $_GET['month'];
	$code	= $_SESSION['userCenterCode'];

	$isExcel= true;
	
	header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=io_".date('Ymd').".xls" );

	include_once('client_state_search.php');

?>
<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>


<table border="1">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="95px">
		<col width="75px">
		<col width="35px">
		<col width="35px">
		<col width="130px">
		<col width="100px">
		<col width="80px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th style="font-size:20px; text-align:center; font-weight:bold;" colspan="11"><?=$year?>년 <?=$month?>월 수급자현황(재가요양)</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">주민번호</th>
			<th class="head">인정번호</th>
			<th class="head">구분</th>
			<th class="head">성별</th>
			<th class="head">계약기간</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head">보호자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
<?
		
	if ($data){
		$row	= Explode(chr(11),$data);
		$rowCnt	= SizeOf($row)-1;
		
		for($i=0; $i<$rowCnt; $i++){
			Parse_Str($row[$i],$col);
			
			if ($col['kind'] == '3'){
				$col['kind'] = '기초';
			}else if ($col['kind'] == '2'){
				$col['kind'] = '의료';
			}else if ($col['kind'] == '4'){
				$col['kind'] = '경감';
			}else if ($col['kind'] == '1'){
				$col['kind'] = '일반';
			}else{
				$col['kind'] = '';
			}

			if ($col['gender'] == '1'){
				$col['gender'] = '남';
			}else{
				$col['gender'] = '여';
			}

			?>
			<tr>
				<td style="text-align:center;" ><?=$i+1;?></td>
				<td style="" ><?=$col['name'];?></td>
				<td style="" ><?=$col['jumin'];?></td>
				<td style="" ><?=$col['appNo'];?></td>
				<td style="" ><?=$col['kind'];?></td>
				<td style="" ><?=$col['gender'];?></td>
				<td style="" ><?=$col['from'];?></td>
				<td style="" ><?=$col['addr'];?></td>
				<td style="mso-number-format:\@;" ><?=$col['telno'];?></td>
				<td style="" ><?=$col['grdNm'];?></td>
				<td style="">&nbsp;</td>
			</tr>
			<?
		}
	}

	?>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>