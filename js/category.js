/*********************************************************
	카테고리 아이콘
*********************************************************/
$ICON_FOLDER = {'open':'../image/f_open.gif', 'close':'../image/f_close.gif', 'list':'../image/f_list.gif'};
$MOUSE_ID = 0;
$CATEGORY_ONLOAD     = false;
$CATEGORY_TREE_ID    = null;
$CATEGORY_TREE_MENU  = null;
$CATEGORY_GROUP_LIST = null;


/*********************************************************
	카테고리
*********************************************************/
function _categoryLoad(id, body){
	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_addr_book_category.php'
		,	data: {
				'id':id
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				$('#'+body).html( xmlHttp );
				
				if (!$CATEGORY_ONLOAD){
					_categoryAllList();
					$CATEGORY_ONLOAD = true;
				}
			}
		}).responseXML;
	}catch(e){
	}
}


/*********************************************************
	현재 선택된 카테고리
*********************************************************/
function _categorySelTreeID(obj){
	$MOUSE_ID = 1;

	try{
		if (obj){
			$cls = $(obj).attr('className').split(' ');
			$cls = $cls[$cls.length - 1];
			
			$('#selTreeID').attr('value', $cls);
		}else if (obj == false){
		}else{
		}
	}catch(e){
	}
}


/*********************************************************
	전체
*********************************************************/
function _categoryAllList(){
	$('#selTreeID').attr('value', 'all');

	_categoryShowPath('cate_all');
	_categoryShowList('all', $CATEGORY_GROUP_LIST);
}


/*********************************************************
	미분류 선택
*********************************************************/
function _categoryNotList(){
	$('#selTreeID').attr('value', 'not');

	_categoryShowPath('cate_not');
	_categoryShowList('not', $CATEGORY_GROUP_LIST);
}


/*********************************************************
	메튜 추가
*********************************************************/
function _categoryTreeAdd(){
	if ($('#newCategory').attr('value').toString().split(' ').join('') == ''){
		alert('추가할 명칭을 입력하여 주십시오.');
		$('#newCategory').focus();
		return;
	}

	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_category_add.php'
		,	data: {
				'id':$('#id').attr('value')
			,	'parent':$('#selTreeID').attr('value')
			,	'new':$('#newCategory').attr('value')
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				if (xmlHttp == 'error'){
					alert('에러가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					_categoryReset('add', xmlHttp, $('#newCategory').attr('value'));
				}
			}
		}).responseXML;
	}catch(e){
	}

	_categoryMenuHide();
}


/*********************************************************
	메튜 수정
*********************************************************/
function _categoryTreeMod(){
	if ($('#modCategory').attr('value').toString().split(' ').join('') == ''){
		alert('수정할 명칭을 입력하여 주십시오.');
		$('#modCategory').focus();
		return;
	}

	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_category_mod.php'
		,	data: {
				'id':$('#id').attr('value')
			,	'code':$('#selTreeID').attr('value')
			,	'mod':$('#modCategory').attr('value')
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				if (xmlHttp == 'error'){
					alert('에러가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					_categoryReset('modify', $('#selTreeID').attr('value'), $('#modCategory').attr('value'));
				}
			}
		}).responseXML;
	}catch(e){
	}

	_categoryMenuHide();
}


/*********************************************************
	메튜 삭제
*********************************************************/
function _categoryTreeDel(){
	if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')){
		_categoryMenuHide();
		return;
	}

	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_category_del.php'
		,	data: {
				'id':$('#id').attr('value')
			,	'code':$('#selTreeID').attr('value')
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				if (xmlHttp == 'error'){
					alert('에러가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					_categoryReset('delete', xmlHttp);
				}
			}
		}).responseXML;
	}catch(e){
	}

	_categoryMenuHide();
}


