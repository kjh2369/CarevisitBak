// JavaScript Document
function TopMenu_Type(){
	var Menu = parseInt(document.getElementById('menu').value,10);
	var MenuBox = document.getElementById('gnb_list').getElementsByTagName("li");
	var MenuLength = MenuBox.length;
	
	for ( var i=0; i<MenuLength; i++){
		var MenuLink = document.getElementById("gnb"+i).getElementsByTagName("a")[0];
		
		MenuLink.i = i;
		
		// �޴��׸����� ���� �� ������ �ִ� �κ�
		MenuLink.onclick = function(){ 
			fnMouseOver(this.i); 
		}
	}

	// �޴��׸����������� ���� �޾ƿͼ� �ش��ϴ°��̸� �̹��� on�� ���ش�.
	fnMouseOver(Menu);
}

// �޴��� 1���� ��ũ�κп� ���콺�� Ű������ ������ ���� �����ϴ� �κ�
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


