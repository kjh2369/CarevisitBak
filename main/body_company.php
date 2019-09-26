<?
	include_once('../inc/_http_uri.php');
?>
<table style="width:1024px;">
	<colgroup>
		<col width="32%">
		<col width="32%">
		<col width="36%">
	</colgroup>
	<tr><?
		if ($gDomain == 'dolvoin.net' && $_SESSION['userCode'] == 'carevisit'){
			$img_emplem = '../admin_img/sso/img_emplem.jpg';
		}else{
			$img_emplem = $gHostImgPath.'/img_emplem.jpg';
		}?>
		<td class="tmp_0 tmp_3" colspan="2" rowspan="2"><img src='<?=$img_emplem;?>'></td>
		<!--td class="tmp_0 tmp_3"></td-->
		<td class="tmp_2 tmp_3"><? include_once('body_board_company.php');?></td>
	</tr>

	<tr>
		<!--td class="tmp_0 tmp_3"></td-->
		<!--td class="tmp_0 tmp_3"></td-->
		<td class="tmp_2 tmp_3"><? include_once('body_board_mananul.php');?></td>
	</tr>

	<tr>
		<td class="tmp_0"><?
			if ($gDomain == 'vaerp.com'){
				include_once('body_board_notice.php');
			}else{
				include_once('body_board_notice.php');
			}?></td>
		<td class="tmp_0"><?
			if ($gDomain == 'vaerp.com'){
				//문의게시판
				include_once('./body_board_qna.php');
			}else{
				//자유계시판
				include_once('body_board_free.php');
			}?></td>
		<td class="tmp_2"><? include_once('body_board_infomation.php');?></td>
	</tr>
</table>