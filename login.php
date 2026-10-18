<?php
@ob_start();
session_start();

if (isset($_POST['proses'])) {
    require 'config.php';

    $user = strip_tags($_POST['user'] ?? '');
    $pass = strip_tags($_POST['pass'] ?? '');

    $sql = 'SELECT member.*, login.user, login.pass, login.role_id
            FROM member
            INNER JOIN login ON member.id_member = login.id_member
            WHERE login.user = ? AND login.pass = MD5(?)';
    $row = $config->prepare($sql);
    $row->execute([$user, $pass]);

    if ($row->rowCount() > 0) {
        $hasil = $row->fetch(PDO::FETCH_ASSOC);
        session_regenerate_id(true);
        $_SESSION['admin'] = $hasil;
        $_SESSION['role_id'] = $hasil['role_id'];
        header('Location: index.php');
        exit;
    }

    $_SESSION['login_error'] = 'Username atau password tidak sesuai.';
    header('Location: login.php');
    exit;
}

$loginError = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f766e">
    <meta name="description" content="Masuk ke sistem manajemen dan penjualan Toko Grosir.">
    <title>Masuk — Toko Grosir</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #132b2a;
            --muted: #617775;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --line: #dce8e5;
            --surface: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(ellipse at 10% 10%, rgba(45, 212, 191, .22), transparent 35%),
                radial-gradient(ellipse at 90% 85%, rgba(15, 118, 110, .15), transparent 35%),
                #f2f7f5;
            display: grid;
            place-items: center;
            padding: 24px;
        }
        .shell {
            width: min(100%, 980px);
            min-height: 570px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--surface);
            border: 1px solid rgba(220, 232, 229, .9);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 28px 80px rgba(19, 43, 42, .12);
        }
        .intro {
            position: relative;
            isolation: isolate;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            background: linear-gradient(145deg, #134e4a 0%, #0f766e 55%, #14b8a6 100%);
        }
        .intro:before, .intro:after {
            content: "";
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 50%;
        }
        .intro:before { width: 340px; height: 340px; right: -145px; top: 110px; }
        .intro:after { width: 230px; height: 230px; right: -90px; top: 165px; }
        .brand { display: flex; align-items: center; gap: 12px; font-weight: 750; letter-spacing: -.02em; }
        .brand-mark {
            width: 44px; height: 44px; border-radius: 14px;
            display: grid; place-items: center; background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.25); font-size: 22px;
        }
        .eyebrow { margin: 0 0 14px; color: #a7f3d0; font-size: 12px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .intro h1 { max-width: 380px; margin: 0; font-size: clamp(32px, 4vw, 48px); line-height: 1.08; letter-spacing: -.045em; }
        .intro-copy { max-width: 350px; color: rgba(255,255,255,.78); line-height: 1.75; margin-top: 20px; }
        .feature-list { display: grid; gap: 13px; margin-top: 30px; }
        .feature { display: flex; gap: 11px; align-items: center; color: rgba(255,255,255,.92); font-size: 14px; }
        .check { display: grid; place-items: center; width: 23px; height: 23px; flex: 0 0 23px; border-radius: 8px; background: rgba(167,243,208,.18); color: #a7f3d0; }
        .intro-foot { color: rgba(255,255,255,.65); font-size: 12px; }
        .form-side { padding: 52px clamp(26px, 5vw, 64px); display: flex; flex-direction: column; justify-content: center; }
        .form-side h2 { margin: 0; font-size: 30px; letter-spacing: -.04em; }
        .subtitle { margin: 10px 0 30px; color: var(--muted); font-size: 14px; line-height: 1.6; }
        .field { margin-bottom: 18px; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; }
        input {
            display: block; width: 100%; height: 50px; padding: 0 15px;
            border: 1px solid var(--line); border-radius: 12px; outline: none;
            color: var(--ink); background: #fbfdfc; font: inherit; transition: border .2s, box-shadow .2s;
        }
        input:focus { border-color: var(--brand); box-shadow: 0 0 0 4px rgba(15,118,110,.12); background: white; }
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 78px; }
        .toggle {
            position: absolute; top: 50%; right: 9px; transform: translateY(-50%);
            border: 0; background: transparent; color: var(--brand-dark); cursor: pointer;
            padding: 8px; border-radius: 8px; font-weight: 700; font-size: 12px;
        }
        .toggle:focus-visible, .submit:focus-visible { outline: 3px solid #5eead4; outline-offset: 3px; }
        .alert { margin: 0 0 18px; padding: 12px 14px; border-radius: 12px; background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; font-size: 13px; }
        .submit {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; min-height: 50px; border: 0; border-radius: 12px;
            color: white; background: var(--brand); font: inherit; font-weight: 750;
            cursor: pointer; box-shadow: 0 9px 18px rgba(15,118,110,.18); transition: transform .15s, background .15s;
        }
        .submit:hover { background: var(--brand-dark); transform: translateY(-1px); }
        .secure-note { margin-top: 22px; text-align: center; color: #7b8c89; font-size: 12px; line-height: 1.6; }
        @media (max-width: 720px) {
            body { padding: 14px; }
            .shell { grid-template-columns: 1fr; min-height: auto; border-radius: 22px; }
            .intro { padding: 28px; min-height: 250px; }
            .intro h1 { font-size: 31px; }
            .intro-copy, .feature-list, .intro-foot { display: none; }
            .form-side { padding: 34px 27px 36px; }
        }
        @media (prefers-reduced-motion: reduce) { *, *:before, *:after { transition: none !important; } }
    </style>
</head>
<body>
    <main class="shell">
        <section class="intro" aria-label="Tentang Toko Grosir">
            <div class="brand"><span class="brand-mark" aria-hidden="true">▦</span><span>Toko Grosir</span></div>
            <div>
                <p class="eyebrow">Sistem manajemen toko</p>
                <h1>Kelola toko lebih mudah, setiap hari.</h1>
                <p class="intro-copy">Satu tempat untuk membantu memantau persediaan, transaksi, dan laporan penjualan.</p>
                <div class="feature-list">
                    <div class="feature"><span class="check">✓</span>Data barang dan kategori lebih teratur</div>
                    <div class="feature"><span class="check">✓</span>Transaksi dan cetak struk</div>
                    <div class="feature"><span class="check">✓</span>Laporan penjualan untuk evaluasi</div>
                </div>
            </div>
            <div class="intro-foot">Akses khusus pengguna yang terdaftar.</div>
        </section>
        <section class="form-side">
            <h2>Selamat datang 👋</h2>
            <p class="subtitle">Masuk menggunakan akun yang telah dibuat oleh administrator.</p>
            <?php if ($loginError !== ''): ?>
                <div class="alert" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php" autocomplete="on">
                <div class="field">
                    <label for="user">Username</label>
                    <input id="user" name="user" type="text" placeholder="Masukkan username" autocomplete="username" required autofocus>
                </div>
                <div class="field">
                    <label for="pass">Password</label>
                    <div class="password-wrap">
                        <input id="pass" name="pass" type="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        <button class="toggle" type="button" id="togglePassword" aria-controls="pass" aria-pressed="false">Lihat</button>
                    </div>
                </div>
                <button class="submit" type="submit" name="proses"><span aria-hidden="true">↗</span> Masuk ke dashboard</button>
            </form>
            <p class="secure-note">🔒 Jangan bagikan username dan password kepada orang lain.</p>
        </section>
    </main>
    <script>
        const password = document.getElementById('pass');
        const toggle = document.getElementById('togglePassword');
        toggle.addEventListener('click', () => {
            const reveal = password.type === 'password';
            password.type = reveal ? 'text' : 'password';
            toggle.textContent = reveal ? 'Sembunyikan' : 'Lihat';
            toggle.setAttribute('aria-pressed', String(reveal));
        });
    </script>
</body>
</html>
