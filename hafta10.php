

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tek Sayılar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #4CAF50;
            margin-top: 20px;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 20px auto;
            max-width: 300px;
        }
        li {
            background-color: #fff;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }
        li:hover {
            transform: scale(1.05);
            background-color: #e0f7fa;
        }
    </style>
</head>
<body>
    <h1>1 ile 100 Arası Tek Sayılar</h1>
    <ul>
        <?php
        for ($i = 1; $i <= 100; $i++) {
            if ($i % 2 != 0) { 
                echo "<li>$i</li>";
            }
        }
        ?>
    </ul>
</body>
</html>
