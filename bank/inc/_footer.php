	<!--���� �޴� ������-->
	<input type="hidden" id="menu" name="menu" value="<?=$menu?>">
	<script type="text/javascript">
		try{
			TopMenu_Type();	//ž�޴�����Ʈǥ��
		}catch(e){
		}
	</script>
</body>
</html>
<iframe src="../refresh.html" width="0" height="0" frameborder="0"></iframe><?
include_once('../../inc/_db_close.php');?>