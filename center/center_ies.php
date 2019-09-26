<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
?>
<base target='_self'>
<script language='javascript'>
<!--

var opener = null;

function search(){
	try{
		$.ajax({
			type: 'POST',
			url : './center_ies_'+opener.work+'.php',
			data: {
				'code':opener.code
			,	'kind':opener.kind
			},
			beforeSend: function (){
			},
			success: function (xmlHttp){
				$('#body').html(xmlHttp);
			},
			error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}

function setItem(para){
	opener.para = para;
	self.close();
}

$(document).ready(function(){
	opener = window.dialogArguments;
	search();
});

-->
</script>
<div id='body'></div>
<?
	include_once("../inc/_footer.php");
?>