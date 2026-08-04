<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <h1 class="text-title-lg font-bold text-gray-800 dark:text-white/90">
        <?= isset($title) ? esc($title) : 'Dashboard' ?>
    </h1>
</div>

<?= $this->endSection() ?>