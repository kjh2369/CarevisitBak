<?
	@session_start();

	//$update_image_name = iconv("UTF-8","EUCKR",$update_image_name);
	//$filepath = '../editor/upload';
	$storage  = $_REQUEST['filepath'];
	$update_image = $_FILES['update_image'];
	$update_image_name = $update_image['name'];

	//$file_name_only = substr($update_image_name,0,strrpos($update_image_name,"."));//�����̸�
	//$file_name_ext = substr($userfile_name,strrpos($userfile_name,"."));//Ȯ�����̸�

 	$needle = strrpos($update_image_name,'.') + 1;
	$extension = substr($update_image_name, $needle);

	if($update_image != "")
	{
		$update_image_name =date("YmdHis").".".$extension;
	}

	// ó����============================
	$Prohibited_Ext = array('html','htm','phtml','php','php3','php4','inc','pl','cgi','jsp');
	$exist = file_exists("$storage/$update_image_name");

	if(($exist) && ($update_image != ""))
	{
		$update_image_name =date("YmdHis").".".$extension;
	 }

	if (in_array($extension,$Prohibited_Ext))
	{
        echo(" <script>alert('$extension ������ ���ε� �����մϴ�.');</script>");
	    exit;
    }else{
		$wFolder = $storage."/".$update_image_name;
		move_uploaded_file($update_image["tmp_name"],$wFolder);
	} // Ȯ���� �˻�
?>
<script language=javascript>
	parent.parent.insertIMG('<?=$update_image_name?>');
	window.location="imgupload.html";
	parent.parent.oEditors.getById['ir1'].exec('SE_TOGGLE_FILEUPLOAD_LAYER');
</script>