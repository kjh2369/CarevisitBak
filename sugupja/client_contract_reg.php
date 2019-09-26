<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_myImage.php");
	include_once('../inc/_ed.php');


	$orgNo	= $_SESSION['userCenterCode'];

	$code = $_POST['code'];
	$ssn  = $ed->de($_POST['ssn']);
	$seq = $_POST['seq'];
	$kind = $_POST['kind'];
	//$svc_kind = $_POST['svc_kind'];


	$sql = 'SELECT	m03_key
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_jumin = \''.$ssn.'\'';

	$tmpKey = $conn->get_data($sql);

	$TGSign = '../sign/sign/client/'.$orgNo.'/CT_TG_'.$tmpKey.'_0_'.$seq.'.jpg';
	$PTSign = '../sign/sign/client/'.$orgNo.'/CT_PT_'.$tmpKey.'_0_'.$seq.'.jpg';

	if (is_file($TGSign)){
		$tmpIf = GetImageSize($TGSign);
		$size = $myImage->getImgSize(90, 45, $tmpIf[0], $tmpIf[1]);
		$w1 = $size['w'];
		$h1 = $size['h'];
	}

	if (is_file($PTSign)){
		$tmpIf = GetImageSize($PTSign);
		$size = $myImage->getImgSize(90, 45, $tmpIf[0], $tmpIf[1]);
		$w2 = $size['w'];
		$h2 = $size['h'];
	}



	$sql = 'select seq
			,	   reg_dt
			,	   svc_seq
			,	   from_dt
			,	   to_dt
			,      use_yoil1
			,      from_time1
			,      to_time1
			,      use_yoil2
			,      from_time2
			,      to_time2
			,      use_yoil3
			,      from_time3
			,      to_time3
			,	   bath_weekly
			,	   from_time
			,	   to_time
			,      use_yoil1_nurse
			,      from_time1_nurse
			,      to_time1_nurse
			,      use_yoil2_nurse
			,      from_time2_nurse
			,      to_time2_nurse
			,	   use_type
			,	   pay_day1
			,	   pay_day2
			,	   pay_day3
			,	   other_text1
			,	   other_text2
			  from client_contract
			 where org_no   = \''.$code.'\'
			   and svc_cd   = \''.$kind.'\'
			   and seq      = \''.$seq.'\'
			   and jumin    = \''.$ssn.'\'
			   and del_flag = \'N\'';

	$contract = $conn -> get_array($sql);

	$sql =  ' select from_dt
			  ,		 to_dt
				from client_his_svc
			   where org_no = \''.$code.'\'
			     and jumin  = \''.$ssn.'\'
				 and seq    = \''.$contract['svc_seq'].'\'';
	$svc = $conn->get_array($sql);

	$seq = ($contract['seq'] != 0 ? $contract['seq'] : 0);

	$reg_dt = $contract['reg_dt'] != '' ? $contract['reg_dt'] : date('Y-m-d', mktime());
	$from_dt = ($contract['from_dt'] != '' ? $contract['from_dt'] : $svc['from_dt']);
	$to_dt = ($contract['to_dt'] != '' ? $contract['to_dt'] : $svc['to_dt']);
