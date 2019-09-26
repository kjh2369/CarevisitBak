<?
	include_once('../inc/_root_return.php');
	include_once('../inc/_db_open.php');

	$sql = 'select name 
			  from menu_left
			 where menu_top = \'H\'
			   and id		= \'3\'';
	$menu_name = $conn -> get_data($sql); 

	echo '<div id="left_box">
				<h2>2014평가자료</h2>
				<ul id="s_gnb">
					<li><a style="cursor:default;"><span>'.$menu_name.'</span></a>
					<ul id="sub_menu">';

	$sql = 'SELECT	m_top, m_left, id,name,url,link_gbn,permit,debug
			FROM	menu_list
			WHERE	m_top	= \'H\'
			AND		m_left	= \'3\'
			ORDER	BY seq,id';
	$conn -> query($sql);
	$conn -> fetch():

	$rowCnt = $conn -> row_count();

	for($i=0; $i<$rowCnt; $i++){
		
		echo ' <li><a href="#" onclick="<?=$link;?>return false;" style="'.($row['debug'] == 'Y' ? 'color:red;' : '').'">'.$row['name'].'</a></li>';
		
			
	} 
	
	echo '</ul></li></ul>
		</div>';


	$conn -> row_free();

?>