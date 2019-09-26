<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code        = $_GET['code'];
	$send_type   = $_GET['send_type'];
	$result      = $_GET['result'];
	$find_nm     = $_POST['find_nm'];
	$find_branch = $_POST['find_branch'];
	$find_center = $_POST['find_center'];
	$find_dept   = $_POST['find_dept'];

	if (empty($result)) $result = 1;

	if ($_SESSION['userLevel'] == 'A'){
		if (empty($find_branch)) $find_branch = $_SESSION['userBranchCode'];
		if (empty($find_center)) $find_center = $_SESSION['userCenterCode'];
	}else if ($_SESSION['userLevel'] == 'B'){
		$find_branch = $_SESSION['userBranchCode'];
	}else if ($_SESSION['userLevel'] == 'C'){
		$find_branch = $_SESSION['userBranchCode'];
		$find_center = $_SESSION['userCenterCode'];
	}else{
		$find_branch = $_SESSION['userBranchCode'];
		$find_center = $_SESSION['userCenterCode'];
	}

	switch($send_type){
		case 'branch':
			$title = '지사';
			break;
		case 'center':
			$title = '가맹점';
			break;
		case 'dept':
			$title = '부서';
			break;
		case 'person':
			$title = '직원';
			break;
	}
?>

<form name='f' method='post'>
<div class='title title_border'><?=$title;?>별</div>

<table class='my_table' style='width:100%;'>
	<colgroup>
		<col width='60px'>
		<col>
		<col width='60px'>
	</colgroup>
	<tbody>
		<?
			$show_branch = false;
			$show_center = false;
			$show_dept   = false;
			$row_span    = 1;

			if ($_SESSION['userLevel'] == 'A'){
				if ($send_type == 'center' || $send_type == 'dept' || $send_type == 'person'){
					//$show_branch = true;
					//$show_center = true;
					$row_span ++;
				}

				if ($send_type == 'dept' || $send_type == 'person'){
					$show_center = true;
					$row_span ++;
				}
			}else if ($_SESSION['userLevel'] == 'B'){
				if ($send_type == 'dept' || $send_type == 'person'){
					$show_center = true;
					$row_span ++;
				}
			}

			if ($send_type == 'person'){
				$show_dept = true;
				$row_span ++;
			}

			/*
			if ($show_branch){
				echo '<tr>
						<th>지사</th>
						<td id=\'body_branch\'></td>
						<th class=\'center\' rowspan=\''.$row_span.'\'><a href=\'#\' onclick=\'find();\'>찾기</a></th>
					  </tr>';
			}
			*/

			if ($show_center){
				echo '<tr>
						<th>가맹점</th>
						<td id=\'body_center\'>';

				if (!$show_branch){
					echo '	</td>
							<th class=\'center\' rowspan=\''.$row_span.'\'><a href=\'#\' onclick=\'find();\'>찾기</a></th>
						  </tr>';
				}
			}

			if ($show_dept){
				echo '<tr>
						<th>부서</th>
						<td id=\'body_dept\'>';

				if (!$show_center){
					echo '	</td>
							<th class=\'center\' rowspan=\''.$row_span.'\'><a href=\'#\' onclick=\'find();\'>찾기</a></th>
						  </tr>';
				}
			}
		?>
		<tr>
			<th><?=$title;?></th>
			<td><input name='find_nm' type='text' value='<?=$find_nm;?>' style='width:100%;'></td>
			<?
				if ($show_branch || $show_center || $show_dept){
				}else{
					echo '<th class=\'center\'><a href=\'#\' onclick=\'find();\'>찾기</a></th>';
				}
			?>
		</tr>
	</tbody>
