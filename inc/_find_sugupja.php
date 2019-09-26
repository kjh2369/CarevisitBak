<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$findName = $_POST['findName'];
	$code = $_GET['code'];
	$kind = $_GET['kind'];
	$type = $_GET['type'];

	switch($type){
	case 'sugupja':
		$title = '수급자';
		break;
	}
?>
<style>
body{
	margin-top:0px;
	margin-left:0px;
}
</style>
<script language='javascript'>
<!--
var retVal = 'cancel';

function _currnetRow(value1, value2, value3, value4){
	var currentItem = new Array();

	currentItem[0] = value1;
	currentItem[1] = value2;
	currentItem[2] = value3;
	currentItem[3] = value4;

	window.returnValue = currentItem;
	window.close();
}

function _submit(){
	document.f.action = '_find_sugupja.php?type=<?=$type;?>&code=<?=$code;?>&kind=<?=$kind;?>';
	document.f.submit();
}
//-->
</script>
<style>
.view_type1{
	margin:0;
	padding:0;
}

.view_type1 thead th{
	margin:0;
	padding:0;
	text-align:center;
}

.view_type1 tbody td{
	margin:0;
	padding:0;
}

view_type2{
	margin:0;
	padding:0;
}
</style>
<form name="f" method="post">
<table class="view_type1" style="height:100%;">
<colgroup>
	<col width="20%">
	<col>
	<col width="5%">
</colgroup>
<thead>
	<tr>
		<th style="height:25px;" colspan="3">::<?=$title;?> 조회::</th>
	</tr>
</thead>
<tbody>
	<tr>
		<th style="height:25px;"><?=$title;?>명</th>
		<td style="height:25px;"><input name="findName" type="text" value="<?=$findName;?>" style="width:100%;"></td>
		<td style="height:25px; text-align:center;"><span class="btn_pack find" onClick="_submit();"></span></td>
	</tr>
	<tr>
		<td style="height:350px;" colspan="3">
			<table class="view_type1" style="height:350px;">
			<colgroup>
				<col width="30px">
				<col width="66px">
				<col width="60px">
				<col width="40px">
				<col width="90px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th style="height:25px;">No</th>
					<th style="height:25px;">주민번호</th>
					<th style="height:25px;">성명</th>
					<th style="height:25px;">성별</th>
					<th style="height:25px;">연락처</th>
					<th style="height:25px;">주소</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="height:325px; vertical-align:top;" colspan="6">
						<div style="overflow-x:hidden; overflow-y:scroll; margin:0; padding:0; width:100%; height:315px;">
							<table class="view_type2" style="margin:0; margin-top:-1px; padding:0;">
							<colgroup>
								<col width="30px">
								<col width="66px">
								<col width="60px">
								<col width="40px">
								<col width="90px">
								<col>
							</colgroup>
							<tbody>
							<?
								switch($type){
								case 'sugupja':
									$sql = "select m03_jumin as jumin, m03_name as name, m81_name as level, m03_tel as tel, m03_juso1 as addr
											  from m03sugupja
											 inner join m81gubun
												on m81_gbn   = 'LVL'
											   and m81_code  =  m03_ylvl
											 where m03_ccode = '$code'
											   and m03_mkind = '$kind'
											   and m03_sugup_status = '1'";

									if ($findName != ''){
										$sql .= " and m03_name like '%$findName%'";
									}

									$sql .= "
											 order by m03_name";
								}

								$conn->query($sql);
								$conn->fetch();
								$rowCount = $conn->row_count();

								if ($rowCount > 0){
									for($i=0; $i<$rowCount; $i++){
										$row = $conn->select_row($i);

										$gender = getGender($row['jumin']);
										$birth  = str_replace('-', '.', getBirthDay($row['jumin']));
										?>
										<tr>
											<td style="height:25px; text-align:center;"	><?=$i+1;?></td>
											<td style="height:25px; text-align:center;"	><?=subStr($row['jumin'], 0, 6).'-'.subStr($row['jumin'],6,1);?></td>
											<td style="height:25px; text-align:left;"	><a href="#" onClick="_currnetRow('<?=$ed->en($row['jumin']);?>','<?=$row['name'];?>','<?=$gender;?>','<?=$birth;?>');"><?=$row['name'];?></a></td>
											<td style="height:25px; text-align:center;"	><?=$gender;?></td>
											<td style="height:25px; text-align:left;"	><?=$myF->phoneStyle($row['tel']);?></td>
											<td style="height:25px; text-align:left;"	><?=$row['addr'];?></td>
										</tr>
									<?
									}
								}else{
								?>
									<tr>
										<td style="height:25px; text-align:center;" colspan="6">::검색된 데이타가 없습니다..::</td>
									</tr>
								<?
								}

								$conn->row_free();
							?>
							</tbody>
							</table>
						</div>
					</td>
				</tr>
			</tbody>
			</table>
		</td>
	</tr>
</tbody>
</table>
</form>
<?
	if ($rowCount == 0){
	?>
		<script>
			document.getElementById('findName').value = '';
		</script>
	<?
	}

	include_once("../inc/_footer.php");
?>