// 입금내역조회
function _income_search(month, pos){
	//_income_find_date(pos);

	document.f.month.value = month;
	document.f.action = 'income_list.php?mode=1';
	document.f.submit();
}

// 입금내역등록
function _income_reg(){
	document.f.action = 'income_reg.php?mode=2';
	document.f.submit();
}

// 입금내역수정
function _income_modify(month, pos){
	_income_find_date(pos);
	
	document.f.month.value = month;
	document.f.action = 'income_modify.php?mode=3';
	document.f.submit();
}

// 입금내역삭제
function _income_delete(month, pos){
	_income_find_date(pos);

	document.f.month.value = month;
	document.f.action = 'income_delete.php?mode=4';
	document.f.submit();
}

// 조회일자
function _income_find_date(pos){
	if (pos == '1'){
		document.f.find_from_date.value = document.f.find_from_date1.value
		document.f.find_to_date.value   = document.f.find_to_date1.value
	}else if (pos == '2'){
		document.f.find_from_date.value = document.f.find_from_date2.value
		document.f.find_to_date.value   = document.f.find_to_date2.value
	}
}

// 입금등록
function _income_reg_check(){
	if (!__checkRowCount('check[]')){
		return;
	}

	if (document.getElementById('mode').value == '2' ||
		document.getElementById('mode').value == '3'){
		var check  = document.getElementsByName('check[]');
		var date   = document.getElementsByName('date[]');
		var item   = document.getElementsByName('item[]');
		var amount = document.getElementsByName('amount[]');

		for(var i=0; i<check.length; i++){
			if (check[i].checked){
				if (!checkDate(date[i].value)){
					alert('일자를 입력하여 주십시오.');
					date[i].focus();
					return;
				}

				if (__replace(item[i].value, ' ', '') == ''){
					alert('내용을 입력하여 주십시오.');
					item[i].focus();
					return;
				}

				if (__NaN(__commaUnset(amount[i].value)) == 0){
					alert('금액을 입력하여 주십시오.');
					amount[i].focus();
					return;
				}
			}
		}
	}else if (document.getElementById('mode').value == '4'){
		if (!confirm('선택하신 내역을 정말로 삭제하시겠습니까?')){
			return;
		}
	}

	document.f.action = 'income_reg_ok.php';
	document.f.submit();
}

//
function _item_focus(p_index, p_next){
	__checkRow(p_index, p_next);
	document.getElementsByName('item[]')[p_index-1].focus();
}

// 입금일자확인
function _income_check_date(p_date, p_index){
	var mode = document.getElementById('mode').value;
	var date = __getObject(p_date);

	if (event.keyCode == 13 || event.keyCode == 9){
		if (mode == '3'){ //수정
			if (__replace(date.value, '-', '') != __replace(date.temp, '-', '')){
				__checkRow(p_index, 'check[]');
			}
			event.keyCode = 9;
			return true;
		}else{ //등록
			if (!checkDate(__getDate(date.value))){
				date.focus();
				return false;
			}else{
				__checkRow(p_index, 'check[]');
				event.keyCode = 9;
				return true;
			}
		}
	}else{
		__onlyNumber(this);
		return true;
	}
}

// 입금내용확인
function _income_check_item(p_item, p_index){
	var mode = document.getElementById('mode').value;
	var item = __getObject(p_item);

	if (event.keyCode == 13 || event.keyCode == 9){
		if (mode == '3'){ //수정
			if (item.value != item.temp){
				__checkRow(p_index, 'check[]');
			}
		}else{ //등록
			if (__replace(item.value, ' ', '') != ''){
				__checkRow(p_index, 'check[]');
			}
		}
		event.keyCode = 9;
		return true;
	}else{
		return true;
	}
}

// 입금금액확인
function _income_check_amount(p_amount, p_index){
	var mode   = document.getElementById('mode').value;
	var amount = __getObject(p_amount);

	if (event.keyCode == 13 || event.keyCode == 9){
		if (mode == '3'){ //수정
			if (__replace(amount.value, ',', '') != __replace(amount.temp, ',', '')){
				__checkRow(p_index, 'check[]');
			}
			document.getElementsByName('taxid[]')[p_index-1].focus();
			return false;
		}else{ //등록
			if (__NaN(amount.value) == 0){
				amount.focus();
				return false;
			}else{
				__checkRow(p_index, 'check[]');
				//event.keyCode = 9;
				document.getElementsByName('taxid[]')[p_index-1].focus();
				return false;
			}
		}
	}else{
		__onlyNumber(this);
		return true;
	}
}

// 행추가 및 포커스 이동
function _income_next(p_index){
	var date   = document.getElementsByName('date[]')[p_index-1];
	var item   = document.getElementsByName('item[]')[p_index-1];
	var amount = document.getElementsByName('amount[]')[p_index-1];
	var check  = document.getElementsByName('check[]');
	var row    = document.getElementById('row_1');

	if (!checkDate(date.value)){
		alert('입금일자를 입력하여 주십시오.');
		date.focus();
		return false;
	}else if (item.value == ''){
		alert('입금내용을 입력하여 주십시오.');
		item.focus();
		return false;
	}else if (__NaN(amount.value)){
		alert('입금금액을 입력하여 주십시오.');
		amount.focus();
		return false;
	}else{
		if (p_index == check[check.length-1].value){
			if (row != null){
				var new_index = _income_add_row('my_table', 'row_1');
			}else{
				var new_index = 0;
			}
		}else{
			var new_index = p_index+1;
		}
	}

	var date = document.getElementsByName('date[]');

	if (row != null){
		date[new_index-1].focus();
	}else{
		date[new_index].focus();
	}

	return true;
}

