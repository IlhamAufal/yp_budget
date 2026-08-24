<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Budget Planning System</title>
  <link rel="icon" href="<?= base_url('favicon.ico') ?>">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
  <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-md space-y-6">
      
      <div class="text-center">
        <a href="<?= base_url('/') ?>" class="inline-block">
          <img class="mx-auto h-12 dark:hidden" src="<?= base_url('assets/images/logo/logo-light.svg') ?>" alt="Logo YP Budget" />
          <img class="mx-auto h-12 hidden dark:block" src="<?= base_url('assets/images/logo/logo-dark.svg') ?>" alt="Logo YP Budget" />
        </a>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-800 sm:p-8">
        
        <div class="mb-8 text-center">
          <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            Selamat Datang
          </h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Budget Planning & Monitoring System
          </p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-base"></i>
            <div><?= session()->getFlashdata('error') ?></div>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('login/process') ?>" method="POST" class="space-y-5">
          <?= csrf_field() ?>

          <div>
            <label for="username" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Username
            </label>
            <div class="relative">
              <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fa-solid fa-user"></i>
              </span>
              <input
                type="text"
                id="username"
                name="username"
                value="<?= old('username') ?>"
                placeholder="Masukkan username"
                required
                autofocus
                class="w-full rounded-xl border border-gray-300 bg-gray-50 py-3 pl-11 pr-4 text-sm text-gray-900 transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-500 dark:focus:bg-gray-900"
              />
            </div>
          </div>

          <div>
            <label
              for="password"
              class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              Password
            </label>

            <div class="relative">
              <span
                class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
              >
                <i class="fa-solid fa-lock"></i>
              </span>

              <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required
                class="w-full rounded-xl border border-gray-300 bg-gray-50 py-3 pl-11 pr-11 text-sm text-gray-900 transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-500 dark:focus:bg-gray-900"
              />

              <button
                type="button"
                id="toggle-password"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                tabindex="-1"
              >
                <i id="toggle-password-icon" class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2.5 cursor-pointer">
              <input
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900"
              />
              <span class="text-sm text-gray-600 dark:text-gray-400">
                Ingat saya
              </span>
            </label>
          </div>

          <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-500/25 transition-all hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/50 active:scale-[0.99] dark:bg-brand-600 dark:hover:bg-brand-500"
          >
            <i class="fa-solid fa-right-to-bracket"></i>
            Masuk
          </button>
        </form>

        <!-- <div class="mt-8 rounded-xl border border-blue-100 bg-blue-50/70 p-4 dark:border-blue-900/30 dark:bg-blue-900/15">
          <div class="flex items-center gap-2 text-sm font-semibold text-blue-800 dark:text-blue-400">
            <i class="fa-solid fa-circle-info"></i>
            <span>Akun Demo</span>
          </div>
          <div class="mt-2 space-y-1 text-xs text-blue-700 dark:text-blue-300">
            <p><span class="font-medium text-blue-900 dark:text-blue-200">Username:</span> admin</p>
            <p><span class="font-medium text-blue-900 dark:text-blue-200">Password:</span> ••••••••</p>
          </div>
        </div> -->

      </div>

      <div class="text-center text-xs text-gray-500 dark:text-gray-400">
        &copy; <?= date('Y') ?> Budget Planning System. All rights reserved.
      </div>

    </div>
  </div>

  <script>
    (function () {
      var btn = document.getElementById('toggle-password');
      var input = document.getElementById('password');
      var icon = document.getElementById('toggle-password-icon');
      if (!btn || !input || !icon) return;

      btn.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
      });
    })();
  </script>
</body>
</html>