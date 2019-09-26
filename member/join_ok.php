<?
/*
* 주민번호체크
주민번호가 있으면 상세등록화면으로 이동 없으면 일치하는주민번호가없다고 메세지창을 띄우고 리턴.
*/
	include_once('../inc/_db_open.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	$code = $_POST['code'];
	$name = $_POST['name'];
	$memNo	= $_POST['mem_no'];	
	//$jumin1 = $_POST['m_jumin1'];
	//$jumin2	= $_POST['m_jumin2'];	

	$sql = "select min(m02_mkind), m02_yname, m02_yjumin
			  from m02yoyangsa
			 inner join mem_his
				on mem_his.org_no    = m02_ccode
			   and mem_his.jumin     = m02_yjumin
			   and DATE_FORMAT(mem_his.join_dt,'%Y%m%d') <= DATE_FORMAT(now(),'%Y%m%d')
			   and mem_his.employ_stat = '1'
			 where m02_ccode = '".$code."'
			   and com_no = '".$memNo."'";
	
	$yoy = $conn -> get_array($sql);
	
	if($yoy['m02_yname'] != $name){ ?>
		<script language='javascript'> 
			alert('직원등록되지 않았거나 정보가 일치하지 않습니다. 다시 입력해주십시오.');
			parent.document.f.name.select();
		</script><?

		return false;
	}
	
	$sql = "select count(*)
			  from mem_his as his
		 left join member as mem
				on mem.org_no = his.org_no
			   and mem.jumin = his.jumin
			 where his.org_no = '".$code."'
			   and his.com_no = '".$memNo."'
			   and his.employ_stat = '1'";
	$id_cnt1 = $conn -> get_data($sql);
	
	$sql = "select count(*)
			  from member
			 where org_no = '".$code."'
			 and   jumin = '".$yoy['m02_yjumin']."'";
	$id_cnt2 = $conn -> get_data($sql);
	
	if($id_cnt1 == $id_cnt2){
	?>
			<script>
				alert("모든 기관에 등록하셨습니다.");
				parent.location.replace = "../member/join.php?join=YES";
			</script><?
			return false;
	}
	
	?>
	<script language='javascript'>
		parent.document.f.action = "../member/member.php?join=YES";
		parent.document.f.submit();
	</script>
	
	<?
		include_once('../inc/_db_close.php');
	?>
