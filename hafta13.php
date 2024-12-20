
<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kisiler";

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

// Formdan veri eklendiğinde
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ekle'])) {
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $email = $_POST['email'];

    $sql = "INSERT INTO kisi (ad, soyad, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $ad, $soyad, $email);

    if ($stmt->execute()) {
        echo "Kişi başarıyla eklendi.";
    } else {
        echo "Hata: " . $conn->error;
    }
    $stmt->close();
}

// Formdan arama yapıldığında
$sonuclar = [];
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ara'])) {
    $arama = $_POST['arama'];

    $sql = "SELECT * FROM kisi WHERE ad LIKE ? OR soyad LIKE ? OR email LIKE ?";
    $stmt = $conn->prepare($sql);
    $arama_param = "%$arama%";
    $stmt->bind_param("sss", $arama_param, $arama_param, $arama_param);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $sonuclar[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kişi Yönetimi</title>
    <style>
        body {
            background-color: #2e8b57; /* Yeşil arka plan */
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
            padding: 50px;
        }

        h1 {
            color: #ffffff;
        }

        form {
            background-color: #4CAF50; /* Form arka planı */
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 30px;
        }

        input[type="text"], input[type="email"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            width: 200px;
            border: none;
        }

        button {
            background-color: #45a049; /* Buton rengi */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #388e3c; /* Hover rengi */
        }

        table {
            margin-top: 30px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            background-color: #4CAF50;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #45a049;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

    <h1>Kişi Ekle</h1>
    <form method="POST">
        <label for="ad">Ad:</label>
        <input type="text" name="ad" id="ad" required>
        <br>
        <label for="soyad">Soyad:</label>
        <input type="text" name="soyad" id="soyad" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <br>
        <button type="submit" name="ekle">Ekle</button>
    </form>

    <h1>Kişi Ara</h1>
    <form method="POST" class="center">
        <label for="arama">Arama:</label>
        <input type="text" name="arama" id="arama" required>
        <br>
        <button type="submit" name="ara">Ara</button>
    </form>

    <?php if (!empty($sonuclar)) : ?>
        <h2>Arama Sonuçları</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Email</th>
            </tr>
            <?php foreach ($sonuclar as $kisi) : ?>
                <tr>
                    <td><?= htmlspecialchars($kisi['id']) ?></td>
                    <td><?= htmlspecialchars($kisi['ad']) ?></td>
                    <td><?= htmlspecialchars($kisi['soyad']) ?></td>
                    <td><?= htmlspecialchars($kisi['email']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</body>
</html>


