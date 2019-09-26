<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
	
	$conn2 = new connection();
	$conn3 = new connection();

	$mCode	= $_GET['code'] != '' ? $_GET['code'] : $_SESSION["userCenterCode"];
	$chk_box1 = $_REQUEST['chk_box1'];
	$chk_box2 = $_REQUEST['chk_box2'];
	$chk_box3 = $_REQUEST['chk_box3'];
	$chk_box4 = $_REQUEST['chk_box4'];
	$chk_box5 = $_REQUEST['chk_box5'];
	$chk_box6 = $_REQUEST['chk_box6'];
	$chk_box7 = $_REQUEST['chk_box7'];
	$chk_box8 = $_REQUEST['chk_box8'];
	$chk_box9 = $_REQUEST['chk_box9'];
	$chk_box10 = $_REQUEST['chk_box10'];
	$chk_box11 = $_REQUEST['chk_box11'];
	$chk_box12 = $_REQUEST['chk_box12'];
	$chk_box13 = $_REQUEST['chk_box13'];
	$chk_box14 = $_REQUEST['chk_box14'];
	$chk_box15 = $_REQUEST['chk_box15'];
	

?>
<script>
function check(){

	for(var i=1; i<=15; i++){
		if(document.getElementById('check_'+i).checked){	
			document.getElementById('chk_'+i).style.display = '';
		}else {
			document.getElementById('chk_'+i).style.display = 'none';
		}
	}
		
}


function y4BohumUmu(cnt, strValue){
	if(strValue == 'Y'){
		document.getElementById('kukmin_u'+cnt).style.display = '';
		document.getElementById('health_u'+cnt).style.display = '';
		document.getElementById('employ_u'+cnt).style.display = '';
		document.getElementById('sanje_u'+cnt).style.display = '';
		document.getElementById('kukmin_m'+cnt).style.display = 'none';
		document.getElementById('health_m'+cnt).style.display = 'none';
		document.getElementById('employ_m'+cnt).style.display = 'none';
		document.getElementById('sanje_m'+cnt).style.display = 'none';
	}else {
		document.getElementById('kukmin_u'+cnt).style.display = 'none';
		document.getElementById('health_u'+cnt).style.display = 'none';
		document.getElementById('employ_u'+cnt).style.display = 'none';
		document.getElementById('sanje_u'+cnt).style.display = 'none';
		document.getElementById('kukmin_m'+cnt).style.display = '';
		document.getElementById('health_m'+cnt).style.display = '';
		document.getElementById('employ_m'+cnt).style.display = '';
		document.getElementById('sanje_m'+cnt).style.display = '';
	}
}
function Gupyeokind(cnt, strValue){
	
	if(strValue == '1Y'){
		document.getElementById('U1_'+cnt).style.display = '';
		document.getElementById('U2_'+cnt).style.display = 'none';
		document.getElementById('U3_'+cnt).style.display = 'none';
		document.getElementById('U4_'+cnt).style.display = 'none';
		document.getElementById('Mu_'+cnt).style.display = 'none';		
	}else if(strValue == '1N'){
		document.getElementById('U1_'+cnt).style.display = 'none';
		document.getElementById('U2_'+cnt).style.display = '';
		document.getElementById('U3_'+cnt).style.display = 'none';
		document.getElementById('U4_'+cnt).style.display = 'none';
		document.getElementById('Mu_'+cnt).style.display = 'none';
	}else if(strValue == '3'){
		document.getElementById('U1_'+cnt).style.display = 'none';
		document.getElementById('U2_'+cnt).style.display = 'none';
		document.getElementById('U3_'+cnt).style.display = '';
		document.getElementById('U4_'+cnt).style.display = 'none';
		document.getElementById('Mu_'+cnt).style.display = 'none';
	}else if(strValue == '4'){
		document.getElementById('U1_'+cnt).style.display = 'none';
		document.getElementById('U2_'+cnt).style.display = 'none';
		document.getElementById('U3_'+cnt).style.display = 'none';
		document.getElementById('U4_'+cnt).style.display = '';
		document.getElementById('Mu_'+cnt).style.display = 'none';
	}else if(strValue == '0'){
		document.getElementById('U1_'+cnt).style.display = 'none';
		document.getElementById('U2_'+cnt).style.display = 'none';
		document.getElementById('U3_'+cnt).style.display = 'none';
		document.getElementById('U4_'+cnt).style.display = 'none';
		document.getElementById('Mu_'+cnt).style.display = '';
	}
	
	
}

function FamCareType(cnt, strValue){

	if(strValue == '0'){
		document.getElementById('fam_U'+cnt).style.display = 'none';
		document.getElementById('fam_UU'+cnt).style.display = 'none';
		document.getElementById('fam_UUU'+cnt).style.display = 'none';
		document.getElementById('fam_MU'+cnt).style.display = '';
	}else if(strValue == 'Y'){
		document.getElementById('fam_U'+cnt).style.display = '';
		document.getElementById('fam_UU'+cnt).style.display = 'none';
		document.getElementById('fam_UUU'+cnt).style.display = 'none';
		document.getElementById('fam_MU'+cnt).style.display = 'none';
	}else if(strValue == '2Y'){
		document.getElementById('fam_U'+cnt).style.display = 'none';
		document.getElementById('fam_UU'+cnt).style.display = '';
		document.getElementById('fam_UUU'+cnt).style.display = 'none';
		document.getElementById('fam_MU'+cnt).style.display = 'none';
	}else if(strValue == '3Y'){
		document.getElementById('fam_U'+cnt).style.display = 'none';
		document.getElementById('fam_UU'+cnt).style.display = 'none';
		document.getElementById('fam_UUU'+cnt).style.display = '';
		document.getElementById('fam_MU'+cnt).style.display = 'none';
	}
}

