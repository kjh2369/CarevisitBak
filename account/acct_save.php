<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$code   = $_SESSION['userCenterCode'];
	$type   = $_POST['type'];
	$today  = Date('Y-m-d');
	$userId = $_SESSION['userCode'];

	$conn->begin();

	if ($type == '1' || $type == '2' || $type == '11' || $type == '12'){
		//수입내역저장
		$entDt    = $_POST['entDt'];
		$seq      = $_POST['seq'];
		$regDt    = $_POST['regDt'];
		$proofY   = $_POST['proofY'];
		$proofM   = $_POST['proofM'];
		$proofD   = $_POST['proofD'];
		$proofNo  = $_POST['proofNo'];
		$itemCd   = $_POST['itemCd'];
		$vatYn    = $_POST['vatYn'];
		$amt      = $_POST['amt'];
		$vat      = $_POST['vat'];
		$bizId    = Str_Replace('-','',$_POST['bizId']);
		$bizGroup = $_POST['bizGroup'];
		$bizType  = $_POST['bizType'];
		$item     = $_POST['item'];
		$other    = $_POST['other'];

		if ($type == '1' || $type == '2'){
			$field = 'income';
		}else if ($type == '11' || $type == '12'){
			$field = 'outgo';
		}else{
			$conn->close();
			echo 9;
			exit;
		}

		if (Empty($entDt)){
			$entDt = $today;
		}

		if (Empty($seq)){
			$sql = 'SELECT IFNULL(MAX('.$field.'_seq),0)+1
					  FROM center_'.$field.'
					 WHERE org_no = \''.$code.'\'
					   AND '.$field.'_ent_dt = \''.$entDt.'\'';
			$seq = $conn->get_data($sql);
			$isNew = true;
		}else{
			$isNew = false;
		}

		if ($isNew){
			/*
			 * 같은 증빙서 번호 입력 가능
			$sql = 'SELECT COUNT(*)
					  FROM center_'.$field.'
					 WHERE org_no     = \''.$code.'\'
					   AND proof_year = \''.$proofY.'\'
					   AND proof_no   = \''.$proofNo.'\'';
			$proofCnt = $conn->get_data($sql);

			if (IntVal($proofCnt) > 0){
				$conn->rollback();
				$conn->close();
				echo 7;
				exit;
			}
			*/

			$sql = 'INSERT INTO center_'.$field.' (
					 org_no
					,'.$field.'_ent_dt
					,'.$field.'_seq
					,create_id
					,create_dt
					,'.$field.'_acct_dt
					,'.$field.'_item_cd
					,vat_yn
					,'.$field.'_amt
					,'.$field.'_vat
					,'.$field.'_item
					,taxid
					,biz_group
					,biz_type
					,other
					,proof_year
					,proof_no) VALUES (
					 \''.$code.'\'
					,\''.$entDt.'\'
					,\''.$seq.'\'
					,\''.$userId.'\'
					,\''.$today.'\'
					,\''.$regDt.'\'
					,\''.$itemCd.'\'
					,\''.$vatYn.'\'
					,\''.$amt.'\'
					,\''.$vat.'\'
					,\''.$item.'\'
					,\''.$bizId.'\'
					,\''.$bizGroup.'\'
					,\''.$bizType.'\'
					,\''.$other.'\'
					,\''.$proofY.'\'
					,\''.$proofNo.'\')';
		}else{
			/*
			$sql = 'SELECT COUNT(*)
					  FROM center_'.$field.'
					 WHERE org_no     = \''.$code.'\'
					   AND proof_year = \''.$proofY.'\'
					   AND proof_no   = \''.$proofNo.'\'';
			$proofCnt = $conn->get_data($sql);

			if (IntVal($proofCnt) > 1){
				$conn->rollback();
				$conn->close();
				echo 7;
				exit;
			}
			*/

			$sql = 'UPDATE center_'.$field.'
					   SET update_id = \''.$userId.'\'
					,      update_dt = \''.$today.'\'
					,      '.$field.'_acct_dt = \''.$regDt.'\'
					,      '.$field.'_item_cd = \''.$itemCd.'\'
					,      vat_yn = \''.$vatYn.'\'
					,      '.$field.'_amt  = \''.$amt.'\'
					,      '.$field.'_vat  = \''.$vat.'\'
					,      '.$field.'_item = \''.$item.'\'
					,      taxid = \''.$bizId.'\'
					,      biz_group = \''.$bizGroup.'\'
					,      biz_type = \''.$bizType.'\'
					,      other = \''.$other.'\'
					,      proof_year = \''.$proofY.'\'
					,      proof_no = \''.$proofNo.'\'
					 WHERE org_no = \''.$code.'\'
					   AND '.$field.'_ent_dt = \''.$entDt.'\'
					   AND '.$field.'_seq    = \''.$seq.'\'';
		}

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$sql = 'SELECT IFNULL(MAX(CAST(proof_no AS unsigned)),0) + 1
				  FROM center_'.$field.'
				 WHERE org_no     = \''.$code.'\'
				   AND proof_year = \''.$proofY.'\'';
		$proofNo = $conn->get_data($sql);

		if (StrLen($proofNo) < 5){
			for($i=StrLen($proofNo)+1; $i<=5; $i++){
				$proofNo = '0'.$proofNo;
			}
		}

	}else{
		echo 9;
		exit;
	}

	$conn->commit();

	if ($type == '1' || $type == '11'){
		echo $proofNo;
	}else{
		echo 1;
	}

	include_once('../inc/_db_close.php');
?>