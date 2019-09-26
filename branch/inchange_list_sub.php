<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$mode   = $_POST['mode'];
	$domain = $myF->_get_domain();

	switch($mode){
		case _COM_:
			if ($domain == _DWCARE_)
				$mark_val = 'ON';
			else if ($domain == _KLCF_)
				$mark_val = 'KL';
			else if ($domain == _KDOLBOM_)
				$mark_val = 'KD';
			else
				$mark_val = 'GE';
			break;
		case _BRAN_:
			if ($_SESSION['userLevel'] == 'A'){
				$mark_val = 'G';
			}else{
				$mark_val = $_SESSION['userBranchCode']; //지사코드
			}
			break;
		case _STORE_:
			$mark_val = 'S';
			break;
	}
?>
<table class="my_table" style="width:100%;">
<colgroup>
	<col width="15%">
	<col width="8%">
	<col width="7%">
	<col width="13%">
	<col width="7%">
	<col width="10%">
	<col width="6%">
	<col>
</colgroup>
<thead>
	<tr>
		<th class="head">지사명</th>
		<th class="head">아이디</th>
		<th class="head">담당자명</th>
		<th class="head">연락처</th>
		<th class="head">직위</th>
		<th class="head">입사일</th>
		<th class="head">상태</th>
		<th class="head last">비고</th>
	</tr>
</thead>
<tbody>
<?
	$sql = "select b00_name as branch
			,      b01_branch as branchCode
			,      b01_code as personCode
			,      b01_id as user_id
			,      b01_name as name
			,      b01_phone as phone
			,      m81_name as position
			,      b01_join_date as joinDate
			,      case b01_stat when '1' then '활동'
								 when '9' then '퇴사' else '' end as stat
			,      b01_other as other
			  from b01person
			 inner join b00branch
			    on b00_code = b01_branch
			  left join m81gubun
			    on m81_gbn = 'POS'
			   and m81_code = b01_position
			 where b01_branch like '$mark_val%'
			   and b01_domain_id = '$gDomainID'
			 order by b01_branch, b01_code";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			?>
				<tr>
					<td class="left"><?=$row['branch'];?></td>
					<td class="left"><?=$row['user_id'];?></td>
					<td class="left"><a href="#" onClick="_inchageReg('<?=$row['branchCode'];?>', '<?=$row['personCode'];?>', 'list', '<?=$mode;?>');"><?=$row['name'];?></a></td>
					<td class="left"><?=$myF->phoneStyle($row['phone']);?></td>
					<td class="left"><?=$row['position'];?></td>
					<td class="center"><?=$myF->dateStyle($row['joinDate']);?></td>
					<td class="left"><?=$row['stat'];?></td>
					<td class="left last"><?=$row['other'];?></td>
				</tr>
			<?
		}
	}else{
	?>
		<tr>
			<td class="center" colspan="8">::검색된 데이타가 없습니다.::</td>
		</tr>
	<?
	}
	$conn->row_free();
?>
</tbody>
</table>
<?
	include_once("../inc/_db_close.php");
?>