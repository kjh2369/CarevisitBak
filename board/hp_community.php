<?
include_once("../inc/_db_open.php");
include_once("../inc/_myFun.php");

if($_GET['homepage'] == 'fw'){ 
	$m_title = 'community'; ?>
	<style>
		/*view
		============================================================*/
		.view_type,.write_type th,.write_type td{border:0;}  
		.view_type{width:100%; border-top:2px solid #4c9400; table-layout:fixed;}  
		.view_type caption{display:none}
		.view_type th{padding:5px 0 5px 0px; border-bottom:1px solid #82bf41; border-right:1px solid #82bf41; background:#92d050 ; color:#000; font-weight:bold; text-align:left; font-weight:normal;}
		.view_type th {text-align:center;}
		.view_type th label	{display:block;}
		.view_type th img{vertical-align:middle;}
		.view_type td{padding:5px 0 4px 15px; border-bottom:1px solid #82bf41;}  
		.view_type td select{border:1px solid #cccccc; vertical-align:middle; height:20px; line-height:18px; _height:16px; font-size:12px; padding:2px;}
		.view_type td.cont{padding:15px; border-bottom:1px solid #82bf41;  color:#252525; line-height:1.5em;}
		.t_right { text-align:right; padding-right:10px !important;}
		.tbl_btn1{ display:inline-block; width:100px; height:30px; line-height:30px; border:1px solid #346501; background-color:#386c00; text-align:center; color:#fff;  font-size:12px; font-weight:bold;}
	</style><?
}else {
	$m_title = 'work'; ?>
<?
}

if($_GET['mode'] == 1){
	include_once("./hp_community_list.php");
}else {
	include_once("./hp_community_view.php");
}
?>
