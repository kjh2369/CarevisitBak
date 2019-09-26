<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
?>
<style>
	.tree{font-size:12px;font-family:Tahoma, Geneva, sans-serif}
	.tree ul{margin:0;padding:0;list-style:none}
	.tree ul ul{margin:0 0 0 -3px}
	.tree li{position:relative;padding:0 0 0 22px;background:url(../image/line_tree.gif) no-repeat 9px 0;line-height:20px;white-space:nowrap;*zoom:1}
	.tree li.last{background-position:9px -1766px}
	.tree li.active a{font-weight:bold;color:#333}
	.tree li.active li a{font-weight:normal;color:#767676}
	.tree a{color:#767676;text-decoration:none}
	.tree a:hover,
	.tree a:active,
	.tree a:focus{text-decoration:underline}
	.tree .toggle{overflow:hidden;position:absolute;top:0;left:0;width:19px;height:19px;padding:0;border:0;background:transparent url(../image/btn_tree.gif) no-repeat;cursor:pointer;font-size:0;color:#fff;text-indent:19px;*text-indent:0;vertical-align:middle}
	.tree .toggle.plus{background-position:5px -15px}
	.tree .toggle.minus{background-position:5px 5px}
</style>
<?
	$sql = "select b00_code as code
			,      concat(b00_name, '[', b00_manager, ']') as name
			,      case when left(b00_name, 1) between '가' and '깋' then '가'
							when left(b00_name, 1) between '까' and '닣' then '나'
							when left(b00_name, 1) between '나' and '닣' then '나'
							when left(b00_name, 1) between '다' and '딯' then '다'
							when left(b00_name, 1) between '따' and '띻' then '따'
							when left(b00_name, 1) between '라' and '맇' then '라'
							when left(b00_name, 1) between '마' and '밓' then '마'
							when left(b00_name, 1) between '바' and '빟' then '바'
							when left(b00_name, 1) between '빠' and '삫' then '빠'
							when left(b00_name, 1) between '사' and '싷' then '사'
							when left(b00_name, 1) between '싸' and '앃' then '싸'
							when left(b00_name, 1) between '아' and '잏' then '아'
							when left(b00_name, 1) between '자' and '짛' then '자'
							when left(b00_name, 1) between '차' and '칳' then '차'
							when left(b00_name, 1) between '카' and '킿' then '카'
							when left(b00_name, 1) between '타' and '팋' then '타'
							when left(b00_name, 1) between '파' and '핗' then '파'
							when left(b00_name, 1) between '하' and '힣' then '하' else '영/숫자' end as firstName
			  from b00branch
			 order by b00_name";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		echo "<div class='tree' style='background-color:#fafafa; border:1px dotted #ccc; border-top:0;'>";

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$class[$i] = "";

			if ($tempFirstName != $row["firstName"]){
				if ($tempFirstName != ""){
					$class[$i-1] = "last";
				}
				$tempFirstName = $row["firstName"];
			}
		}
		$class[$i-1] = "last";
		$tempFirstName = "";

		$seq = 1;
		$id = 0;

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$code = $row['code'];
			$name = $row['name'];

			if ($tempFirstName != $row["firstName"]){
				$seq = 1;
				$id++;

				if ($tempFirstName != ""){
					echo "
						</ul>
						</li>
						</ul>
						 ";
				}
				$tempFirstName = $row["firstName"];

				echo "
					<ul>
						<li>
							<button id='id_btn_$id' class='toggle minus' type='button' onFocus='this.blur();' onClick='showTreeMenu(\"$id\");'>+</button>
							<a href='#' onFocus='this.blur();' onClick='showTreeMenu(\"$id\");' style='color:#000;'>$tempFirstName</a>
							<ul id='id_tree_$id' style='display:visible'>
					 ";
			}

			echo "<li class='".$class[$i]."'><a id='idBranch[]' href='#' style='color:#000;' onFocus='this.blur();' onClick='_b2cCenterList(\"$code\");'>$name</a></li>";

			$seq ++;

			echo '<input name="branchCode[]"  type="hidden" value="'.$code.'">';
		}

		echo "
						</ul>
					</li>
				</ul>
			</div>
			 ";
		echo '<input name="branchCount" type="hidden" value="'.$rowCount.'">';
	}
	$conn->row_free();

	include_once("../inc/_db_close.php");
?>