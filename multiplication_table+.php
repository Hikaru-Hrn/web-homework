<!DOCTYPE html>
<html>
<head>
    <title>My Server Lab</title>
    <style>
        table { border-collapse: collapse}
        td { border: 1px solid #ccc; padding: 5px; width: 30px; text-align: center}
        th { border: 1px solid #ccc; padding: 5px; width: 30px; background-color: #34495e; color: white}
        .diagonal {background-color: #ebf5fb}
        button { border 2px; padding: 5px}
    </style>
</head>
<body>
        <form>
        <p>X: <input type="number" name="sizeX" value="<?= $sizeX ?? 10 ?>" min="1" max"100"></p>
        <p>Y:  <input type="number" name="sizeY" value="<?= $sizeY ?? 10 ?>" min="1" max"100"></p>
        <button type="submit">Создать</button>
        </form>

<?php
$sizeX = min($_GET['sizeX'], 100);
if ($sizeX < 1)
        $sizeX = 1;
$sizeY = min($_GET['sizeY'], 100);
if ($sizeY < 1)
        $sizeY = 1;


echo '<table>';
for ($i = 1; $i <= $sizeY; $i++){
        echo '<tr>';
        for ($j = 1; $j<= $sizeX; $j++){
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