// 입금등록 행추가
function _income_add_row(p_table, p_tbody){
	var table = __getObject(p_table);
	var tbody = __getObject(p_tbody);
	var pos = tbody.childNodes.length+1;
	
	var new_seq = tbody.childNodes.length+1;
	var id  = 'row_'+new_seq;
	var row_tr = table.insertRow(pos);
	var row_td	= new Array();
	var cols = 11;

	row_tr.id = id;

	for(var i=0; i<cols; i++){
		row_td[i] = document.createElement("td");
	}
	
	/*
	row_td[0].innerHTML = '<input name="check[]" type="checkbox" class="checkbox" value="'+new_seq+'">';
	row_td[1].innerHTML = '<input name="date[]" type="text" value="" maxlength="8" class="date" onKeyDown="return _income_check_date(this,'+new_seq+');" onFocus="__replace(this, \'-\', \'\');" onBlur="__getDate(this);" onClick="_carlendar(this);" alt="tag" tag="__checkRow('+new_seq+',\'check[]\');">';
	row_td[2].innerHTML = '<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onkeydown="return _income_check_item(this,'+new_seq+');" onfocus="this.select();">';
	row_td[3].innerHTML = '<input name="amount[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" onkeydown="return _income_check_amount(this,'+new_seq+');" onfocus="__commaUnset(this);" onblur="__commaSet(this);">';
	row_td[4].innerHTML = '<!--span class="btn_pack m icon"><span class="add"></span><button type="button" onClick="_income_add_row(\''+p_table+'\',\''+id+'\');">추가</button></span> -->'
						+ '<span class="btn_pack m icon"><span class="delete"></span><button type="button" onClick="_income_delete_row(\''+id+'\');">삭제</button></span>';
	*/
	
	row_td[0].innerHTML  = '<input name="check[]" type="checkbox" class="checkbox" value="'+new_seq+'">';
	row_td[1].innerHTML  = '<input name="date[]" type="text" value="" maxlength="8" class="date" onKeyDown="return _income_check_date(this,'+new_seq+');" onFocus="__replace(this, \'-\', \'\');" onBlur="__getDate(this);" onClick="_carlendar(this);" alt="tag" tag="_item_focus('+new_seq+', \'check[]\');">';
	row_td[2].innerHTML  = '<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onkeydown="return _income_check_item(this,'+new_seq+');" onfocus="this.select();">';
	row_td[3].innerHTML  = '<input name="vat_'+(new_seq-1)+'" type="radio" class="radio" style="margin:0;" value="Y" onclick="_set_vat('+new_seq+');" onkeydown="__enterFocus();">유 '
						 + '<input name="vat_'+(new_seq-1)+'" type="radio" class="radio" style="margin:0;" value="N" onclick="_set_vat('+new_seq+');" onkeydown="__enterFocus();" checked>무';
	row_td[4].innerHTML  = '<input name="amount[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" onkeydown="return _income_check_amount(this,'+new_seq+');" alt="onblur" tag="_set_vat('+new_seq+');">';
	row_td[5].innerHTML  = '<input name="vat[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" onfocus="" onblur="__commaSet(this);" readonly>';
	row_td[6].innerHTML  = '<input name="tot_amt[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" onfocus="" onblur="__commaSet(this);" readonly>';
	row_td[7].innerHTML  = '<input name="taxid[]" type="text" value="" style="width:100%;" maxlength="10" onkeydown="return _income_check_item(this,'+new_seq+');" alt="taxid">';
	row_td[8].innerHTML  = '<input name="biz_group[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this,'+new_seq+');">';
	row_td[9].innerHTML  = '<input name="biz_type[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="if(event.keyCode==13){_income_next('+new_seq+');}">';
	row_td[10].innerHTML = '<span class="btn_pack m"><button type="button" onClick="_income_delete_row(\''+id+'\');">삭제</button></span>';

	for(var i=0; i<cols-1; i++){
		row_td[i].className  = 'center';
	}
	row_td[10].className = 'center last';
	
	for(var i=0; i<cols; i++){
		row_tr.appendChild(row_td[i]);
	}

	__init_form(document.f);

	return new_seq;
}

// 입금등록 행삭제
function _income_delete_row(tbody){
	var row = document.getElementById(tbody);

	row.parentNode.removeChild(row);
}

// 부가세
function _set_vat(p_index){
	var vat_yn  = __get_value(document.getElementsByName('vat_'+(p_index-1)));
	var amount  = document.getElementsByName('amount[]')[p_index-1];
	var vat     = document.getElementsByName('vat[]')[p_index-1];
	var tot_amt = document.getElementsByName('tot_amt[]')[p_index-1];
	var taxid   = document.getElementsByName('taxid[]')[p_index-1];

	var cost = __commaUnset(amount.value);

	if (vat_yn == 'Y'){
		//vat.value = __commaSet(cutOff(parseInt(cost, 10) * 0.1));
		vat.value = __commaSet(Math.floor(parseInt(cost, 10) / 10));
	}else{
		vat.value = 0;
	}

	tot_amt.value = __commaSet(parseInt(cost, 10) + parseInt(__commaUnset(vat.value), 10));
	amount.value  = __commaSet(cost);
	
	var mode = document.getElementById('mode').value;

	if (mode == '3'){ //수정
		var vat_temp = __get_temp(document.getElementsByName('vat_'+(p_index-1)));

		if (vat_yn != vat_temp) __checkRow(p_index, 'check[]');
	}
}