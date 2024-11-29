<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastgele Tablo Oluşturucu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        form {
            margin: 20px auto;
            padding: 15px;
            width: 300px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #5a67d8;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #434190;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #5a67d8;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Rastgele Tablo Oluşturucu</h1>
    <form method="POST">
        <label for="rows">Satır Sayısı:</label>
        <input type="number" id="rows" name="rows" min="1" placeholder="Örn: 5" required>
        <label for="cols">Sütun Sayısı:</label>
        <input type="number" id="cols" name="cols" min="1" placeholder="Örn: 4" required>
        <button type="submit">Tabloyu Oluştur</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $rows = intval($_POST['rows']);
        $cols = intval($_POST['cols']);

        if ($rows > 0 && $cols > 0) {
            echo "<h2>Oluşturulan Tablo:</h2>";
            echo "<table>";
            echo "<tr>";
            for ($header = 1; $header <= $cols; $header++) {
                echo "<th>Sütun $header</th>";
            }
            echo "</tr>";

            for ($i = 0; $i < $rows; $i++) {
                echo "<tr>";
                for ($j = 0; $j < $cols; $j++) {
                    $randomNumber = rand(1, 100);
                    echo "<td>$randomNumber</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Lütfen geçerli bir sayı giriniz.</p>";
        }
    }
    ?>
</body>
</html>
