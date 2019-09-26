<?
	include("../inc/_header.php");

	$_PARAM = $_REQUEST;
	
	$mPopup   = $_PARAM['mPopup']; 
	$mCode    = $_PARAM['mCode'];
	$mKind    = $_PARAM['mKind'];
	$mSugup   = Trim($_PARAM['mSugup']);
	$mKey     = Trim($_PARAM['mKey']);
	$mYear    = $_PARAM['mYear'];
	$mMonth   = $_PARAM['mMonth'];
	$mSvcCode = $_PARAM['mSvcCode'];
	$mPage    = $_PARAM['mPage'];

	if ($mSugup == '' and $mKey != ''){
		$mSugup = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);
	}

	if ($mPopup == 'Y' or $mSugup == ''){
		exit;
	}
?>
<table class="view_type1" style="width:100%; margin-top:0px;">
<tr style="height:24px;">
<th style="width:13%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">요양보호사</th>
<th style="width:25%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">제공서비스</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">횟수</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">수가</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">급여계</th>
<th style="width:32%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">비고</th>
</tr>
<?
	$sql = "select m02_yname"
		 . ",      suga_cont"
		 . ",      sum(case when length(ifnull(t01_conf_fmtime,'')) != 4"
		 . "                  or length(ifnull(t01_conf_totime,'')) != 4 then 0 else 1 end) as suag_count"
		 . ",      t01_conf_suga_value"
		 . ",      sum(case when length(ifnull(t01_conf_fmtime,'')) != 4"
		 . "                  or length(ifnull(t01_conf_totime,'')) != 4 then 0 else t01_conf_suga_value end) as total_suga_value"
		 . "  from t01iljung"
		 . " inner join m02yoyangsa"
		 . "    on m02_ccode  = t01_ccode"
		 . "   and m02_mkind  = t01_mkind"
		 . "   and m02_yjumin = t01_yoyangsa_id1"
		 . " inner join ("
		 . "       select m01_mcode as suga_mcode"
		 . "       ,      m01_mcode2 as suga_mcode2"
		 . "       ,      m01_suga_cont as suga_cont"
		 . "       ,      m01_sdate as suga_sdate"
		 . "       ,      m01_edate as suga_edate"
		 . "         from m01suga"
		 . "        where m01_mcode = '".$mCode
		 . "'       union all"
		 . "       select m11_mcode as suga_mcode"
		 . "       ,      m11_mcode2 as suga_mcode2"
		 . "       ,      m11_suga_cont as suga_cont"
		 . "       ,      m11_sdate as suga_sdate"
		 . "       ,      m11_edate as suga_edate"
		 . "         from m11suga"
		 . "        where m11_mcode = '".$mCode
		 . "'      ) as suga_table"
		 . "    on t01_ccode = suga_mcode"
		 . "   and t01_conf_suga_code = suga_mcode2"
		 . "   and t01_conf_date between suga_sdate and suga_edate"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mSugup
		 . "'  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
		 . "'  and t01_del_yn = 'N'"
		 . "   and length(ifnull(t01_conf_fmtime,'')) = 4"
		 . "   and length(ifnull(t01_conf_totime,'')) = 4"
		 . "   and t01_conf_soyotime >= 30"
		 . " group by m02_yname, suga_cont, t01_conf_suga_value"
		 . " order by m02_yname";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	$totalValue = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$totalValue += $row['total_suga_value'];
		echo '<tr>';
		echo '<td>'.$row['m02_yname'].'</td>';
		echo '<td>'.$row['suga_cont'].'</td>';
		echo '<td style="text-align:right;">'.$row['suag_count'].'</td>';
		echo '<td style="text-align:right;">'.number_format($row['t01_conf_suga_value']).'</td>';
		echo '<td style="text-align:right;">'.number_format($row['total_suga_value']).'</td>';
		echo '<td></td>';
		echo '</tr>';
	}

	$conn->row_free();

	echo '<tr>';
	echo '<td colspan="3"></td>';
	echo '<td style="text-align:right; font-weight:bold;">계</td>';
	echo '<td style="text-align:right; font-weight:bold;">'.number_format($totalValue).'</td>';
	echo '<td></td>';
	echo '</tr>';
?>
</table>
<?
	include("../inc/_footer.php");
?>