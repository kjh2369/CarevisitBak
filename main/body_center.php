<?
	include_once('../inc/_http_uri.php');

	if ($gDomain == 'kacold.net'){
		$imgpath = $gHostImgPath.'/img_emplem_'.$_SESSION['userArea'].'.jpg';
		if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/img_emplem.jpg';
	}else{
		$imgpath = $gHostImgPath.'/img_emplem.jpg';
	}
?>
<table style="width:1024px;">
	<colgroup>
		<col width="32%">
		<col width="32%">
		<col width="36%">
	</colgroup>
	<tr>
		<td class="tmp_0 tmp_3" colspan="2" rowspan="2" style="background:url(<?=$imgpath;?>) no-repeat; <?=($gDomain == 'kacold.net' ? 'height:357px;' : '');?>"><?
			if ($gDomain == 'kacold.net' || //한국재가노인복지협회
				$gDomain == 'forweak.net' ){ //다케어
				if($gDomain == 'forweak.net'){ ?>
					<!-- 4대보험가입내역 -->
					<div style="position:relative; top:25px; text-align:right; cursor:pointer;" onclick="__go_menu('center','../yoyangsa/mem_4insu.php');"><img src='../img/btn_sys.png' /></div><?
				}
			}else {
				if (!$isDemo){?>
					<!-- 4대보험가입내역 -->
					<div style="position:relative; top:24px; text-align:right;"><img src='../img/btn_sys.png' onclick="__go_menu('center','../yoyangsa/mem_4insu.php');" style="cursor:pointer;" /></div>

					<!-- 재무회계 --><?
					if ($gDomain == 'carevisit.net'){ ?>
						<!--div style="position:relative; top:30px; text-align:right;" ><img src='../img/btn_finance.png' onclick="window.open('../popup/labor/index.html','REPORT','width=386,height=587,left=550,top=50,scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no');" style="cursor:pointer;" /></div--><?
					}
				}
			}?>
		</td>
		<!--td class="tmp_0 tmp_3"></td-->
		<td class="tmp_2 tmp_3"><? include_once('body_board_company.php');?></td>
	</tr>

	<tr>
		<!--td class="tmp_0 tmp_3"></td-->
		<!--td class="tmp_0 tmp_3"></td-->
		<td class="tmp_2 tmp_3"><?
			if ($gDomain != 'kacold.net'){
				include_once('body_board_mananul.php');
			}else{
				include_once('body_board_temp.php');
			}?>
		</td>
	</tr>
	<tr>
		<td class="tmp_0"><?
			if ($gDomain != 'kacold.net'){
				if ($gDomain == 'vaerp.com'){
					if ($debug){
						include_once('body_board_dataroom.php');
					}else{
						include_once('body_board_labor.php');
					}
				}else{
					include_once('body_board_labor.php');
				}
			}?>
		</td>
		<td class="tmp_0"><?
			if ($gDomain != 'kacold.net'){
				if ($gDomain == 'vaerp.com'){
					include_once('./body_board_qna.php');
				}else{
					include_once('body_board_free.php');
				}
			}?>
		</td>
		<td class="tmp_0" style="border-right:none;"><?
			if ($gDomain != 'kacold.net'){
				include_once('body_board_notice.php');
			}?>
		</td>
	</tr>
</table>