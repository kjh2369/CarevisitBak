<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사정기록_욕구
	 *********************************************************/

	//약도 파일
	#$userMap = '../hce/user_map/'.$orgNo.'/'.$hce->SR.'/'.$hce->IPIN.'_'.$hce->rcpt.'.jpg';

	//if (!Is_File($userMap)) $userMap = '';

	//주소
	$sql = 'SELECT	addr
			,		addr_dtl
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	#$row = $conn->get_array($sql);
	#$addr = $row['addr'].' '.$row['addr_dtl'];
	#Unset($row);

	//사정기록
	/*
	$sql = 'SELECT	ispt_seq
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$isptSeq = $conn->get_data($sql);
	*/
	$isptSeq = '1';

	if ($_POST['hcptSeq']) $tmpHcptSeq = $_POST['hcptSeq'];
	if (!$tmpHcptSeq) $tmpHcptSeq = $hce->rcpt;

	$sql = 'SELECT	lifedays
			,		faircopy
			,		dwelling
			,		leisure
			,		interview
			,		local
			,		link
			,		educ
			,		emergency
			,		ext
			,		social_opinion
			,		rough_text
			,		rough_file
			FROM	hce_inspection_needs
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$lifedays		= StripSlashes($row['lifedays']);
	$faircopy		= StripSlashes($row['faircopy']);
	$dwelling		= StripSlashes($row['dwelling']);
	$leisure		= StripSlashes($row['leisure']);
	$interview		= StripSlashes($row['interview']);
	$local			= StripSlashes($row['local']);
	$link			= StripSlashes($row['link']);
	$educ			= StripSlashes($row['educ']);
	$emergency		= StripSlashes($row['emergency']);
	$ext			= StripSlashes($row['ext']);
	$socialOpinion	= StripSlashes($row['social_opinion']);
	$roughText		= StripSlashes($row['rough_text']);
	$roughFile		= $row['rough_file'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		//__fileUploadInit($('#frmFile'), 'fileUploadCallback');
		__init_form(document.f);
	});

	//마우스 이벤트
	function lfMouseEvt(obj,evt){
		var cnt = $('td',obj).length;

		if (evt == 'OVER'){
			$('td',obj).eq(cnt-1).css('background-color','#efefef');
			$('td',obj).eq(cnt-2).css('background-color','#efefef');
			$('td',obj).eq(cnt-3).css('background-color','#efefef');
		}else{
			$('td',obj).eq(cnt-1).css('background-color','#ffffff');
			$('td',obj).eq(cnt-2).css('background-color','#ffffff');
			$('td',obj).eq(cnt-3).css('background-color','#ffffff');
		}
	}

	function lfDisabled(obj,enabled){
		if (enabled){
			$(obj).css('background-color','#ffffff').attr('disabled',false).focus();
		}else{
			$(obj).css('background-color','#efefef').attr('disabled',true);
		}
	}

	//저장
	function lfSaveSub(){
		var data = {};

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_apply.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					top.frames['frmTop'].lfTarget();
					alert('정상적으로 처리되었습니다.');
					/*
					if (!$('#imgMap').val()){
						alert('정상적으로 처리되었습니다.');
						return;
					}

					var frm = $('#frmFile');
						frm.attr('action', './hce_ispt_7_map_upload.php');
						frm.submit();
					*/
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	/*
	function lfFileUpload(){
		if (!$('#imgMap').val()){
			return;
		}


	}
	*/

	/*
	function fileUploadCallback(data, state){
		if (state == 'success'){
			$('#imgMapView').attr('src','<?=$userMap;?>?timestamp=' + new Date().getTime()).show();
			alert('정상적으로 처리되었습니다.');
		}else{
			alert('약도 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}
	}

	//네이버 지도
	function lfLoadMap(){
		var w = 800;
		var h = 600;
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;


		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
		//var url = 'http://map.naver.com/?query=<?=urlencode($addr);?>';
		var url = 'http://map.naver.com/';
		var win = window.open('', 'MAP', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'query':'<?=$addr;?>'
			};

		var form = document.createElement('form');
		var objs;

		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', 'MAP');
		form.setAttribute('method', 'get');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	//맵확인
	function lfShowImg(obj){
		if (!__checkImageExp2(obj)){
			return;
		}

		//윈도우9이상 버전에서 생기는 문제로 아래의 함수를 건너뜀.
		return;

		var path;

		try{
			path = __get_file_path(obj);
		}catch(e){
			alert('ERROR\n'+e);
			return;
		}

		$('#imgMapView').hide();
		$('#divMapView').css('filter',"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='file://"+path+"', sizingMethod='image')").show();
	}
	*/
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px" span="2">
		<col width="1100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="20">- 욕구</th>
		</tr>
		<tr>
			<th class="head" rowspan="6">예방적<br>서비스</th>
			<th class="head">일상생활<br>지원</th>
			<td><textarea id="txtLifedays" name="multi" style="width:100%; height:35px;"><?=$lifedays;?></textarea></td>
		</tr>
		<tr>
			<th class="head">정서지원</th>
			<td><textarea id="txtFaircopy" name="multi" style="width:100%; height:35px;"><?=$faircopy;?></textarea></td>
		</tr>
		<tr>
			<th class="head">주거환경<br>개선지원</th>
			<td><textarea id="txtDwelling" name="multi" style="width:100%; height:35px;"><?=$dwelling;?></textarea></td>
		</tr>
		<tr>
			<th class="head">여가활동<br>지원</th>
			<td><textarea id="txtLeisure" name="multi" style="width:100%; height:35px;"><?=$leisure;?></textarea></td>
		</tr>
		<tr>
			<th class="head">상담지원</th>
			<td><textarea id="txtInterview" name="multi" style="width:100%; height:35px;"><?=$interview;?></textarea></td>
		</tr>
		<tr>
			<th class="head">지역사회<br>지원개발</th>
			<td><textarea id="txtLocal" name="multi" style="width:100%; height:35px;"><?=$local;?></textarea></td>
		</tr>
		<tr>
			<th class="head" rowspan="2">사회안정망<br>구축</th>
			<th class="head">연계지원</th>
			<td><textarea id="txtLink" name="multi" style="width:100%; height:35px;"><?=$link;?></textarea></td>
		</tr>
		<tr>
			<th class="head">교육지원</th>
			<td><textarea id="txtEduc" name="multi" style="width:100%; height:35px;"><?=$educ;?></textarea></td>
		</tr>
		<tr>
			<th class="head" colspan="2">긴급지원</th>
			<td><textarea id="txtEmergency" name="multi" style="width:100%; height:35px;"><?=$emergency;?></textarea></td>
		</tr>
		<tr>
			<th class="head" colspan="2">기타</th>
			<td><textarea id="txtExt" name="multi" style="width:100%; height:35px;"><?=$ext;?></textarea></td>
		</tr>
		<tr>
			<th class="bold last" colspan="20">- 사회복지사소견</th>
		</tr>
		<tr>
			<td colspan="3"><textarea id="txtSocial" name="multi" style="width:100%; height:150px;"><?=$socialOpinion;?></textarea></td>
		</tr>
	</tbody>
</table>
<!--
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last">- 약도</th>
		</tr>
		<tr>
			<td class="left last">
				<form id="frmFile" name="frmFile" method="post" enctype="multipart/form-data">
					<div style="float:left; width:auto;"><span class="btn_pack small"><button type="button" onclick="lfLoadMap();" style="color:#666666;">지도보기</button></span></span></div>
					<div style="float:left; width:50px; margin-left:3px; margin-top:-1px; background:url(../image/find_file.gif) no-repeat left 50%;">
						<input type="file" name="imgMap" id="imgMap" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-5px;" onchange="lfShowImg(this);">
					</div>
					<div style="float:left; width:auto;"><span class="btn_pack small"><button type="button" onclick="" style="color:#666666;">삭제</button></span></span></div>
				</form>
			</td>
		</tr>
		<tr>
			<td class="last">
				<div id="divMapView" style="height:300px;">
					<img id="imgMapView" src="<?=$userMap;?>?timestamp=<?=Date();?>" border="0">
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">설명</th>
			<td><textarea id="txtRough" name="multi" style="width:100%; height:35px;"><?=$roughText;?></textarea></td>
		</tr>
	</tbody>
</table>
-->
<input id="bodyIdx" type="hidden" value="7">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
	//
?>