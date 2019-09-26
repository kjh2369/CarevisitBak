<table id="tbl_desire" class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col span="3">
		<col width="80px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">년월</th>
			<th class="head">수급자 현상태/욕구평가</th>
			<th class="head">장기요양/수급자 요구내용</th>
			<th class="head">요양보호사 서비스내용</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="my_desire">
	<?
		for($i=0; $i<$desire_cnt; $i++){
			$id = $i;?>
			<tr id="desire_row_<?=$id;?>">
				<td class="center"><input name="desire_dt[]" type="text" class="yymm" value="<?=substr($desire[$i]['desire_yymm'],0,4).'-'.substr($desire[$i]['desire_yymm'],4,2);?>" style="width:100%;" alt="tag" tag="check_yymm('desire_row_<?=$id;?>');"></td>
				<td class="center"><textarea name="desire_1[]" style="width:100%; height:50px;"><?=$desire[$i]['desire_status'];?></textarea></td>
				<td class="center"><textarea name="desire_2[]" style="width:100%; height:50px;"><?=$desire[$i]['desire_content'];?></textarea></td>
				<td class="center"><textarea name="desire_3[]" style="width:100%; height:50px;"><?=$desire[$i]['desire_service'];?></textarea></td><?
				if ($i == 0){?>
					<td class="left last"><span class="btn_pack m"><button type="button" onclick="desire_tbl.t_add_row();">추가</button></span></td><?
				}else{?>
					<td class="left last"><span class="btn_pack m"><button type="button" onclick="desire_tbl.t_delete_row('desire_row_<?=$id;?>', 0);">삭제</button></span></td><?
				}?>
			</tr><?
		}
	?>
	</tbody>
</table>

<script language='javascript'>

//기타입력사항
var desire_tbl = new Table();
	desire_tbl.class_nm	= 'desire_tbl';
	desire_tbl.table_nm	= 'tbl_desire';
	desire_tbl.body_nm	= 'my_desire';
	desire_tbl.row_nm	= 'desire_row';
	desire_tbl.span_nm	= null;
	desire_tbl.row_count = <?=$desire_cnt;?>;
	desire_tbl.head_cnt	= 0;
	desire_tbl.tabindex	= 41;
	desire_tbl.column	= new Array(new Array('desire_dt[]', 'yymm', 'focus', new Array(new Array('alt', 'tag'), new Array('tag', 'check_yymm(id);'))),
									new Array('desire_1[]', 'textarea', '50px'),
									new Array('desire_2[]', 'textarea', '50px'),
									new Array('desire_3[]', 'textarea', '50px'),
									new Array('delete','button'));

	if (<?=$desire_cnt;?> == 0){
		desire_tbl.t_add_row();
	}
</script>