// 요양보호사 보험가입기간 체크
function checkInsLimitDate(cnt){
	// 보험가입 여부
	var insYN = document.getElementById('insYN'+cnt).value;

	// 보험가입 시작일자
	var yFromDate = __getDate(document.getElementById('insFromDate'+cnt).value);
	var cFromDate = __getDate(document.getElementById('centerInsFromDate'+cnt).value);

	// 보험가입 종료일자
	var yToDate = __getDate(document.getElementById('insToDate'+cnt).value);
	var cToDate = __getDate(document.getElementById('centerInsToDate'+cnt).value);

	if (insYN == 'Y'){
		if (!checkDate(yFromDate)) return;
		if (!checkDate(cFromDate)) return;

		if (diffDate('d', yFromDate, cFromDate) > 0){
			alert('보험가입 시작일자는 '+cFromDate+'부터입니다. 확인후 다시 입력하여 주십시오.');
			document.getElementById('insFromDate'+cnt).value = cFromDate;
			return;
		}
	}else{
		if (!checkDate(yToDate)) return;
		if (!checkDate(cToDate)) return;
		
		if (diffDate('d', yFromDate, yToDate) < 0){
			alert('보험가입 종료일자는 '+yFromDate+'~'+yToDate+'입니다. 확인후 다시 입력하여 주십시오.');
			document.getElementById('insToDate'+cnt).value = yFromDate;
			return;
		}

		if (diffDate('d', yToDate, cToDate) < 0){
			alert('보험가입 종료일자는 '+cFromDate+'까지입니다. 확인후 다시 입력하여 주십시오.');
			document.getElementById('insToDate'+cnt).value = cFromDate;
			return;
		}
	}
}

function save(){
	var f = document.f;
	
	f.action = 'yoy_save.php';
	f.submit();
}


window.onload = function(){
	
	check();
	
	__init_form(document.f);
}

// 직원 보험가입여부
function ins_join_yn(ins_yn, now_yn, f_date, t_date, cnt){

	var join_date = document.getElementById('yIpsail'+cnt);

	f_date = __getObject(f_date);
	t_date = __getObject(t_date);
	
	if (now_yn == 'N'){
		if (ins_yn == 'Y'){
			f_date.disabled = false;
			f_date.style.backgroundColor = '#ffffff';
			//f_date.value = f_date.tag;
			if (join_date.value != ''){
				if (join_date.value > f_date.tag){
					f_date.value = join_date.value;
				}else{
					f_date.value = f_date.tag;
				}
			}else{
				f_date.value = f_date.tag;
			}

			t_date.disabled = false;
			t_date.style.backgroundColor = '#ffffff';
			t_date.value = t_date.tag;

			t_date.alt = 'checkInsLimitDate';
			t_date.style.cursor = '';
			t_date.onfocus = function(){
				__replace(this, '-', '');
			}
			t_date.onclick = function(){
				_carlendar(this);
			}
			t_date.onchange = function(){
				checkInsLimitDate();
			}
		}else{
			f_date.disabled = true;
			f_date.style.backgroundColor = '#eeeeee';
			f_date.value = '';

			t_date.disabled = true;
			t_date.style.backgroundColor = '#eeeeee';
			t_date.value = '';

			t_date.alt = 'not';
			t_date.style.backgroundColor = '#eeeeee';
			t_date.style.cursor = 'default';
			t_date.onfocus = function(){
				this.blur();
			}
			t_date.onclick = null;
			t_date.onchange = null;
		}
	}else{
		if (ins_yn == 'Y'){
			f_date.disabled = false;
			f_date.style.backgroundColor = '#ffffff';
			f_date.value = f_date.tag;
			f_date.alt = 'checkInsLimitDate';
			f_date.style.cursor = '';
			f_date.onfocus = function(){
				__replace(this, '-', '');
			}
			f_date.onclick = function(){
				_carlendar(this);
			}
			f_date.onchange = function(){
				checkInsLimitDate();
			}
			
			t_date.disabled = false;
			t_date.style.backgroundColor = '#ffffff';
			t_date.value = t_date.tag;

			t_date.alt = 'checkInsLimitDate';
			t_date.style.cursor = '';
			t_date.onfocus = function(){
				__replace(this, '-', '');
			}
			t_date.onclick = function(){
				_carlendar(this);
			}
			t_date.onchange = function(){
				checkInsLimitDate();
			}
		}else{
			f_date.disabled = false;
			f_date.value = f_date.tag;
			f_date.alt = 'not';
			f_date.style.backgroundColor = '#eeeeee';
			f_date.style.cursor = 'default';
			f_date.onfocus = function(){
				this.blur();
			}
			f_date.onclick = null;
			f_date.onchange = null;
	
			t_date.disabled = false;
			t_date.value = t_date.tag;
			t_date.alt = 'not';
			t_date.style.backgroundColor = '#eeeeee';
			t_date.style.cursor = 'default';
			t_date.onfocus = function(){
				this.blur();
			}
			t_date.onclick = null;
			t_date.onchange = null;
		}
	}
}

