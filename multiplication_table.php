<!DOCTYPE html>
<html>
<head>
    <title>My Server Lab</title>
    <style>
        table { border-collapse: collapse}
	td { border: 1px solid #ccc; padding: 5px; width: 30px; text-align: center}
	th { border: 1px solid #ccc; padding: 5px; width: 30px; background-color: #34495e; color: white}
	.diagonal {background-color: #ebf5fb}
    </style>
</head>
<body>

<?php
echo '<table>';
for ($i = 1; $i <= 10; $i++){
	echo '<tr>';
	for ($j = 1; $j<= 10; $j++){
		if ($i === 1 and $j===1){
			echo '<th>' . ' ' . '</th>';
		}
		else if ($i === 1 or $j === 1){
			echo '<th>' . $i*$j . '</th>';
		}
		else if ($i === $j)
		{
			echo '<td class="diagonal">' . $i*$j . '</td>';
		}
		else{
			echo '<td>' . $i*$j . '</td>';
		}
	}
	echo '</tr>';
}
echo '</table>';
?>

</body>
</html>
