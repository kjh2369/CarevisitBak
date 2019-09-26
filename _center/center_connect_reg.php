<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	/*
	 *	기관연결정보 등록
	 */

	$orgNo	= $_REQUEST['orgNo'];
	$type	= $_REQUEST['type'];
	$today	= Date('Ymd');

	if ($type == 'Contract'){
		$title = '계약정보 등록 및 변경';
	}else if ($type == 'Branch'){
		$title = '지사정보 등록 및 변경';
	}else if ($type == 'Account'){
		$title = '출금계좌정보 등록 및 변경';
	}else if ($type == 'Service'){
		$title = '계약서비스 등록 및 변경';
	}else if ($type == 'Deposit'){
		$title = '무통장입금 등록';
	}else if ($type == 'Tax'){
		$title = '세금계산서 발행이력';
	}else if ($type == 'StopSet'){
		$title = '미납 및 중지팝업 설정';
	}else{?>
		<script type="text/javascript">
			$(document).ready(function(){
				self.close();
			});
		</script><?
		exit;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		$('textarea').each(function(){
			__init_object(this);
		});

		self.focus();
	});

	window.onunload = function(){
		opener.lfResult('<?=$type;?>');
	}

	function lfContMove(pos, posDt){
		if (!posDt){
			if ($('#cboRsCd').val() == '2' || $('#cboRsCd').val() == '4'){
				posDt = $('#txtContDt').attr('orgDt').split('-').join('');
			}else{
				posDt = $('#txtFromDt').attr('orgDt');
			}
		}

		var parm = new Array();
			parm = {
				'orgNo':'<?=$orgNo;?>'
			,	'type':'<?=$type;?>'
			,	'pos':pos
			,	'posDt':posDt
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

		form.setAttribute('method', 'post');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border"><?=$title;?></div><?
if ($type == 'Contract'){
	include_once('./center_connect_reg_contract.php');
}else if ($type == 'Branch'){
	include_once('./center_connect_reg_branch.php');
}else if ($type == 'Account'){
	include_once('./center_connect_reg_account.php');
}else if ($type == 'Service'){
	include_once('./center_connect_reg_service.php');
}else if ($type == 'Tax'){
	include_once('./center_connect_reg_tax_his.php');
}else if ($type == 'StopSet'){
	include_once('./center_connect_reg_stopset.php');
}else if ($type == 'Deposit'){?>
	<script type="text/javascript">
		$(document).ready(function(){
			lfLoadBody();
		});

		function lfLoadBody(){
			$.ajax({
				type:'POST'
			,	url:'./center_acct_in_reg.php'
			,	data:{
					'orgNo':'<?=$orgNo;?>'
				}
			,	beforeSend:function(){
				}
			,	success:function(html){
					$('#ID_TYPE_BODY').html(html);

					var obj = __GetTagObject($('#ID_CMS_LIST'),'DIV');
					$(obj).height(__GetHeight($(obj)));

					$('input:text',$('#ID_TYPE_BODY')).each(function(){
						__init_object(this);
					});
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
	<div id="ID_TYPE_BODY"></div><?
}

	if ($type != 'Deposit' && $type != 'StopSet'){?>
		<div style="position:absolute; bottom:-13px;">
			<table id="tblBtn" class="my_table" style="width:100%;">
				<colgroup>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<td class="center bottom last" style="padding-top:10px; padding-bottom:20px;"><?
							if ($type == 'Contract'){?>
								<div style="float:right; width:auto; margin-right:5px;">
									<span class="btn_pack m"><button onclick="lfContMove(-1);">이전계약</button></span>
									<span class="btn_pack m"><button onclick="lfContMove(1);">다음계약</button></span>
								</div>
								<div style="float:left; width:auto; margin-left:5px;">
									<!--span class="btn_pack m"><button onclick="lfContDel();" style="color:RED;">계약삭제</button></span-->
								</div><?
							}?>
							<div style="float:center; width:auto;"><?
								if ($type != 'Tax' && $type != 'Service'){
									if ($type == 'StopSet'){?>
										<span class="btn_pack m"><button id="ID_BTN_SAVE" onclick="lfStopSet('Y');">중지설정</button></span>
										<span class="btn_pack m"><button id="ID_BTN_CANCEL" onclick="lfStopSet('N');">중지해제</button></span><?
									}else{?>
										<span class="btn_pack m"><button id="ID_BTN_SAVE" onclick="lfSave();">저장</button></span><?
									}
								}?>
								<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div><?
	}
	include_once('../inc/_footer.php');
?>