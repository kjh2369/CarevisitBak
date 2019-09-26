<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgGiho= $_SESSION['userCenterGiho'];
	$orgNm	= $conn->_storeName($orgNo);
?>

<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
		__init_form(document.f);
	});

	function lfSearch(page){
		if (!page) page = 1;

		$.ajax({
			type:'POST'
		,	url	:'./process_counseling_list.php'
		,	data:{
				'page':page
			,	'memberName':$('#txtMemberName').val()
			,	'counseler':$('#txtCounseler').val()
			,	'counselType':$('#cboCounselType').val()
			,	'fromDt':$('#txtFrom').val()
			,	'toDt':$('#txtTo').val()
			,	'orderBy':$('input:radio[name="optOrder"]:checked').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function (html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfReg(type, code, ssn, yymm, seq){
		var f    = document.f;

		var width  = 875;
		var height = 700;

		var top  = (window.screen.height - height) / 2;
		var left = (window.screen.width  - width)  / 2;

		f.type.value = type;
		f.code.value = code;
		f.ssn.value = ssn;
		f.seq.value = seq;
		f.yymm.value = yymm;

		window.open('about:blank','COUNSEL_REG','top='+top+',left='+left+',width='+width+',height='+height+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');

		f.target = 'COUNSEL_REG';
		f.action = '../yoyangsa/process_counseling_reg.php';
		f.submit();
	}

	function lfSave(){
		var f = document.f;

		f.target = '_self';

		f.action = '../yoyangsa/counsel_member_save.php';
		f.submit();
	}

	//직원 과정상담출력
	function lfShow(code, seq, ssn, mode ,yymm, root){
		
		if(mode == 'stat'){
		
			var	arguments = 'root=sugupja'
					  + '&dir=P'
					  + '&fileName=stat'
					  + '&fileType=pdf'
					  + '&target=show.php'
					  + '&showForm='
					  + '&code='+code
					  + '&jumin='+ssn
					  + '&regDt='+dt
					  + '&param=';

			__printPDF(arguments);

		}else {
			var w = 700;
			var h = 900;
			var l = (window.screen.width  - w) / 2;
			var t = (window.screen.height - h) / 2;

			var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
			var f   = document.f;

			if(code != '') document.getElementById('code').value = code;
			if(yymm != '') document.getElementById('yymm').value = yymm;
			if(seq != '') document.getElementById('seq').value  = seq;
			if(ssn != '') document.getElementById('ssn').value  = ssn;
			if(root != '') document.getElementById('root').value  = root;

			f.target = 'SHOW_PDF';
			f.action = '../counsel/counsel_show.php?type='+mode.toUpperCase();
			f.submit();
			f.target = '_self';
			f.action = '../sugupja/counsel_client.php';

		}
	}

</script>
<script type="text/javascript" src="../js/report.js"	></script>
<script type="text/javascript" src="../js/counsel.js"	></script>

<div class="title title_border">과정상담 리스트</div>
<form name="f" method="post">
<table class="my_table" style="width:100%">
	<colgroup>
		<col width="55px">
		<col width="150px">
		<col width="45px">
		<col width="150px">
		<col width="55px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$orgGiho;?></td>
			<th>기관명</th>
			<td class="left" colspan="3"><?=$orgNm;?></td>
			<td class="left bottom last" rowspan="4">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
		<tr>
			<th>직원명</th>
			<td><input id="txtMemberName" name="txtMemberName" type="text" value="" style="width:100%;"></td>
			<th>상담자</th>
			<td><input id="txtCounseler" name="txtCounseler" type="text" value="" style="width:100%;"></td>
			<th>상담유형</th>
			<td>
				<select id="cboCounselType" name="cboCounselType" style="width:auto;">
					<option value="ALL">전체</option>
					<option value="PROCESS">과정상담</option>
					<option value="STRESS">불만 및 고충처리</option>
					<option value="CASE">불만 사례관리회의 고충처리</option>
				</select>
			</td>
		</tr>
		<tr>
			<th class="">작성기간</th>
			<td class="" colspan="5">
				<input id="txtFrom" type="text" value="" class="date"> ~
				<input id="txtTo" type="text" value="" class="date">
			</td>
		</tr>
		<tr>
			<th class="bottom">정렬조건</th>
			<td class="bottom" colspan="5">
				<label><input id="optOrderDesc" name="optOrder" type="radio" value="DESC" class="radio" checked>최근일자순별</label>
				<label><input id="optOrderAsc" name="optOrder" type="radio" value="ASC" class="radio">작성순서순별</label>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%; border-top:1px solid #0e69b0;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="90px">
		<col width="90px">
		<col width="130px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">직원명</th>
			<th class="head">상담자</th>
			<th class="head">상담유형</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="6">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<input name="code" type="hidden" value="<?=$orgNo?>">
<input name="type" type="hidden" value="">
<input name="ssn" type="hidden" value="">
<input name="seq"   type="hidden" value="">
<input name="yymm"   type="hidden" value="">
<input name="root"   type="hidden" value="">
<input name="para_m_cd"  type="hidden" value="">
<input name="para_seq"  type="hidden" value="">
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>