<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$type = $_GET['type'];
?>
<script type="text/javascript">

</script>
<div class="title title_border">메뉴관리</div>
<form id="f" name="f" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody><?
		$sql = 'SELECT	*
				FROM	menu_top
				ORDER	BY seq,id';

		$menuTop = $conn->_fetch_array($sql);

		$first = true;
		$border = 'border-top:1px solid #d9d9d9;';

		if (Is_Array($menuTop)){
			foreach($menuTop as $top){
				$str = lfMsg($top);?>
				<tr>
					<th class="bold bottom last" style="<?=(!$first ? $border : '');?>" colspan="2"><?=$top['id'].'. '.$top['name'].' ['.$str.']';?></th>
				</tr><?

				$first = false;

				$sql = 'SELECT	*
						FROM	menu_left
						WHERE	m_top	= \''.$top['id'].'\'
						ORDER	BY seq,id';

				$menuLeft = $conn->_fetch_array($sql);

				if (Is_Array($menuLeft)){
					foreach($menuLeft as $left){
						if ($topId != $top['name']){
							$topId  = $top['name'];
							$topBd	= $border;
						}else{
							$topBd	= '';
						}

						$str = lfMsg($left);?>
						<tr>
							<td class="bottom" style="<?=$topBd;?>"></td>
							<th class="bottom last" style="border-top:1px solid #d9d9d9;"><?=$left['id'].'. '.$left['name'].' ['.$str.']';?></th>
						</tr>
						<tr>
							<td class="bottom" style=""></td>
							<td class="bottom last" style="border-top:1px solid #d9d9d9; padding-left:20px;"><?
								$sql = 'SELECT	*
										FROM	menu_list
										WHERE	m_top	= \''.$top['id'].'\'
										AND		m_left	= \''.$left['id'].'\'
										ORDER	BY seq,id';

								$conn->query($sql);
								$conn->fetch();

								$rowCnt = $conn->row_count();

								for($i=0; $i<$rowCnt; $i++){
									$row = $conn->select_row($i);
									$str = lfMsg($row);?>
									<div style=""><?=$row['id'].'. '.$row['name'].' ['.$str.']';?></div><?
								}

								$conn->row_free();?>
							</td>
						</tr><?
					}
				}
			}
		}?>
	</tbody>
</table>
</form>
<?
	function lfMsg($row){
		if ($row['permit'] == 'A'){
			$str = '전체';
		}else if ($row['permit'] == 'C'){
			$str = '재가요양';
		}else if ($row['permit'] == 'V'){
			$str = '바우처';
		}else if ($row['permit'] == 'S'){
			$str = '재가지원';
		}else if ($row['permit'] == 'R'){
			$str = '자원연계';
		}

		if ($row['debug'] == 'Y'){
			$str .= ($str ? '/' : '').'<span style="color:blue;">테스트</span>';
		}

		if ($row['use_yn'] != 'Y'){
			$str .= ($str ? '/' : '').'<span style="color:red;">사용안함</span>';
		}

		if ($row['demo_yn'] == 'Y'){
			$str .= ($str ? '/' : '').'<span style="color:blue;">데모출력</span>';
		}

		return $str;
	}

	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>