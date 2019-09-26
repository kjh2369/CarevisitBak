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
		form.setAttribute('action', '../_center/center.php?menu=GoodEOSCenter'+para);

		if (popYn == 'Y'){
			form.setAttribute('target', 'POP_'+menuId);
		}

		document.body.appendChild(form);
		form.submit();
	}
</script>
<div id="left_box">
	<h2>기관관리</h2>
	<ul id="sidnav">
		<li><a style="cursor:default;">기관관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="lfMenu('CENTER_REG');">기관등록</a></li>
				<li><a href="#" onclick="location.href='../center/list.php?menu=GoodEOSCenter'; return false;">기관조회</a></li>
				<!--li><a href="#" onclick="lfMenu('DOC');">계약서/등록증 관리</a></li-->
			</ul>
		</li>
	</ul>
</div>