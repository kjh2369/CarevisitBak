<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	

	$code = $_GET['code'];
	$jumin = $ed->de($_GET['jumin']);

	
	$sql = 'select DISTINCT	m02_yjumin
			,	   m02_yname as yname
			,	   code
			,	   pswd
			,	   tel
			,	   mobile
			,	   email
			,	   postno
			,	   addr
			,	   addr_dtl
			,	   m02_ytel
			,	   m02_email
			,	   m02_yipsail
			,	   m02_ytoisail
			,	   m02_ypostno
			,	   m02_yjuso1
			,	   m02_yjuso2
		      from m02yoyangsa
			  left join member
			    on org_no = m02_ccode
			   and jumin  = m02_yjumin
			 where m02_ccode = \''.$code.'\'
			   and m02_yjumin = \''.$jumin.'\'
			   and m02_mkind = \'0\'';
	
	$mst = $conn -> get_array($sql); 
	
	$tel = ($mst['mobile'] != '' ? $mst['mobile'] : $mst['m02_ytel']);
	$email = ($mst['email'] != '' ? $mst['email'] : $mst['m02_email']);
	$post = ($mst['postno'] != '' ? $mst['postno'] : $mst['m02_ypostno']);
	$addr = ($mst['addr'] != '' ? $mst['addr'] : $mst['m02_yjuso1']);
	$addr_dtl = ($mst['addr_dtl'] != '' ? $mst['addr_dtl'] : $mst['m02_yjuso2']);
	
	if($post != ''){
		$post = '('.$post.')';
	}else {
		$post = '';
	}
	
?>
<table class="write_type" cellspacing="0" border="1" summary="게시판 상세내용: 제목,작성자,등록일,조회수,첨부파일">  
	<caption>게시판 상세내용</caption>  
	<colgroup>  
		<col width="100"/>  
		<col width="*" /> 
	</colgroup>  
	<thead>
		<tr>
			<th>아이디</th>
			<td><input type="text" id="JoinID" name="JoinID" style="width:160px; ime-mode:disabled;" title="필수 아이디" value="" onblur="id_chk();" /></td>
		</tr>
		<tr>
			<th>비밀번호</th>
			<td><input type="text" id="JoinPass" name="JoinPass" style="width:160px; ime-mode:disabled;" title="필수 비밀번호" value="<?=$mst['pswd'];?>" /></td>
		</tr>
		<tr>
			<th>이름</th>
			<td><?=$mst['yname'];?></td>
		</tr>
		
	</thead>
	<tbody>
		<tr>
			<th>연락처</th>
			<td><input type="text" id="JoinTel" name="JoinTel" class="phone" style="width:160px; ime-mode:disabled;" onKeyDown = "javascript:onlyNumberInput(event)"  onFocus="__replace(this,'-','');" onBlur="__getPhoneNo(this);" title="필수 연락처" value="<?=$tel;?>" /></td>
		</tr>
		<tr>
			<th>이메일</th>
			<td><input type="text" id="Email" name="Email" style="width:160px; ime-mode:disabled;" title="필수 이메일" value="<?=$email;?>" /></td>
		</tr>
	</tbody>
</table>
<div align="right" style="margin-top:10px;"><a href="../sub1/index.php?mtype=<?=$_GET['mtype']?>&mode=1" class="tbl_btn1" >목록보기</a></div>
<?
	echo $html;

	unset($html);

	$conn->close();
	
?>
