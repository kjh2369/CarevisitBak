<?
	include_once('../inc/_root_return.php');
?>
<div id="left_box">
	<h2>지사관리</h2>
	<ul id="s_gnb">
		<li><a style="cursor:default;">지사관리</a>
			<ul id="sub_menu">
			<?
				switch($_SESSION['userLevel']){
					case 'A':
						echo '<li><a href=\'#\' onclick=\'location.href="../branch/branch_reg.php?mode='._BRAN_.'&menu=branch";\'>지사등록</a></li>';
						echo '<li><a href=\'#\' onclick=\'location.href="../branch/branch_list.php?mode='._BRAN_.'&menu=branch";\'>지사조회</a></li>';
						echo '<li><a href=\'#\' onclick=\'location.href="../branch/inchange_reg.php?mode='._BRAN_.'&menu=branch";\'>담당자등록</a></li>';
						echo '<li><a href=\'#\' onclick=\'location.href="../branch/inchange_list.php?mode='._BRAN_.'&menu=branch";\'>담당자조회</a></li>';
						//echo '<li><a href=\'#\' onclick=\'location.href="../branch/branch2center.php";\'>지사/기관연결</a></li>';
						break;
					case 'B':
						echo '<li><a href=\'#\' onclick=\'location.href="../branch/branch_reg.php?mode='._BRAN_.'&menu=branch";\'>지사관리</a></li>';
						echo '<li><a href=\'#\' onclick=\'location.href="../branch/inchange_reg.php?mode='._BRAN_.'&menu=branch";\'>담당자등록</a></li>';
						echo '<li><a href=\'#\' onclick=\'location.href="../branch/inchange_list.php?mode='._BRAN_.'&menu=branch";\'>담당자조회</a></li>';
						break;
					default:
						return;
				}
			?>
			</ul>
		</li>
	</ul>
</div>