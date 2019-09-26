<?
	include_once('../inc/_root_return.php');
?>
<script type="text/javascript">
	function lfMenu(menuId, para, popYn){
		if (!para) para = '';
		if (!popYn) popYn = 'N';

		var parm = new Array();
			parm = {
				'menuId':menuId
			,	'popYn':popYn
			};
		var win = null;

		if (popYn == 'Y'){
			win = window.open('about:blank', 'POP_'+menuId, 'left=100, top=100, width=1024, height=768, scrollbars=yes, status=no, resizable=yes');
			win.opener = self;
			win.focus();
		}

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
		form.setAttribute('action', '../_center/center.php?menu=GoodEOSClaim'+para);

		if (popYn == 'Y'){
			form.setAttribute('target', 'POP_'+menuId);
		}

		document.body.appendChild(form);
		form.submit();
	}
</script>
<div id="left_box">
	<h2>요금 및 입금관리</h2><?
	if ($_SESSION['userLevel'] == 'A'){?>
		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">요금 및 입금관리</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="lfMenu('FEE_MAKE');">청구요금생성</a></li>
					<!--<li><a href="#" onclick="lfMenu('FEE_LIST');">요금내역</a></li>-->
					<li><a href="#" onclick="lfMenu('FEE_EDIT');">청구요금조정</a></li>
					<li><a href="#" onclick="lfMenu('FEE_EXCEL');">청구요금Excel</a></li>

					<li><a href="#" onclick="lfMenu('PAY_IN_REG');">입금등록</a></li>
					<li><a href="#" onclick="lfMenu('PAY_IN_LIST');">입금내역조회</a></li>

					<!--li><a href="#" onclick="lfMenu('ACCT_CMS');">입금등록(CMS,무통장)</a></li-->

					<!--li><a href="#" onclick="lfMenu('ACCT_LINK');">입금적용</a></li-->

					<!--li><a href="#" onclick="lfMenu('IN_LIST');">입금내역조회</a></li-->
					<!--li><a href="#" onclick="lfMenu('IN_OVER');">과입금내역조회</a></li-->

					<!--
					<li><a href="#" onclick="lfMenu('ACCT_DEPOSIT');">입금관리</a></li>
					<li><a href="#" onclick="lfMenu('ACCT_COMPANY');">청구내역</a></li>
					<li><a href="#" onclick="lfMenu('ACCT_MONTH');">월별청구내역</a></li>
					-->
				</ul>
			</li>
			<li class="top_line"><a style="cursor:default;">청구관리</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="lfMenu('TAX');">세금계산서발행이력</a></li>
					<li><a href="#" onclick="lfMenu('CLAIM_LIST');">청구내역(월별)</a></li>
					<li><a href="#" onclick="lfMenu('CLAIM_ORG');">청구내역(기관별)</a></li>
					<li><a href="#" onclick="lfMenu('CLAIM_ORG_DTL');">청구내역(기관별 상세)</a></li>
					<li><a href="#" onclick="lfMenu('CLAIM_ACCT');">청구 및 입금내역(월별)</a></li>
					<li><a href="#" onclick="lfMenu('CLAIM_ACCT_ORG');">청구 및 입금내역(기관별)</a></li>
					<li><a href="#" onclick="lfMenu('DEFAULT');">미납기관내역(기관별)</a></li>
					<li><a href="#" onclick="lfMenu('POPUP_DEF');">미납기관 팝업설정</a></li>
				</ul>
			</li>
			<li class="top_line"><a style="cursor:default;">마감관리</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="lfMenu('CLSMG');">마감관리</a></li>
					<li><a href="#" onclick="lfMenu('CLAIM_YYMM');">청구설정</a></li>
				</ul>
			</li>
		</ul><?
	}?>
</div>