?>
<table class="my_table my_border_blue" style="width:100%; margin-bottom:5px;">
	<colgroup>
		<col width="100px">
		<col width="220px">
		<col width="100px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center" rowspan="2">작성일자</th>
			<th class="center" rowspan="2">계약기간</th>
			<th class="head bold" colspan="2">서명관리</th>
			<th class="head" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">이용자</th>
			<th class="head">대리인(보호자)</th>
		</tr>
		<tr>
			<td class="center" style="height:50px;">
				<input name="reg_dt" type="text" value="<?=$reg_dt;?>" class="date">
			</td>
			
			<td class="left" style="height:50px;">
				<?
					echo '<span class=\'btn_pack find\' style=\'margin-top:13px;\' onClick=\'find_svc_date("'.$code.'", "'.$ed->en($ssn).'",["from_dt","to_dt","svc_key"]);\'></span>';
				?>
				<input id="from_dt" name="from_dt" type="text" value="<?=$from_dt;?>" class="date" style="margin-top:14px;" /> ~
				<input id="to_dt" name="to_dt" type="text" value="<?=$to_dt;?>"  class="date" />
				<input id="svc_key" name="svc_key" type="hidden" value="<?=$contract['svc_seq'];?>" />
			</td>
			<td rowspan="2" style="height:50px;" id="ID_CELL_SIGN_TG" onclick="lfSetSign(this,'TG','<?=$seq;?>');" class="center"><?if(is_file($TGSign)){?><img src="<?=$TGSign;?>" style="width:<?=$w1;?>; height:<?=$h1;?>;"><?}?></td>
			<td rowspan="2" style="height:50px;" id="ID_CELL_SIGN_PT" onclick="lfSetSign(this,'PT','<?=$seq;?>');" class="center"><?if(is_file($PTSign)){?><img src="<?=$PTSign;?>" style="width:<?=$w2;?>; height:<?=$h2;?>;"><?}?></td>
			<td rowspan="2" style="height:50px;"></td>
			<!--th class="head center">케어구분</th>
			<td class="left last">
				<input name="svc_kind" type="radio" class="radio" value="200" onclick="svc_gbn('200','500');" <? if($contract['svc_kind'] == '200'){echo 'checked';} ?>>방문요양
				<input name="svc_kind" type="radio" class="radio" value="500" onclick="svc_gbn('500','200');" <? if($contract['svc_kind'] == '500'){echo 'checked';} ?>>방문목욕
			</td-->
		</tr>
	</tbody>
</table>

<table id="svc_200" class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="300px">
		<col width="120px">
		<col width="100px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head bold" colspan="3">방문요양급여 이용시간</th>
			<th class="head" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">구분</th>
			<th class="head">이용일</th>
			<th class="head">이용시간</th>
			
		</tr>
		<tr>
			<th class="head">이용시간1</th>
			<td class="left">
				<input name="use_yoil1_1"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1'][0]  == 'Y'){echo 'checked';} ?> onclick="check_umu();">월
				<input name="use_yoil1_2"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1'][1]  == 'Y'){echo 'checked';} ?> onclick="check_umu();">화
				<input name="use_yoil1_3" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1'][2] == 'Y'){echo 'checked';} ?>>수
				<input name="use_yoil1_4" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1'][3] == 'Y'){echo 'checked';} ?> onclick="check_umu();">목
				<input name="use_yoil1_5" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1'][4] == 'Y'){echo 'checked';} ?> onclick="check_umu();">금
				<input name="use_yoil1_6"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1'][5]  == 'Y'){echo 'checked';} ?> onclick="check_umu();"><font color="blue">토</font>
				<input name="use_yoil1_7"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1'][6]  == 'Y'){echo 'checked';} ?> onclick="check_umu();"><font color="red">일</font>
			</td>
			<td class="center">
				<input name="from_time1" type="text" class="no_string" value="<?=$myF->timeStyle($contract['from_time1'])?>" alt="time" onclick="chk_yoil('200','1');"/> ~
				<input name="to_time1" type="text" class="no_string" value="<?=$myF->timeStyle($contract['to_time1'])?>" alt="time" onclick="chk_yoil('200','1');" />
			</td>
		</tr>
		<tr>
			<th class="head">이용시간2</th>
			<td class="left">
				<input id="use_yoil2_1" name="use_yoil2_1"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('200','3', this);" <? if($contract['use_yoil2'][0]  == 'Y'){echo 'checked';} ?>  />월
				<input name="use_yoil2_2"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('200','3', this);" <? if($contract['use_yoil2'][1]  == 'Y'){echo 'checked';} ?>  />화
				<input name="use_yoil2_3" type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('200','3', this);" <? if($contract['use_yoil2'][2] == 'Y'){echo 'checked';} ?>  />수
				<input name="use_yoil2_4" type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('200','3', this);" <? if($contract['use_yoil2'][3] == 'Y'){echo 'checked';} ?>  />목
				<input name="use_yoil2_5" type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('200','3', this);" <? if($contract['use_yoil2'][4] == 'Y'){echo 'checked';} ?>  />금
				<input name="use_yoil2_6"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('200','3', this);" <? if($contract['use_yoil2'][5]  == 'Y'){echo 'checked';} ?>  /><font color="blue">토</font>
				<input name="use_yoil2_7"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('200','3', this);" <? if($contract['use_yoil2'][6]  == 'Y'){echo 'checked';} ?>  /><font color="red">일</font>
			</td>
			<td class="center">
				<input name="from_time2" type="text" class="no_string" value="<?=$myF->timeStyle($contract['from_time2'])?>" alt="time" onclick="chk_yoil('200','2');"> ~
				<input name="to_time2" type="text" class="no_string" value="<?=$myF->timeStyle($contract['to_time2'])?>" alt="time" onclick="chk_yoil('200','2');">
			</td>
		</tr>
		<tr>
			<th class="head">별첨(요양)</th>
			<td colspan="4"><textarea id="otherTxt_1" name="otherTxt_1" style="width:100%; height:60px;"><?=stripslashes($contract['other_text1']);?></textarea></td>
		</tr>
	</tbody>
