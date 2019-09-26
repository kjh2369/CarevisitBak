<div class="tmp_margin_1">
	<div style="width:auto; float:left; padding-left:5px;"><img src="../image/caption_3_vaerp_com.gif"></div>
	<div style="width:auto; float:right; padding-right:5px;"><img src="../image/more.gif" style="cursor:pointer;" onclick="__go_menu('other','../news/board_list.php?board_type=L');"></div>
</div>
<div class="tmp_margin_3">
	<div style="width:auto; float:left;"><img src="../image/board_3.jpg"></div>
	<div style="width:auto; float:left;"><?
		$sql = 'SELECT	brd_cd
				,		brd_id
				,		subject
				FROM	board_list
				WHERE	org_no	= \''._COM_CD_.'\'
				AND		brd_type= \'data\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		del_yn	= \'N\'
				ORDER	BY reg_dt DESC
				LIMIT	4';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<div class="tmp_arrow nowrap" style="width:180px; text-align:left; padding-left:20px;"><a href="javascript:lfShowDR('<?=_COM_CD_;?>','<?=$row['brd_cd'];?>','<?=$row['brd_id'];?>');" onclick="return;"><?=StripSlashes($row['subject']);?></a></div><?
		}

		$conn->row_free();?>
	</div>
</div>
<script type="text/javascript">
	function lfShowDR(orgNo,cd,id){
		var width	= 800;
		var height	= 600;
		var top		= (screen.availHeight - height) / 2;
		var left	= (screen.availWidth - width) / 2;

		var target = 'POP_BOARD_REG';
		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = '../board/board_reg.php';
		var win = window.open('', target, option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type'	:'data'
			,	'orgNo'	:orgNo
			,	'cd'	:cd
			,	'id'	:id
			,	'mode'	:'data'
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

		form.setAttribute('target', target);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>