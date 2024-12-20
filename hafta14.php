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
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f0fe;
            color: #333;
            padding: 20px;
        }
        form {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #1a73e8;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        button {
            background-color: #1a73e8;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1666c1;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        .sonuclar {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .sonuclar h2 {
            color: #1a73e8;
        }
        .sonuclar p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Anket</h1>
    <form method="POST">
        <?php foreach ($sorular as $index => $soru) : ?>
            <label><?= htmlspecialchars($soru) ?></label>
            <?php foreach ($secenekler[$index] as $secenek) : ?>
                <input type="radio" name="cevap_<?= $index ?>" value="<?= htmlspecialchars($secenek) ?>" required>
                <?= htmlspecialchars($secenek) ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <button type="submit" name="oyla">Oy Ver</button>
    </form>

    <div class="sonuclar">
        <h2>Sonuçlar</h2>
        <?php foreach ($sonuclar as $soru => $yuzdeler) : ?>
            <h3><?= htmlspecialchars($soru) ?></h3>
            <?php foreach ($
