<div class="title">자료실</div>
<?
	include_once('../inc/_header.php');

	$id   = $_REQUEST['id'];
	
?>
<table id="tblList" class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr >
			<th class="head" style="height:255px;">내용</th>
			<td class="left top" ><?
				if (!empty($id)){
		
					$sql = 'SELECT	content
							FROM	han_dataroom
							WHERE	area_cd	= \'\'
							AND     dataroom_id = \''.$id.'\'
							AND		group_cd= \'\'
							AND		del_flag= \'N\'
							UNION	ALL
							SELECT	content
							FROM	han_dataroom
							WHERE	area_cd	= \''.$_SESSION['userArea'].'\'
							AND     dataroom_id = \''.$id.'\'
							AND		group_cd= \'\'
							AND		del_flag= \'N\'
							UNION	ALL
							SELECT	content
							FROM	han_dataroom
							WHERE	area_cd	= \''.$_SESSION['userArea'].'\'
							AND		group_cd= \''.$_SESSION['userGroup'].'\'
							AND     dataroom_id = \''.$id.'\'
							AND		del_flag= \'N\'';
					
					$str = $conn->get_data($sql);

					echo $str;
				} ?>
			</td>
		</tr>
		<tr >
			<th class="head" style="height:80px;">첨부파일</th>
			<td class="left top" ><?
				$sql = 'SELECT	seq
						,		file_path
						,		file_name
						,		file_type
						,		file_size
						FROM	han_dataroom_files
						WHERE	dataroom_id = \''.$id.'\'';
			
				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);
					$size = $row['file_size'];

					$depth = 0;
					while(true){
						if ($size > 1000){
							$size /= 1000;
							$depth ++;
						}else{
							break;
						}
					}

					$size = Round($size,2);

					if ($depth == 1){
						$gbn = 'KB';
					}else if ($depth == 2){
						$gbn = 'MB';
					}else if ($depth == 3){
						$gbn = 'GB';
					}else if ($depth == 4){
						$gbn = 'TB';
					}else{
						$gbn = 'Byte';
					}?>
					<div class="clear left">
						<div style="float:left; width:auto;"><a href="#" onclick="lfAttachDownload('<?=$id;?>','<?=$row['seq'];?>'); return false;"><?=$row['file_name'];?></a>[<?=$size.$gbn;?>]</div>
					</div><?
				}

				$conn->row_free();
			?>	
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr style="height:25px;">
			<td colspan="2" style="text-align:right;">
				<div style="float:right; width:auto; padding-right:5px;"><a id="btnGbn" href="#" onclick="self.close();">닫기</a></div>
			</td>
		</tr>
	</tfoot>
</table>

<?
	include_once("../inc/_db_close.php");

?>
<script type='text/javascript'>
	function lfAttachDownload(id,seq){
		var parm = new Array();
			parm = {
				'type'	:'DATAROOM'
			,	'mode'	:'ATTACH_DOWNLOAD'
			,	'id'	:id
			,	'seq'	:seq
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

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', '../goodeos/han_dataroom_fun.php');

		document.body.appendChild(form);

		form.submit();
	}

	
</script>