<!DOCTYPE html>
<html>
<head>
    <title>My Server Lab</title>
    <style>
        table { border-collapse: collapse}
	td { border: 1px solid #ccc; padding: 5px; width: 30px; text-align: center}
    </style>
</head>
<body>

<?php
echo '<table>';
for ($i = 1; $i <= 10; $i++){
	echo '<tr>';
	for ($j = 1; $j<= 10; $j++){
		echo '<td>' . $i*$j . '</td>';
	}
	echo '</td>';
}
echo '</table>'
?>

</body>
</html>
