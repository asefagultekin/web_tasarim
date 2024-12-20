<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "anket";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}


$sorular = [
    "PHP programlama dilini ne kadar etkili öğrenebildiniz?",
    "Bilgisayar programcılığı alanında bir kariyer yapmak ister misiniz?",
    "Programlama yaparken karşılaştığınız zorluklarla başa çıkma konusunda ne kadar başarılı hissediyorsunuz?",
    "Visual Studio Code (VSCode) gibi bir kod editörü ile çalışma deneyiminiz hakkında ne düşünüyorsunuz?",
    "Programlama dillerindeki temel kavramları anlama seviyeniz nedir?"
];

$secenekler = [
    [
        "Çok etkili öğrendim, hemen her konuda bilgi sahibiyim.",
        "Biraz öğrendim, bazı konularda eksiklerim var.",
        "Hiç öğrenmedim, yalnızca temel şeyleri öğrendim.",
        "Henüz öğrenmedim, fakat ilgileniyorum."
    ],
    [
        "Evet, kesinlikle bu alanda çalışmak istiyorum.",
        "Bilmiyorum, karar vermedim.",
        "Hayır, bu alanda çalışmak istemiyorum."
    ],
    [
        "Çok başarılıyım, zorlukları hızla aşabiliyorum.",
        "Ortalama düzeydeyim, bazen zorlanıyorum.",
        "Zorluklarla başa çıkmakta zorlanıyorum.",
        "Henüz programlama yapmadım."
    ],
    [
        "VSCode'u çok verimli buluyorum, sık kullanıyorum.",
        "VSCode'u kullanıyorum ama daha çok öğrenmem gerekiyor.",
        "VSCode'u hiç kullanmadım ama denemek isterim.",
        "VSCode'u kullanmıyorum ve kullanmayı düşünmüyorum."
    ],
    [
        "Temel kavramları tamamen anlıyorum ve kullanabiliyorum.",
        "Temel kavramları öğrenmeye başladım ama bazen karışabiliyor.",
        "Temel kavramları anlamakta zorlanıyorum.",
        "Henüz programlama ile tanışmadım."
    ]
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['oyla'])) {
    foreach ($sorular as $index => $soru) {
        $cevap = $_POST['cevap_' . $index];
        $sql = "INSERT INTO anket_sonuclari (soru, cevap) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $soru, $cevap);
        $stmt->execute();
    }
    echo "<div class='success'>Oylarınız başarıyla kaydedildi!</div>";
}

$sonuclar = [];
foreach ($sorular as $index => $soru) {
    $sql = "SELECT cevap, COUNT(*) as sayi FROM anket_sonuclari WHERE soru = ? GROUP BY cevap";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $soru);
    $stmt->execute();
    $result = $stmt->get_result();

    $toplam = 0;
    $cevap_sayilari = [];
    while ($row = $result->fetch_assoc()) {
        $cevap_sayilari[$row['cevap']] = $row['sayi'];
        $toplam += $row['sayi'];
    }

    $yuzdeler = [];
    foreach ($secenekler[$index] as $secenek) {
        $sayi = $cevap_sayilari[$secenek] ?? 0;
        $yuzde = $toplam > 0 ? ($sayi / $toplam) * 100 : 0;
        $yuzdeler[$secenek] = round($yuzde, 2);
    }

    $sonuclar[$soru] = $yuzdeler;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anket</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 900px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #3498db;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-size: 1.1em;
            color: #555;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        .secenekler {
            margin-bottom: 20px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1.2em;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 30px auto;
        }

        button:hover {
            background-color: #2980b9;
        }

        .success {
            background-color: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .sonuclar {
            margin-top: 40px;
        }

        .sonuclar h2 {
            font-size: 2em;
            color: #2980b9;
            margin-bottom: 20px;
            text-align: center;
        }

        .sonuclar p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Anket</h1>
        <form method="POST">
            <?php foreach ($sorular as $index => $soru) : ?>
                <label><?= htmlspecialchars($soru) ?></label>
                <div class="secenekler">
                    <?php foreach ($secenekler[$index] as $secenek) : ?>
                        <input type="radio" name="cevap_<?= $index ?>" value="<?= htmlspecialchars($secenek) ?>" required>
                        <?= htmlspecialchars($secenek) ?>
                        <br>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="oyla">Oy Ver</button>
        </form>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['oyla'])) : ?>
            <div class="success">Oylarınız başarıyla kaydedildi!</div>
        <?php endif; ?>

        <div class="sonuclar">
            <h2>Sonuçlar</h2>
            <?php foreach ($sonuclar as $soru => $yuzdeler) : ?>
                <h3><?= htmlspecialchars($soru) ?></h3>
                <?php foreach ($yuzdeler as $secenek => $yuzde) : ?>
                    <p><?= htmlspecialchars($secenek) ?>: %<?= htmlspecialchars($yuzde) ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
