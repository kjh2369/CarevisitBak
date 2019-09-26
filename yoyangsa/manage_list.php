<?
	include("../inc/_header.php");
	include("../inc/_http_referer.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$menu = $_POST['mIndex'];
	$tab  = $_POST['mTab'];
	$mCode = $_POST["mCode"]; //기관코드
	$mKind = $_POST["mKind"]; //기관분류코드
	$mEmploy = $_POST["mEmploy"]; //고용상태
	$mYoy = $_POST['mYoy'];
	$beforeYM = date("Ym",strtotime("+1 month"));	
	
	include_once('manager_head.php');

	$sql = "select m02_yname as name
			,      m02_yjumin as jumin
			,      m02_ygoyong_kind
			,      m02_key
			,      m02_ygoyong_stat
			,     (select count(t01_jumin)
				     from t01iljung
				    where t01_ccode = m02_ccode
				      and t01_mkind = m02_mkind
				      and t01_sugup_date like '$beforeYM%'
				      and m02_yjumin in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
				      and t01_del_yn = 'N') as iljungCount
			,     (select datediff(date_add(date_format(concat(ifnull(max(r260_date),''),'000000'), '%Y-%m-%d'), interval 2 month), date_format(now(), '%Y-%m-%d'))
					 from r260talk
					where r260_ccode = m02_ccode
					  and r260_mkind = m02_mkind
					  and r260_yoyangsa = m02_yjumin) as talkDays
			,     (select datediff(date_add(date_format(concat(ifnull(max(r270_date),''),'000000'), '%Y-%m-%d'), interval 2 month), date_format(now(), '%Y-%m-%d'))
					 from r270test
					where r270_ccode = m02_ccode
					  and r270_mkind = m02_mkind
					  and r270_yoy_code = m02_yjumin) as testDays
			  from m02yoyangsa
			 where m02_ccode = '$mCode'
			   and m02_mkind = '$mKind'";
	if ($mEmploy != ""){
		$sql .= " and  m02_ygoyong_stat = '$mEmploy'";
	}
	$sql .= " order by m02_yname";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	ob_start();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$name = $row["name"];
			$namtTitle = $row["name"];
			$jumin = subStr($row["jumin"], 0, 6)."-".subStr($row["jumin"], 6, 1)."******";

			echo "
				<tr>
					<td class='left' title='$namtTitle'>$name</td>
					";

			//근로계약서 출력
			if($row['m02_ygoyong_kind'] == "1"){
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_92.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">출력</a></td>";
			}else if ($row['m02_ygoyong_kind'] == "2"){
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_91.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">출력</a></td>";
			}else {
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_90.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">출력</a></td>";
			}

			// 개인정보보호 동의서
			echo "<td style='padding:0; margin:0; text-align:center; padding-top:2px;'><a type='button' onFocus='this.blur();' onClick='';>-</a></td>";

			//익월서비스일정표
			if ($row['iljungCount'] > 0){
				//echo "<td style='padding:0; margin:0; text-align:center; padding-top:2px;'><a href='#' onFocus='this.blur();' onClick=\"window.open('../work/cal_show.php?code=$mCode&kind=$mKind&year=".subStr($beforeYM, 0, 4)."&month=".subStr($beforeYM, 4, 2)."&sugupja=".$ed->en($row["jumin"])."&type=y', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">보기</a></td>";
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"serviceCalendarShow('".$mCode."', '".$mKind."', '".subStr($beforeYM, 0, 4)."', '".subStr($beforeYM, 4, 2)."', '".$ed->en($row["jumin"])."', 'y', 'y', 'pdf', 'y'); return false;\">보기</a></td>";
			}else{
				if($row["m02_ygoyong_stat"] == "1"){
					echo "<td class='center'><a href='#' onFocus='this.blur();' onClick='stat_chk();return false;'>미등록</a></td>";
					//echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../iljung/iljung.php?menuIndex=work&menuSeq=1&manager=true&gubun=reg', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');return false;\">미등록</td>";
				}else {
					echo "<td class='center'><a href='#' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>";
				}
			}

			//상담일지(격월주기)
			if($row['talkDays'] > 0){
			?>
				<td id="td_6_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_showPopup(document.getElementById('td_6_<?=$i;?>'),'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
			<?
			}else {
				if($row["m02_ygoyong_stat"] == "1"){
				?>
					<td id="td_6_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_showPopup(document.getElementById('td_6_<?=$i;?>'),'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
				<?
				}else {
				?>
					<td style='padding:0; margin:0; text-align:center; padding-top:2px;'><a href='#' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>
				<?
				}
			}

			//직무평가 및 만족도조사
			if($row['testDays'] > 0){
			?>
				<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_showPopup(document.getElementById('td_7_<?=$i;?>'),'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;" >작성완료</a></td>
			<?
			}else {
				if($row["m02_ygoyong_stat"] == "1"){
				?>
					<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_showPopup(document.getElementById('td_7_<?=$i;?>'),'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
				<?
				}else {
				?>
					<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>
				<?
				}
			}

			echo "
					<td class='center'><a type='button' onFocus='this.blur();' onClick=''>-</a></td>
					<td class='center'><a type='button' onFocus='this.blur();' onClick=''>-</a></td>
					<td class='center'><a type='button' onFocus='this.blur();' onClick=''>-</a></td>
					<td class='center'><a type='button' onFocus='this.blur();' onClick=''>-</a></td>
					<td class='center'><a type='button' onFocus='this.blur();' onClick=''>-</a></td>
					<td class='other'>&nbsp;</td>
				</tr>
				 ";
		}
	}else{
		echo '<tr><td class="center" colspan="13">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
	$list = ob_get_contents();
	ob_end_clean();
	echo $list;
?>
</table>

<div id="idTalkPopup" style="z-index:11; left:0; top:0; width:220px; position:absolute; color:#000000; display:none;">
</div>