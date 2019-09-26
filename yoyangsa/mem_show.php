<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_mySalary.php");
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//학력,구분,주거
	$sql = 'select mem_marry as marry
			,      mem_edu_lvl as edu
			,      mem_gbn as gbn
			,      mem_abode as abode
			  from counsel_mem
			 where org_no  = \''.$code.'\'
			   and mem_ssn = \''.$jumin.'\'';

	$loCounsel = $conn->get_array($sql);

	if ($loCounsel['marry'] == 'Y'){
		$loCounsel['marry'] = '결혼';
	}else if ($loCounsel['marry'] == 'N'){
		$loCounsel['marry'] = '미혼';
	}else{
		$loCounsel['marry'] = '';
	}

	if ($loCounsel['edu'] == '1'){
		$loCounsel['edu'] = '중졸이하';
	}else if ($loCounsel['edu'] == '3'){
		$loCounsel['edu'] = '고졸';
	}else if ($loCounsel['edu'] == '5'){
		$loCounsel['edu'] = '대학중퇴';
	}else if ($loCounsel['edu'] == '7'){
		$loCounsel['edu'] = '대졸이상';
	}else{
		$loCounsel['edu'] = '';
	}

	if ($loCounsel['gbn'] == '1'){
		$loCounsel['gbn'] = '일반';
	}else if ($loCounsel['gbn'] == '3'){
		$loCounsel['gbn'] = '차상위';
	}else if ($loCounsel['gbn'] == 'A'){
		$loCounsel['gbn'] = '기초수급자';
	}else{
		$loCounsel['gbn'] = '';
	}

	if ($loCounsel['abode'] == '1'){
		$loCounsel['abode'] = '전세';
	}else if ($loCounsel['abode'] == '3'){
		$loCounsel['abode'] = '월세';
	}else if ($loCounsel['abode'] == '5'){
		$loCounsel['abode'] = '자가';
	}else{
		$loCounsel['abode'] = '';
	}

	//직원정보
	$sql = 'select m02_picture as pic
			,      m02_yname as name
			,      m02_ytel2 as phone
			,      m02_ytel as mobile
			,      m02_email as email
			,      m02_ypostno as postno
			,      m02_yjuso1 as addr
			,      m02_yjuso2 as addr_dtl
			  from m02yoyangsa
			 where m02_ccode  = \''.$code.'\'
			   and m02_yjumin = \''.$jumin.'\'
			 order by m02_mkind
			 limit 1';
	$loMem = $conn->get_array($sql);

	if (!is_file($loMem['pic'])){
		$loMem['pic'] = '../image/no_img_bg.gif';
	}
	?>
	<div class="title title_border">요양보호사 정보</div>
	<div id="div_body">
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="105px">
				<col width="70px">
				<col width="80px">
				<col width="200px">
				<col width="70px">
				<col width="80px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td class="center top bottom" style="padding-top:5px;" rowspan="5">
						<div id="pictureView" style="width:90px; height:120px;"><img id="img_picture" src="<?=$loMem['pic'];?>" style="border:1px solid #000;" width="90" height="120">
					</td>
					<th rowspan="3">개인정보</th>
					<th>주민번호</th>
					<td class="left"><?=$myF->issStyle($jumin);?></td>
					<th rowspan="3">연락처</th>
					<th>유선</th>
					<td class="left last"><?=$myF->phoneStyle($loMem['phone'],'.');?></td>
				</tr>
				<tr>
					<th>성명</th>
					<td class="left"><?=$loMem['name'];?></td>
					<th>무선</th>
					<td class="left last"><?=$myF->phoneStyle($loMem['mobile'],'.');?></td>
				</tr>
				<tr>
					<th>결혼여부</th>
					<td class="left"><?=$loCounsel['marry'];?></td>
					<th>e-mail</th>
					<td class="left last"><?=$loMem['email'];?></td>
				</tr>
				<tr>
					<th rowspan="3">소재</th>
					<th>우편번호</th>
					<td class="left"><?=substr($loMem['postno'],0,3).'-'.substr($loMem['postno'],3);?></td>
					<th>학력</th>
					<td class="left last" colspan="2"><?=$loCounsel['edu'];?></td>
				</tr>
				<tr>
					<td class="left" colspan="2"><?=$loMem['addr'];?></td>
					<th>구분</th>
					<td class="left last" colspan="2"><?=$loCounsel['gbn'];?></td>
				</tr>
				<tr>
					<td class="center top" rowspan="1"></td>
					<td class="left" colspan="2"><?=$loMem['addr_dtl'];?></td>
					<th>주거</th>
					<td class="left last" colspan="2"><?=$loCounsel['abode'];?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="height:30px; text-align:center;">
		<span class="btn_pack m" style="margin-top:5px;"><button type="button" onclick="window.self.close();">확인</button></span>
	</div><?
	unset($loCounsel);
	unset($loMem);

	include_once("../inc/_footer.php");
?>
<script language='javascript'>
window.onload = function(){
	var body = document.getElementById('div_body');

	body.style.height = document.body.clientHeight - 70;
}
window.self.focus();
</script>