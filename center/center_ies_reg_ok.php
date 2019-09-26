<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code   = $_POST['code']; //기관기호
	$kind   = $_POST['kind']; //서비스구분
	$seq    = $_POST['seq'];  //순번
	$gbn    = $_POST['gbn'];  //1:추가 / 2:수정
	$insCD  = $_POST['ins'];  //보험사 코드
	$fromDT = $_POST['from']; //가입기간
	$toDT   = $_POST['to'];   //가입기간
	$regID  = $_SESSION['userCode'];


	/*********************************************************

		보험사명

	*********************************************************/
	$sql = 'select g01_name
			  from g01ins
			 where g01_code = \''.$ins.'\'';

	$insNm = $conn->get_data($sql);


	/*********************************************************

		가입가능여부

	*********************************************************/
	if ($gbn == '1' || $gbn == '3'){
		$sql = 'select sum(cnt) as cnt
				  from (
					   select count(*) as cnt
						 from ies_center
						where org_no       = \''.$code.'\'
						  and ies_kind     = \''.$kind.'\'
						  and ies_from_dt <= \''.$from.'\'
						  and ies_to_dt   >  \''.$from.'\'
						union all
					   select count(*) as cnt
						 from ies_center
						where org_no       = \''.$code.'\'
						  and ies_kind     = \''.$kind.'\'
						  and ies_from_dt <= \''.$to.'\'
						  and ies_to_dt   >  \''.$to.'\'
					   ) as t';

		$iesCnt = $conn->get_data($sql);


		if ($iesCnt > 0){
			$conn->close();
			echo 'error_1';
			exit;
		}
	}



	$conn->begin();


	/*********************************************************

		배상책임보험 수정

	*********************************************************/
	if ($gbn == '2'){
		/*********************************************************
			직원 보험정보 수정
		*********************************************************/


		/*********************************************************
			기관 보험정부 수정
		*********************************************************/
		$sql = 'update ies_center
				   set ies_ins_cd  = \''.$insCD.'\'
				,      ies_ins_nm  = \''.$insNm.'\'
				,      ies_from_dt = \''.$fromDT.'\'
				,      ies_to_dt   = \''.$toDT.'\'
				,      update_id   = \''.$regID.'\'
				,      update_dt   = now()
				 where org_no      = \''.$code.'\'
				   and ies_seq     = \''.$seq.'\'
				   and del_flag    = \'N\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->close();
			echo 'error_2';
			exit;
		}



	/*********************************************************

		배상책임보험 추가 및 연장

	*********************************************************/
	}else{
		/*********************************************************
			다음 순번
		*********************************************************/
		$sql = 'select ifnull(max(ies_seq), 0) + 1
				  from ies_center
				 where org_no = \''.$code.'\'';
		$seq = $conn->get_data($sql);


		/*********************************************************
			배상책임보험 정보 추가
		*********************************************************/
		$sql = 'insert into ies_center (
				 org_no
				,ies_seq
				,ies_kind
				,ies_ins_cd
				,ies_ins_nm
				,ies_from_dt
				,ies_to_dt
				,insert_id
				,insert_dt) values (
				 \''.$code.'\'
				,\''.$seq.'\'
				,\''.$kind.'\'
				,\''.$insCD.'\'
				,\''.$insNm.'\'
				,\''.$fromDT.'\'
				,\''.$toDT.'\'
				,\''.$regID.'\'
				,now())';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->close();
			echo 'error_3';
			exit;
		}


		/*********************************************************
			연장인경우 직원 배상책임보험 정보를 수정한다.
		*********************************************************/
		if ($gbn == '3'){
		}
	}

	$conn->commit();

	echo 'ok';

	include_once('../inc/_db_close.php');
?>