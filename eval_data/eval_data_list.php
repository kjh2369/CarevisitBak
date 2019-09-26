<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$report_menu    = $_POST['report_menu'];
	$find_report_nm = $_POST['find_report_nm'];
	
	$code	= $_SESSION["userCenterCode"];

	$detail = '<span class="btn_pack m"><button type="button" onclick="eval_detail_view(this);" disabled="true">상세</button>';
	$attach = '<image src="../image/icon_file_1.png" alt=\'첨부파일\'>';

?>

<!--table class="my_table my_border">
	<colgroup>
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<th class="center">리포트 검색</th>
		<td class="last">
			<input name="find_report_nm" type="text" value="<?=$find_report_nm;?>" onkeypress="if(event.keyCode == 13){find_report(document.f.find_report_nm.value);}">
		</td>
		<td class="last"><a href="#" onclick="find_report(document.f.find_report_nm.value);"><img src="../image/btn_rep_search.gif"></a></td>
	</tbody>
</table-->

<script type="text/javascript" src="../js/report.js"></script>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="300px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="*">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">순번</th>
			<th class="head">목차</th>
			<th class="head">상세설명</th>
			<th class="head">첨부파일</th>
			<th class="head">작성</th>
			<th class="head last">비고</th>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">1</td>
			<td class="left" rowspan="2">운영규정</td>
			<td class="center" rowspan="2"><?=$detail?></td>
			<td class="center " ><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report0_1.hwp';" alt="첨부파일"></td>
			<td class="center last" rowspan="2"></td>
			<td class="center other" rowspan="2"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center " ><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report0_2.hwp';" alt="첨부파일"></td>
			<td class="center last" ></td>
			<td class="center other" ></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="4">2</td>
			<td class="left">급여제공지침(응급상황대응지침)</td>
			<td class="center" rowspan="4"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report1.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report1_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">급여제공지침(감염관리지침)</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report1_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">급여제공지침(노인학대,폭력에대한예방및 대응지침)</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report1_3.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">급여제공지침(윤리 및 성희롱 예방지침)</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report1_4.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">3</td>
			<td class="left">인계인수</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report2.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report2.hwp';" alt="첨부파일"></td>
			<td class="center last">
				<a href="#" onclick="_report_list_dtl2('','MEMTAKE','<?=$code;?>','2012평가자료 > 인계인수');"><img src="../image/btn_rep_list.gif" alt="리스트"></a>
			</td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">4</td>
			<td class="left">자격요건</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report3.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="3">5</td>
			<td class="left">간담회(기관간담회)</td>
			<td class="center" rowspan="3"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report4.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report4_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">간담회(직원간담회및결과기록부)</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report4_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">간담회(직원간담회의록)</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report4_3.hwp';" alt="첨부파일"></td>
			<td class="center last">
				<a href="#" onclick="_report_list_dtl2('','MONMR','<?=$code;?>','2012평가자료 > 직원간담회의록');"><img src="../image/btn_rep_list.gif" alt="리스트"></a>
			</td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">6</td>
			<td class="left">근무직원비율의적정(근로자명부)</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report5.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report5.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">7</td>
			<td class="left">건강검진(건강검진관리대장)</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report6.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report6_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">건강검진(건강검진결과통보서)</td>
			<td class="center last"><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report6_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">8</td>
			<td class="left">포상(복지)제도</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report7.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report7.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">9</td>
			<td class="left">적정한급여</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report8.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">10</td>
			<td class="left">근로계약(표준근로계약서)</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report9.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report9_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">근로계약(급여지급명세서)</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report9_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">11</td>
			<td class="left">계약에따른급여</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report10.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">12</td>
			<td class="left">4대보험</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report11.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">13</td>
			<td class="left">신규교육</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report12.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report12.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">14</td>
			<td class="left">급여제공교육</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report13.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report13.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">15</td>
			<td class="left">업무범위교육</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report14.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report14.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">16</td>
			<td class="left">개인정보보호</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report15.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report15.doc';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">개인정보보호법_주요내용_및_이행사항</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report15.ppt';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">17</td>
			<td class="left">질향상계획</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report16.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report16.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">18</td>
			<td class="left">청결한 방</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report17.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report17.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">19</td>
			<td class="left">취사공간</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report18.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">20</td>
			<td class="left">복장</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report19.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">21</td>
			<td class="left">낙상예방</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report20.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report20.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">22</td>
			<td class="left">낙상예방자료</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report21.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report21.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">23</td>
			<td class="left">수급자상담</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report22.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">24</td>
			<td class="left">직원소개</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report23.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">25</td>
			<td class="left">급여계약서제공</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report24.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report24.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">26</td>
			<td class="left">수급자파악</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report25.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report25.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">27</td>
			<td class="left">직원관리</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report26.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report26.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">28</td>
			<td class="left">근무현황표</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report27.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report27.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">29</td>
			<td class="left">시간준수</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report28.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report28.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">30</td>
			<td class="left">명세서발부</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report29.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report29.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">31</td>
			<td class="left">본인부담금</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report30.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report30.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">32</td>
			<td class="left">배상책임보험</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report31.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">33</td>
			<td class="left">재가급여연계</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report32.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report32.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">34</td>
			<td class="left">홈페이지게시 및 수정</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report33.jpg');">상세</button></td>
			<td class="center " ><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">35</td>
			<td class="left">수급자상태욕구평가</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report34.jpg');">상세</button></td>
			<td class="center" rowspan="3"><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report34_35_36.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">36</td>
			<td class="left">욕구반영</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report35.jpg');">상세</button></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">37</td>
			<td class="left">표준장기요양이용계획서</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report36.jpg');">상세</button></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">38</td>
			<td class="left">체계적급여제공기록</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report37.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report37.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">39</td>
			<td class="left">급여제공 후 기록</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report38.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report38.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">40</td>
			<td class="left">급여내용설명</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report39.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report39.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">41</td>
			<td class="left">직원변경(요양보호사변경신고서)</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report40.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report40_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="left">직원변경(업무인수)</td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report40_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="3">42</td>
			<td class="left"  rowspan="3">수분섭취</td>
			<td class="center" rowspan="3"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report41.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report41_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report41_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report41.pptx';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="3">43</td>
			<td class="left" rowspan="3">배변도움자료</td>
			<td class="center" rowspan="3"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report42.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report42_1.doc';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report42_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report42_3.pptx';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="3">44</td>
			<td class="left" rowspan="3">욕창평가</td>
			<td class="center" rowspan="3"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report43.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report43_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report43_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report43_3.ppt';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">45</td>
			<td class="left" rowspan="2">욕창예방자료</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report44.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report44_1.doc';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report44_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">46</td>
			<td class="left">안전한체위변경</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report45.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report45.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">47</td>
			<td class="left" rowspan="2">노인학대예방자료</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report46.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report46_1.ppt';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report46_2.ppt';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">48</td>
			<td class="left">노인학대방지</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report47.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report47.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">49</td>
			<td class="left" rowspan="2">관절구축예방</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report48.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report48_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report48_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">50</td>
			<td class="left" rowspan="2">기능회복훈련</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report49.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report49_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report49_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">51</td>
			<td class="left">구강상태</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report50.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report50.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">52</td>
			<td class="left">신체청결상태</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report51.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center">53</td>
			<td class="left">상태호전</td>
			<td class="center"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report52.jpg');">상세</button></td>
			<td class="center "><?=$attach;?></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center" rowspan="2">54</td>
			<td class="left" rowspan="2">실시 및 결과반영</td>
			<td class="center" rowspan="2"><span class='btn_pack m'><button type='button' onclick="eval_detail_view(this, '../eval_data/img/report53.jpg');">상세</button></td>
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report53_1.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#f2f5ff';" onmouseout="this.style.backgroundColor='#ffffff';">
			<td class="center "><image src="../image/icon_file.png" style="cursor:pointer;" onclick="location.href='./file/report53_2.hwp';" alt="첨부파일"></td>
			<td class="center last"></td>
			<td class="center other"></td>
		</tr>
		<tr>
			<td class="left last bottom" colspan="8">&nbsp;</td>
		</tr>
	</tbody>
</table>
<div id="EVAL_DETAIL" style="position:absolute; top:0; left:0; width:auto; background-color:#ffffff; border:2px solid #0e69b0; display:none;"></div>
<input name="data_cnt" type="hidden" value="<?=$row_count;?>">
<?
	include_once('../inc/_db_close.php');
?>