</table>
<?
	switch($send_type){
		case 'branch':
			$colgroup = '<col width=\'30px\'>
						 <col width=\'60px\'>
						 <col width=\'130px\'>
						 <col>';
			break;

		case 'center':
			$colgroup = '<col width=\'30px\'>
						 <col width=\'80px\'>
						 <col width=\'190px\'>
						 <col>';
			break;

		case 'dept':
			$colgroup = '<col width=\'30px\'>
						 <col width=\'60px\'>
						 <col width=\'130px\'>
						 <col>';
			break;

		case 'person':
			$colgroup = '<col width=\'30px\'>
						 <col width=\'100px\'>
						 <col width=\'130px\'>
						 <col>';
			break;
	}
?>
<table id="list" class='my_table' style='width:100%;'>
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class='head'><input name="check_all" type="checkbox" class="checkbox" onclick="__checkMyValue('check[]', this.checked);"></th>
			<?
				switch($send_type){
					case 'branch':
						echo '<th class=\'head\'>지사기호</th>
							  <th class=\'head\'>지사명</th>
							  <th class=\'head\'>비고</th>';
						break;

					case 'center':
						echo '<th class=\'head\'>가맹점기호</th>
							  <th class=\'head\'>가맹점명</th>
							  <th class=\'head\'>비고</th>';
						break;

					case 'dept':
						echo '<th class=\'head\'>부서코드</th>
							  <th class=\'head\'>부서명</th>
							  <th class=\'head\'>비고</th>';
						break;

					case 'person':
						echo '<th class=\'head\'>사번</th>
							  <th class=\'head\'>직원명</th>
							  <th class=\'head\'>비고</th>';
						break;
				}
			?>
		</tr>
	</thead>
	<tbody >
		<tr>
			<td colspan='10'>
				<div id="list_body" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100;">
					<table class='my_table' style='width:100%;'>
						<colgroup><?=$colgroup;?></colgroup>
						<tbody>
						<?
							if ($send_type == 'branch'){
								$sql = "select b00_code as cd, b00_name as nm
										  from b00branch
										 where b00_domain = '$gDomain'
										   and b00_com_yn = 'N'";

								if (!empty($find_nm))
									$sql .= " and b00_name >= '$find_nm'";

								$sql .= " order by b00_code";
							}else if ($send_type == 'center'){
								$sql = "select m00_mcode as cd, m00_mkind as kind, m00_store_nm as nm
										  from m00center
										 inner join b02center
											on b02_center = m00_mcode
										   and b02_kind   = m00_mkind
										 where m00_domain = '$gDomain'
										   and m00_del_yn = 'N'";

								#if (!empty($find_branch))
								#	$sql .= " and m00_mcode = (select b02_center from b02center where b02_branch = '".$find_branch."' limit 1)";

								if (!empty($find_nm))
									$sql .= " and m00_store_nm >= '$find_nm'";

								$sql .= " group by m00_mcode
										  order by m00_store_nm";
							}else if ($send_type == 'dept'){
								$sql = "select org_no as no, dept_cd as cd, dept_nm as nm, m00_store_nm as other
										  from dept
										 inner join m00center
										    on m00_mcode = dept.org_no
									     inner join b02center
										    on b02_center = m00_mcode
										   and b02_kind   = m00_mkind
										 where del_flag   = 'N'
										   and b02_branch = '$find_branch'
										   and org_no     = '$find_center'";

								if (!empty($find_nm))
									$sql .= " and dept_nm >= '$find_nm'";

								$sql .= " order by org_no, order_seq";
							}else if ($send_type == 'person'){
								$sql = "select m02_ccode as no, min(m02_mkind) as kind, m02_key as cd, m02_mem_no as mem_no, m02_yname as nm
										  from m02yoyangsa
										 inner join m00center
										    on m00_mcode = m02_ccode
									     inner join b02center
										    on b02_center = m00_mcode
										   and b02_kind   = m00_mkind
										 where m02_ccode  = '$find_center'
										   and m02_del_yn = 'N'";

								if (!empty($find_dept))
									$sql .= " and m02_dept_cd = '$find_dept'";

								if (!empty($find_nm))
									$sql .= " and m02_yname >= '$find_nm'";

								$sql .= " group by m02_ccode, m02_yjumin
										  order by m02_yname";
							}

							$conn->fetch_type = 'assoc';
							$conn->query($sql);
							$conn->fetch();

							$row_count = $conn->row_count();

							for($i=0; $i<$row_count; $i++){
								$row = $conn->select_row($i);

								if ($send_type == 'person'){
									$show_cd = $myF->formatString($row['mem_no'], '########');
								}else{
									$show_cd = $row['cd'];
								}

								if (!empty($row['no']))
									$cd = $row['no'].'/'.$row['cd'];
								else
									$cd = $row['cd'];

								switch($result){
									case 1:
										$val = $cd;
										break;

									case 2:
										$val = $cd.'|'.$row['nm'];
										break;

									default:
										$val = '';
								}

								echo '<tr>';
								echo '<td class=\'center\'><div class=\'center\'><input id=\'chk'.$i.'\' name=\'check[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$val.'\' ></div></td>';
								echo '<td class=\'center\'><div class=\'center\'><label for=\'chk'.$i.'\'>'.$show_cd.'</label></div></td>';
								echo '<td class=\'center\'><div class=\'left\'><label for=\'chk'.$i.'\'>'.$row['nm'].'</label></div></td>';
								echo '<td class=\'center\'><div class=\'left\'>'.$row['other'].'</div></td>';
								echo '</tr>';
							}

							$conn->row_free();
						?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th colspan='4'><div class='right'>
				<a href='#' onclick='current();'>확인</a> |
				<a href='#' onclick='quit();'>닫기</a>
			</div></th>
		</tr>
	</tbody>