/*********************************************************
	카테고리 트리 수정
*********************************************************/
function _categoryReset(type, code, name){
	switch(type){
		case 'add':
			$cds = code.split('|');

			if ($('#selTreeID').attr('value') != ''){
				$treeID  = $('#selTreeID').attr('value');
				$index   = __str2num($('#'+$treeID+'_idx').text())+1;
				$newTree = getHttpRequest('./mail_category.php?id='+$('#id').attr('value')+'&code='+$cds[0]+'&index='+$index);
				
				$('#'+$treeID+'_root').after($newTree);
				$('#'+$treeID+'_img').attr('src', $cds[1] == 1 ? $ICON_FOLDER['open'] : $ICON_FOLDER['list']);
			}else{
				$treeID  = 'divTreeMenuList'
				$index   = '0';
				$newTree = getHttpRequest('./mail_category.php?id='+$('#id').attr('value')+'&code='+$cds[0]+'&index='+$index);
				$nowTree = $('#'+$treeID).html();

				$('#'+$treeID).html($nowTree + $newTree);
			}
			
			break;

		case 'modify':
			$('#'+code+'_nm').text(name);
			break;

		case 'delete':
			$cds = code.split('|');
			$cd  = $cds[0].split('/');

			for($i=0; $i<$cd.length; $i++){
				$('#cate_'+$cd[$i]+'_body').remove();
			}

			$('#cate_'+$cds[2]+'_img').attr('src', $cds[1] == 1 ? $ICON_FOLDER['open'] : $ICON_FOLDER['list']);
			break;
	}
}


/*********************************************************
	카테고리 메뉴 선택
*********************************************************/
function _categoryMouseDown(obj, body, posX, posY){
	$event = window.event.button;

	if ($event == 1){
		_categoryShowMenu(obj, body);
	}else{
		_categoryMenu(posX, posY);
	}
}


/*********************************************************
	메뉴
*********************************************************/
function _categoryMenu(posX, posY){
	if ($('#selTreeID').attr('value') != ''){
		$treeID = $('#selTreeID').attr('value');

		$left = __str2num($('#'+$treeID+'_idx').text()) * 17;
		$pos  = $('#'+$treeID+'_root').offset();

		$x = $pos.left + posX + $left;
		$y = $pos.top  + posY
	}else{
		$event = window.event;

		$x = $event.x + posX - 7;
		$y = $event.y + posY - 5;
	}

	if ($('#selTreeID').attr('value') != ''){
		$('#divMenuMod').show();
		$('#divMenuDel').show();
	}else{
		$('#divMenuMod').hide();
		$('#divMenuDel').hide();
	}

	
	if ($('#selTreeID').attr('value') != ''){
		$('#modCategory').attr('value', $('#'+$('#selTreeID').attr('value')+'_nm').text());
	}

	$('#newCategory').attr('value', '').css('ime-mode','active');
	$('#divGroupMenu').css('left', $x).css('top', $y).show();
}


function _categoryMenuRoot(posX, posY){
	$event = window.event.button;

	if ($event == 1) return;

	$('#selTreeID').attr('value', '');

	_categoryMenu(posX, posY);
}

function _categoryMenuHide(){
	$MOUSE_ID = 0;
	$('#divGroupMenu').hide();
}


