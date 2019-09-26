<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지
	 *********************************************************/
	$orgNo = $_SESSION['userCenterCode'];
	$orgType = $hce->SR;

	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;

	$id = $_POST['id'];
	$sr = $_POST['sr'];
	
	
	$file = './hce_ispt_'.$id.'.php';

	if (Is_File($file)){?>
		<script type="text/javascrip">
			$(document).ready(function(){
				$('input:radio').unbind('click').bind('click',function(){
					if ($(this).val() == $(this).attr('otherVal')){
						$('#'+$(this).attr('otherObj')).css('background-color','#ffffff').attr('disabled',false).focus();
					}else{
						$('#'+$(this).attr('otherObj')).css('background-color','#efefef').attr('disabled',true);
					}

					if ($(this).attr('name') != 'optHeatMaterial'){
						if ($(this).attr('id') == 'optHeat_4'){
							$('input:radio[name="optHeatMaterial"]').css('background-color','#ffffff').attr('disabled',false).focus();
						}else{
							$('input:radio[name="optHeatMaterial"]').css('background-color','#efefef').attr('disabled',true);
						}
					}

					if ($(this).attr('name') == 'opt7'){
						if ($(this).attr('id') == 'opt7_5'){
							$('#txt7Nm').css('background-color','#efefef').attr('disabled',true);
							$('#txt7Tel').css('background-color','#efefef').attr('disabled',true);
						}else{
							$('#txt7Nm').css('background-color','#ffffff').attr('disabled',false).focus();
							$('#txt7Tel').css('background-color','#ffffff').attr('disabled',false);
						}
					}
				});

				$('input:checkbox').unbind('click').bind('click',function(){
					/*if ($(this).val() == $(this).attr('otherVal')){
						$('#'+$(this).attr('otherObj')).css('background-color','#ffffff').attr('disabled',false).focus();
					}else{
						$('#'+$(this).attr('otherObj')).css('background-color','#efefef').attr('disabled',true);
					}*/
					lfChkDisable(this)
				});

				$('input:radio:checked').click();

				$('input:checkbox:checked').each(function(){
					lfChkDisable(this);
				});
			});

			function lfChkDisable(obj){
				if ($(obj).val() == $(obj).attr('otherVal')){
					$('#'+$(obj).attr('otherObj')).css('background-color','#ffffff').attr('disabled',false).focus();
				}else{
					$('#'+$(obj).attr('otherObj')).css('background-color','#efefef').attr('disabled',true);
				}
			}
		</script><?

		include_once($file);
	}

	include_once('../inc/_db_close.php');
?>