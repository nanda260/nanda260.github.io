<?php
session_start();

// List kata 5 huruf
$kata_list = ['tebak', 'paket', 'karet', 'peluk', 'lipat', 'batur', 'wulan', 'nanda', 'ndole', 'diare', 'basah',  'motor', 'pintu', 'jaket', 'viral', 'cinta', 'rindu', 'senja', 'manja', 'setia'];

// Inisialisasi sesi
if (!isset($_SESSION['target'])) {
    $_SESSION['target'] = $kata_list[array_rand($kata_list)];
    $_SESSION['percobaan'] = [];
    $_SESSION['status'] = 'main';
    $_SESSION['maks'] = 6;
    $_SESSION['error'] = '';
}

// Proses tebakan
if (isset($_POST['tebakan']) && $_SESSION['status'] === 'main') {
    $input = strtolower(trim($_POST['tebakan']));
    $target = $_SESSION['target'];
    $panjang = strlen($target);

    if (strlen($input) !== $panjang || !ctype_alpha($input)) {
        $_SESSION['error'] = "Masukkan kata dengan tepat $panjang huruf (tanpa angka/simbol).";
    } else {
        $_SESSION['percobaan'][] = $input;
        $_SESSION['error'] = '';
        if ($input === $target) {
            $_SESSION['status'] = 'menang';
        } elseif (count($_SESSION['percobaan']) >= $_SESSION['maks']) {
            $_SESSION['status'] = 'kalah';
        }
    }
}

// Reset game
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Fungsi mengevaluasi tebakan
function cek_huruf($tebakan, $target)
{
    $hasil = [];
    $panjang = strlen($target);
    $target_terpakai = array_fill(0, $panjang, false);

    // Langkah 1: Cek posisi benar
    for ($i = 0; $i < $panjang; $i++) {
        if ($tebakan[$i] === $target[$i]) {
            $hasil[$i] = ['huruf' => $tebakan[$i], 'status' => 'benar'];
            $target_terpakai[$i] = true;
        }
    }

    // Langkah 2: Cek huruf salah posisi
    for ($i = 0; $i < $panjang; $i++) {
        if (!isset($hasil[$i])) {
            $ditemukan = false;
            for ($j = 0; $j < $panjang; $j++) {
                if (!$target_terpakai[$j] && $tebakan[$i] === $target[$j]) {
                    $ditemukan = true;
                    $target_terpakai[$j] = true;
                    break;
                }
            }
            $hasil[$i] = [
                'huruf' => $tebakan[$i],
                'status' => $ditemukan ? 'posisi_salah' : 'tidak_ada'
            ];
        }
    }

    return $hasil;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=cached" />
    <link rel="icon" href="logo-me-white.png">
    <title>Game Tebak Kata</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cal+Sans&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Special+Gothic+Expanded+One&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #121416;
            color: #fff;
            text-align: center;

        }

        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        main img{
            width: 100px;
            
            padding-top: 50px;
        }

        footer {
            background-color: rgb(0, 0, 0);
            color: #fff;
            padding: 15px 0;
            text-align: center;
            font-size: 14px;
            margin-top: 50px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }


        h1 {
            color: #0B5ED7;
            font-size: 28px;
        }

        .kotak {
            display: inline-block;
            width: 50px;
            height: 50px;
            margin: 3px;
            font-size: 24px;
            line-height: 50px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 5px;
        }

        .benar {
            background-color: #4caf50;
        }

        .posisi_salah {
            background-color: #ffeb3b;
            color: #000;
        }

        .tidak_ada {
            background-color: #555;
        }

        .tebakan {
            margin-bottom: 10px;
        }

        input[type="text"] {
            font-size: 20px;
            padding: 10px;
            width: 140px;
            text-align: center;
            text-transform: uppercase;
        }

        button {
            font-size: 16px;
            padding: 10px 20px;
            margin: 10px;
            background: #0B5ED7;
            color: #fff;
            border: 1px solid #0B5ED7;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: ease-in-out 0.3s;

            &:hover {
                background-color: transparent;
                box-shadow: 0px 6px 10px rgba(11, 93, 215, 0.39);

            }
        }

        .info {
            margin-top: 15px;
            font-size: 16px;
            color: #aaa;
        }

        .error {
            color: #ff7373;
            font-size: 14px;
            margin-top: 8px;
        }

        /* Tambahan untuk HP / layar kecil */
        @media (max-width: 600px) {

            h1 {
                font-size: 24px;
            }

            h2 {
                font-size: 18px;
            }

            .kotak {
                width: 50px;
                height: 50px;
                font-size: 28px;
                line-height: 50px;
                margin: 5px;
            }

            input[type="text"] {
                font-size: 20px;
                padding: 12px;
                width: 180px;
            }

            button {
                font-size: 18px;
                padding: 12px 24px;
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
            }

            .info {
                font-size: 13px;
            }

            .error {
                font-size: 16px;
            }

            footer p {
                font-size: 12px;
            }
        }
    </style>

</head>

<body>
    <main>

    <img src="logo-me-white.png" alt="Logo">
        <h1><span style="color: white;">Tebak Kata - </span>Wordle</h1>
        <p class="info">Masukkan kata dengan <b><?= strlen($_SESSION['target']) ?></b> huruf. Total percobaan: <?= $_SESSION['maks'] ?>.</p>

        <?php foreach ($_SESSION['percobaan'] as $tebakan): ?>
            <div class="tebakan">
                <?php
                $hasil = cek_huruf($tebakan, $_SESSION['target']);
                for ($i = 0; $i < strlen($_SESSION['target']); $i++) {
                    $item = $hasil[$i];
                    echo "<div class='kotak {$item['status']}'>{$item['huruf']}</div>";
                }
                ?>
            </div>
        <?php endforeach; ?>

        <?php if ($_SESSION['status'] === 'main'): ?>
            <form method="post">
                <input type="text" name="tebakan" maxlength="<?= strlen($_SESSION['target']) ?>" required autofocus>
                <button type="submit">Tebak</button>
            </form>
            <?php if ($_SESSION['error']): ?>
                <div class="error"><?= $_SESSION['error'] ?></div>
            <?php endif; ?>
        <?php elseif ($_SESSION['status'] === 'menang'): ?>
            <h2 style="color:lightgreen;"><i class="fa-solid fa-fire" style="padding-right: 5px"></i> Kamu Menang! Kata: <b><?= strtoupper($_SESSION['target']) ?></b></h2>
        <?php else: ?>
            <h2 style="color:salmon;"><i class="fa-solid fa-heart-crack" style="padding-right: 5px"></i> Kamu Kalah! Kata yang benar: <b><?= strtoupper($_SESSION['target']) ?></b></h2>
        <?php endif; ?>

        <form method="post">
            <button name="reset"><i class="fa-solid fa-rotate" style="padding-right: 5px;"></i> Main Lagi</button>
        </form>
    </main>
    <footer>
        <div class="container">
            <p>&copy; 2025 Nanda Prihadi. All Rights Reserved.</p>
            <p>Designed & Developed by Nanda Prihadi</p>
        </div>
    </footer>
</body>

</html>