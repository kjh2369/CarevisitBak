// JavaScript Document
function TopMenu_Type(){
	var Menu = document.getElementById('menu').value;
	var MenuBox = document.getElementById('gnb_list').getElementsByTagName("li");
	var MenuLength = MenuBox.length;
	
	for ( var i=0; i<MenuLength; i++){
		var MenuLink = document.getElementById("gnb"+i).getElementsByTagName("a")[0];
		MenuLink.i = i;
		
		if(Menu == '0'){
			var idx = 0;
		}else if(Menu == '1'){
			var idx = 1;
		}

		// �޴��׸����� ���� �� ������ �ִ� �κ�
		MenuLink.onclick = function()	{ 
			fnMouseOver(this.i); 
		}	
	}

	// �޴��׸����������� ���� �޾ƿͼ� �ش��ϴ°��̸� �̹��� on�� ���ش�.
	switch (Menu){
		case '0':
			fnMouseOver(idx);
			break;
		case '1':
			fnMouseOver(idx);
			break;
	}

}

// �޴��� 1���� ��ũ�κп� ���콺�� Ű������ ������ ���� �����ϴ� �κ�
function fnMouseOver(val){
	var MenuBox = document.getElementById('gnb_list').getElementsByTagName("li");
	var MenuLength = MenuBox.length;
	
	for ( var i=0; i<MenuLength; i++ ) {
		
		if ( i == val ) {
			document.getElementById('gnb'+i).className ="on";
		}else {
			document.getElementById('gnb'+i).className ="";
		}
	}
}


