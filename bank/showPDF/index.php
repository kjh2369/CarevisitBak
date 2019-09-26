<?
	parse_str($_POST['arguments'], $val);

	$target = !empty($val['target']) ? $val['target'] : 'show_pdf.php';
?>
<html>
<head>
	<title>CAREVISIT</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='imagetoolbar' content='no'>
</head>
<body style="margin:0;">
<iframe name="framePDF" src="about:blank" style="width:100%; height:100%;" frameborder="1" scrolling="no"></iframe>
<form name="f" method="post">
<input name="para" type="hidden" value="<?=$_POST['arguments'];?>">
</form>
</body>
</html>

<script type="text/javascript">
	var f = document.f;
	f.target = "framePDF";
	f.action = "./<?=$target;?>";
	f.submit();
	window.onload=function(){
		self.focus();
	}
</script>