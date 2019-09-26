<?
/*
부서관리 만들기

1. 센터코드(org_no), 부서코드(dept_cd), 부서이름(dept_nm), 순번(order_seq) 만들기
2. 추가,삭제,저장 버튼만들기
3. 추가,삭제,저장 자바스크립트 만들기
4. 추가를 눌렀을때 행 추가 그리고 입력됬을 때 체크박스 표시
5. 부서코드는 자동으로 1-99까지 1씩증가
6. 그리고 저장을 하면 디비 데이터 저장 그리고 데이터는 라인에 값 그대로 표시
8. 그리고 체크박스에 체크하고 삭제버튼을 누르면 선택된 라인 삭제
9. 삭제하면 del_flag = 'Y'로 업데이트 리스트에는 N인 데이터만 출력
*/
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_db_open.php");

	$org_no = $_POST['org_no'] != '' ? $_POST['org_no'] : $_SESSION['userCenterCode'];

?>
<script language="javascript">
	window.onload = function(){
		__init_form(document.f);
	}

	function new_row(){
		var row_no = document.f.row_count.value;

		var order_seq = document.getElementsByName("order_seq[]");
		var dept_code = document.getElementsByName("dept_cd[]");
		var dept_name = document.getElementsByName("dept_nm[]");
		var seq_cnt = order_seq.length;
		var max_seq = 0;
		//var next_dep = 0;


			for(var i=0; i<seq_cnt; i++){
				if(parseInt(order_seq[i].value) > max_seq){
					max_seq = order_seq[i].value;

				}
			}

			//next_dep++;
			max_seq ++;

		if(row_no == ""){
			row_no = 0;
		}

		var row       = document.getElementById("row_1");
		var row_count = parseInt($('#row_1 tr').length);
		//var order_seq = document.getElementsByName("order_seq[]");
		var row_tr    = document.createElement("tr");
		var row_td_1  = document.createElement("td");
		//var row_td_2  = document.createElement("td");
		var row_td_3  = document.createElement("td");
		var row_td_4  = document.createElement("td");
		var row_td_5  = document.createElement("td");
		

		row_td_1.innerHTML  = '<input name="checkDept[]" type="checkbox" class="checkbox" value="'+row_count+'">';
		row_td_3.innerHTML  = '<input name="dept_nm[]" type="text" style="width:92%;" onchange="__checkRow('+row_count+',\'checkDept[]\');" value="" maxlength="30">';
		row_td_4.innerHTML  = '<input name="order_seq[]" type="text" class="number" onkeydown="__onlyNumber(this);" style="width:100%" value="'+max_seq+'" tag="'+max_seq+'" onchange="setSeq(this); __checkRow('+row_count+',\'checkDept[]\');" maxlength="30">';

		row_td_1.className = 'center';
		row_td_3.className = 'center';
		row_td_5.className = 'last other';

		row_tr.appendChild(row_td_1);
		row_tr.appendChild(row_td_3);
		row_tr.appendChild(row_td_4);
		row_tr.appendChild(row_td_5);

		row.appendChild(row_tr);

		dept_name[row_count].focus();

		__init_form(document.f);
	}
	
	
	function save(){
		var f = document.f;

		var check = document.getElementsByName("checkDept[]");
		var dept_name = document.getElementsByName("dept_nm[]");
		var check_cnt = check.length;
		
		if(!__checkRowCount('checkDept[]')){
			return;
		}

		for(i=0; i<check_cnt; i++){
			if(check[i].checked == true && dept_name[i].value == '' ){
				alert("선택한 항목에 부서를 입력해주십시오.");
				dept_name[i].focus();

				return;
			}
		}
		
		f.action = 'dept_reg_ok.php';
		f.submit();
	}

	function del(){

		var f = document.f;

		if(!__checkRowCount('checkDept[]')){
			return;
		}

		if (!confirm('선택한 데이터를 삭제하시겠습니까?')){
			return;
		}

		f.action = 'dept_del.php';
		f.submit();
	}
	/*
	function check(code){

		var f = document.f;
		var checkDept =	document.getElementsByName("checkDept[]");

		var checkCnt = checkDept.length;

		for(var i = 0; i<checkCnt; i++){
			var duplicate = false;
			var obj = document.getElementById("dept_code_"+checkDept[i].value);

			if(code.name != obj.name){
				if(code.value == obj.value){
					duplicate = true;
					alert("입력하신 코드는 이미 사용하신 코드입니다.");
					code.value = '';
					code.focus();
					break;
				}
			}
		}

		return duplicate;
	}
	*/
		/*
		1. 체크배열을 가져온다.
		2. 체크에 길이를 구한다.
		3. 길이만큼 루틴.
		*/

	function setSeq(obj){
		var check = document.getElementsByName("checkDept[]");
		var order_seq = document.getElementsByName("order_seq[]");
		var dept_code = document.getElementsByName("dept_cd[]");
		var seq_cnt = order_seq.length;

		if(parseInt(obj.value) > parseInt(obj.tag)){
			//순번이 커졌을 경우
			//기존값보다 크고 바뀌는값보다 작거나같을경우 -1
			for(i=0; i<seq_cnt; i++){
				if(obj.tag < parseInt(order_seq[i].value) && obj.value >= parseInt(order_seq[i].value)){
					if(obj != order_seq[i]){
						order_seq[i].value--;
						order_seq[i].tag = order_seq[i].value;
						check[i].checked = true;
					}
				}
			}
			obj.tag = obj.value;

		}else if(parseInt(obj.value) < parseInt(obj.tag)){
			//순번이 작아졌을 경우
			//기존값보다 작고 바뀌는값보다 크거나같을경우 +1
			for(i=0; i<seq_cnt; i++){
				if(obj.tag > parseInt(order_seq[i].value) && obj.value <= parseInt(order_seq[i].value)){
					if(obj != order_seq[i]){
						order_seq[i].value++;
						order_seq[i].tag = order_seq[i].value;
						check[i].checked = true;
					}
				}
			}
			obj.tag = obj.value;
		}

		return obj;

	}
