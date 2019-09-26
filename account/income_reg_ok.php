<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	/*
	 * io_type 설정
	 * i : 입금내역
	 * o : 지출내역
	 *
	 * ------------------------------------
	 *
	 * mode 설정
	 * 1 : 등록
	 * 2 : 수정
	 */

	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$io_type= $_POST['io_type'];
	$mode	= $_POST['mode'];
	$code	= $_POST['find_center_code'];
	$account= $_POST['account_firm_code'];
	$check	= $_POST['check'];
	$count	= sizeof($check);

	$now_date = date('Y-m-d', mkTime());

	$find_from_date = $_POST['find_from_date'];
	$find_to_date   = $_POST['find_to_date'];

	if ($io_type == 'i'){
		$io_table = 'income';
		$io_filed = 'income';
	}else{
		$io_table = 'outgo';
		$io_filed = 'outgo';
	}

	$conn->begin();

	if ($mode == '2'){
		$sql = "select ifnull(max(".$io_table."_seq), 0) + 1
				  from center_".$io_table."
				 where org_no               = '$code'
				   and ".$io_table."_ent_dt = '$now_date'";
		$seq = $conn->get_data($sql);
	}

	for($i=0; $i<$count; $i++){
		if ($mode == '2'){ // 등록
			$date		= $_POST['date'][$i];
			$item		= $_POST['item'][$i];
			$amount		= str_replace(',', '', $_POST['amount'][$i]);
			$vat_yn		= $_POST['vat_'.$i];
			$vat		= str_replace(',', '', $_POST['vat'][$i]);
			$taxid		= str_replace('-', '', $_POST['taxid'][$i]);
			$biz_group	= $_POST['biz_group'][$i];
			$biz_type	= $_POST['biz_type'][$i];

			$sql = "insert into center_".$io_table." (
					 org_no
					,".$io_table."_ent_dt
					,".$io_table."_seq
					,create_id
					,create_dt
					,account_firm_cd
					,".$io_table."_acct_dt
					,vat_yn
					,".$io_table."_amt
					,".$io_table."_vat
					,".$io_table."_item
					,taxid
					,biz_group
					,biz_type
					) values (
					 '$code'
					,'$now_date'
					,'$seq'
					,'$code'
					,'$now_date'
					,'$account'
					,'$date'
					,'$vat_yn'
					,'$amount'
					,'$vat'
					,'$item'
					,'$taxid'
					,'$biz_group'
					,'$biz_type')";

			if (!$conn->execute($sql)){
				echo "
					<script>
						alert('입금내역 저장중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
						history.back();
					</script>
					 ";
				exit;
			}

			$seq ++;
		}else if ($mode == '3'){ // 수정
			$date		= $_POST['date'][$check[$i]-1];
			$item		= $_POST['item'][$check[$i]-1];
			$amount		= str_replace(',', '', $_POST['amount'][$check[$i]-1]);
			$vat_yn		= $_POST['vat_'.($check[$i]-1)];
			$vat		= str_replace(',', '', $_POST['vat'][$check[$i]-1]);
			$taxid		= str_replace('-', '', $_POST['taxid'][$check[$i]-1]);
			$biz_group	= $_POST['biz_group'][$check[$i]-1];
			$biz_type	= $_POST['biz_type'][$check[$i]-1];

			$ent_dt  = $_POST['ent_dt'][$check[$i]-1];
			$ent_seq = $_POST['ent_seq'][$check[$i]-1];

			$sql = "update center_".$io_table."
					   set update_id			 = '$code'
					,      update_dt			 = '$now_date'
					,      account_firm_cd		 = '$account'
					,      ".$io_table."_acct_dt = '$date'
					,      vat_yn			     = '$vat_yn'
					,      ".$io_table."_amt     = '$amount'
					,      ".$io_table."_vat     = '$vat'
					,      ".$io_table."_item    = '$item'
					,      taxid				 = '$taxid'
					,      biz_group			 = '$biz_group'
					,      biz_type				 = '$biz_type'
					 where org_no				 = '$code'
					   and ".$io_table."_ent_dt  = '$ent_dt'
					   and ".$io_table."_seq     = '$ent_seq'";

			if (!$conn->execute($sql)){
				echo "
					<script>
						alert('입금내역 수정중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
						history.back();
					</script>
					 ";
				exit;
			}

			$sql = "select deposit_seq
					  from center_".$io_table."
					 where org_no                = '$code'
					   and ".$io_table."_ent_dt  = '$ent_dt'
					   and ".$io_table."_seq     = '$ent_seq'";
			$deposit_seq = $conn->get_data($sql);

			if ($deposit_seq > 0){
				$sql = "update unpaid_deposit
						   set deposit_amt    = '$amount'
						,      update_dt      = '$now_date'
						,      update_id      = '$code'
						 where org_no         = '$code'
						   and deposit_ent_dt = '$ent_dt'
						   and deposit_seq    = '$deposit_seq'";

				echo $sql;

				if (!$conn->execute($sql)){
					echo "
						<script>
							alert('입금내역 수정중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
							history.back();
						</script>
						 ";
					exit;
				}
			}
		}else if ($mode == '4'){ // 삭제
			$ent_dt  = $_POST['ent_dt'][$check[$i]];
			$ent_seq = $_POST['ent_seq'][$check[$i]];

			$sql = "update center_".$io_table."
					   set del_flag = 'Y'
					 where org_no                = '$code'
					   and ".$io_table."_ent_dt  = '$ent_dt'
					   and ".$io_table."_seq     = '$ent_seq'";

			if (!$conn->execute($sql)){
				echo "
					<script>
						alert('입금내역 삭제중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
						history.back();
					</script>
					 ";
				exit;
			}

			$sql = "select deposit_seq
					  from center_".$io_table."
					 where org_no                = '$code'
					   and ".$io_table."_ent_dt  = '$ent_dt'
					   and ".$io_table."_seq     = '$ent_seq'";
			$deposit_seq = $conn->get_data($sql);

			if ($deposit_seq > 0){
				$sql = "update unpaid_deposit
						   set del_flag    = 'Y'
						 where org_no         = '$code'
						   and deposit_ent_dt = '$ent_dt'
						   and deposit_seq    = '$deposit_seq'";

				if (!$conn->execute($sql)){
					echo "
						<script>
							alert('입금내역 삭제중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
							history.back();
						</script>
						 ";
					exit;
				}
			}
		}
	}

	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');

	switch('<?=$mode;?>'){
	case '2': //등록
		location.replace('income_reg.php?io_type=<?=$io_type;?>&mode=2&find_center_code=<?=$code;?>');
		break;
	case '3': //수정
		location.replace('income_modify.php?io_type=<?=$io_type;?>&mode=3&find_center_code=<?=$code;?>&year=<?=$year;?>&month=<?=$month;?>');
		break;
	case '4': //삭제
		location.replace('income_delete.php?io_type=<?=$io_type;?>&mode=4&find_center_code=<?=$code;?>&year=<?=$year;?>&month=<?=$month;?>');
		break;
	default: //리스트
		location.replace('income_list.php?io_type=<?=$io_type;?>&mode=1&find_center_code=<?=$code;?>&year=<?=$year;?>&month=<?=$month;?>');
	}
</script>