</table>
<table id="svc_500" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="80px" >
		<col width="200px" >
		<col width="120px" >
		<col width="230px" >
		<col width="150px" >
	</colgroup>
	<thead>
		<tr>
			<th class="head bold" colspan="5">방문목욕급여 이용시간</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="head" colspan="2">이용일</th>
			<th class="head">이용시간</th>
			<th class="head">이용방법/월</th>
			<th class="head">비고</th>
		</tr>
		<tr>
			<td class="left"  colspan="2">
				<input name="use_yoil3_1"  type="checkbox" class="checkbox" value="Y" <? if($contract['bath_weekly'][0]  == 'Y'){echo 'checked';} ?> onclick="check_only();">월
				<input name="use_yoil3_2"  type="checkbox" class="checkbox" value="Y" <? if($contract['bath_weekly'][1]  == 'Y'){echo 'checked';} ?> onclick="check_only();">화
				<input name="use_yoil3_3" type="checkbox" class="checkbox" value="Y" <? if($contract['bath_weekly'][2] == 'Y'){echo 'checked';} ?> onclick="check_only();">수
				<input name="use_yoil3_4" type="checkbox" class="checkbox" value="Y" <? if($contract['bath_weekly'][3] == 'Y'){echo 'checked';} ?> onclick="check_only();">목
				<input name="use_yoil3_5" type="checkbox" class="checkbox" value="Y" <? if($contract['bath_weekly'][4] == 'Y'){echo 'checked';} ?> onclick="check_only();">금
				<input name="use_yoil3_6"  type="checkbox" class="checkbox" value="Y" <? if($contract['bath_weekly'][5]  == 'Y'){echo 'checked';} ?> onclick="check_only();"><font color="blue">토</font>
				<input name="use_yoil3_7"  type="checkbox" class="checkbox" value="일" <? if($contract['bath_weekly'][6]  == 'Y'){echo 'checked';} ?> onclick="check_only();"><font color="red">일</font>
			</td>
			<td class="center" >
				<input name="from_time" type="text" class="no_string" value="<?=$myF->timeStyle($contract['from_time'])?>" alt="time" onclick="chk_yoil('500','1');" > ~
				<input name="to_time" type="text" class="no_string" value="<?=$myF->timeStyle($contract['to_time'])?>" alt="time" onclick="chk_yoil('500','2');" >
			</td>
			<td class="left" >
				<input name="use_type" type="radio" class="radio" value="1" <? if($contract['use_type'] == '1'){echo 'checked';} ?>>매주
				<input name="use_type" type="radio" class="radio" value="2" <? if($contract['use_type'] == '2'){echo 'checked';} ?>>격주
				<input name="use_type" type="radio" class="radio" value="3" <? if($contract['use_type'] == '3'){echo 'checked';} ?>>매월
				<input name="use_type" type="radio" class="radio" value="4" <? if($contract['use_type'] == '4'){echo 'checked';} ?>>월3회
			</td>
			<td class="last" ></td>
		</tr>
		<tr>
			<th class="head">별첨(목욕)</th>
			<td colspan="4"><textarea id="otherTxt_2" name="otherTxt_2" style="width:100%; height:60px;"><?=stripslashes($contract['other_text2']);?></textarea></td>
		</tr>
	</tbody>
