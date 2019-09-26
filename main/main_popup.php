<?
	include_once('../inc/_header.php');

	if (!isset($_SESSION['userCode']) || $_SESSION['userCode'] == ''){
		echo '<script language=\'javascript\'>
				self.close();
			  </script>';
	}

	$sql = 'select pop.id as id
				,      tbl.subject as subject
				,      tbl.reg_dt as reg_dt
				  from (
					   select notice_id as id
						 from popup_notice
						where org_no   = \''.$_SESSION['userCenterCode'].'\'
						  and from_dt <= \''.date('Y-m-d').'\'
						  and to_dt   >= \''.date('Y-m-d').'\'
						  and read_yn  = \'N\'
						union all
					   select notice_id as id
						 from popup_notice
						where org_no   = \'all\'
						  and from_dt <= \''.date('Y-m-d').'\'
						  and to_dt   >= \''.date('Y-m-d').'\'
						  and read_yn  = \'N\'
					   ) as pop
				 inner join tbl_goodeos_notice as tbl
					on tbl.id = pop.id
				 order by reg_dt desc';

	include_once('../inc/_footer.php');
?>