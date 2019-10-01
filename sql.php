
<?
	error_reporting(E_ERROR);
	ini_set("display_errors", 1);

	$sql = $_POST['sql'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Document</title>
</head>
<style>  
	html { font-family:tahoma; font-size:10pt;}  
	.mytable { border-collapse:collapse;}  
	.mytable th, .mytable td { border:1px solid black; padding: 2px 8px 2px 8px; }
	.mytable tr:first-child { background-color: lightgray; text-align: center; }
</style>
<body>
	<table width="100%" cellspacing="0"><tr>
		<td style="width:50%;">
			<div id="app">
				<form name="form" action="/sql.php" method="post">
						<textarea v-bind:name="name" rows="6" style="width:80%"><?=$sql?></textarea>
						<button v-on:click="sqlSearch">search</button>
				</form>
			</div>
		</td>
		<td style="width:50%;">
			<ul>
				<li>show tables</li>
				<li>han_member</li>
				<li>menu_top</li>
			</ul>
		</td>
	</tr></table>
</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
    new Vue({
        el: '#app',
        data : {
            name: 'sql'
        },
        methods: {
            sqlSearch: function(e) {
                document.form.submit();
            }
        }
    });
</script>
<?
	include_once('inc/_db_open.php');

	$sql = $_POST['sql'];
	echo '<pre>'.$sql.'</pre>';
	// $sql = "show tables";
	// $sql = 'select * from han_member';
	// $sql = 'select * from care_area';

	$conn->fetch_type = 'assoc';
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();


	$i = 0;
	while ($i < mysql_num_fields($conn->result))
	{
	   $fld = mysql_fetch_field($conn->result, $i);
	   $tblField[] = $fld->name;
	   $i = $i + 1;
	}
		
	echo '<table class="mytable">';

	// column name header
	echo '<tr>';
	echo '<td nowrap>No</td>';
	for($col = 0; $col < count($tblField); $col++) {
		echo '<td nowrap>'.$tblField[$col].'</td>';
	}
	echo '</tr>';

	
	// result row
	for($i = 0; $i < $row_count; $i++) {
		$row = $conn->select_row($i);
		echo '<tr>';
		echo '<td nowrap>'.($i+1).'</td>';
		for($col = 0; $col < count($tblField); $col++) {
			$key = $tblField[$col];
			echo '<td nowrap>'.$row[$key].'</td>';
		}
		echo '</tr>';
	}

	echo '</table>';

	$conn->row_free();

	include_once('inc/_db_close.php');
?>
</html>
