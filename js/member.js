// 회원가입 신청
function _mem_join(form){
	form.action = '../member/join.php?join=YES';
	form.submit();
}

// 로그인
function _mem_login(form){
	form.action = '../member/login.php?join=YES';
	form.submit();
}


