<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
?>
<table style="width:100%;">
<tr>
<td class="title"><?=$mYear;?>년<?=$mMonth;?>월 공단부담청구서(상세내역)</td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:100%; margin-top:-10px;">
<tr style="height:24px;">
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">수급자</th>
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">주민번호</th>
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">구분</th>
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">급여총액</th>
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">서비스총액</th>
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">공단청구액</th>
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">본인부담액</th>
<th style="width:; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">비고</th>
</tr>
<?
	/*
	$sql = "select m03_name as sugupName"
		 . ",      t13_jumin as sugupJumin"
		 . ",      t13_bonin_yul as boninYul"
		 . ",      case t13_bonin_yul when '1' then '일반대상자(15%)'"
		 . "                          when '2' then '의료급여수급권자(7.5%)'"
		 . "                          when '3' then '본인부담경감대상(7.5%)'"
		 . "                          when '4' then '기초생활수급권자(0%)' else '' end as gubun"
		 . ",      t13_max_amt as maxAmt"
		 . ",      t13_suga_tot4 as sugaAmt"
		 . ",      t13_chung_amt4 as chungAmt"
		 . ",      t13_bonbu_tot4 as boninAmt"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_pay_date = '".$mYear.$mMonth
		 . "'  and t13_type = '2'"
		 . " order by m03_name";
	*/
	$sql = "select m03_name as sugupName
			,      t13_jumin as sugupJumin
			,      t13_bonin_yul as boninYul
			,      concat(STP.m81_name,'(', m03_bonin_yul, ')') as gubun
			,      t13_max_amt as maxAmt
			,      t13_suga_tot4 as sugaAmt
			,      t13_chung_amt4 as chungAmt
			,      t13_bonbu_tot4 as boninAmt
			,      m03_skind as bonin_kind
			  from t13sugupja
			 inner join (
				   select m03_name, m03_jumin, m03_ylvl, m03_skind, m03_bonin_yul, m03_sdate, m03_edate
					 from m03sugupja
					where m03_ccode = '$mCode'
					  and m03_mkind = '$mKind'
					union all
				   select m03_name, m31_jumin, m31_level, m31_kind, m31_bonin_yul, m31_sdate, m31_edate
					 from m31sugupja
					inner join m03sugupja
					   on m31_ccode = m03_ccode
					  and m31_mkind = m03_mkind
					  and m31_jumin = m03_jumin
					where m31_ccode = '$mCode'
					  and m31_mkind = '$mKind'
				   ) as sugupja
				on t13_jumin = m03_jumin
			   and t13_pay_date between left(m03_sdate, 6) and left(m03_edate, 6)
			 inner join m81gubun as STP
				on STP.m81_gbn  = 'STP'
			   and STP.m81_code = m03_skind
			 where t13_ccode    = '$mCode'
			   and t13_mkind    = '$mKind'
			   and t13_pay_date = '$mYear$mMonth'
			   and t13_type     = '2'
			 order by m03_name ";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';

			if ($sugupJumin != $row['sugupJumin']){
				$sugupJumin  = $row['sugupJumin'];
				echo '<td style="text-align:left;">'.$row['sugupName'].'</td>';
				echo '<td style="text-align:left;">'.subStr($row['sugupJumin'], 0, 6).'-'.subStr($row['sugupJumin'], 6, 1).'******</td>';
			}else{
				echo '<td style="border-top:1px solid #ffffff;" colspan="2"></td>';
			}

			echo '<td style="text-align:left;">'.$row['gubun'].'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['maxAmt'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['sugaAmt'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['chungAmt'],'원').'</td>';
			echo '<td style="text-align:right;">'.$myF->numberFormat($row['boninAmt'],'원').'</td>';
			echo '<td style="text-align:center;">';

			if ($row['sugaAmt'] > 0){
				echo '<input type="button" value="출력" class="btnSmall2" onClick=\'printPerson("'.$mCode.'", "'.$mKind.'", "'.$mYear.$mMonth.'", "'.$ed->en($row['sugupJumin']).'", "'.$row['bonin_kind'].'");\'>';
			}
			echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr>';
		echo '<td style="text-align:center;" colspan="7">::검색된 데이타가 없습니다.::</td>';
		echo '</tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>