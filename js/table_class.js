var Table = function(){
	this.init();

	if (this.tabindex > 0){
		this.tabtext = ' tabindex="'+this.tabindex+'" ';
	}else{
		this.tabtext = '';
	}
}

Table.prototype.init = function(){
	this.class_nm  = null;	//클래스명
	this.table_nm  = null;	//테이블 ID
	this.body_nm   = null;	//바디 ID
	this.row_nm    = null;	//행 ID
	this.span_nm   = null;	//머지 열 ID
	this.column    = null;	//열 배열
	this.first_add = true;  //첫행 추가버튼
	this.head_cnt  = 1;
	this.row_count = 1;
	this.tabindex  = 0;
}

Table.prototype.t_check_next = function(p_target, p_index, p_next){
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
				if (tag != null && tag != ''){
					alert(tag);
					target[p_index].focus();
					return false;
				}else{
					switch(className){
						case 'number':
							target[p_index].value = 0;
							break;
					}
				}
			}
		}

		if (p_next == 'focus'){
			event.keyCode = 9;
			return true;
		}else if (p_next == 'add'){
			var tbody = __getObject(this.body_nm);

			if (tbody.childNodes.length-this.head_cnt > p_index+this.head_cnt){
				document.getElementsByName(this.column[0][0])[p_index+this.head_cnt].focus();
			}else{
				this.t_add_row(this.body_nm);
			}
		}
	}else{
		if (className == 'date' || className == 'number' || className == 'no_string' || className == 'phone'){
			__onlyNumber(target[p_index]);
		}else{
			__enterFocus();
		}
		return true;
	}
}

Table.prototype.t_add_row = function(){
	
	var tbody	= __getObject(this.body_nm);
	var row		= document.createElement('tr');
	var seq		= $('#'+this.body_nm+' tr').length;
	var column	= eval(this.column);
	var col		= new Array();
	var text	= null;
	
	row.id = this.row_nm+'_'+seq;

	for(var i=0; i<column.length; i++){
		col[i] = document.createElement('td');

		text = '';
		
		if (column[i][1] == 'no'){
			text = seq;
		}else if (column[i][1] == 'check'){
			text = '<input name="'+column[i][0]+'" type="checkbox" class="checkbox" value="'+seq+'" '+this.tabtext+'>';
		}else if (column[i][1] == 'date'){
			text = '<input name="'+column[i][0]+'" type="text" value="" '+this.tabtext+' maxlength="8" class="date" onclick="_carlendar(this);" onKeyDown="'+this.class_nm+'.t_check_next(\''+column[i][0]+'\', '+(seq-this.head_cnt)+', \''+column[i][2]+'\',\''+this.body_nm+'\');" onFocus="__replace(this, \'-\', \'\');" onBlur="__getDate(this);">';
		}else if (column[i][1] == 'select'){
			text = '<select name="'+column[i][0]+'" style="width:auto;" onkeydown="'+this.class_nm+'.t_check_next(\''+column[i][0]+'\', '+(seq-this.head_cnt)+', \''+column[i][2]+'\',\''+this.body_nm+'\');" '+this.tabtext+'>';
			for(var k=0; k<column[i][3].length; k++){
				text += '<option value="'+column[i][3][k][0]+'" '+column[i][3][k][2]+'>'+column[i][3][k][1]+'</option>';
			}
			text += '</select>';
		}else if (column[i][1] == 'radio'){
			text = '';
			for(var k=0; k<column[i][3].length; k++){
				text += '<input name="'+column[i][0]+'_'+(seq-this.head_cnt)+'" type="radio" class="radio" '+this.tabtext+' value="'+column[i][3][k][0]+'" '+column[i][3][k][2]+'>'+column[i][3][k][1];
			}
		}else if (column[i][1] == 'textarea'){
			text = '<textarea name="'+column[i][0]+'" style="width:100%; height:'+column[i][2]+';"></textarea>';
		}else if (column[i][1] == 'button'){
			var buttonType = 'del';

			if (this.first_add){
				if (this.row_count == 0){
					buttonType = 'add';
				}
			}

			if (buttonType == 'add'){
				text = '<span class="btn_pack m"><button type="button" onclick="'+this.class_nm+'.t_add_row()">추가</button></span>';
			}else{
				text = '<span class="btn_pack m"><button type="button" onclick="'+this.class_nm+'.t_delete_row(\''+this.row_nm+'_'+seq+'\','+(seq-this.head_cnt)+')">삭제</button></span>';
			}
		}else{
			var value = null;

			switch(column[i][1]){
				case 'number':
					value = 0;
					break;
				default:
					value = '';
			}
			text  = '<input name="'+column[i][0]+'" type="text" value="'+value+'" '+this.tabtext+' class="'+column[i][1]+'" style="width:100%;" onkeydown="'+this.class_nm+'.t_check_next(\''+column[i][0]+'\', '+(seq-this.head_cnt)+', \''+column[i][2]+'\',\''+this.body_nm+'\');" onfocus="this.select();" ';
			
			if (column[i].length > 3){
				for(var j=0; j<column[i][3].length; j++){
					var tag = column[i][3][j];

					text += tag[0] + '=\'' + tag[1].split('id').join('"'+row.id+'"') + '\' ';
				}
			}

			text += '>';
		}

		if (i == 0){
			text += '<input name="delete_yn[]" type="hidden" value="N">';
			text += '<input name="back_row_id[]" type="hidden" value="'+row.id+'">';
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
		document.getElementsByName(column[1][0])[seq-this.head_cnt].focus();
	}else{
		document.getElementsByName(column[0][0])[seq-this.head_cnt].focus();
	}

	__init_form(document.f);

	this.row_count ++;
	
	if (this.span_nm != null){
		if (typeof(this.span_nm) == 'object'){
			var span_row = this.span_nm;
		}else{
			var span_row = document.getElementById(this.span_nm);
		}
		span_row.setAttribute('rowSpan', this.row_count+this.head_cnt);
	}
}

Table.prototype.t_delete_row = function (p_row_id, p_seq){
	if (this.row_count == 1){
		/*
		alert('더이상 행을 삭제할 수 없습니다.');
		return;
		*/
	}
	
	/*
	var row = document.getElementById(p_row_id);

	row.style.display = 'none';

	document.getElementsByName('delete_yn[]')[p_seq].value = 'Y';
	*/

	var tbl = __getObject(this.table_nm);

	for(var i=this.head_cnt; i<tbl.rows.length; i++){
		if (tbl.rows[i].id == p_row_id){
			tbl.deleteRow(i);
		}
	}
}

Table.prototype.t_real_delete = function (p_f, p_seq){
	document.getElementsByName('delete_yn[]')[p_seq].value = 'Y';
	
	p_f.data_execute.value = 'delete';
	p_f.submit();
}