</table>
<table id="svc_200" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="80px">
		<col width="300px">
		<col width="120px">
		<col width="100px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head bold" colspan="3">방문간호급여 이용시간</th>
			<th class="head" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">구분</th>
			<th class="head">이용일</th>
			<th class="head">이용시간</th>
		</tr>
		<tr>
			<th class="head">이용시간1</th>
			<td class="left">
				<input name="use_yoil1_nurse1"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1_nurse'][0]  == 'Y'){echo 'checked';} ?> onclick="check_umu();">월
				<input name="use_yoil1_nurse2"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1_nurse'][1]  == 'Y'){echo 'checked';} ?> onclick="check_umu();">화
				<input name="use_yoil1_nurse3" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1_nurse'][2] == 'Y'){echo 'checked';} ?>>수
				<input name="use_yoil1_nurse4" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1_nurse'][3] == 'Y'){echo 'checked';} ?> onclick="check_umu();">목
				<input name="use_yoil1_nurse5" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1_nurse'][4] == 'Y'){echo 'checked';} ?> onclick="check_umu();">금
				<input name="use_yoil1_nurse6"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1_nurse'][5]  == 'Y'){echo 'checked';} ?> onclick="check_umu();"><font color="blue">토</font>
				<input name="use_yoil1_nurse7"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil1_nurse'][6]  == 'Y'){echo 'checked';} ?> onclick="check_umu();"><font color="red">일</font>
			</td>
			<td class="center">
				<input name="from_time1_nurse" type="text" class="no_string" value="<?=$myF->timeStyle($contract['from_time1_nurse'])?>" alt="time" onclick="chk_yoil('800','1');"/> ~
				<input name="to_time1_nurse" type="text" class="no_string" value="<?=$myF->timeStyle($contract['to_time1_nurse'])?>" alt="time" onclick="chk_yoil('800','1');" />
			</td>
		</tr>
		<tr>
			<th class="head">이용시간2</th>
			<td class="left">
				<input id="use_yoil2_nurse1"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('800','3', this);" <? if($contract['use_yoil2_nurse'][0]  == 'Y'){echo 'checked';} ?>  />월
				<input name="use_yoil2_nurse2"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('800','3', this);" <? if($contract['use_yoil2_nurse'][1]  == 'Y'){echo 'checked';} ?>  />화
				<input name="use_yoil2_nurse3" type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('800','3', this);" <? if($contract['use_yoil2_nurse'][2] == 'Y'){echo 'checked';} ?>  />수
				<input name="use_yoil2_nurse4" type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('800','3', this);" <? if($contract['use_yoil2_nurse'][3] == 'Y'){echo 'checked';} ?>  />목
				<input name="use_yoil2_nurse5" type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('800','3', this);" <? if($contract['use_yoil2_nurse'][4] == 'Y'){echo 'checked';} ?>  />금
				<input name="use_yoil2_nurse6"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('800','3', this);" <? if($contract['use_yoil2_nurse'][5]  == 'Y'){echo 'checked';} ?>  /><font color="blue">토</font>
				<input name="use_yoil2_nurse7"  type="checkbox" class="checkbox" value="Y" onclick="chk_yoil('800','3', this);" <? if($contract['use_yoil2_nurse'][6]  == 'Y'){echo 'checked';} ?>  /><font color="red">일</font>
			</td>
			<td class="center">
				<input name="from_time2_nurse" type="text" class="no_string" value="<?=$myF->timeStyle($contract['from_time2_nurse'])?>" alt="time" onclick="chk_yoil('800','2');"> ~
				<input name="to_time2_nurse" type="text" class="no_string" value="<?=$myF->timeStyle($contract['to_time2_nurse'])?>" alt="time" onclick="chk_yoil('800','2');">
			</td>
		</tr>
		<!--tr>
			<th class="head">별첨(간호)</th>
			<td colspan="6"><textarea id="otherTxt_1" name="otherTxt_1" style="width:100%; height:60px;"><?=stripslashes($contract['other_text1']);?></textarea></td>
		</tr-->
	</tbody>
