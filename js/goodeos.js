function _save_notice(){
	var f = document.f;

	if (!__alert(f.subject)) return;
	if (!__alert(f.content)) return;

	f.action = '../goodeos/notice_save.php';
	f.submit();
}

function _delete_notice(){
	var f = document.f;

	if (!confirm('삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')){
		return;
	}

	f.action = '../goodeos/notice_delete.php';
	f.submit();
}

function _delete_visit_quest(){
	var f = document.f;

	if (!confirm('삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')){
		return;
	}

	f.action = '../goodeos/visit_quest_reg_delete.php';
	f.submit();
}

function _list_notice(){
	var f = document.f;

	f.action = '../goodeos/notice_list.php';
	f.submit();
}

function _reg_notice(id, page, mode){
	var f = document.f;

	f.action = '../goodeos/notice_reg.php?id='+id+'&page='+page+'&mode='+mode;
	f.submit();
}