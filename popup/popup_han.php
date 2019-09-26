<?
	include_once('../inc/_header.php');

	$noticeID = $_GET['id'];

	$sql = 'SELECT	subject
			,		content
			FROM	han_notice
			WHERE	notice_id = \''.$noticeID.'\'
			AND		del_flag = \'N\'';

	$row = $conn->get_array($sql);

	$subject = StripSlashes($row['subject']);
	$content = StripSlashes($row['content']);

	Unset($row);
?>
<script type='text/javascript'>
	$(document).ready(function(){
		lfResize();
		self.focus();
	});

	$(window).bind('resize', function(e){
		window.resizeEvt;
		$(window).resize(function(){
			clearTimeout(window.resizeEvt);
			window.resizeEvt = setTimeout(function(){
				lfResize();
			}, 250);
		});
	});

	function lfResize(){
		var top = $('#divBody').offset().top;
		var body = document.body;
		var height = body.offsetHeight;
		var h = height - top;

		$('#divBody').height(h);

		try{
			lfResizeSub();
		}catch(e){
		}
	}

	function setCookie(name, value, expiredays ){
		var todayDate = new Date();
			todayDate.setDate( todayDate.getDate() + expiredays );

		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
	}
	function end(){
		var f = document.f;

		if (f.check.checked){
			setCookie('HAN_POPUP_<?=$noticeID;?>','done',1);
		}

		self.close();
	}

</script>
<form name="f" method="post" action="">
<div class="title title_border nowrap"><?=$subject;?></div>
<div id="divBody" style="width:100%; height:150px; padding:5px; overflow-x:auto; overflow-y:auto; border:1px solid #ccc;"><?=$content;?></div>
<div style="position:absolute; top:380px; width:400px; color:WHITE; background-color:#000;"><label><input type="checkbox" name="check" value="checkbox"  style="border:0;" onClick="end();"/>오늘 하루동안 열지않기</label></div>
<div style="position:absolute; top:385px; left:350px; background-color:#000;"><a href="#" onclick="end();"><span style="color:WHITE;">닫기</span></a></div>
</form>

<?
	include_once('../inc/_footer.php');
?>
