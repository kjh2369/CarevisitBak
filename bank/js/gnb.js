// JavaScript Document
function TopMenu_Type(){
	var Menu = parseInt(document.getElementById('menu').value,10);
	var MenuBox = document.getElementById('gnb_list').getElementsByTagName("li");
	var MenuLength = MenuBox.length;
	
	for ( var i=0; i<MenuLength; i++){
		var MenuLink = document.getElementById("gnb"+i).getElementsByTagName("a")[0];
		
		MenuLink.i = i;
		
		// 메뉴항목위로 갔을 때 반응을 넣는 부분
		MenuLink.onclick = function(){ 
			fnMouseOver(this.i); 
		}
	}

	// 메뉴항목페이지에서 값을 받아와서 해당하는값이면 이미지 on을 해준다.
	fnMouseOver(Menu);
}

// 메뉴의 1뎁스 링크부분에 마우스나 키보드의 반응에 의해 실행하는 부분
function fnMouseOver(val){
	var MenuBox = document.getElementById('gnb_list').getElementsByTagName("li");
	var MenuLength = MenuBox.length;
	
	for ( var i=0; i<MenuLength; i++ ) {
		
		if ( i == val ) {
			document.getElementById('gnb'+i).getElementsByTagName("a")[0].className ="on";
		}else {
			document.getElementById('gnb'+i).getElementsByTagName("a")[0].className ="";
		}
	}
	
}


