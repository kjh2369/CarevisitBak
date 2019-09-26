<?
include("../inc/_db_open.php");

$conn -> begin();
/*
$sql = "update m03sugupja
		   set m03_jumin = '0304263358221'
		 where m03_jumin like '030426%'
		   and m03_ccode = 'DW-F-043-02' ";
$conn -> execute($sql);

$sql = "update m03sugupja
		   set m03_jumin = '0211103358212'
		 where m03_jumin like '0211103%'
		   and m03_ccode = 'DW-F-043-02' ";
$conn -> execute($sql);

$sql = "update m03sugupja
		   set m03_jumin = '0510013358218'
		 where m03_jumin like '1010011%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '4903131055219'
		 where m03_jumin like '4903131%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5111042357814'
		 where m03_jumin like '5111042%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5306121386918'
		 where m03_jumin like '5306121%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5504181357115'
		 where m03_jumin like '5504181%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5801041394711'
		 where m03_jumin like '5801041%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5905272394819'
		 where m03_jumin like '5905272%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5906301357414'
		 where m03_jumin like '5906301%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5908121396911'
		 where m03_jumin like '5908121%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '5912081357318'
		 where m03_jumin like '5912081%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6002052357325'
		 where m03_jumin like '6002052%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6006121357319'
		 where m03_jumin like '6006121%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6102171395214'
		 where m03_jumin like '6102171%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6206081395116'
		 where m03_jumin like '6206081%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6208292395112'
		 where m03_jumin like '6208292%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6309231395217'
		 where m03_jumin like '6309231%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6410302357412'
		 where m03_jumin like '6410302%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6510031390512'
		 where m03_jumin like '6510031%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6512012395110'
		 where m03_jumin like '6512012%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6601092396816'
		 where m03_jumin like '6601092%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6612062392321'
		 where m03_jumin like '6612062%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '6902041458314'
		 where m03_jumin like '6902041%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '7208061057317'
		 where m03_jumin like '7208061%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '7703251328811'
		 where m03_jumin like '7703251%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '8504171358017'
		 where m03_jumin like '8504171%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '8906131030028'
		 where m03_jumin like '8906131%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '9009091358211'
		 where m03_jumin like '9009091%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '9109122912311'
		 where m03_jumin like '9109122%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '9512291178411'
		 where m03_jumin like '9512291%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);
$sql = "update m03sugupja
		   set m03_jumin = '9602062223718'
		 where m03_jumin like '9602062%'
		   and m03_ccode = 'DW-F-043-02'";
$conn -> execute($sql);


$conn -> commit();
*/
include('../inc/_db_close.php');

?>
