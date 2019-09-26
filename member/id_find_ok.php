<?
/*
* 아이디찾기

*/
	include_once('../inc/_db_open.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	$code = $_POST['code'];
	$name = $_POST['name'];
	$jumin1 = $_POST['jumin1'];
	$jumin2	= $_POST['jumin2'];	
	

	$sql = "select m02_yname, member.code
			  from m02yoyangsa
		inner join member
			    on org_no = m02_ccode
			   and jumin = m02_yjumin
			 where m02_yjumin = '".$jumin1.$jumin2."'
			   and m02_ygoyong_stat = '1'";
	$yoy = $conn -> get_array($sql);
	
	$id = $yoy['code'];

	if($yoy['m02_yname'] != $name){ ?>
		<script language='javascript'> 
			alert('직원등록되지 않았거나 이름과 주민번호가 일치하지 않습니다. 다시 입력해주십시오.');
			parent.document.f.name.select();
		</script><?

		return false;
	}

	$sql = "select count(*)
			  from member
			 where jumin = '".$jumin1.$jumin2."'
			   and org_no = '".$code."'";
	$find_cnt = $conn -> get_array($sql);
	

	//db에 저장되있는데 이름과 입력에서 받아온 이름이 같지 않으면 이름과 주민이 일치하지않다. 
	if($find_cnt[0] == 0){ ?>
		<script language='javascript'> 
			alert('가입되지않은 기관입니다.');
			parent.document.f.code.select();
		</script><?

		return false;
	}
	
	?>
	<script language='javascript'>  
		parent.document.f.action = "../member/id_pwd_find.php?join=YES&id=<?=$id?>";
		parent.document.f.submit();
	</script>
	
	<?
	include_once('../inc/_db_close.php');
?>
