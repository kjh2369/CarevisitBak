
<?
	define(__SESSION_NOT__,'Y');
	
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	//include_once("../inc/_myFun.php");
	
	/*************************************************

	회원정보 등록

	*************************************************/
	
	
	$memGbn = $_POST['memGbn'];
	$userId = $_POST['userId'];				//아이디
	$userPwd = $_POST['userPwd'];			//비밀번호
	$jumin  = $ed->de($_POST['userJumin']);	//주민번호
	$code   = $_POST['userCode'];			//기관기호
	$Email  = $_POST['userEmail'];			//이메일
	$post	= $_POST['userPost'];			//우편번호
	$addr	 = $_POST['userAddr'];			//주소
	$dtlAddr = $_POST['userDtlAddr'];		//상세주소

	$conn -> begin();
	
	//직원정보조회
	$sql = "select count(*)
			  from member
			 where org_no = '".$code."'
			   and code = '".$JoinID."'";
	$mem = $conn->get_data($sql);
	
	if($mem == 0){
		
		if($memGbn == 'Y'){
			
			//직원정보조회
			$sql = "select *
					  from m02yoyangsa
					 where m02_ccode = '".$code."'
					   and m02_yjumin = '".$jumin."'
					 group by m02_yjumin";
			$mem = $conn->get_array($sql);
			
			$mobile = $mem['m02_ytel'];
			$tel    = $mem['m02_ytel2'];

			if($jumin and $jumin == $mem['m02_yjumin'] and $JoinID and $JoinPass){
				//직원정보저장
				$sql = "insert into member(org_no, code, pswd, name, jumin, tel, mobile, email, postno, addr, addr_dtl, insert_dt) values (
								'".$code."'
								,'$JoinID'
								,'".$JoinPass."'
								,'".$mem['m02_yname']."'
								,'".$mem['m02_yjumin']."'
								,'".$mem['m02_ytel2']."'
								,'".$mem['m02_ytel']."'
								,'".$Email."'
								,'".$mem['m02_ypostno']."'
								,'".$mem['m02_yjuso1']."'
								,'".$mem['m02_yjuso2']."'
								,now())";
				
				if (!$conn->execute($sql)){
						$conn->rollback();
						echo '<script>alert("데이타 처리중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오."); history.back();</script>';
						exit;
				}
			}

		}
	
	}else {
		
		if($memGbn == 'Y'){
			$sql = "update member
					   set pswd = '".$JoinPass."'
						 , email = '".$Email."'
						 , update_id = '".$JoinID."'
						 , update_dt = now()
					 where org_no = '".$code."'
					   and code = '".$JoinID."'";
			
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo '<script>alert("데이타 처리중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오."); history.back();</script>';
				exit;
			}
		}
		
		$mobile = '';
		$tel    = '';
	}

	$conn -> commit();
	
	
include_once("../inc/_db_close.php");

?>