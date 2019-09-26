<?
	$info_picture = $mst[$basic_kind]['m02_picture'];
	$info_mem_no  = $mst[$basic_kind]['m02_mem_no'];
	$info_mem_cd  = $member['code'];
	$info_mem_nm  = $mst[$basic_kind]["m02_yname"];
	$info_mem_ssn = $jumin;
	$info_join_dt = $mst[$basic_kind]["m02_yipsail"];
	$info_period  = $myF->dateDiff('d', $myF->dateStyle($mst[$basic_kind]["m02_yipsail"]), date('Y-m-d', mktime()));

	if ($info_period > 31){
		$info_period  = $myF->dateDiff('m', $myF->dateStyle($mst[$basic_kind]["m02_yipsail"]), date('Y-m-d', mktime()));
		$info_period .= '개월';
	}else{
		$info_period .= '일';
	}

	$info_dept_cd = $mst[$basic_kind]['m02_dept_cd'];

	$sql = "select dept_nm
			  from dept
			 where org_no   = '$code'
			   and dept_cd  = '$info_dept_cd'
			   and del_flag = 'N'
			 order by order_seq";

	$info_dept_nm = $conn->get_data($sql);

	$info_job_cd = $mst[$basic_kind]['m02_yjikjong'];

	$sql = "select sub_nm
			  from job_kind
			 where sub_cd = '$info_job_cd'";

	$info_job_nm = $conn->get_data($sql);

	$info_pay_step = $mst[$basic_kind]['m02_pay_step'];

	$info_mobile = $mst[$basic_kind]["m02_ytel"];
	$info_phone  = $mst[$basic_kind]["m02_ytel2"];
	$info_email  = $mst[$basic_kind]["m02_email"];
?>
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
				<div id="infoPicture" style="width:90px; height:120px;"><img id="img_info_picture" src="../mem_picture/<?=$info_picture;?>" style="border:1px solid #000;" width="90" height="120">
				<input name="picture_nm" type="hidden" value="<?=$info_picture;?>">
			</td>
			<th>사번</th>
			<td class="left"><?=$myF->formatString($info_mem_no, '####-####');?></td>
			<th>사용자ID</th>
			<td class="left"><?=$info_mem_cd;?></td>
			<th rowspan="3">소속</th>
			<th>부서</th>
			<td class="left last"><?=$info_dept_nm;?></td>
		</tr>
		<tr>
			<th>성명</th>
			<td class="left"><?=$info_mem_nm;?></td>
			<th>주민번호</th>
			<td class="left"><?=$myF->issStyle($info_mem_ssn);?></td>
			<th>직무</th>
			<td class="left last"><?=$info_job_nm;?></td>
		</tr>
		<tr>
			<th>입사일자</th>
			<td class="left"><?=$myF->dateStyle($info_join_dt);?></td>
			<th>근무기간</th>
			<td class="left"><?=$info_period;?></td>
			<th>호봉</th>
			<td class="left last"><?=$info_pay_step;?></td>
		</tr>
		<tr>
			<td rowspan="3" colspan="4"></td>
			<th rowspan="3">연락처</th>
			<th>핸드폰</th>
			<td class="left last"><?=$myF->phoneStyle($info_mobile);?></td>
		</tr>
		<tr>
			<th>유선</th>
			<td class="left last"><?=$myF->phoneStyle($info_phone);?></td>
		</tr>
		<tr>
			<td class="center top" rowspan="1">
				<!--div style="width:50px; height:18px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="mem_picture" id="file" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin:0;" onchange="__showLocalImage(this,'infoPicture');"></div-->
			</td>
			<th>e-mail</th>
			<td class="left last"><?=$info_email;?></td>
		</tr>
	</tbody>
</table>