/*********************************************************
	카테고리 트리 메뉴
*********************************************************/
function _categoryShowMenu(obj, body, exit){
	$cls = $(obj).attr('className').split(' ');
	$cls = $cls[$cls.length - 1];
	
	if (body != null){
		if ($('#'+$cls+'_div').text() == 'list')
			$folderGbn = 'list';
		else if ($('#'+$cls+'_div').text() == 'close')
			$folderGbn = 'open';
		else
			$folderGbn = 'close';

		if (!exit){
			_categoryShowList( $('#'+$cls+'_cd').text(), body );
			_categoryShowPath($cls);
		}else{
			$('#selPopID').attr('value', $cls);
			$('.pop_name').css('font-weight', 'normal');
			$('#'+$cls+'_nm').css('font-weight', 'bold');
		}

		if ($folderGbn == 'list') return;

		$('#'+$cls+'_div').text( $folderGbn );
	}else{
		if ($('#'+$cls+'_div').text() != 'list'){
			$('#'+$cls+'_div').text( 'close' );
		}
	}

	$('.'+$cls).each(function(){
		$thisCls = $(this).attr('className').split(' ');
		$thisCls = $thisCls[$thisCls.length - 1];

		switch($(this).attr('tagName')){
			case 'DIV':
				if ($cls != $thisCls){
					if ($folderGbn == 'open'){
						if ($('#'+$thisCls+'_img').attr('src') == $ICON_FOLDER['open'])
							_categoryShowMenu($(this),null);
						
						$(this).show();
					}else if ($folderGbn == 'close'){
						_categoryShowMenu($(this),null);
						$(this).hide();
					}
				}
				break;

			case 'IMG':
				if ($cls == $thisCls && $('#'+$thisCls+'_div').text() != 'list')
					$(this).attr('src', $ICON_FOLDER[$folderGbn]);
				break;
		}
	});
	
	if ($folderGbn != 'close' && !exit)
		_categoryShowPath($cls);
}


/*********************************************************
	경로출력
*********************************************************/
function _categoryShowPath(cls){
	if ($('#'+cls+'_div').text() != 'close')
		$str = $('#'+cls+'_str').text();
	else
		$str = '';

	$('#divGroupPath').text( $str );
}


/*********************************************************
	카테고리 리스트
*********************************************************/
function _categoryShowList(code, body){
	if (code == null){
		code = $('#selTreeID').attr('value').split('_');
	
		if (code.length > 1)
			code = code[code.length-1];

		$addFlag = true;
	}else{
		$addFlag = false;
	}

	if ($addFlag){
		$page = __str2num($('.intPageNo').text()) + 1;
	}else{
		$page = 1;
	}

	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_addr_book_list.php'
		,	data: {
				'id':$('#id').attr('value')
			,	'cd':code
			,	'page':$page
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				if ($addFlag){
					$('.intPageNo').remove();
					$('.strPageEnd').remove();
					$('#'+body).after( xmlHttp );
					$('#listPage').attr('value', $('.intPageNo').text());
				}else{
					$('.dbData').remove();
					$('#'+body).html( xmlHttp );
				}

				if ($('.strPageEnd').text() != 'Y'){
					$('#btnList1').show();
					$('#btnList2').hide();
				}else{
					$('#btnList1').hide();
					$('#btnList2').show();
				}
			}
		}).responseXML;
	}catch(e){
	}
}


/*********************************************************
	카테고리 팝업
*********************************************************/
function _categoryPopupON(index, posX, poxY){
	$pos1 = $('#div_'+index+'_2').offset();
	$pos2 = $('#div_'+index+'_2').parent().offset();

	$x = $pos1.left + posX;
	$y = $pos1.top - $pos2.top + poxY;

	if ($y + $('#popCategory').height() > $('#divGroupListBody').height())
		$y = $y - $('#popCategory').height() - poxY;
	
	$('#popCategory').css('left', $x).css('top', $y).show();
}


/*********************************************************
	카테고리 팝업 닫기
*********************************************************/
function _categoryPopupOFF(){
	$('#popCategory').hide();
}


/*********************************************************
	카테고리 팝업 선택
*********************************************************/
function _categorySelected(){
	$seq = $('#selListSeq').attr('value');
	$id  = $('#selPopID').attr('value');

	$('#cate_id_'+$seq+'_2').text( '/'+$('#'+$id+'_cd').text() );
	$('#cate_str_'+$seq+'_2').text( $('#'+$id+'_str').text() );
	
	_categoryPopupOFF();
}


