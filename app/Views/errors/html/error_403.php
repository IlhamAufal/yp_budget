<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Akses Ditolak') ?></title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f3f4f6; color: #1f2937; }
        main { width: min(92vw, 560px); padding: 2.5rem; border: 1px solid #e5e7eb; border-radius: 1rem; background: #fff; box-shadow: 0 16px 40px rgba(15, 23, 42, .10); text-align: center; }
        .code { margin: 0; color: #dc2626; font-size: 4rem; line-height: 1; font-weight: 800; }
        h1 { margin: 1rem 0 .5rem; font-size: 1.5rem; }
        p { margin: 0 0 1.5rem; color: #4b5563; line-height: 1.6; }
        a { display: inline-block; margin: .25rem; padding: .7rem 1.1rem; border-radius: .5rem; text-decoration: none; font-weight: 700; }
        .logout { background: #dc2626; color: #fff; }
        .back { border: 1px solid #d1d5db; color: #374151; }
    </style>
</head>
<body>
    <main>
        <p class="code">403</p>
        <h1><?= esc($title ?? 'Akses Ditolak') ?></h1>
        <p><?= esc($message ?? 'Anda tidak memiliki hak akses ke halaman ini.') ?></p>
        <a class="logout" href="<?= site_url('logout') ?>">Logout</a>
        <a class="back" href="<?= site_url('login') ?>">Kembali ke Login</a>
    </main>
</body>
</html>
