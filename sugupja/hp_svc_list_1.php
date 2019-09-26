<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	$orgNo  = $_GET['code'];
	$year  = $_GET['year'];
	$month = $_GET['month'];
	$appNo = $_GET['appNo'];
	$svcGbn = $_GET['svcGbn'];
	$printYn = $_GET['printYn'];
	
	//횡성
	if($orgNo == 'drcare'){
		if($year.$month <= '201612'){ 
			$orgNo = '34273000017';
		} 
	}


	if($svcGbn == '200'){
		//요양
		$prtMode = 'CLTLCCNEW2'; 
	}else if($svcGbn == '500'){
		//목욕
		$prtMode = 'CLTLCBNEW';	
	}else {
		//간호
		$prtMode = 'CLTLCNNEW';
	}


	$sql = 'SELECT	jumin, level
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		app_no = \''.$appNo.'\'
			AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$year.$month.'\'
			AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) >= \''.$year.$month.'\'';

	$row = $conn->get_array($sql);

	$jumin = $row['jumin'];
	$level = $row['level'];
	
	#수급자성명
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'0\'
			AND		m03_jumin = \''.$jumin.'\'';

	$name = $conn->get_data($sql);
	
	#센터명
	$sql = 'select m00_store_nm
			  from m00center
			 where m00_mcode = \''.$orgNo.'\'';
	$c_nm = $conn -> get_data($sql); 


	$para  = ' "code":"'.$orgNo.'"';
	$para .= ',"c_cd":"'.$ed->en($jumin).'"';
	$para .= ',"name":"'.$name.'"';
	$para .= ',"level":"'.$level.'"';
	$para .= ',"appNo":"'.$appNo.'"';
	$para .= ',"mode":"cv"';
	$para .= ',"kind":"0"';
	$para .= ',"dt":"'.$year.$month.'"';
	$para .= ',"homepageYn":"Y"';

	if (!empty($para)) $para  = '{'.$para.'}';
	//$para  = (!empty($para)?','.$para:'');
	
	if($svcGbn==200){
		$strName = '요양';
	}else if($svcGbn==500){
		$strName = '목욕';
	}else {
		$strName = '간호';
	}
?>
<div id="divStart"></div><?
if($printYn == 'Y'){ ?>
<div align="center" style="font-size:14pt; margin-bottom:10px;"><?=$year;?>년 <?=$month?>월 서비스 내역</div>	
<? 
} 

?>

<table>
	<colgroup>
	<col width="250px">
	<col width="250px">
	<col width="250px">
	</colgroup>
	<tr>
		<td colspan="12">&nbsp;</td>
	</tr>
	<tr>
		<td style="width:250px;">장기요양기관기호 : <?=$orgNo;?></td>
		<td style="width:250px;">장기요양기관명 : <?=$c_nm;?></td>
		<td style="width:250px;">장기요양등급 : <?=$level;?>등급</td>
		<td style="width:250px;">
		
		<div class='left' style='float:left;  padding-right:5px;'><input type="button" name="Search" id="Search" value="<?=$strName;?> 제공기록지" style="border=1 solid #B3B3B3; background=white; font-size:9pt;height=20px;width=40" onclick='_report_show_pdf(<?=$para?>,"<?=$prtMode;?>");'></div>
		
		</td>
	</tr>
	<tr>
		<td >수급자 성명 : <?=$name;?></td>
		<td >생년월일 : <?=$myF->dateStyle($myF->issToBirthday($jumin),'KOR');?></td>
		<td >장기요양인정번호 : <?=$appNo;?></td>
	</tr>
	<tr>
		<td colspan="12">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="12">&nbsp;</td>
	</tr>
</table>
<table class='list_type'>
	<colgroup>
	<col width="40px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="65px">
	<col width="100px">
	</colgroup>
	<tr>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="3">순번</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" colspan="3">일정관리</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" colspan="4">서비스제공</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" colspan="4">변화상태</th>
	</tr>
	<tr>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2" colspan="2">서비스 시간</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;">총시간</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">정서지원</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">신체활동</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">인지활동</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">가사 및<br style="mso-data-placement:same-cell;">일상생활</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">신체기능</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">식사기능</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">인지기능</th>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center;" rowspan="2">배변변화</th>
	</tr>
	<tr>
		<th style="border:0.5pt solid BLACK; background-color:#EAEAEA;">요양요원</th>
	</tr><?
	$tmpGbn = Array('1'=>'호전', '2'=>'유지', '3'=>'악화');

	$sql = 'SELECT	from_dt
			,		from_tm
			,		to_dt
			,		to_tm
			,		proc_time
			,		mem_nm
			,		dtl_1, dtl_2, dtl_3, dtl_4, dtl_5, dtl_6, dtl_7, dtl_8, dtl_9, dtl_10
			FROM	lg2cv
			WHERE	org_no = \''.$orgNo.'\'
			AND		app_no = \''.$appNo.'\'
			AND		svc_gbn= \''.$svcGbn.'\'
			AND		left(reg_dt, 6) = \''.$year.$month.'\'
			AND		del_flag = \'N\'
			ORDER	BY svc_gbn, name, app_no, from_dt, from_tm, to_dt, to_tm';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td style="text-align:center; border:0.5pt solid BLACK;" rowspan="2"><?=$no;?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">시작시간</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$myF->dateStyle($row['from_dt'],'.');?><br style="mso-data-placement:same-cell;"><?=$myF->timeStyle($row['from_tm']);?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['proc_time'];?>분</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['dtl_1'];?>분</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['dtl_2'];?>분</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['dtl_3'];?>분</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['dtl_4'];?>분</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$tmpGbn[$row['dtl_5']];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$tmpGbn[$row['dtl_6']];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$tmpGbn[$row['dtl_7']];?></td>
			<td style="text-align:left; border:0.5pt solid BLACK;">
				대변실수횟수 : <?=$row['dtl_8'];?>회<br style="mso-data-placement:same-cell;">
				소변실후횟수 : <?=$row['dtl_9'];?>회
			</td>
		</tr>
		<tr>
			<td style="text-align:center; border:0.5pt solid BLACK;">종료시간</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$myF->dateStyle($row['to_dt'],'.');?><br style="mso-data-placement:same-cell;"><?=$myF->timeStyle($row['to_tm']);?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['mem_nm'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">특이사항</td>
			<td style="text-align:left; border:0.5pt solid BLACK;" colspan="7"><?=$row['dtl_10'];?></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();
	?>

</table>



<div id="divLast"></div>
