<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_db_open.php");

	$code = $_SESSION['userCenterCode'];
?>
<script language="javascript">
	window.onload = function(){
		__init_form(document.f);
	}

	function new_row(){
		var row_no = document.f.row_count.value;

		var job_seq = document.getElementsByName("job_seq[]");
		var job_cd = document.getElementsByName("job_cd[]");
		var dept_name = document.getElementsByName("job_nm[]");
		var seq_cnt = job_seq.length;
		var max_seq = 0;

		for(var i=0; i<seq_cnt; i++){
			if(parseInt(job_seq[i].value) > max_seq){
				max_seq = job_seq[i].value;
			}
		}

		max_seq ++;

		if(row_no == ""){
			row_no = 0;
		}

		var row       = document.getElementById("row_1");
		var row_count = parseInt($('#row_1 tr').length);
		var row_tr    = document.createElement("tr");
		var row_td_1  = document.createElement("td");
		var row_td_3  = document.createElement("td");
		var row_td_4  = document.createElement("td");
		var row_td_5  = document.createElement("td");

		row_td_1.innerHTML  = '<input name="check[]" type="checkbox" class="checkbox" value="'+row_count+'">';
		row_td_3.innerHTML  = '<input name="job_nm[]" type="text" style="width:92%;" onchange="__checkRow('+row_count+',\'check[]\');" maxlength="30">';
		row_td_4.innerHTML  = '<input name="job_seq[]" type="text" class="number" onkeydown="__onlyNumber(this);" style="width:100%" value="'+max_seq+'" tag="'+max_seq+'" onchange="setSeq(this); __checkRow('+row_count+',\'check[]\');" maxlength="30">';


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

		var check = document.getElementsByName("check[]");
		var dept_name = document.getElementsByName("job_nm[]");
		var check_cnt = check.length;

		if(!__checkRowCount('check[]')){
			return;
		}

		for(i=0; i<check_cnt; i++){
			if(check[i].checked == true && dept_name[i].value == '' ){
				alert("선택한 항목에 직무명을 입력해주십시오.");
				dept_name[i].focus();
				return;
			}
		}

		f.action = 'job_reg_ok.php';
		f.submit();
	}

	function del(){

		var f = document.f;

		if(!__checkRowCount('check[]')){
			return;
		}

		if (!confirm('선택한 데이터를 삭제하시겠습니까?')){
			return;
		}

		f.action = 'job_del_ok.php';
		f.submit();
	}

	function setSeq(obj){
		var check = document.getElementsByName("check[]");
		var job_seq = document.getElementsByName("job_seq[]");
		var job_cd = document.getElementsByName("job_cd[]");
		var seq_cnt = job_seq.length;

		if(parseInt(obj.value) > parseInt(obj.tag)){
			for(i=0; i<seq_cnt; i++){
				if(obj.tag < parseInt(job_seq[i].value) && obj.value >= parseInt(job_seq[i].value)){
					if(obj != job_seq[i]){
						job_seq[i].value--;
						job_seq[i].tag = job_seq[i].value;
						check[i].checked = true;
					}
				}
			}
			obj.tag = obj.value;

		}else if(parseInt(obj.value) < parseInt(obj.tag)){
			for(i=0; i<seq_cnt; i++){
				if(obj.tag > parseInt(job_seq[i].value) && obj.value <= parseInt(job_seq[i].value)){
					if(obj != job_seq[i]){
						job_seq[i].value++;
						job_seq[i].tag = job_seq[i].value;
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
	<div style="width:auto; float:left;">직무관리</div>
	<div style="width:70%; padding-top:8px; text-align:right; float:left;"> 예) 요양보호사, 간호사, 사무원 ...</div>
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
		<th class="head">직무명</th>
		<th class="head">순번</th>
		<th class="last head">비고</th>
	</tr>
	<tbody id="row_1">
	<?
		$sql = 'select org_no, job_cd, job_nm, job_seq
				  from job_kind
				 where org_no   = \''.$code.'\'
				   and del_flag = \'N\'
				 order by job_seq';
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		if ($rowCount > 0){
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				?>
				<tr>
					<td class="center">
						<input name="check[]" type="checkbox" class="checkbox" value="<?=$i?>">
					</td>
					<input type="hidden" name="job_cd[]" style="width:100%" tag="<?=$row['job_cd']?>" value="<?=$row['job_cd']?>" maxlength="2">
					<td>
						<input type="text" name="job_nm[]" style="width:100%" onchange="__checkRow(<?=$i?>,'check[]');" value="<?=$row['job_nm']?>" maxlength="30">
					</td>
					<td>
						<input type="text" name="job_seq[]" style="width:100%" class="number" onkeydown="__onlyNumber(this);" onchange="setSeq(this); __checkRow(<?=$i?>,'check[]');" value="<?=$row['job_seq']?>" tag="<?=$row['job_seq']?>" maxlength="11">
					</td>
					<td class="last other"></td>
				</tr><?
			}
		}

		?>
	</tbody>
	<input type="hidden" name="row_count" value="<?=$rowCount?>">
	<input type="hidden" name="code" value="<?=$code?>">
</table>
</form>

<?
	include_once("../inc/_db_close.php");
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>