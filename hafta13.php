
<?php
// Veritabanı bağlantısı
$servername = "localhost"; // veya hosting sağlayıcınızın sağladığı sunucu adı
$username = "root";        // Veritabanı kullanıcı adı
$password = "";            // Veritabanı şifresi
$dbname = "test";          // Veritabanı adı

// Veritabanı bağlantısı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

// 1. Form işlemi (Veri ekleme)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_add'])) {
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $email = $_POST['email'];

    $sql = "INSERT INTO kisi (ad, soyad, email) VALUES ('$ad', '$soyad', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Yeni kayıt başarıyla eklendi!";
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
}

// 2. Form işlemi (Arama)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_search'])) {
    $search_name = $_POST['search_name'];

    $sql = "SELECT soyad, email FROM kisi WHERE ad = '$search_name'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Soyad: " . $row["soyad"] . "<br> E-posta: " . $row["email"] . "<br><br>";
        }
    } else {
        echo "Kullanıcı bulunamadı.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veri Girişi ve Arama</title>
</head>
<body>
    <h2>1. Form: Yeni Kişi Ekle</h2>
    <form method="POST" action="">
        <label for="ad">Ad:</label>
        <input type="text" name="ad" id="ad" required><br><br>
        
        <label for="soyad">Soyad:</label>
        <input type="text" name="soyad" id="soyad" required><br><br>
        
        <label for="email">E-posta:</label>
        <input type="email" name="email" id="email" required><br><br>

        <input type="submit" name="submit_add" value="Kişi Ekle">
    </form>

    <hr>

    <h2>2. Form: Kişi Arama</h2>
    <form method="POST" action="">
        <label for="search_name">Ad:</label>
        <input type="text" name="search_name" id="search_name" required><br><br>
        
        <input type="submit" name="submit_search" value="Ara">
    </form>
</body>
</html>