</table><?


if($gDayAndNight){ ?>
	<table id="svc_night" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
		<colgroup>
			<col width="10%" >
			<col width="38%" >
			<col width="16%" >
			<col width="*" >
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="4">주야간보호급여 이용시간</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="head">구분</th>
				<th class="head">이용일</th>
				<th class="head">이용시간</th>
				<th class="head">비고</th>
			</tr>
			<tr>
				<th class="head">이용시간</th>
				<td class="left">
					<input name="use_yoil4_1"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil3'][0]  == 'Y'){echo 'checked';} ?> onclick="chk_yoil('900','4', this);">월
					<input name="use_yoil4_2"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil3'][1]  == 'Y'){echo 'checked';} ?> onclick="chk_yoil('900','4', this);">화
					<input name="use_yoil4_3" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil3'][2] == 'Y'){echo 'checked';} ?>  onclick="chk_yoil('900','4', this);">수
					<input name="use_yoil4_4" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil3'][3] == 'Y'){echo 'checked';} ?> onclick="chk_yoil('900','4', this);">목
					<input name="use_yoil4_5" type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil3'][4] == 'Y'){echo 'checked';} ?> onclick="chk_yoil('900','4', this);">금
					<input name="use_yoil4_6"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil3'][5]  == 'Y'){echo 'checked';} ?> onclick="chk_yoil('900','4', this);"><font color="blue">토</font>
					<input name="use_yoil4_7"  type="checkbox" class="checkbox" value="Y" <? if($contract['use_yoil3'][6]  == 'Y'){echo 'checked';} ?> onclick="chk_yoil('900','4', this);"><font color="red">일</font>
				</td>
				<td class="center">
					<input name="from_time4" type="text" class="no_string" value="<?=$myF->timeStyle($contract['from_time3'])?>" alt="time" onclick="chk_yoil('900','4');"/> ~
					<input name="to_time4" type="text" class="no_string" value="<?=$myF->timeStyle($contract['to_time3'])?>" alt="time" onclick="chk_yoil('900','4');" />
				</td>
				<td></td>
			</tr>
		</tbody>
	</table><?
} ?>
<table id="svc_500" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="10%">
		<col width="*" >
	</colgroup>
	<tbody>
		<tr>
			<th>이용료납부</th>
			<td>매월<input name="pay_day1" type="text" style="width:30px; text-align:right;" maxlength="2" value="<?=$contract['pay_day1'];?>">일 갑에게 <input name="pay_day2" type="text" style="width:30px; text-align:right;"  maxlength="2" value="<?=$contract['pay_day2'];?>">일까지 제3호서식의 장기요양급여 이용료 세부내역서를 통보한다.</br>
			'갑' 은 매월<input name="pay_day3" type="text" style="width:30px; text-align:right;" maxlength="2" value="<?=$contract['pay_day3'];?>">일까지 본인부담금을 납부 한다.
			</td>

		</tr>
	</tbody>
</table>
<input name="kind" type="hidden" value="<?=$kind?>"/>			<!--서비스구분-->
<input name="svc_dt" type="hidden" value=""/>					<!--계약기간-->
<input name="seq" type="hidden" value="<?=$seq;?>"/>			<!--순번-->
<input name="ssn" type="hidden" value="<?=$ed->en($ssn);?>"/>	<!--주민-->
<input name="svc_seq"  type="hidden" value="">