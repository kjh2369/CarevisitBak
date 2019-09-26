<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code   = $_GET['code'];
	$sugaCD = str_replace('-', '', $_GET['sugaCD']);
	$stndDT = str_replace('-', '', $_GET['stndDT']);

	$sql = 'select suga_nm
			,      suga_cost
			  from (
				   select m01_mcode2 as suga_cd
				   ,      m01_scode as temp_cd
				   ,      m01_suga_cont as suga_nm
				   ,      m01_suga_value as suga_cost
				   ,      m01_sdate as from_dt
				   ,      m01_edate as to_dt
					 from m01suga
					where m01_mcode = \''.$code.'\'
					union all
				   select m11_mcode2
				   ,      m11_scode
				   ,      m11_suga_cont
				   ,      m11_suga_value
				   ,      m11_sdate
				   ,      m11_edate
					 from m11suga
					where m11_mcode = \''.$code.'\'
				   ) as t
			 where suga_cd  = \''.$sugaCD.'\'
			   and from_dt <= \''.$stndDT.'\'
			   and to_dt   >= \''.$stndDT.'\'';

	$tmp = $conn->get_array($sql);
	echo 'name='.$tmp['suga_nm'].'&cost='.$tmp['suga_cost'];
	unset($tmp);

	include_once('../inc/_db_close.php');
?>