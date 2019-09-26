

<?

	$mode = $_POST['mode'];
	$year = substr($find_ym, 0,4);
	$month = substr($find_ym, 5,2);

	#엑셀출력시 헤드부분	
	if($mode == 'detail'){
		$title = $year.'년'.$month.'월 수입/지출현황';
		$col1 = 4;
		$col2 = 7;
	}

	$r_dt = date('Y.m.d',mktime());
 
	$head = 'style=\'background-color:#dbf8da; font-family:굴림; font-weight:bold;\'';
	$tot_css_c = 'style=\'background-color:#efefef; text-align:center; font-family:굴림; font-weight:bold;\'';
	$tot_css_r = 'style=\'background-color:#efefef; font-family:굴림; font-weight:bold;\'';
	$css = 'style=\'font-family:굴림;\'';

?>
<div align="center" style="font-size:15pt; font-family:굴림; font-weight:bold;"><?=$title?></div>
<div>
	<table>
		<tr>
			<td colspan="<?=$col1?>" style="text-align:left; font-size:12pt; font-family:굴림; font-weight:bold;">센터명 : <?=$center_nm?></td>
			<td colspan="<?=$col2?>" style="text-align:right; font-size:12pt; font-family:굴림; font-weight:bold;">일자 : <?=$r_dt?></td>
		</tr>
	</table>
</div>