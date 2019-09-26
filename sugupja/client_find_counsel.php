<?
	include_once('../inc/_header.php');
	//include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code		= $_SESSION['userCenterCode'];
	$find_nm	= $_POST['find_nm'];
	$find_dt	= $_POST['find_dt'];
	$find_no	= $_POST['find_no'];
	$find_addr	= $_POST['find_addr'];

	if (is_array($_POST['find_type'])){
		foreach($_POST['find_type'] as $i => $f){
			$f_type[$f] = $f;
		}
	}
?>
<base target="_self">

<script language='javascript'>
<!--

function find(){
	var f = document.f;

	f.submit();
}

function current_row(i){
	var ssn         = document.getElementsByName('ssn[]')[i];
	var name        = document.getElementsByName('name[]')[i];
	var phone       = document.getElementsByName('phone[]')[i];
	var mobile      = document.getElementsByName('mobile[]')[i];
	var postno      = document.getElementsByName('postno[]')[i];
	var addr        = document.getElementsByName('addr[]')[i];
	var addr_dtl    = document.getElementsByName('addr_dtl[]')[i];
	var protect_nm  = document.getElementsByName('protect_nm[]')[i];
	var protect_rel = document.getElementsByName('protect_rel[]')[i];
	var protect_tel = document.getElementsByName('protect_tel[]')[i];
	var clt_dt      = document.getElementsByName('counsel_dt[]')[i];
	var clt_seq     = document.getElementsByName('counsel_seq[]')[i];
	var clt_kind    = document.getElementsByName('counsel_kind[]')[i];


	var opener = window.dialogArguments;
	var form   = opener.document.f;

	form.jumin1.value       = ssn.value.substring(0,6);
	form.jumin2.value       = ssn.value.substring(6,13);
	form.name.value         = name.value;
	form.mobile.value       = __getPhoneNo(mobile.value);
	form.phone.value        = __getPhoneNo(phone.value);
	//form.postno1.value      = postno.value.substring(0,3);
	//form.postno2.value      = postno.value.substring(3,6);
	//form.addr.value         = addr.value;
	//form.addr_dtl.value     = addr_dtl.value;
	form.txtPostNo.value      = postno.value;
	form.txtAddr.value         = addr.value;
	form.txtAddrDtl.value     = addr_dtl.value;
	form.protect_nm.value   = protect_nm.value;
	form.protect_rel.value  = protect_rel.value;
	form.protect_tel.value  = __getPhoneNo(protect_tel.value);
	form.counsel_dt.value   = clt_dt.value;
	form.counsel_seq.value  = clt_seq.value;
	form.counsel_kind.value = clt_kind.value;

	window.returnValue = true;
	window.close();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<div class="title">초기상담기록지 리스트</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="50px">
		<col width="80px">
		<col width="50px">
		<col width="80px">
		<col width="40px">
		<col>
		<col width="40px">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">성명</th>
			<td class="center"><input name="find_nm" type="text" value="<?=$find_nm;?>" style="width:100%;"></td>
			<th class="head">작성일</th>
			<td class="center"><input name="find_dt" type="text" value="<?=$find_dt;?>" class="no_string" style="width:100%;" onkeydown="__onlyNumber(this);"></td>
			<th class="head">연락처</th>
			<td class="center"><input name="find_no" type="text" value="<?=$find_no;?>" class="no_string" style="width:100%;" onkeydown="__onlyNumber(this);"></td>
			<th class="head">주소</th>
			<td class="center"><input name="find_addr" type="text" value="<?=$find_addr;?>" style="width:100%;"></td>
			<td class="center last" rowspan="2"><a href="#" onclick="find();">찾기</a></td>
		</tr>
		<tr>
			<th class="head">구분</th>
			<td colspan="7">
			<?
				$kind_list = $conn->kind_list($code, true);

				if (is_array($kind_list)){
					foreach($kind_list as $k => $k_list){
						if ($k_list['id'] < 30){
							echo '<input name=\'find_type[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$k_list['code'].'\' '.($f_type[$k_list['code']] == $k_list['code'] ? 'checked' : '').'>'.$k_list['name'];
						}
					}
				}

				unset($kind_list);
			?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col width="80px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">작성일</th>
			<th class="head">연락처</th>
			<th class="head last">주소</th>
		</tr>
	</thead>
</table>

<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:178px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="70px">
			<col width="80px">
			<col width="80px">
			<col width="90px">
			<col>
		</colgroup>
		<?
			ob_start();

			$sql = "select counsel.client_ssn as ssn
					,      counsel.client_nm as name
					,      counsel.client_phone as phone
					,      counsel.client_mobile as mobile
					,      case when counsel.client_counsel != '3' then normal.talker_dt else baby.talker_dt end as talker_dt
					,      counsel.client_postno as postno
					,      counsel.client_addr as addr
					,      counsel.client_addr_dtl as addr_dtl
					,      counsel.client_dt
					,      counsel.client_seq
					,      counsel.client_counsel
					,      counsel.client_protect_nm as protect_nm
					,      counsel.client_protect_rel as protect_rel
					,      counsel.client_protect_tel as protect_tel
					  from counsel_client as counsel
					  left join counsel_client_normal as normal
						on normal.org_no     = counsel.org_no
					   and normal.client_dt  = counsel.client_dt
					   and normal.client_seq = counsel.client_seq
					  left join counsel_client_baby as baby
						on baby.org_no     = counsel.org_no
					   and baby.client_dt  = counsel.client_dt
					   and baby.client_seq = counsel.client_seq
					 where counsel.org_no   = '$code'
					   and counsel.del_flag = 'N'";

			if ($find_nm != '') $sql .= " and counsel.client_nm >= '$find_nm'";
			if ($find_dt != '') $sql .= " and replace(case counsel.client_counsel when '1' then normal.talker_dt when '2' then baby.talker_dt  else '-' end, '-', '') like '$find_dt%'";
			if ($find_no != '') $sql .= " and counsel.client_phone like '$find_no%'";
			if ($find_addr != '') $sql .= " and counsel.client_addr like '%$find_addr%'";

			if (is_array($_POST['find_type'])){
				$first = true;
				$sql .= " and counsel.client_counsel in (";
				foreach($_POST['find_type'] as $i => $f){
					$sql .= (!$first ? ',' : '')."'$f'";
					$first = false;
				}
				$sql .= ")";
			}

			$sql .= "  and (select count(*)
			                  from m03sugupja
							 where m03_ccode  = counsel.org_no
							   and m03_jumin  = counsel.client_ssn
							   and m03_del_yn = 'N') = 0
					 order by counsel.client_nm";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$no			 = $i + 1;
				$name		 = $row['name'];
				$ssn		 = $row['ssn'];
				$birthday	 = $myF->issToBirthday($row['ssn'],'.');
				$write_dt	 = str_replace('-','.',$row['talker_dt']);
				$phone_no	 = $myF->phoneStyle($row['phone'],'.');
				$phone		 = $row['phone'];
				$mobile		 = $row['mobile'];
				$postno		 = $row['postno'];
				$addr        = $row['addr'];
				$addr_dtl    = $row['addr_dtl'];
				$protect_nm  = $row['protect_nm'];
				$protect_rel = $row['protect_rel'];
				$protect_tel = $row['protect_tel'];
				$clt_dt		 = $row['client_dt'];
				$clt_seq	 = $row['client_seq'];
				$clt_kind	 = $row['client_counsel'];

				echo "
					<tbody onmouseover=\"this.style.backgroundColor='efefef';\" onmouseout=\"this.style.backgroundColor='ffffff';\" onclick=\"current_row($i);\">
						<tr>
							<td class='center'>$no</td>
							<td class='center'>$name</td>
							<td class='center'>$birthday</td>
							<td class='center'>$write_dt</td>
							<td class='center'>$phone_no</td>
							<td class='last'>&nbsp;$addr</td>
						</tr>
					</tbody>
					<input name='ssn[]'        type='hidden' value='$ssn'>
					<input name='name[]'       type='hidden' value='$name'>
					<input name='phone[]'      type='hidden' value='$phone'>
					<input name='mobile[]'     type='hidden' value='$mobile'>
					<input name='postno[]'     type='hidden' value='$postno'>
					<input name='addr[]'       type='hidden' value='$addr'>
					<input name='addr_dtl[]'   type='hidden' value='$addr_dtl'>
					<input name='protect_nm[]' type='hidden' value='$protect_nm'>
					<input name='protect_rel[]' type='hidden' value='$protect_rel'>
					<input name='protect_tel[]' type='hidden' value='$protect_tel'>
					<input name='counsel_dt[]'  type='hidden' value='$clt_dt'>
					<input name='counsel_seq[]' type='hidden' value='$clt_seq'>
					<input name='counsel_kind[]' type='hidden' value='$clt_kind'>";
			}

			$conn->row_free();

			$value = ob_get_contents();

			ob_end_clean();

			echo $value;
		?>
	</table>
</div>

</form>

<?
	include_once('../inc/_footer.php');
?>