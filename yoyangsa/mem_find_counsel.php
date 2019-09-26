<?
	include_once('../inc/_header.php');
	//include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code		= $_SESSION['userCenterCode'];
	$ssn        = $ed->de($_GET['ssn']);
	$find_nm	= $_POST['find_nm'];
	$find_dt	= $_POST['find_dt'];
	$find_no	= $_POST['find_no'];
	$find_addr	= $_POST['find_addr'];

	if (strlen($ssn) != 13) $ssn = '';
?>
<base target="_self">

<script language='javascript'>
<!--

function find(){
	var f = document.f;

	f.submit();
}

function current_row(i){
	var ssn      = document.getElementsByName('ssn[]')[i];
	var name     = document.getElementsByName('name[]')[i];
	var phone    = document.getElementsByName('phone[]')[i];
	var mobile   = document.getElementsByName('mobile[]')[i];
	var email    = document.getElementsByName('email[]')[i];
	var postno   = document.getElementsByName('postno[]')[i];
	var addr     = document.getElementsByName('addr[]')[i];
	var addr_dtl = document.getElementsByName('addr_dtl[]')[i];
	var picture  = document.getElementsByName('picture[]')[i];
	var edu_lvl  = document.getElementsByName('edu_lvl[]')[i];
	var gbn      = document.getElementsByName('gbn[]')[i];
	var abode    = document.getElementsByName('abode[]')[i];

	var opener = window.dialogArguments;
	var form   = opener.document.f;

	form.ssn1.value         = ssn.value.substring(0,6);
	form.ssn2.value         = ssn.value.substring(6,13);
	form.counsel_name.value = name.value;
	form.mem_mobile.value   = __getPhoneNo(mobile.value);
	form.mem_phone.value    = __getPhoneNo(phone.value);
	form.mem_email.value    = email.value
	//form.mem_postno1.value  = postno.value.substring(0,3);
	//form.mem_postno2.value  = postno.value.substring(3,6);
	form.txtPostNo.value = postno.value;
	form.txtAddr.value     = addr.value;
	form.txtAddrDtl.value = addr_dtl.value;
	form.mem_counsel_gbn.value = gbn.value;
	form.counsel_mode.value = '2';
	//form.picture_nm.value = picture.value;

	//__object_set_value('counsel_edu_level', edu_lvl.value, opener, true);
	//__object_set_value('counsel_gubun',     gbn.value,     opener, true);
	//__object_set_value('counsel_abode',     abode.value,   opener, true);

	if (picture.value != ''){
		opener.document.getElementById('img_picture').src = '../mem_picture/'+picture.value;
	}else{
		opener.document.getElementById('img_picture').src = '../image/no_img_bg.gif';
	}

	var URL = '../counsel/mem_counsel_body.php';
	var parms = {'path':'find_mem','code':'<?=$code;?>','jumin':ssn.value};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:parms,
			onSuccess:function(responseHttpObj){
				try{
					var body = opener.document.getElementById('counsel_body');
						body.innerHTML = responseHttpObj.responseText;
				}catch(e){
					__show_error(e);
				}

				window.returnValue = true;
				window.close();
			}
		}
	);

	//window.returnValue = mem_cd;
	//window.close();
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
			<td class="center last"><a href="#" onclick="find();">찾기</a></td>
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

<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:204px;">
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

			$sql = "select mem_ssn
					,      mem_nm
					,      mem_picture
					,      mem_counsel_dt
					,      mem_phone
					,      mem_mobile
					,      mem_email
					,      mem_postno
					,      mem_addr
					,      mem_addr_dtl
					,      mem_edu_lvl
					,      mem_gbn
					,      mem_abode
					  from counsel_mem
					 where org_no = '$code'
					   and mem_ssn not in (select m02_yjumin
											 from m02yoyangsa
											where m02_ccode  = '$code'
											  and m02_del_yn = 'N')";

			if ($ssn != '') $sql .= " and mem_ssn = '$ssn'";
			if ($find_nm != '') $sql .= " and mem_nm >= '$find_nm'";
			if ($find_dt != '') $sql .= " and replace(mem_counsel_dt, '-', '') like '$find_dt%'";
			if ($find_no != '') $sql .= " and mem_phone like '$find_no%'";
			if ($find_addr != '') $sql .= " and mem_addr like '%$find_addr%'";

			$sql .= "
					 order by mem_nm";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$no      = $i + 1;
				$name     = $row['mem_nm'];
				$ssn      = $row['mem_ssn'];
				$picture  = $row['mem_picture'];
				$birthday = substr($row['mem_ssn'], 0, 6);
				$birthday = substr($birthday, 0, 2).'.'.substr($birthday, 2, 2).'.'.substr($birthday, 4, 2);
				$write_dt = str_replace('-','.',$row['mem_counsel_dt']);
				$phone_no = $myF->phoneStyle($row['mem_phone'],'.');
				$phone    = $row['mem_phone'];
				$mobile   = $row['mem_mobile'];
				$email    = $row['mem_email'];
				$postno   = $row['mem_postno'];
				$addr     = $row['mem_addr'];
				$addr_dtl = $row['mem_addr_dtl'];
				$edu_lvl  = $row['mem_edu_lvl'];
				$gbn      = $row['mem_gbn'];
				$abode    = $row['mem_abode'];

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
					<input name='ssn[]'      type='hidden' value='$ssn'>
					<input name='name[]'     type='hidden' value='$name'>
					<input name='picture[]'  type='hidden' value='$picture'>
					<input name='phone[]'    type='hidden' value='$phone'>
					<input name='mobile[]'   type='hidden' value='$mobile'>
					<input name='email[]'    type='hidden' value='$email'>
					<input name='postno[]'   type='hidden' value='$postno'>
					<input name='addr[]'     type='hidden' value='$addr'>
					<input name='addr_dtl[]' type='hidden' value='$addr_dtl'>
					<input name='edu_lvl[]'  type='hidden' value='$edu_lvl'>
					<input name='gbn[]'      type='hidden' value='$gbn'>
					<input name='abode[]'    type='hidden' value='$abode'>";
			}

			$conn->row_free();

			$value = ob_get_contents();

			ob_end_clean();

			echo $value;
		?>
	</table>
</div>

</form>

<script language='javascript'>

</script>
<?
	include_once('../inc/_footer.php');
?>