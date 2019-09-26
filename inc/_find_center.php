<?
/*
	기관코드검색창
	기관리스트를 조회해서 선택하면 선택한 기관의 기관코드를 입력창에 받기
*/
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$findCname = $_POST['findCname'];
	$findname  = $_POST['findname'];
	$findCtel  = str_replace('-', '', $_POST['findCtel']);

?>
<script language="javascript">
<!--

function changeCode(code, kind){
	var currentItem = new Array();

	currentItem[0] = code;
	currentItem[1] = kind;

	window.returnValue = currentItem;
	window.close();
}

function _submit(){
	document.f.action = '_find_center.php?join=YES';
	document.f.submit();
}

window.onload = function(){
	var h    = document.body.offsetHeight;
	var body = document.getElementById('body_div');

	h = h - __getObjectTop(body) - 1;

	body.style.height = h;

	__init_form(document.f);
}

-->
</script>
<form name="f" method="post">

<div class="title title_border">기관조회</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="200px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관명</th>
			<td><input name="findCname" type="text" value="<?=$findCname;?>" style="width:100%;"></td>
			<th>대표자명</th>
			<td><input name="findname" type="text" value="<?=$findname;?>" style="width:100%;"></td>
			<th>전화번호</th>
			<td><input name="findCtel" type="text" value="<?=$findCtel;?>" style="width:100%;"></td>
			<td class="left" style="padding-top:1px;"><span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick="_submit();">조회</button></span></td>
		</tr>
	</tbody>
</table>
<?
	$colgroup = '<col width="40px">
				 <col width="100px">
				 <col width="70px">
				 <col width="150px">
				 <col width="100px">
				 <col width="100px">
				 <col>';
?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head">기관분류</th>
			<th class="head">기관명</th>
			<th class="head">대표자명</th>
			<th class="head">전화번호</th>
			<th class="head">주소</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top center" colspan="7">
				<div id="body_div" style="overflow-x:hidden; overflow-y:scroll; margin:0; padding:0; width:100%; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody>
						<?
							if (empty($findCname) && empty($findname) && empty($findCtel)){
								echo '<tr>
										<td class=\'center\' colspan=\'7\'>::조회할 검색어를 입력하여 주십시오.::</td>
									  </tr>';
							}else{
								if($findCname != '') $wsl = 'and m00_store_nm like "%'.$findCname.'%"';
								if($findname != '') $wsl = 'and m00_mname like "%'.$findname.'%"';
								if($findCtel != '') $wsl = 'and m00_ctel  like "%'.$findCtel.'%"';

								$sql = "select m00_mcode as code
										,      m00_code1 as cd
										,      m00_mkind as kind
										,      m00_cname as name
										,      m00_mname as manager
										,      m00_ctel as tel
										,      m00_caddr1 as addr
										  from m00center 
										 where m00_mcode is not null $wsl
										 group by m00_mcode
										 order by m00_cname";

								$conn->query($sql);
								$conn->fetch();

								
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i);

									echo '<tr onclick=\'changeCode("'.$row['code'].'","'.$row['kind'].'");\' onmouseover=\'this.style.backgroundColor="#efefef";\' onmouseout=\'this.style.backgroundColor="#ffffff";\' style=\'cursor:pointer;\'>';
									echo '<td class=\'center\'>'.($i+1).'</td>';
									echo '<td class=\'center\'>'.$row['cd'].'</td>';
									echo '<td class=\'center\'>'.$conn->kind_name_svc($row['kind']).'</td>';
									echo '<td class=\'center\'>'.$row['name'].'</td>';
									echo '<td class=\'center\'>'.$row['manager'].'</td>';
									echo '<td class=\'center\'>'.$myF->phoneStyle($row['tel'],'.').'</td>';
									echo '<td class=\'center\'>'.$row['addr'].'</td>';
									echo '</tr>';
								}

								$conn->row_free();

								
							}
						?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>

</form>
<?
	include_once("../inc/_footer.php");
?>