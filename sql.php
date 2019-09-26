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
.mytable { border-collapse:collapse; }  
.mytable th, .mytable td { border:1px solid black; }
.mytable tr:first-child { color: red;}
</style>
<body>
    <div id="app">
        <form action="/sql.php" method="post">
                <textarea v-bind:name="name" cols="30" rows="10"></textarea>
                <button v-on:click="sqlSearch">search</button>
        </form>
    </div>
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

	$find_center = $_POST['find_center'];
	$find_dept   = $_POST['find_dept'];
	$sql = $_POST['sql'];
	echo $sql;
	echo('<script> document.querySelector("textarea").value="'.$sql.'" </script>');
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
	for($col = 0; $col < count($tblField); $col++) {
		echo '<td nowrap>'.$tblField[$col].'</td>';
	}
	echo '</tr>';

	// result row
	for($i = 0; $i < $row_count; $i++) {
		$row = $conn->select_row($i);
		echo '<tr>';
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
