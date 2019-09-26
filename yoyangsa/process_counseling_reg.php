<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$type  = strtolower($_POST['type']);
	
	$group_btn = '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:10px; margin-bottom:10px;\'>';
	$group_btn .= '<colgroup>';
	$group_btn .= '<col>';
	$group_btn .= '</colgroup>';
	$group_btn .= '<tbody>';
	$group_btn .= '<tr>';
	$group_btn .= '<th class=\'right my_border_blue\'>';
	
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'save\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'lfSave();\'>저장</button></span> ';

	$group_btn .= '</th>';
	$group_btn .= '</tr>';
	$group_btn .= '</tbody>';
	$group_btn .= '</table>';

?>

<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
	});

	function lfSave(){
	
		var f = document.f;
		
		f.target = '_self';
		
		f.action = '../yoyangsa/counsel_member_save.php';
		f.submit();
	}

</script>

<?
	echo $group_btn;
	
	echo '<form name="f" method="post">';
	
	if($type == 'process'){
		include_once('../counsel/mem_stress_reg.php');
	}else {
		include_once('../counsel/client_counsel_'.$type.'_reg.php');
	}

	echo '<input name="mode" type="hidden" value="'.$type.'">';
	echo '<input name="is_pop" type="hidden" value="Y">';
	echo '</form>';
	
	echo $group_btn;
	
	include_once('../inc/_db_close.php');
	include_once('../inc/_footer.php');

?>

