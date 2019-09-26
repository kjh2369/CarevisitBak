<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="105px">
		<col width="95px">
		<col width="110px">
		<col width="70px">
		<col width="110px">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center bottom" style="padding-top:5px;" rowspan="5">
				<div id="stressPicture" style="width:90px; height:120px;"><img id="img_stress_picture" src="../mem_picture/<?=$mst[$basic_kind]['m02_picture'];?>" style="border:1px solid #000;" width="90" height="120">
				<input name="picture_nm" type="hidden" value="<?=$mst[$basic_kind]['m02_picture'];?>">
			</td>
			<th>사번</th>
			<td></td>
			<th>사용자ID</th>
			<td></td>
			<th rowspan="3">소속</th>
			<th>부서</th>
			<td class="last"></td>
		</tr>
		<tr>
			<th>성명</th>
			<td></td>
			<th>주민번호</th>
			<td></td>
			<th>직무</th>
			<td class="last"></td>
		</tr>
		<tr>
			<th>입사일자</th>
			<td></td>
			<th>근무기간</th>
			<td></td>
			<th>호봉</th>
			<td class="last"></td>
		</tr>
		<tr>
			<td rowspan="3" colspan="4"></td>
			<th rowspan="3">연락처</th>
			<th>핸드폰</th>
			<td class="last"></td>
		</tr>
		<tr>
			<th>유선</th>
			<td class="last"></td>
		</tr>
		<tr>
			<td class="center top" rowspan="1">
				<!--div style="width:50px; height:18px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="mem_picture" id="file" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin:0;" onchange="__showLocalImage(this,'stressPicture');"></div-->
			</td>
			<th>e-mail</th>
			<td class="last"></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="100px">
		<col width="70px">
		<col width="450px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="last" colspan="5">상담이력</th>
			<th class="last" style="text-align:right;"><span style="margin-right:5px;"><a href="#" onclick="alert('준비중입니다.');">작성</a></span></th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">상담일자</th>
			<th class="head">상담자</th>
			<th class="head">상담유형</th>
			<th class="head">처리결과</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>