/*********************************************************
	리스트 추가
*********************************************************/
function _categoryAddList(){
	$category = $('#selTreeID').attr('value').split('_');
	
	if ($category.length > 1)
		$category = $category[$category.length-1];
	
	if (!__isMail( $('#regEmail').attr('value') )){
		alert('이메일 형식이 올바르지 않습니다. 확인하여 주십시오.');
		$('#regEmail').focus();
		return;
	}


	if ($('#regMobile').attr('value') != '연락처'){
		$mobile = $('#regMobile').attr('value');
	}else{
		$mobile = '';
	}
	

	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_addr_book_list_add.php'
		,	data: {
				'id':$('#id').attr('value')
			,	'name':$('#regName').attr('value')
			,	'email':$('#regEmail').attr('value')
			,	'mobile':$mobile
			,	'category':$category
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				if (xmlHttp == 'error'){
					alert('에러가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else if (xmlHttp == 'duplicate'){
					alert('동일한 정보가 등록되어 있습니다.');
				}else{
					_categoryShowList($category, $CATEGORY_GROUP_LIST);
				}
			}
		}).responseXML;
	}catch(e){
	}
}


/*********************************************************
	리스트 수정
*********************************************************/
function _categoryListModify(seq){
	$('#name_'+seq+'_2').attr('value', $('#name_'+seq+'_1').text() );
	$('#mail_'+seq+'_2').attr('value', $('#mail_'+seq+'_1').text() );
	$('#tel_'+seq+'_2').attr('value', $('#tel_'+seq+'_1').text().split('.').join('-') );
	$('#cate_id_'+seq+'_2').text( $('#cate_id_'+seq+'_1').text() );
	$('#cate_str_'+seq+'_2').text( $('#cate_str_'+seq+'_1').text() != '' ? $('#cate_str_'+seq+'_1').text() : '미분류' );

	$('#div_'+seq+'_1').hide();
	$('#div_'+seq+'_2').show();
}


/*********************************************************
	리스트 수정 취소
*********************************************************/
function _categoryListModifyCancel(seq){
	$('#div_'+seq+'_2').hide();
	$('#div_'+seq+'_1').show();
}


/*********************************************************
	리스트 수정 저장
*********************************************************/
function _categoryListModifyOK(seq){
	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_addr_book_list_add.php'
		,	data: {
				'id':$('#id').attr('value')
			,	'seq':seq
			,	'name':$('#name_'+seq+'_2').attr('value')
			,	'email':$('#mail_'+seq+'_2').attr('value')
			,	'mobile':$('#tel_'+seq+'_2').attr('value')
			,	'category':$('#cate_id_'+seq+'_2').text()
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				if (xmlHttp == 'error'){
					alert('에러가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					$category = $('#selTreeID').attr('value').split('_');
	
					if ($category.length > 1)
						$category = $category[$category.length-1];
					
					_categoryShowList($category, $CATEGORY_GROUP_LIST);
				}
			}
		}).responseXML;
	}catch(e){
	}
}


/*********************************************************
	리스트 삭제
*********************************************************/
function _categoryListDelete(seq){
	if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	try{
		$.ajax({
			type: 'POST'
		,	url : './mail_addr_book_list_del.php'
		,	data: {
				'id':$('#id').attr('value')
			,	'seq':seq
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				if (xmlHttp == 'error'){
					alert('에러가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					$category = $('#selTreeID').attr('value').split('_');
	
					if ($category.length > 1)
						$category = $category[$category.length-1];
					
					_categoryShowList($category, $CATEGORY_GROUP_LIST);
				}
			}
		}).responseXML;
	}catch(e){
	}
}


/*********************************************************
	전체선택
*********************************************************/
function _categoryCheckBox(checked){
	$('.checkbox').each(function(){
		$(this).attr('checked', checked ? 'checked' : '');
	});
}






// 카테고리 로드
function _loadCategory(){
	var body = document.getElementById('body');
	var URL = 'category_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
			},
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 카테고리 추가
function _caregoryAdd(p_code){
	var modal = showModalDialog('category_add.php?parent='+p_code, window, 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:yes');

	//alert(modal);

	_loadCategory();
}