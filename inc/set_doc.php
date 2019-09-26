<?
	if (is_array($urlPage)){
		if ($urlPage[sizeOf($urlPage)-1] == 'main.php'){
			//계약서 및 사업자 등록증 팝업을 설정한다.
			$sql = 'SELECT	cont_dt, stop_dt, doc_type, doc_gbn, file_path
					FROM	cv_doc
					WHERE	org_no		 = \''.$_SESSION['userCenterCode'].'\'
					AND		cont_dt		<= \''.Date('Ymd').'\'
					AND		cancel_yn	 = \'N\'
					AND		IFNULL(skip_dt,\'\') != \''.Date('Ymd').'\'
					AND		IFNULL(file_path,\'\') = \'\'';

			$R1 = $conn->_fetch_array($sql);

			if (is_array($R1)){?>
				<script type="text/javascript">
					$(document).ready(function(){
						__fileUploadInit($('#docF'), 'fileUploadCallback');
					});

					function fileUpload(){
						var frm = $('#docF');
							frm.attr('action', '../inc/set_doc_upload.php?docType='+$('#docType').val()+'&contDt='+$('#docContDt').val());
							frm.submit();
					}

					function fileUploadCallback(data, state){
						if (!data){
							alert('정상적으로 처리되었습니다.');

							if ($('#tmpSetDocId').val().indexOf('/'+$('#docType').val()) < 0){
								$('#tmpSetDocId').val($('#tmpSetDocId').val()+'/'+$('#docType').val());
							}

							$('#ID_POP_ORG_SUB').hide();
						}else{
							alert(data);
						}
					}

					function lfDocReg(obj,contDt,docType,docYn){
						var msg = '';

						if ($('#tmpSetDocId').val().indexOf('/'+docType) >= 0){
							docYn = 'Y';
						}

						$('#docContDt').val(contDt);
						$('#docType').val(docType);

						if (docType == '01'){
							msg = '계약서';
						}else if (docType == '02'){
							msg = '등록증';
						}

						msg = '등록된 '+msg;

						if (docYn == 'Y'){
							msg += '이 있습니다.';
						}else{
							msg += '이 없습니다.';

						}
						$('#ID_CELL_FILE').css('color',docYn == 'Y' ? 'BLUE' : 'RED').text(msg);
						$('#ID_POP_ORG_SUB').css('top',$(obj).offset().top).css('left',$(obj).offset().left+$(obj).width()).show();
					}

					function lfSetStop(contDt){
						$.ajax({
							type:'POST'
						,	url:'../inc/set_doc_skip.php'
						,	data:{
								'contDt':contDt
							}
						,	beforeSend:function(){
							}
						,	success:function(result){
								$('#ID_POP_BODY').hide();
							}
						,	error: function (request, status, error){
								alert('[ERROR No.02]'
									 +'\nCODE : ' + request.status
									 +'\nSTAT : ' + status
									 +'\nMESSAGE : ' + request.responseText);
							}
						});
					}
				</script>
				<div id="ID_POP_BODY" style="position:absolute; left:0; top:0; width:100%; height:100%; padding-left:10px; padding-top:45px; display:; z-index:11; background:url('../image/tmp_bg.png');"><?
					if ($R1[0]['stop_dt'] >= Date('Ymd')){?>
						<div style="position:relative; text-align:right; left:0; top:0; width:1004px;">
							<a href="#" onclick="$('#ID_POP_BODY').hide();"><img src="../image/btn_exit.png"></a>
						</div><?
					}?>
					<div id="ID_POP_ORG" style="position:absolute; width:1004px; height:650px; padding:30px; border:2px solid #003399; background-color:WHITE;"><?
						$IsFirst = true;
						foreach($R1 as $tmpI => $R){
							if ($IsFirst){
								if ($R['doc_gbn'] == '1'){?>
									<p style="text-align:justify; line-height:1.5em; font-size:15px;">
									<b>알려드립니다!</b><br><br>
									현재 <?=$_SESSION['userCenterName'];?>는(은) 케어비지트와 계약이 되어있지 않습니다.<br>
									<?=$myF->dateStyle($R['stop_dt'],'KOR');?> 까지 아래의 "<b>계약서다운</b>"을 받아 날인 및 계약서 하단의 <b>CMS 동의란 작성</b> 하시고, 그 계약서를 스캔 받아 "<b>계약서등록</b>"에 돌려주시고 사업자등록증(고유번호증)도 스캔 받아 "<b>사업자등록</b>"에 올려 주십시오.<br><br><br>

									<b>※<?=$myF->dateStyle($R['stop_dt'],'KOR');?> 까지 계약서가 등록되지 않을 시 프로그램이 중단 됩니다.</b><br><br>
									<center>[<a href="../doc/doc_cont_excel.php?contDt=<?=$R['cont_dt'];?>" style="font-size:15px;"><b>계약서 다운로드</b></a>]</center>
									</p><?
									$IsFirst = false;
								}
							}?>
							<div style="margin-top:5px; font-size:15px;">
								[ <span onclick="lfDocReg(this,'<?=$R['cont_dt'];?>','<?=$R['doc_type'];?>','<?=$R['file_path'] ? 'Y' : 'N';?>');" style="width:auto; cursor:pointer;">
									<a href="#" style="font-weight:bold;" onclick="return false;"><?
										if ($R['doc_type'] == '01'){
											//계약서
											echo '계약서등록';
										}else if ($R['doc_type'] == '02'){
											//등록증
											echo '사업자등록';
										}?>
									</a>
								</span>]
							</div><?
						}

						if ($R1[0]['stop_dt'] >= Date('Ymd')){?>
							<div style="position:relative; text-align:right;">
								<a href="#" onclick="lfSetStop('<?=$R['cont_dt'];?>');">그만보기</a>
							</div><?
						}?>
						<input id="tmpSetDocId" type="hidden" value="">
					</div>
				</div>
				<form id="docF" name="docF" method="post" enctype="multipart/form-data">
					<div id="ID_POP_ORG_SUB" style="position:absolute; z-index:100; left:0; top:0; width:200px; height:130px; border:2px solid #363dcb; background-color:WHITE; display:none;">
						<div style="width:100%; text-align:right;">
							<div style="float:right; width:auto; margin-top:5px; margin-right:5px; cursor:pointer;"><img src="../popup/kacold_popup/btn_close.png" onclick="$('#ID_POP_ORG_SUB').hide();"></div>
						</div>
						<table style="width:100%; border:none;">
							<tr>
								<td style="background-color:WHITE; border:none;">
									<div style="padding-top:10px;">
										<div style="float:left; width:190px; font-size:13px; margin-left:5px;" class="nowrap" id="ID_CELL_FILE"></div>
									</div>
									<div style="width:100%; padding:5px;">
										<input type="file" name="docFile" id="docFile" style="width:100%;">
										<input type="hidden" id="docType">
										<input type="hidden" id="docContDt">
									</div>
									<div style="font-size:13px; text-align:center;">
										<img src="../popup/kacold_popup/btn_register.png" onclick="fileUpload();">
									</div>
								</td>
							</tr>
						</table>
					</div>
				</form><?
			}
			Unset($R1);
		}
	}
?>