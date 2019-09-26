<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$domain = $myF->_get_domain();
?>
<table class="my_table" style="width:100%;">
<colgroup>
	<col width="7%">
	<col width="13%">
	<col width="7%">
	<col width="11%">
	<col width="20%">
	<col width="10%">
	<col width="6%">
	<col>
</colgroup>
<thead>
	<tr>
		<th class="head">지사코드</th>
		<th class="head">지사명</th>
		<th class="head">대표자</th>
		<th class="head">전화번호</th>
		<th class="head">주소</th>
		<th class="head">가입일</th>
		<th class="head">상태</th>
		<th class="head last">비고</th>
	</tr>
</thead>
<tbody>
<?
	$sql = "select *
			  from b00branch
			 where b00_com_yn = 'N'
			   and b00_domain = '$domain'
			 order by b00_code";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			switch($row['b00_stat']){
			case '1':
				$stat = '서비스';
				break;
			case '2':
				$stat = '일시정지';
				break;
			case '3':
				$stat = '보류';
				break;
			case '4':
				$stat = '해지';
				break;
			case '9':
				$stat = '기타';
				break;
			}
			?>
				<tr>
					<td class="center"><?=$row['b00_code'];?></td>
					<td class="left"><a href="#" onClick="_branchReg('<?=$row['b00_code'];?>','list');"><?=$row['b00_name'];?></a></td>
					<td class="left"><?=$row['b00_manager'];?></td>
					<td class="left"><?=$myF->phoneStyle($row['b00_phone']);?></td>
					<td class="left"><?=$row['b00_addr1'];?></td>
					<td class="center"><?=$myF->dateStyle($row['b00_join_date']);?></td>
					<td class="center"><?=$stat;?></td>
					<td class="left last"><?=$row['b00_other'];?></td>
				</tr>
			<?
		}
	}else{
		echo '<tr>
				<td class=\'center last bottom\' colspan=\'8\'>'.$myF->message('nodata', 'N').'</td>
			  </tr>';
	}
	$conn->row_free();
?>
</tbody>
</table>
<?
	include_once("../inc/_db_close.php");
?>