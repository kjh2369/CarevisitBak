<?
	include_once('../inc/_root_return.php');
?>
<script type="text/javascript">
	function lfMenu(menuId){
		var parm = new Array();
			parm = {
				'menuId':menuId
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
		form.setAttribute('action', './charge.php?menu=GoodEOSCharge');

		document.body.appendChild(form);
		form.submit();
	}
</script>
<div id="left_box">
	<h2>요금관리</h2>
	<ul id="s_gnb">
		<li><a style="cursor:default;">기관요금관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="lfMenu('CHARGE_HOMECARE');">재가요양</a></li>
			</ul>
		</li>
	</ul>
</div>