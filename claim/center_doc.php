<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");
	
	$orgNo = $_SESSION['userCenterCode'];

	$sql = 'select doc_type, cont_dt, file_path
			from   cv_doc
			where  org_no = \''.$orgNo.'\'';
	$cv  = $conn -> _fetch_array($sql,'doc_type');

	$tmpFileA = explode('/', $cv[1]['file_path']);
	$tmpFileB = explode('/', $cv[2]['file_path']);
	$tmpFileC = explode('/', $cv[3]['file_path']);
	$fileA = explode('.', $tmpFileA[4]);
	$fileB = explode('.', $tmpFileB[4]);
	$fileC = explode('.', $tmpFileC[4]);
	
	for($i=1; $i<=3; $i++){
		if($cv[$i]['cont_dt']){
			$contDt = $cv[$i]['cont_dt'];
		}
	}

	//$contDt = ($contDt != '' ? $contDt : date('Ymd'));
	 
?>
<script type="text/javascript">
	$(document).ready(function(){
		__fileUploadInit($('#docF'), 'fileUploadCallback');
		$('input:text').each(function(){
			__init_object(this);
		});

	});

	function fileUpload(){
		
		if (!$('#txtContDt').val()){
			alert('계약서일자를 입력하여 주십시오.');
			$('#txtContDt').focus();
			return;
		}
		
		var frm = $('#docF');
			frm.attr('action', './center_doc_upload.php');
			frm.submit();
	}

	function fileUploadCallback(data, state){
		if (!data){
			$('#ID_POP_BODY').hide();
			alert('정상적으로 처리되었습니다.');
			location.reload();
		}else{
			alert(data);
		}
	}

	function lfDelete(orgNo, contDt, gbn){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./center_doc_delete.php'
		,	data:{
				'orgNo':orgNo
			,	'contDt':contDt
			,	'gbn':gbn
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				$('#tempLodingBar').remove();
				location.reload();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
	
	function lfFileDown(gbn){
		var frm = $('#docF');

			$('#docType').val(gbn);

			frm.attr('action', './center_doc_download.php');
			frm.submit();
	}

</script>

<div class="title" style="width:auto; float:left;">계약서/등록증/자동이체(CMS) 동의서 스캔 자료 등록</div>
<!--div style="width:auto; font-weight:bold; margin-top:9px; text-align:right;">※ <span style="color:red;">계약서,사업자등록증,자동이체동의서</span> 스캔 자료 등록</div-->
<form id="docF" name="docF" method="post" enctype="multipart/form-data">
	<input type="hidden" id="docType" name="docType">
	<table class="my_table my_border" style="width:100%; border-bottom:none;">
		<colgroup>
		<col width="100px">
		<col width="*">
		</colgroup>
		<tr>
			<th>계약서일자</th>
			<td class="left"><input id="txtContDt" name="txtContDt" type="text" value="<?=$myF->dateStyle($contDt);?>" class="date"></td>
		</tr>
		<tr>
			<th>계약서</th>
			<td>
				<input type="file" name="docFile1" id="docFile1" style="width:50%;">&nbsp;<? if($cv[1]['file_path']){ ?><a href="#" onclick="lfFileDown('1');">계약서.<?=$fileA[1];?></a>
				<span style="margin-top:3px;" class="btn_pack small"><button onclick="lfDelete('<?=$orgNo;?>','<?=$contDt;?>','1');" >삭제</button></span><? }?>
			</td>
		</tr>
		<tr>
			<th>사업자등록증</th>
			<td >
				<input type="file" name="docFile2" id="docFile2" style="width:50%;">&nbsp;<? if($cv[2]['file_path']){ ?><a href="#" onclick="lfFileDown('2');">등록증.<?=$fileB[1];?></a>
				<span style="margin-top:3px;" class="btn_pack small"><button onclick="lfDelete('<?=$orgNo;?>','<?=$contDt;?>','2');" >삭제</button></span><? }?>
			</td>
		</tr>
		<tr>
			<th>CMS 동의서</th>
			<td>
				<input type="file" name="docFile3" id="docFile3" style="width:50%;">&nbsp;<? if($cv[3]['file_path']){ ?><a href="#" onclick="lfFileDown('3');">CMS동의서.<?=$fileC[1];?></a>
				<span style="margin-top:3px;" class="btn_pack small"><button onclick="lfDelete('<?=$orgNo;?>','<?=$cv[1]['cont_dt'];?>','3');" >삭제</button></span><? }?>
			</td>
		</tr>
		<tr>
			<td class="center" colspan="2" style="background-color:WHITE; padding-top:10px; border:none;">				
				 <div style="font-size:13px; text-align:center;"><img src="../popup/kacold_popup/btn_register.png" onclick="fileUpload();"></div>
			</td>
		</tr>
	</table>
</form>
<?
	include_once("../inc/_footer.php");
	include_once("../inc/_body_footer.php");
?>