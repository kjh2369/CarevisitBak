<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION['userCenterCode'];
	$kind	= $_SESSION['userCenterKind'][0];

	if ($code == '32811000079' || $code == '34119000603'){
	}else{
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function go_detail(year, month){
	var f = document.f;

	f.year.value = year;
	f.month.value = month;
	f.action = 'unpaid_auto_detail.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<form name="f" method="post">

<div class="title">본인부담금공제</div>

<table class="my_table my_border" style="border-bottom:none;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">년도</th>
			<th class="head last">월별</th>
		</tr>
	</thead>
	<tbody>
	<?
		/*if ($debug){
			$sql = "SELECT LEFT(t13_pay_date,4) as yy
					,      sum(case when substring(t13_pay_date, 5, 2) = '01' then 1 else 0 end) as m01
					,      sum(case when substring(t13_pay_date, 5, 2) = '02' then 1 else 0 end) as m02
					,      sum(case when substring(t13_pay_date, 5, 2) = '03' then 1 else 0 end) as m03
					,      sum(case when substring(t13_pay_date, 5, 2) = '04' then 1 else 0 end) as m04
					,      sum(case when substring(t13_pay_date, 5, 2) = '05' then 1 else 0 end) as m05
					,      sum(case when substring(t13_pay_date, 5, 2) = '06' then 1 else 0 end) as m06
					,      sum(case when substring(t13_pay_date, 5, 2) = '07' then 1 else 0 end) as m07
					,      sum(case when substring(t13_pay_date, 5, 2) = '08' then 1 else 0 end) as m08
					,      sum(case when substring(t13_pay_date, 5, 2) = '09' then 1 else 0 end) as m09
					,      sum(case when substring(t13_pay_date, 5, 2) = '10' then 1 else 0 end) as m10
					,      sum(case when substring(t13_pay_date, 5, 2) = '11' then 1 else 0 end) as m11
					,      sum(case when substring(t13_pay_date, 5, 2) = '12' then 1 else 0 end) as m12

					,      sum(case when substring(unpaid_yymm, 5, 2) = '01' then 1 else 0 end) as i01
					,      sum(case when substring(unpaid_yymm, 5, 2) = '02' then 1 else 0 end) as i02
					,      sum(case when substring(unpaid_yymm, 5, 2) = '03' then 1 else 0 end) as i03
					,      sum(case when substring(unpaid_yymm, 5, 2) = '04' then 1 else 0 end) as i04
					,      sum(case when substring(unpaid_yymm, 5, 2) = '05' then 1 else 0 end) as i05
					,      sum(case when substring(unpaid_yymm, 5, 2) = '06' then 1 else 0 end) as i06
					,      sum(case when substring(unpaid_yymm, 5, 2) = '07' then 1 else 0 end) as i07
					,      sum(case when substring(unpaid_yymm, 5, 2) = '08' then 1 else 0 end) as i08
					,      sum(case when substring(unpaid_yymm, 5, 2) = '09' then 1 else 0 end) as i09
					,      sum(case when substring(unpaid_yymm, 5, 2) = '10' then 1 else 0 end) as i10
					,      sum(case when substring(unpaid_yymm, 5, 2) = '11' then 1 else 0 end) as i11
					,      sum(case when substring(unpaid_yymm, 5, 2) = '12' then 1 else 0 end) as i12
					  FROM client_family as cf
					 INNER JOIN t13sugupja as conf
						ON t13_ccode = cf.org_no
					   AND t13_mkind = '0'
					   AND t13_jumin = cf.cf_jumin
					   AND t13_type  = '2'
					  LEFT JOIN unpaid_auto_list as unpaid
						ON unpaid.org_no       = t13_ccode
					   AND unpaid.unpaid_yymm  = t13_pay_date
					   AND unpaid.unpaid_jumin = cf.cf_mem_cd
					 WHERE cf.org_no = '1234'
					 GROUP BY LEFT(t13_pay_date,4)
					 ORDER BY yy";
		}else{*/

			$sql = "select left(t13_pay_date, 4) as yy

					,      sum(case when substring(t13_pay_date, 5, 2) = '01' then 1 else 0 end) as m01
					,      sum(case when substring(t13_pay_date, 5, 2) = '02' then 1 else 0 end) as m02
					,      sum(case when substring(t13_pay_date, 5, 2) = '03' then 1 else 0 end) as m03
					,      sum(case when substring(t13_pay_date, 5, 2) = '04' then 1 else 0 end) as m04
					,      sum(case when substring(t13_pay_date, 5, 2) = '05' then 1 else 0 end) as m05
					,      sum(case when substring(t13_pay_date, 5, 2) = '06' then 1 else 0 end) as m06
					,      sum(case when substring(t13_pay_date, 5, 2) = '07' then 1 else 0 end) as m07
					,      sum(case when substring(t13_pay_date, 5, 2) = '08' then 1 else 0 end) as m08
					,      sum(case when substring(t13_pay_date, 5, 2) = '09' then 1 else 0 end) as m09
					,      sum(case when substring(t13_pay_date, 5, 2) = '10' then 1 else 0 end) as m10
					,      sum(case when substring(t13_pay_date, 5, 2) = '11' then 1 else 0 end) as m11
					,      sum(case when substring(t13_pay_date, 5, 2) = '12' then 1 else 0 end) as m12

					,      sum(case when substring(unpaid_yymm, 5, 2) = '01' then 1 else 0 end) as i01
					,      sum(case when substring(unpaid_yymm, 5, 2) = '02' then 1 else 0 end) as i02
					,      sum(case when substring(unpaid_yymm, 5, 2) = '03' then 1 else 0 end) as i03
					,      sum(case when substring(unpaid_yymm, 5, 2) = '04' then 1 else 0 end) as i04
					,      sum(case when substring(unpaid_yymm, 5, 2) = '05' then 1 else 0 end) as i05
					,      sum(case when substring(unpaid_yymm, 5, 2) = '06' then 1 else 0 end) as i06
					,      sum(case when substring(unpaid_yymm, 5, 2) = '07' then 1 else 0 end) as i07
					,      sum(case when substring(unpaid_yymm, 5, 2) = '08' then 1 else 0 end) as i08
					,      sum(case when substring(unpaid_yymm, 5, 2) = '09' then 1 else 0 end) as i09
					,      sum(case when substring(unpaid_yymm, 5, 2) = '10' then 1 else 0 end) as i10
					,      sum(case when substring(unpaid_yymm, 5, 2) = '11' then 1 else 0 end) as i11
					,      sum(case when substring(unpaid_yymm, 5, 2) = '12' then 1 else 0 end) as i12

					  from m02yoyangsa

					 inner join m03sugupja
						on m03_ccode     = m02_ccode
					   and m03_mkind     = m02_mkind
					 /*  and m03_yoyangsa1 = m02_yjumin */

					 inner join t13sugupja
						on t13_ccode = m03_ccode
					   and t13_mkind = m03_mkind
					   and t13_jumin = m03_jumin
					   and t13_type  = '2'

					 inner join t01iljung
						on t01_ccode               = t13_ccode
					   and t01_mkind               = t13_mkind
					   and t01_jumin               = t13_jumin
					   and t01_yoyangsa_id1        = m02_yjumin
					   and t01_del_yn              = 'N'
					   /*and t01_toge_umu          = 'Y'*/




					   and left(t01_sugup_date, 6) = t13_pay_date

					 inner join salary_basic
						on salary_basic.org_no       = t13_ccode
					   and salary_basic.salary_yymm  = t13_pay_date
					   and salary_basic.salary_jumin = m02_yjumin

					  left join unpaid_auto_list
						on unpaid_auto_list.org_no      = t13_ccode
					   and unpaid_auto_list.unpaid_yymm = t13_pay_date
					   and unpaid_auto_list.unpaid_jumin = m02_yjumin

					 where m02_ccode        = '$code'
					   and m02_mkind        = '$kind'
					   and m02_del_yn       = 'N'
					   /*and m02_ygoyong_stat = '1'*/
					   /*and case when ifnull(m02_yfamcare_type, '') = '' then '0' else m02_yfamcare_type end != '0'*/

					 group by left(t13_pay_date, 4)
					 order by yy";


			/*
			$sql = "SELECT	left(t13_pay_date, 4) as yy
					, sum(case when substring(t13_pay_date, 5, 2) = '01' then 1 else 0 end) as m01
					, sum(case when substring(t13_pay_date, 5, 2) = '02' then 1 else 0 end) as m02
					, sum(case when substring(t13_pay_date, 5, 2) = '03' then 1 else 0 end) as m03
					, sum(case when substring(t13_pay_date, 5, 2) = '04' then 1 else 0 end) as m04
					, sum(case when substring(t13_pay_date, 5, 2) = '05' then 1 else 0 end) as m05
					, sum(case when substring(t13_pay_date, 5, 2) = '06' then 1 else 0 end) as m06
					, sum(case when substring(t13_pay_date, 5, 2) = '07' then 1 else 0 end) as m07
					, sum(case when substring(t13_pay_date, 5, 2) = '08' then 1 else 0 end) as m08
					, sum(case when substring(t13_pay_date, 5, 2) = '09' then 1 else 0 end) as m09
					, sum(case when substring(t13_pay_date, 5, 2) = '10' then 1 else 0 end) as m10
					, sum(case when substring(t13_pay_date, 5, 2) = '11' then 1 else 0 end) as m11
					, sum(case when substring(t13_pay_date, 5, 2) = '12' then 1 else 0 end) as m12

					, sum(case when substring(unpaid_yymm, 5, 2) = '01' then 1 else 0 end) as i01
					, sum(case when substring(unpaid_yymm, 5, 2) = '02' then 1 else 0 end) as i02
					, sum(case when substring(unpaid_yymm, 5, 2) = '03' then 1 else 0 end) as i03
					, sum(case when substring(unpaid_yymm, 5, 2) = '04' then 1 else 0 end) as i04
					, sum(case when substring(unpaid_yymm, 5, 2) = '05' then 1 else 0 end) as i05
					, sum(case when substring(unpaid_yymm, 5, 2) = '06' then 1 else 0 end) as i06
					, sum(case when substring(unpaid_yymm, 5, 2) = '07' then 1 else 0 end) as i07
					, sum(case when substring(unpaid_yymm, 5, 2) = '08' then 1 else 0 end) as i08
					, sum(case when substring(unpaid_yymm, 5, 2) = '09' then 1 else 0 end) as i09
					, sum(case when substring(unpaid_yymm, 5, 2) = '10' then 1 else 0 end) as i10
					, sum(case when substring(unpaid_yymm, 5, 2) = '11' then 1 else 0 end) as i11
					, sum(case when substring(unpaid_yymm, 5, 2) = '12' then 1 else 0 end) as i12

					FROM	client_family
					INNER	JOIN	m02yoyangsa
							ON		m02_ccode	= client_family.org_no
							AND		m02_mkind	= '$kind'
							AND		m02_yjumin	= client_family.cf_mem_cd
					INNER	JOIN	m03sugupja
							ON		m03_ccode	= client_family.org_no
							AND		m03_mkind	= '0'
							AND		m03_jumin	= client_family.cf_jumin
					INNER	JOIN	t13sugupja
							ON		t13_ccode		= client_family.org_no
							AND		t13_mkind		= '0'
							AND		t13_jumin		= client_family.cf_jumin
							AND		t13_type		= '2'
							AND		t13_bonbu_tot4	> 0
					INNER	JOIN	salary_basic
							ON		salary_basic.org_no			= client_family.org_no
							AND		salary_basic.salary_yymm	= t13_pay_date
							AND		salary_basic.salary_jumin	= client_family.cf_mem_cd
					INNER	JOIN	t01iljung
							ON		t01_ccode				= client_family.org_no
							AND		t01_mkind				= '0'
							AND		t01_jumin				= client_family.cf_jumin
							AND		t01_yoyangsa_id1		= client_family.cf_mem_cd
							AND		t01_toge_umu			= 'Y'
							AND		LEFT(t01_sugup_date,6)	= t13_pay_date
					LEFT	JOIN	unpaid_auto_list
							ON		unpaid_auto_list.org_no			= client_family.org_no
							AND		unpaid_auto_list.unpaid_yymm	= t13_pay_date
							AND		unpaid_auto_list.unpaid_jumin	= client_family.cf_mem_cd
					WHERE	client_family.org_no = '$code'
					ORDER	BY yy";
			*/


		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch_assoc();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$row['yy'];?>년</td>
				<td class="left last">
				<?
					for($j=1; $j<=12; $j++){
						$mon   = ($j < 10 ? '0' : '').$j;
						$class = 'my_month ';

						if ($row['i'.$mon] > 0){
							// 자동계산 수행
							$class .= 'my_month_y ';
							$color  = 'color:#000000;';
							$text   = 'Y';
						}else{
							if ($row['m'.$mon] > 0){
								// 자동계산 수행가능
								$class .= 'my_month_1 ';
								$color  = 'color:#000000;';
								$text   = 'Y';
							}else{
								// 자동계산 수행불가능
								$class .= 'my_month_2 ';
								$color  = 'color:#aaaaaa;';
								$text   = 'N';
							}
						}

						if ($text == 'Y'){
							$text = '<a href="#" onclick=\'go_detail("'.$row['yy'].'","'.$mon.'")\';>'.$j.'월</a>';
						}else{
							$text = '<span style="'.$color.' cursor:default;">'.$j.'월</span>';
						}

						if ($j == 12){
							$style = 'float:left;';
						}else{
							$style = 'float:left; margin-right:2px;';
						}?>
						<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
					}
				?>
				</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
		<?
			if ($row_count > 0){?>
				<td class="left last bottom" colspan="2"><?=$myF->message($row_count, 'N');?></td><?
			}else{?>
				<td class="center last bottom" colspan="2"><?=$myF->message('nodata', 'N');?></td><?
			}
		?>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">

<input type="hidden" name="year" value="">
<input type="hidden" name="month" value="">

</form>

<?
	}
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>