</script>
<form name="f" method="post">
<div class="title">
	<div style="width:auto; float:left;">부서관리</div>
	<div style="width:100%; padding-top:8px; text-align:right;">
		<span class="btn_pack m"><button type="button" onClick="new_row();">추가</button></span>
		<span class="btn_pack m"><button type="button" onClick="del();">삭제</button></span>
		<span class="btn_pack m"><button type="button" onClick="save();">저장</button></span>
	</div>
</div>

<table class="my_table my_border" width="100%">
	<colgroup>
		<col width="50px">
		<col width="130px">
		<col width="50px">
		<col>
	</colgroup>
	<tr>
		<th class="head">구분</th>
		<th class="head">부서명</th>
		<th class="head">순번</th>
		<th class="last head">비고</th>
	</tr>
	<tbody id="row_1">


	<?
		$sql = "select org_no, dept_cd, dept_nm, order_seq
				  from dept
				 where del_flag = 'N'
				   and org_no = '$org_no'
				 order by order_seq";
		
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		if ($rowCount > 0){
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				?>
				<tr>
					<td class="center">
						<input name="checkDept[]" type="checkbox" class="checkbox" value="<?=$i?>">
					</td>
					<input type="hidden" name="dept_cd[]" style="width:100%" tag="<?=$row['dept_cd']?>" value="<?=$row['dept_cd']?>" maxlength="2">
					<td>
						<input type="text" name="dept_nm[]" style="width:100%" onchange="__checkRow(<?=$i?>,'checkDept[]');" value="<?=$row['dept_nm']?>" maxlength="30">
					</td>
					<td>
						<input type="text" name="order_seq[]" style="width:100%" class="number" onkeydown="__onlyNumber(this);" onchange="setSeq(this); __checkRow(<?=$i?>,'checkDept[]');" value="<?=$row['order_seq']?>" tag="<?=$row['order_seq']?>" maxlength="11">
					</td>
					<td class="last other"></td>
				</tr><?
			}
		}

		?>
	</tbody>
	<input type="hidden" name="row_count" value="<?=$rowCount?>">
	<input type="hidden" name="org_no" value="<?=$org_no?>">
</table>
</form>

<?
	include_once("../inc/_db_close.php");
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>