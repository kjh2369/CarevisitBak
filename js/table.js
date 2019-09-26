var my_column = null;
var my_row_count = 1;

function _t_check_next(p_target, p_index, p_next, p_tbody){
	var target    = document.getElementsByName(p_target);
	var className = target[p_index].getAttribute('className');
	var tag       = target[p_index].getAttribute('tag');

	if (event.keyCode == 13 || event.keyCode == 9){
		switch(className){
		case 'date':
			if (!checkDate(__getDate(target[p_index].value))){
				target[p_index].focus();
				return false;
			}else{
				var duplicate = false;
				for(var i=0; i<target.length; i++){
					if (i != p_index){
						if (__replace(target[i].value, '-', '') == __replace(target[p_index].value, '-', '')){
							duplicate = true;
							break;
						}
					}
				}

				if (duplicate){
					alert('입력하신 일자는 이미 등록된 일자입니다. 확인하여 주십시오.');
					target[p_index].focus();
					return false;
				}
			}
			break;
		default:
			if (target[p_index].value == ''){
				if (tag != ''){
					alert('명칭을 입력하여 주십시오.');
					target[p_index].focus();
					return false;
				}
			}
		}

		if (p_next == 'focus'){
			event.keyCode = 9;
			return true;
		}else if (p_next == 'add'){
			var tbody	= __getObject(p_tbody);

			if (tbody.childNodes.length-1 > p_index){
				document.getElementsByName(my_column[0][0])[p_index+1].focus();
			}else{
				_t_add_row(p_tbody);
			}
		}
	}else{
		if (className == 'date'){
			__onlyNumber(target[p_index]);
		}else{
			__enterFocus();
		}
		return true;
	}
}

/*
 * 행을 추가한다.
 */
function _t_add_row(p_tbody){
	var tbody	= __getObject(p_tbody);
	var row		= document.createElement('tr');
	var seq		= tbody.childNodes.length+1;
	var column	= eval(my_column);
	var col		= new Array();
	var text	= null;
	
	row.id = 'row_'+seq;

	for(var i=0; i<column.length; i++){
		col[i] = document.createElement('td');

		text = '';
		
		if (column[i][1] == 'no'){
			text = seq;
		}else if (column[i][1] == 'check'){
			text = '<input name="'+column[i][0]+'" type="checkbox" class="checkbox" value="'+seq+'">';
		}else if (column[i][1] == 'date'){
			text = '<input name="'+column[i][0]+'" type="text" value="" maxlength="8" class="date" onKeyDown="_t_check_next(\''+column[i][0]+'\', '+(seq-1)+', \''+column[i][2]+'\',\''+p_tbody+'\');" onFocus="__replace(this, \'-\', \'\');" onBlur="__getDate(this);">';
		}else if (column[i][1] == 'select'){
			text = '<select name="'+column[i][0]+'" style="width:auto;" onkeydown="_t_check_next(\''+column[i][0]+'\', '+(seq-1)+', \''+column[i][2]+'\',\''+p_tbody+'\');">';
			for(var k=0; k<column[i][3].length; k++){
				text += '<option value="'+column[i][3][k][0]+'">'+column[i][3][k][1]+'</option>';
			}
			text += '</select>';
		}else if (column[i][1] == 'radio'){
			text = '';
			for(var k=0; k<column[i][3].length; k++){
				text += '<input name="'+column[i][0]+'_'+(seq-1)+'" type="radio" class="radio" value="'+column[i][3][k][0]+'" '+column[i][3][k][2]+'>'+column[i][3][k][1];
			}
		}else if (column[i][1] == 'button'){
			text = '<span class="btn_pack m"><button type="button" onclick="_t_delete_row(\'row_'+seq+'\','+(seq-1)+')">삭제</button></span>';
		}else{
			text = '<input name="'+column[i][0]+'" type="text" value="" style="width:100%;" onkeydown="_t_check_next(\''+column[i][0]+'\', '+(seq-1)+', \''+column[i][2]+'\',\''+p_tbody+'\');" onfocus="this.select();">';
		}

		if (i == 0){
			text += '<input name="delete_yn[]" type="hidden" value="N">';
		}

		col[i].innerHTML = text;

		if (column[i][1] == 'button'){
			col[i].className = 'left last';
		}else{
			col[i].className = 'center';
		}

		row.appendChild(col[i]);
	}
	
	tbody.appendChild(row);

	if (column[0][1] == 'no'){
		document.getElementsByName(column[1][0])[seq-1].focus();
	}else{
		document.getElementsByName(column[0][0])[seq-1].focus();
	}
	
	__init_form(document.f);

	my_row_count ++;
}

/*
 * 행을 삭제한다.
 */
function _t_delete_row(p_row_id, p_seq){
	var row = document.getElementById(p_row_id);

	if (my_row_count == 1){
		alert('더이상 행을 삭제할 수 없습니다.');
		return;
	}

	//row.parentNode.removeChild(row);

	row.style.display = 'none';

	document.getElementsByName('delete_yn[]')[p_seq].value = 'Y';

	my_row_count --;
}

/*
 * 행삭제
 */
function _t_real_delete(p_f, p_seq){
	document.getElementsByName('delete_yn[]')[p_seq].value = 'Y';
	
	p_f.data_execute.value = 'delete';
	p_f.submit();
}