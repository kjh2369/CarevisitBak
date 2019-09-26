<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$conn->fetch_type = 'assoc';

	$code      = $_GET['code'];
	$send_type = $_GET['send_type'];
?>

<form name='f' method='post'>
<div class='title title_border'>찾아보기</div>
<div id="list_body" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100;">
<?
	###################################################
	#
	# 본사 및 지사조회
		$sql = "select b00_code as cd, b00_name as nm, b00_manager as manager, b00_com_yn as com
				  from b00branch
				 where b00_stat   = '1'
				   and b00_com_yn = 'Y'
				 order by b00_name";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$company[$row['cd']] = $row;
		}

		$conn->row_free();

		$sql = "select b00_code as cd, b00_name as nm, b00_manager as manager, b00_com_yn as com
				  from b00branch
				 where b00_stat   = '1'
				   and b00_com_yn = 'N'
				 order by b00_name";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$branch[$row['cd']] = $row;
		}

		$conn->row_free();
	#
	###################################################

	###################################################
	#
	# 본사직원조회
		foreach($company as $i =>$a){

			###################################################
			# 본사 및 지사 직원조회
			$sql = "select b01_branch as branch, b01_code as cd, b01_id as id, b01_name as nm, b01_position as position
					  from b01person
					 where b01_branch = '".$a['cd']."'
					   and b01_stat   = '1'
					 order by b01_name";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$person[$row['branch']][$row['cd']] = $row;
			}

			$conn->row_free();
		}
	#
	###################################################

	###################################################
	#
		foreach($branch as $i =>$b){

			###################################################
			# 본사 및 지사 직원조회
			$sql = "select b01_branch as branch, b01_code as cd, b01_id as id, b01_name as nm, b01_position as position
					  from b01person
					 where b01_branch = '".$b['cd']."'
					   and b01_stat   = '1'
					 order by b01_name";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$person[$row['branch']][$row['cd']] = $row;
			}

			$conn->row_free();

			###################################################
			# 가맹점 조회
			$sql = "select branch, m00_mcode as cd, m00_cname as nm, m00_mname as manager
					  from m00center
					 inner join (
						   select b02_branch as branch, b02_center as center, min(b02_kind) as kind
							 from b02center
							where b02_branch = '".$b['cd']."'
							group by b02_center
						   ) as branch
						on center = m00_mcode
					   and kind   = m00_mkind";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$center[$row['branch']][$row['cd']] = $row;
			}

			$conn->row_free();
		}
	#
	###################################################

	###################################################
	#
	# 가맹점 직원조회
		foreach($branch as $i =>$b){
			foreach($center[$b['cd']] as $i => $c){
				$sql = "select m02_ccode as cd, m02_dept_cd as dept_cd, dept_nm, m02_mem_no as id, m02_yname as nm
						  from m02yoyangsa
						 inner join dept
							on org_no    = m02_ccode
						   and dept_cd   = m02_dept_cd
						   and del_flag  = 'N'
						 where m02_ccode = '".$c['cd']."'
						   and m02_mkind =  ".$conn->_mem_kind()."
						 order by m02_yname";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					$member[$c['branch']][$row['cd']]['cd']      = $row['cd'];
					$member[$c['branch']][$row['cd']]['dept_cd'] = $row['dept_cd'];
					$member[$c['branch']][$row['cd']]['dept_nm'] = $row['dept_nm'];
					$member[$c['branch']][$row['cd']]['id']      = $row['id'];
					$member[$c['branch']][$row['cd']]['nm']      = $row['nm'];
				}

				$conn->row_free();
			}
		}
	#
	###################################################

	###################################################
	#
	# 본사
		echo '<div style=\'padding-left:0px;\'><input name=\'company_all\' type=\'checkbox\' class=\'checkbox\' value=\'all\'><a href=\'#\' onclick=\'return set_check("tree_company");\'>본사</a></div>';

		echo '<div id=\'tree_company\' style=\'display:none;\'>';
		foreach($company as $i =>$a){
			echo '<div style=\'padding-left:17px;\'><input name=\'company[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$a['cd'].'\'>'.$a['nm'].'['.$a['manager'].']</div>';

			echo '<div style=\'padding-left:34px;\'><input name=\'member_'.$a['cd'].'[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$a['cd'].'\'>직원</div>';

			foreach($person[$a['cd']] as $ii => $p){
				echo '<div style=\'padding-left:51px;\'><input name=\'member_'.$p['branch'].'_'.$p['cd'].'[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$p['id'].'\'>'.$p['nm'].'</div>';
			}
		}
		echo '</div>';
	#
	###################################################


	###################################################
	#
	# 지사
		echo '<div style=\'padding-left:0px;\'><input name=\'branch\' type=\'checkbox\' class=\'checkbox\' value=\'branch\'><a href=\'#\' onclick=\'return set_check("tree_branch");\'>지사</a></div>';

		echo '<div id=\'tree_branch\' style=\'display:none;\'>';
		foreach($branch as $i =>$b){
			echo '<div style=\'padding-left:17px;\'><input name=\'branch[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$b['cd'].'\'>'.$b['nm'].'['.$b['manager'].']</div>';

			echo '<div style=\'padding-left:34px;\'><input name=\'member_'.$b['cd'].'[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$b['cd'].'\'>직원</div>';

			foreach($person[$b['cd']] as $ii => $p){
				echo '<div style=\'padding-left:51px;\'><input name=\'member_'.$p['branch'].'_'.$p['cd'].'[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$p['id'].'\'>'.$p['nm'].'</div>';
			}

			echo '<div style=\'padding-left:34px;\'><input name=\'center_'.$b['cd'].'[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$b['cd'].'\'>가맹점</div>';

			foreach($center[$b['cd']] as $ii => $c){
				echo '<div style=\'padding-left:51px;\'><input name=\'center_'.$c['branch'].'_'.$c['cd'].'[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$c['cd'].'\'>'.$c['nm'].'['.$c['manager'].']'.'</div>';

				foreach($member[$c['branch']] as $iii => $m){
					echo '<div style=\'padding-left:68px;\'><input name=\'member_'.$c['branch'].'_'.$m['cd'].'[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$m['id'].'\'>'.$m['nm'].'['.$m['dept_nm'].']'.'</div>';
				}
			}
		}
		echo '</div>';
	#
	###################################################
?>
</div>
</form>
<?
	include_once("../inc/_footer.php");
?>
<script language='javascript'>
<!--

function current(){
	var chk = document.getElementsByName('check[]');
	var rst = '';

	for(var i=0; i<chk.length; i++){
		if (chk[i].checked)
			rst += chk[i].value + '//';
	}

	if (rst == ''){
		alert('리스트를 선택하여 주십시오.');
		return;
	}

	window.returnValue = rst;
	window.close();
}

function quit(){
	window.returnValue = '';
	window.close();
}

function set_check(id){
	var tree = document.getElementsByName(id);

	for(var i=0; i<tree.length; i++){
		if (tree[i].style.display != ''){
			tree[i].style.display  = '';
		}else{
			tree[i].style.display  = 'none';
		}
	}

	return false;
}

window.onload = function(){
	var body = document.getElementById('list_body');
	var h    = document.body.offsetHeight - 42;

	body.style.height = h;

	__init_form(document.f);
}

-->
</script>