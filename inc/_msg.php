<?php
	include_once('../inc/_header.php');

	$gubun = $_GET['gubun'];

	ob_start();

	switch($gubun){
		case 100:
			echo '<script>';
			echo 'function cur_close(rst){
					window.returnValue = rst;
					win_close();
				  }';
			echo 'function win_close(){
					window.close();
				  }';
			echo 'window.onload = function(){
					self.focus();
				  }';
			echo '</script>';

			echo '<div style=\'width:100%; height:100%; padding:10px 10px 0 10px; border-bottom:none;\'>';
			echo '<div class=\'my_border_blue\' style=\'width:100%; height:30px; padding-top:7px;\'>';
			echo '<p style=\'font-weight:bold; text-align:center;\'>[전체복사 구분]</p>';
			echo '</div>';
			echo '<div class=\'my_border_blue\' style=\'width:100%; height:auto; text-align:center; border-top:none; padding:20px;\'>';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'cur_close(1);\' style=\'font-weight:bold;\'>&nbsp;&nbsp;기존에 등록된 실적데이타는 수정하지 않음.&nbsp;&nbsp;&nbsp;</button></span>';
			echo '<br><br>';
			echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'cur_close(2);\' style=\'font-weight:bold;\'>&nbsp;기존에 등록된 실적데이타를 포함하여 수정함.&nbsp;</button></span>';
			echo '</div>';
			echo '</div>';
			break;
	}

	$html = ob_get_contents();

	ob_clean();

	echo $html;

	include_once('../inc/_footer.php');
?>