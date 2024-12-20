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
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #ff7e5f, #feb47b);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 850px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        h1 {
            font-size: 3rem;
            text-align: center;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 20px;
            font-size: 1.2em;
            color: #2c3e50;
        }

        input[type="radio"] {
            margin-right: 15px;
            accent-color: #e74c3c;
        }

        .secenekler {
            margin-bottom: 25px;
        }

        button {
            background-color: #e74c3c;
            color: #fff;
            padding: 15px 30px;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 30px auto;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        .success {
            background-color: #2ecc71;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        .sonuclar {
            margin-top: 40px;
        }

        .sonuclar h2 {
            font-size: 2.2em;
            color: #3498db;
            text-align: center;
            margin-bottom: 25px;
        }

        .sonuclar p {
            font-size: 1.2em;
            color: #34495e;
            margin-bottom: 15px;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
            button {
                padding: 12px 20px;
                font-size: 1.1em;
            }
            .container {
                padding: 20px;
            }
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
                    <p><?= htmlspecialchars