</table>

</form>
<?
	include_once("../inc/_footer.php");
?>
<script language='javascript'>
<!--

function find(){
	var f = document.f;

	f.action = 'note_list_find_1.php?code=<?=$code;?>&send_type=<?=$send_type;?>';
	f.submit();
}

function current(){
	var chk = document.getElementsByName('check[]');
	var rst = '';

	for(var i=0; i<chk.length; i++){
		if (chk[i].checked)
			rst += chk[i].value + '//';
	}

	if (rst == ''){
		alert('리스트를 선택하여 주십시오.');
		return;
	}

	window.returnValue = rst;
	window.close();
}

function quit(){
	window.returnValue = '';
	window.close();
}

function set_branch(){
	var body = document.getElementById('body_branch');

	try{
		var branch = document.getElementById('find_branch').value;
	}catch(e){
		var branch = '<?=$find_branch;?>';
	}

	if (body == null) return;

	var URL = '../inc/_branch_list.php';
	var params  = {'find_branch':branch};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
				set_center();
			}
		}
	);
}

function set_center(){
	var body = document.getElementById('body_center');

	try{
		var branch = document.getElementById('find_branch').value;
		var center = document.getElementById('find_center').value;
	}catch(e){
		var branch = '<?=$find_branch;?>';
		var center = '<?=$find_center;?>';
	}

	if (body == null) return;

	var URL = '../inc/_center_list.php';
	var params  = {'find_branch':branch,'find_center':center};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
				set_dept();
			}
		}
	);
}

function set_dept(){
	var body = document.getElementById('body_dept');

	try{
		var center = document.getElementById('find_center').value;
		var dept   = document.getElementById('find_dept').value;
	}catch(e){
		var center = '<?=$find_center;?>';
		var dept   = '<?=$find_dept;?>';
	}

	if (body == null) return;

	var URL = '../inc/_dept_list.php';
	var params  = {'find_center':center,'find_dept':dept};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

window.onload = function(){
	var list = document.getElementById('list');
	var body = document.getElementById('list_body');
	var h    = document.body.offsetHeight - (__getObjectTop(list) + 52);

	body.style.height = h;

	set_branch();
	set_center();
	set_dept();

	__init_form(document.f);
}

-->
</script>