</script>
<style>
div .head{
height:52px;
font-size:9pt;
text-align:center;
padding-top:18px;
border-top:none;
border-left:none;
border-right:1px solid #a6c0f3;
border-bottom:1px solid #a6c0f3;
background-color:#f7faff;
}

div .head_h{
height:26px;
font-size:9pt;
text-align:center;
border-top:none;
border-left:none;
border-right:1px solid #a6c0f3;
border-bottom:1px solid #a6c0f3;
background-color:#f7faff;
}
div .left{
width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; padding-left:5px; padding-top:3px;
}
div .center{
width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:center; padding-top:3px;
}

</style>
<form name="f" method="post">
	<div style="width:850px;">
		<div style="width:650px; float:left;">
			<span><input type="checkbox" class="checkbox" name="check_1" value="<?=$chk_box1 != '' ? $chk_box1 : 'Y';?>"<?if($chk_box1 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>핸드폰번호</span>
			<span><input type="checkbox" class="checkbox" name="check_2" value="<?=$chk_box2 != '' ? $chk_box2 : 'Y';?>"<?if($chk_box2 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>자격증</span>
			<span><input type="checkbox" class="checkbox" name="check_3" value="<?=$chk_box3 != '' ? $chk_box3 : 'Y';?>"<?if($chk_box3 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>직책</span>
			<span><input type="checkbox" class="checkbox" name="check_4" value="<?=$chk_box4 != '' ? $chk_box4 : 'Y';?>"<?if($chk_box4 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>은행</span>
			<span><input type="checkbox" class="checkbox" name="check_5" value="<?=$chk_box5 != '' ? $chk_box5 : 'Y';?>"<?if($chk_box5 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>4대보험</span>
			<span><input type="checkbox" class="checkbox" name="check_6" value="<?=$chk_box6 != '' ? $chk_box6 : 'Y';?>"<?if($chk_box6 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>연장특별수당</span>
			<span><input type="checkbox" class="checkbox" name="check_7" value="<?=$chk_box7 != '' ? $chk_box7 : 'Y';?>"<?if($chk_box7 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>직급수당</span>
			<span><input type="checkbox" class="checkbox" name="check_8" value="<?=$chk_box8 != '' ? $chk_box8 : 'Y';?>"<?if($chk_box8 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>고용형태</span>
			<span><input type="checkbox" class="checkbox" name="check_9" value="<?=$chk_box9 != '' ? $chk_box9 : 'Y';?>"<?if($chk_box9 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>고용상태</span></br>
			<span><input type="checkbox" class="checkbox" name="check_10" value="<?=$chk_box10 != '' ? $chk_box10 : 'Y';?>"<?if($chk_box10 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>입사일자</span>
			<span><input type="checkbox" class="checkbox" name="check_11" value="<?=$chk_box11 != '' ? $chk_box11 : 'Y';?>"<?if($chk_box11 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>퇴사일자</span>
			<span><input type="checkbox" class="checkbox" name="check_12" value="<?=$chk_box12 != '' ? $chk_box12 : 'Y';?>"<?if($chk_box12 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>근무가능요일</span>
			<span><input type="checkbox" class="checkbox" name="check_13" value="<?=$chk_box13 != '' ? $chk_box13 : 'Y';?>"<?if($chk_box13 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>일반수급자 급여산정</span>
			<span><input type="checkbox" class="checkbox" name="check_14" value="<?=$chk_box14 != '' ? $chk_box14 : 'Y';?>"<?if($chk_box14 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>동거가족 급여산정</span>
			<span><input type="checkbox" class="checkbox" name="check_15" value="<?=$chk_box15 != '' ? $chk_box15 : 'Y';?>"<?if($chk_box15 == 'Y'){ echo "checked";}?> onKeyDown="__enterFocus();" onclick="check();"/>배상책임보험</span>
		</div>
		<div style="width:195px; margin-top:20px;" align="right"><span class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onclick="save();">저장</button></span></div>
	</div>

	<div style="width:3200px;">

	<?	
	
		$sql = "select m02_mkind
				,      m02_yname
				,      m02_yjumin
				,	   m02_ytel
				,	   m02_yjakuk_kind
				,	   m02_yjagyuk_no
				,	   m02_yjakuk_date
				,	   m02_yjikjong
				,	   m02_ybank_name
				,	   m02_ygyeoja_no
				,	   m02_ygoyong_kind
				,	   m02_ygoyong_stat
				,      m02_pay_type
				,	   m02_ygupyeo_kind
				,	   m02_ygibonkup
				,	   m02_ysuga_yoyul
				,	   m02_yfamcare_umu
				,	   m02_yipsail 
				,	   m02_ytoisail
				,      m02_y4bohum_umu
				,      m02_ykuksin_mpay
				,      m02_health_mpay
				,      m02_employ_mpay
				,      m02_sanje_mpay
				,      m02_ygunmu_mon
				,	   m02_ygunmu_tue
				,      m02_ygunmu_wed  
				,      m02_ygunmu_thu  
				,      m02_ygunmu_fri
				,      m02_ygunmu_sat
				,      m02_ygunmu_sun
				,	   m02_add_payrate
				,	   m02_rank_pay
				,      m02_yfamcare_type 
				,      m02_yfamcare_pay
				,      m02_ins_code
				,      m02_ins_yn
				,	   m02_ins_from_date
				,	   m02_ins_to_date
				  from m02yoyangsa
				 where m02_ccode = '".$mCode."'
				   and m02_del_yn = 'N'";

		$conn -> query($sql);
		$conn -> fetch();
		$row_count = $conn->row_count();
		
		?>
		<div style="width:40px; float:left; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">NO</div><?	
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:right; padding-right:5px;"><?=$i+1;?></div>
				<?
			}?>
		</div>
		<div style="width:110px; float:left; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">요양보호사</div><?	
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; padding-left:5px;"><?=$row['m02_yname']?></div>
				<input name="yName" type="hidden" value="<?=$row['m02_yname'];?>" />
				<?
			}?>
		</div>
		<div style="width:110px; float:left; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">주민번호</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:center;"><?=$myF->issStyle($row['m02_yjumin'])?></div>
				<?
			}?>
		</div>
		<div id="chk_1" style="width:110px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%; float:left">핸드폰번호</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<input name="yTel<?=$i?>" type="text" style="padding-left:3px; text-align:left;"  maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" value="<?=$myF->phoneStyle($row['m02_ytel'])?>" width="110px" tabindex="<?=$i+1?>" />
				</div>
				<?
			}?>
		</div>
		<div id="chk_2" style="width:370px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div style="width:370px;">
				<div class="head_h" style="width:100%;">자격증</div>
				<div class="head_h" style="width:200px; float:left;">종류</div>
				<div class="head_h" style="width:90px; float:left;">번호</div>
				<div class="head_h" style="width:80px; float:left;">발급일자</div>
			</div>
			<div style="width:200px; float:left;"><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<select name="yJakukKind<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" tabindex="<?=$i+1?>">
					<?
					$sql = "select m99_code, m99_name
							  from m99license
							 order by m99_seq";
					$conn2->query($sql);
					$row2 = $conn2->fetch();
					$row_count2 = $conn2->row_count();

					for($j=0; $j<$row_count2; $j++){
						$row2 = $conn2->select_row($j);
					?>
						<option value="<?=$row2[0];?>"<? if($row['m02_yjakuk_kind'] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
					<?
					}
					$conn2->row_free();
					?>
					</select>
				</div>
				<?
			}?>
			</div>
			<div style="width:90; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div class="center">
						<input name="yJagyukNo<?=$i?>" type="text" style="width:80px; padding-left:3px; text-align:left;" onFocus="this.select();" value="<?=$row['m02_yjagyuk_no'];?>" tabindex="<?=$i+1?>"/>
					</div>
					<?
				}?>
			</div>
			<div style="width:80; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div class="center" style="width:80;">
						<input name="yJakukDate<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:left;" value="<?=$myF->dateStyle($row['m02_yjakuk_date']);?>" onClick="_carlendar(this);" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" tabindex="<?=$i+1?>">
					</div>
					<?
				}?>
			</div>
		</div>
		<div id="chk_3" style="width:170px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">직책</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<select name="yJikJong<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" tabindex="<?=$i+1?>">
					<?
						$sql = $conn2->get_query("98");
						$conn2->query($sql);
						$row2 = $conn2->fetch();
						$row_count2 = $conn2->row_count();

						for($j=0; $j<$row_count2; $j++){
							$row2 = $conn2->select_row($j);
						?>
							<option value="<?=$row2[0];?>" tag="<?=$row2[2];?>" <? if($row['m02_yjikjong'] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
						<?
						}

						$conn2->row_free();
					?>
					</select>
				</div>
				<?
			}?>
		</div>
		<div id="chk_4" style="width:200px; float:left; display:none; border-top:1 solid #a6c0f3;">			
			<div style="width:100%;">
				<div class="head_h" style="width:100%;">은행</div>
				<div class="head_h" style="width:90px; float:left;">은행명</div>
				<div class="head_h" style="width:110px; float:left;">계좌번호</div>
			</div>
			<div style="width:90px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div class="left">
						<input name="yBankName<?=$i?>" type="text" style="width:80px; padding-left:3px; text-align:left;" value="<?=$row['m02_ybank_name'];?>" tabindex="<?=$i+1?>">
					</div>
					<?
				}?>
			</div>
			<div style="width:110px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div class="center">
						<input name="yGyeojaNo<?=$i?>" type="text" style="width:100px; padding-left:3px; text-align:left;" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" value="<?=$row['m02_ygyeoja_no'];?>" tabindex="<?=$i+1?>">
					</div>
					<?
				}?>
			</div>
		</div>
		<div id="chk_5" style="width:370; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div style="width:100%;">
				<div class="head_h" style="width:100%;">4대보험</div>
				<div class="head_h" style="width:50; float:left;">유무</div>
				<div class="head_h" style="width:80; float:left;">국민연금</div>
				<div class="head_h" style="width:80; float:left;">건강보험</div>
				<div class="head_h" style="width:80; float:left;">고용보험</div>
				<div class="head_h" style="width:80; float:left;">산재보험</div>
			</div>
			<div style="width:50px; float:left;"><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center" align="center">
					<select name="y4BohumUmu<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" onchange="y4BohumUmu('<?=$i?>',this.value);">
					<option value="Y" <? if($row['m02_y4bohum_umu'] == 'Y'){?>selected<?} ?>>유</option>
					<option value="N" <? if($row['m02_y4bohum_umu'] == 'N'){?>selected<?} ?>>무</option>
				</select>
				</div>
				<?
			}?>
			</div>
			<div style="width:80px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div id="kukmin_u<?=$i?>" class="center" style="display:<?=$row['m02_y4bohum_umu'] == 'Y' ? '' : 'none';?>;">
						<input name="yKuksinMpay<?=$i?>" type="text" style="width:70px; padding-right:3px; text-align:right;" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" value="<?=number_format($row['m02_ykuksin_mpay']);?>" tabindex="<?=$i+1?>">
					</div>
					<div id="kukmin_m<?=$i?>" style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; display:<?=$row['m02_y4bohum_umu'] == 'N' ? '' : 'none';?>;"></div>
					<?
				}?>
			</div>

			<div style="width:80px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div id="health_u<?=$i?>" class="center" style="display:<?=$row['m02_y4bohum_umu'] == 'Y' ? '' : 'none';?>;">
						<input name="yHealthMpay<?=$i?>" type="text" style="width:70px; padding-right:3px; text-align:right;" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" value="<?=number_format($row['m02_health_mpay']);?>" tabindex="<?=$i+1?>">
					</div>
					<div id="health_m<?=$i?>" style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; display:<?=$row['m02_y4bohum_umu'] == 'N' ? '' : 'none';?>;"></div>
					<?
				}?>
			</div>
			<div style="width:80px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div id="employ_u<?=$i?>" class="center" style="display:<?=$row['m02_y4bohum_umu'] == 'Y' ? '' : 'none';?>;">
						<input name="yEmployMpay<?=$i?>" type="text" style="width:70px; padding-right:3px; text-align:right;" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" value="<?=number_format($row['m02_employ_mpay']);?>" tabindex="<?=$i+1?>">
					</div>
					<div id="employ_m<?=$i?>" style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; display:<?=$row['m02_y4bohum_umu'] == 'N' ? '' : 'none';?>;"></div>
					<?
				}?>
			</div>
			<div style="width:80px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); ?>
					<div id="sanje_u<?=$i?>" class="center" style="display:<?=$row['m02_y4bohum_umu'] == 'Y' ? '' : 'none';?>;">
						<input name="ySanjeMpay<?=$i?>" type="text" style="width:70px; padding-right:3px; text-align:right;" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" value="<?=number_format($row['m02_sanje_mpay']);?>" tabindex="<?=$i+1?>">
					</div>
					<div id="sanje_m<?=$i?>" style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; display:<?=$row['m02_y4bohum_umu'] == 'N' ? '' : 'none';?>;"></div>
					<?
				}?>
			</div>
		</div>
		<div id="chk_6" style="width:80px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">연장특별수당</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<input name="addPayRate<?=$i?>" type="text" style="width:70px; padding-right:3px; text-align:right;" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onchange="if(this.value==''){this.value='0.0';}" value="<?=number_format($row['m02_add_payrate']);?>" tabindex="<?=$i+1?>">
				</div>

				<?
			}?>
		</div>
		<div id="chk_7" style="width:80px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">직급수당</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<input name="rank_pay<?=$i?>" type="text" style="width:70px; padding-right:3px; text-align:right;" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" value="<?=number_format($row['m02_rank_pay']);?>" tabindex="<?=$i+1?>">
				</div>
				<?
			}?>
		</div>
		<div id="chk_8" style="width:100px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">고용형태</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<select name="yGoyongKind<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" tabindex="<?=$i+1?>">
						<option value="1" <? if($row['m02_ygoyong_kind'] == '1'){?>selected<?} ?>>정규직</option>
						<option value="2" <? if($row['m02_ygoyong_kind'] == '2'){?>selected<?} ?>>60시간미만</option>
						<option value="3" <? if($row['m02_ygoyong_kind'] == '3'){?>selected<?} ?>>60시간이상</option>
					</select>
				</div>
				<?
			}?>
		</div>
		<div id="chk_9" style="width:60px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">고용상태</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<select name="yGoyongStat<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" tabindex="<?=$i+1?>">
						<option value="1" <? if($row['m02_ygoyong_stat'] == '1'){?>selected<?} ?>>활동</option>
						<option value="2" <? if($row['m02_ygoyong_stat'] == '2'){?>selected<?} ?>>휴직</option>
						<option value="9" <? if($row['m02_ygoyong_stat'] == '9'){?>selected<?} ?>>퇴사</option>
					</select>
				</div>
				<?
			}?>
		</div>
		<div id="chk_10" style="width:90px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">입사일자</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<input name="yIpsail<?=$i?>" type="text" style="width:80px; padding-left:3px; text-align:left;" value="<?=$myF->dateStyle($row['m02_yipsail']);?>" onClick="_carlendar(this);" class="date" alt="checkInsLimitDate" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" tabindex="<?=$i+1?>">
				</div>
				<?
			}?>
		</div>
		<div id="chk_11" style="width:90px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">퇴사일자</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<input name="yToisail<?=$i?>" type="text" style="width:80px; padding-left:3px; text-align:left;" value="<?=$myF->dateStyle($row['m02_ytoisail']);?>" onClick="_carlendar(this);" class="date" alt="checkInsLimitDate" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" tabindex="<?=$i+1?>">
				</div>
				<?
			}?>
		</div>
		<div id="chk_12" style="width:290px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div class="head" style="width:100%;">근무가능요일</div><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i); ?>
				<div class="center">
					<input name="yGunmuMon<?=$i?>" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_mon"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">월</font>
					<input name="yGunmuTue<?=$i?>" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_tue"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">화</font>
					<input name="yGunmuWed<?=$i?>" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_wed"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">수</font>
					<input name="yGunmuThu<?=$i?>" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_thu"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">목</font>
					<input name="yGunmuFri<?=$i?>" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_fri"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">금</font>
					<input name="yGunmuSat<?=$i?>" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_sat"] == "Y"){echo "checked";}?>><font style="font-weight:bold; color:#0000ff;">토</font>
					<input name="yGunmuSun<?=$i?>" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_sun"] == "Y"){echo "checked";}?>><font style="font-weight:bold; color:#ff0000;">일</font>
				</div>
				<?
			}?>
		</div>
		<div id="chk_13" style="width:450px; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div style="width:450px; float:left;">
				<div class="head_h" style="width:100%;">일반수급자 급여산정</div>
				<div class="head_h" style="width:80; float:left;">산정방식</div>
				<div class="head_h" style="width:370; float:left;">급여액</div>
			</div>
			<div style="width:80px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); 
					
					if ($row["m02_ygupyeo_kind"] == '1' || $row["m02_ygupyeo_kind"] == '2'){
						if ($row['m02_pay_type'] == 'Y'){
							$pay_type = '1'; //시급(고정급)
						}else{
							$pay_type = '2'; //시급(변동급)
						}
					}else if ($row["m02_ygupyeo_kind"] == '3'){
						$pay_type = '3'; //월급

						if ($row['m02_pay_type'] == 'Y'){
							$pay_com_type = 'Y';
						}
					}else if ($row["m02_ygupyeo_kind"] == '4'){
						$pay_type = '4'; //총액비율
					}else{
						$pay_type = '0';
					}
					?>
					
					
					<div class="center">
						<select name="yGupyeoKind<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" onchange="Gupyeokind('<?=$i?>',this.value);" tabindex="<?=$i+1?>">
							<option value="0" <? if($pay_type == '0'){?>selected<?} ?>>무</option>
							<option value="1Y" <? if($pay_type == '1'){?>selected<?} ?>>고정시급</option>
							<option value="1N" <? if($pay_type == '2'){?>selected<?} ?>>변동시급</option>
							<option value="4" <? if($pay_type == '4'){?>selected<?} ?>>총액비율</option>
							<option value="3" <? if($pay_type == '3'){?>selected<?} ?>>월급</option>
						</select>
					</div>
					<?
				}?>
			</div>
			<div style="width:370px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);
						
					$kind = $row['m02_mkind'];
					$jumin = $row['m02_yjumin'];

					if ($row["m02_ygupyeo_kind"] == '1' || $row["m02_ygupyeo_kind"] == '2'){
						if ($row['m02_pay_type'] == 'Y'){
							$pay_type = '1'; //시급(고정급)
						}else{
							$pay_type = '2'; //시급(변동급)
						}
					}else if ($row["m02_ygupyeo_kind"] == '3'){
						$pay_type = '3'; //월급

						if ($row['m02_pay_type'] == 'Y'){
							$pay_com_type = 'Y';
						}
					}else if ($row["m02_ygupyeo_kind"] == '4'){
						$pay_type = '4'; //총액비율
					}else{
						$pay_type = '0';
					}

					switch($pay_type){
					case '1':
						$hourly_1 = $row["m02_ygibonkup"];
						break;
					case '2':
						$sql = "select m02_gubun
								,      m02_pay
								  from m02pay
								 where m02_ccode = '$mCode'
								   and m02_mkind = '$kind'
								   and m02_jumin = '$jumin'";
						$conn2->query($sql);
						$conn2->fetch();
						$row_count2 = $conn2->row_count();
	
						for($j=0; $j<$row_count2; $j++){
							$row2 = $conn2->select_row($j);
							$hourly_2[$row2['m02_gubun']] = $row2['m02_pay'];
						}

						$conn2->row_free();
						break;
					case '3':
						$hourly_3 = $row["m02_ygibonkup"];
						break;
					case '4':
						$hourly_4 = $row["m02_ysuga_yoyul"];
						break;
					}

					?>	
						<div id="U1_<?=$i?>" style="display:<?=$pay_type == '1' ? '' : 'none';?>;" class="left">
							<input name="yGibonKup1<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:right;" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="고정시급을 입력하여 주십시오." value="<?=number_format($hourly_1);?>" tabindex="<?=$i+1?>">
						</div>
						<div id="U2_<?=$i?>" style="display:<?=$pay_type == '2' ? '' : 'none';?>;" class="left">
							<span>1등급<input name="yGibonKup<?=$i?>[]" type="text" style="width:50px; padding-left:3px; text-align:right;" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(1등급)을 입력하여 주십시오." value="<?=number_format($hourly_2[1]);?>" tabindex="<?=$i+1?>"></span>
							<span>2등급<input name="yGibonKup<?=$i?>[]" type="text" style="width:50px; padding-left:3px; text-align:right;" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(2등급)을 입력하여 주십시오." value="<?=number_format($hourly_2[2]);?>" tabindex="<?=$i+1?>"></span>
							<span>3등급<input name="yGibonKup<?=$i?>[]" type="text" style="width:50px; padding-left:3px; text-align:right;" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(3등급)을 입력하여 주십시오." value="<?=number_format($hourly_2[3]);?>" tabindex="<?=$i+1?>"></span>
							<span>일반<input name="yGibonKup<?=$i?>[]" type="text" style="width:50px; padding-left:3px; text-align:right;" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(일반)을 입력하여 주십시오." value="<?=number_format($hourly_2[9]);?>" tabindex="<?=$i+1?>"></span>
							<input name="yGibonKupCode<?=$i?>[]" type="hidden" value="1">
							<input name="yGibonKupCode<?=$i?>[]" type="hidden" value="2">
							<input name="yGibonKupCode<?=$i?>[]" type="hidden" value="3">
							<input name="yGibonKupCode<?=$i?>[]" type="hidden" value="9">
						</div>
						<div id="U3_<?=$i?>" style="display:<?=$pay_type == '3' ? '' : 'none';?>;" class="left">
							<input name="yGibonKup3<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:right;" value="<?=number_format($hourly_3);?>" tabindex="<?=$i+1?>">
							<input name="yGibonKupCom<?=$i?>" type="checkbox" class="checkbox" value="Y" <? if($row['m02_pay_type'] == 'Y'){ ?> checked <?}?>>포괄임금제 
						</div>
						<div id="U4_<?=$i?>" style="display:<?=$pay_type == '4' ? '' : 'none';?>;" class="left">
							<input name="ySugaYoyul<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:right;" maxlength="4" class="no_string" style="text-align:right;" onKeyDown="__onlyNumber(this,'.');" onBlur="if(this.value == ''){this.value = '0';}" tag="수가총액비율을 입력하여 주십시오." value="<?=$hourly_4;?>" tabindex="<?=$i+1?>">%
						</div>
						<div id="Mu_<?=$i?>" style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; display:<? if($pay_type == '0' or $row['m02_ygupyeo_kind'] == ''){ echo '';}else { echo 'none'; }?>;"></div><?
				}?>
			</div>
		</div>
		<div id="chk_14" style="width:200; float:left; display:none; border-top:1 solid #a6c0f3;">
			<div style="width:200; float:left;">
				<div class="head_h" style="width:200;">동거가족 급여산정</div>
				<div class="head_h" style="width:120; float:left;">산정방식</div>
				<div class="head_h" style="width:80; float:left;">급여액</div>
			</div>
			<div style="width:120; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);
					
					//동거가족케어
					if($row['m02_yfamcare_type'] == '1'){
							$famcare_type = 1; //고정급
						if($row['m02_yfamcare_umu'] == 'N'){
							$famcare_type = '0';  //무
						}
					}else if($row['m02_yfamcare_type'] == '2'){
							$famcare_type = 2; //수가총액
					}else if($row['m02_yfamcare_type'] == '3'){
							$famcare_type = 3; //고정급
					}else {
						$famcare_type = '0';
					}

					?>
					
					<div class="center">
						<select name="yFamCareType<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" onchange="FamCareType('<?=$i?>',this.value);" tabindex="<?=$i+1?>">
							<option value="0" <? if($famcare_type == '0'){?>selected<?} ?>>무</option>
							<option value="Y" <? if($famcare_type == '1'){?>selected<?} ?>>고정시급</option>
							<option value="2Y" <? if($famcare_type == '2'){?>selected<?} ?>>수가총액비율</option>
							<option value="3Y" <? if($famcare_type == '3'){?>selected<?} ?>>고정급</option>
						</select>
					</div>
					<?
				}?>
			</div>
			<div style="width:80px; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); 
				
					//동거가족케어
					if($row['m02_yfamcare_type'] == '1'){
							$famcare_type = 1; //고정급
						if($row['m02_yfamcare_umu'] == 'N'){
							$famcare_type = '0';  //무
						}
					}else if($row['m02_yfamcare_type'] == '2'){
							$famcare_type = 2; //수가총액
					}else if($row['m02_yfamcare_type'] == '3'){
							$famcare_type = 3; //고정급
					}else {
						$famcare_type = '0';
					}
					
					switch($famcare_type){
					case '1':
						$famcare_pay1 = $row['m02_yfamcare_pay'];
						break;
					case '2':
						$famcare_pay2 = $row['m02_yfamcare_pay'];
						break;
					case '3':
						$famcare_pay3 = $row['m02_yfamcare_pay'];
						break;
					}

					?>
					
					<div id="fam_U<?=$i?>" class="center" style="display:<? if($famcare_type == '1'){ echo '';} else{ echo 'none';} ?>">
						<input name="yFamCarePay1_<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:right;" value="<?=number_format($famcare_pay1);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="고정시급을 입력하여 주십시오." tabindex="<?=$i+1?>">
					</div>
					<div id="fam_UU<?=$i?>" class="center" style="display:<? if($famcare_type == '2'){ echo '';} else{ echo 'none';} ?>">
						<input name="yFamCarePay2_<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:right;" value="<?=number_format($famcare_pay2, $famcare_type == '2' ? 1 : 0);?>" maxlength="4" class="no_string" style="text-align:right;" onKeyDown="__onlyNumber(this,'.');" onBlur="if(this.value==''){this.value='0';}" tag="수가총액비율을 입력하여 주십시오." tabindex="<?=$i+1?>">
					</div>
					<div id="fam_UUU<?=$i?>" class="center" style="display:<? if($famcare_type == '3'){ echo '';} else{ echo 'none';} ?>">
						<input name="yFamCarePay3_<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:right;" value="<?=number_format($famcare_pay3);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" style="text-align:right;" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="고정급을 입력하여 주십시오." tabindex="<?=$i+1?>">
					</div>
					<div id="fam_MU<?=$i?>" style="width:100%; height:26px; border-right:1px solid #a6c0f3; border-bottom:1px solid #a6c0f3; text-align:left; display:<?=$famcare_type == '0' ? '' : 'none';?>;" tabindex="<?=$i+1?>"></div><?
				}
				?>
			</div>
		</div>
		<div id="chk_15" style="width:210; display:none; border-top:1 solid #a6c0f3;">
			<div style="width:210;">
				<div class="head_h" style="width:210;">배상책임보험</div>
				<div class="head_h" style="width:50; float:left;">가입여부</div>
				<div class="head_h" style="width:160; float:left;">가입기간</div>
			</div>
			<div style="width:50; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); 
					
					// 기관의 보험가입정보
					$sql = "select g02_ins_code as code
							,      g02_ins_from_date as fromDate
							,      g02_ins_to_date as toDate
							  from g02inscenter
							 where g02_ccode = '$mCode'
							   and g02_mkind = '0'";
					$ins = $conn->get_array($sql);

					// 보험가입여부
					if ($row['m02_ins_yn'] == 'Y'){
						$insYN = 'Y';
					}else{
						$insYN = 'N';
					}

				?>
					<div class="center" align="center">
						<select name="insYN<?=$i?>" style="width:auto;" onKeyDown="__enterFocus();" onchange="ins_join_yn(this.value, '<?=$insYN;?>', 'insFromDate<?=$i?>', 'insToDate<?=$i?>', '<?=$i?>');" tabindex="<?=$i+1?>">
						<option value="Y"  <? if($insYN == 'Y'){?>selected<?} ?>>유</option>
						<option value="N"  <? if($insYN == 'N'){?>selected<?} ?>>무</option>
					</select>
					</div>
					<?
				}?>
			</div>
			<div style="width:160; float:left;"><?
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i); 
					
					// 기관의 보험가입정보
					$sql = "select g02_ins_code as code
							,      g02_ins_from_date as fromDate
							,      g02_ins_to_date as toDate
							  from g02inscenter
							 where g02_ccode = '$mCode'
							   and g02_mkind = '0'";
					$ins = $conn->get_array($sql);

					// 보험가입여부
					if ($row['m02_ins_yn'] == 'Y'){
						$insYN = 'Y';
					}else{
						$insYN = 'N';
					}

					// 보험정보
					if ($insYN == 'Y'){
						$insCode     = $row['m02_ins_code'];
						$insFromDate = $row['m02_ins_from_date'];
						$insToDate   = $row['m02_ins_to_date'];
					}else{
						$insCode = $ins['code'];

						$sql = "select g03_ins_to_date
								  from g03insapply
								 where g03_jumin          = '".$row["m02_yjumin"]."'
								   and g03_ins_from_date >= '".$ins['fromDate']."'
								 order by g03_ins_to_date desc
								 limit 1";
						$tempDate = $conn->get_data($sql);

						if (strLen($tempDate) == 8){
							$tempDate = $myF->dateStyle($tempDate);
							$tempDate = $myF->dateAdd('day', 1, $tempDate, 'Ymd');
							$insFromDate = ($ins['fromDate'] > $tempDate ? $ins['fromDate'] : $tempDate);
						}else{
							$insFromDate = ($ins['fromDate'] > $row['m02_yipsail'] ? $ins['fromDate'] : $row['m02_yipsail']);
						}
					}

				$ins[1]    = $insFromDate;
				$insToDate = $ins['toDate'];
					
					?>
					<div class="center">
					<input name="centerInsFromDate<?=$i?>" type="hidden" value="<?=$myF->dateStyle($ins[1]);?>">
					<input name="centerInsToDate<?=$i?>" type="hidden" value="<?=$myF->dateStyle($ins[2]);?>">
					<input name="insFromDate<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:left;" value="<? if($insYN == 'Y'){echo $myF->dateStyle($insFromDate);} ?>" tag="<?=$myF->dateStyle($insFromDate);?>" alt="checkInsLimitDate" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onchange="checkInsLimitDate('<?=$i?>');" onClick="_carlendar(this);" tabindex="<?=$i+1?>">-<input name="insToDate<?=$i?>" type="text" style="width:70px; padding-left:3px; text-align:left;" value="<? if($insYN == 'Y'){echo $myF->dateStyle($insToDate);} ?>" tag="<?=$myF->dateStyle($insToDate);?>" class="date" alt="checkInsLimitDate" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onchange="checkInsLimitDate('<?=$i?>');" onClick="_carlendar(this);" tabindex="<?=$i+1?>">
					</div>
					<script language="javascript">
					ins_join_yn(document.getElementById('insYN<?=$i?>').value, '<?=$insYN?>', 'insFromDate<?=$i?>', 'insToDate<?=$i?>', '<?=$i?>');
					</script>
					<input name="kind<?=$i?>" type="hidden" value="<?=$row['m02_mkind'];?>">
					<input name="jumin<?=$i?>" type="hidden" value="<?=$ed->en($row['m02_yjumin']);?>">
					<input name="ins_code<?=$i?>"	type="hidden" value="<?=$insCode;?>">
					<?
				}?>
			</div>
		</div>
	</div>
	<?
		$conn->row_free(); 
?>

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
