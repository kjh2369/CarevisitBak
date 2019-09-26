<?
	include_once('../inc/_login.php');

	if ($isDemo) return;

	//가상계좌
	/*$sql = 'SELECT	a.vr_no, b.bank_nm
			FROM	cv_vr_list AS a
			INNER	JOIN	cv_bank AS b
					ON		b.bank_cd = a.bank_cd
			WHERE	a.org_no = \''.$_SESSION['userCenterCode'].'\'
			AND		a.key_yn = \'Y\'';

	$brR = $conn->get_array($sql);*/?>
		<style type="text/css" media="screen">
		#ID_QUICK_MENU {
			position:absolute;
			height:131px;   /* 퀵메뉴, 배너 이미지의 높이 */
			width:144px;    /* 퀵메뉴, 배너 이미지의 너비*/
			margin:0px 0px 0px 1024px;   /* 가장 오른쪽의 수치가 화면 가운데에서 얼마만큼 오른쪽으로 레이어를 붙일 것인지 설정  */
			top: 120px;  /* 배너 상단에서 얼마나 떨어뜨릴지 설정*/
			left: 10px;     /* 레이어의 시작점이 왼쪽으로 부터 50% 지정 */
			text-align: left;
			padding: 0px;
		}
		</style>
		<script type="text/javascript">
			$(function() {
				var offset = $("#ID_QUICK_MENU").offset();
				var topPadding = 15;
				$(window).scroll(function() {
					if ($(window).scrollTop() > offset.top) {
						$("#ID_QUICK_MENU").stop().animate({
						marginTop: $(window).scrollTop() - offset.top + topPadding
						}, 500);
					} else {
						$("#ID_QUICK_MENU").stop().animate({
							marginTop: 0
						});
					};
				});
			});
		</script>

		<div id="ID_QUICK_MENU" style="font-weight:bold; background:url('../image/vr_bg.png');"><?
			/*
				<div style="margin-left:12px; margin-top:55px;">은행명: <?=$brR['bank_nm'];?></div>
				<div style="margin-left:12px; margin-top:12px; color:#980000;"><?=$brR['vr_no'];?></div>
			 */?>
			<div style="margin-left:12px; margin-top:55px;">은행명: 농협</div>
			<div style="margin-left:12px; margin-top:12px; color:#980000;">301-0164-4623-31</div>
			<div style="margin-left:12px; margin-top:12px;">(주)케어비지트</div>
		</div><?
	//}

	Unset($brR);
?>