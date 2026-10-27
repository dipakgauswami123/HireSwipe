</main>
<script src="assets/js/menu.js" defer></script>
<?php foreach (($scripts ?? []) as $src): ?>
    <script src="<?= e($src) ?>" defer></script>
<?php endforeach; ?>
<?php include __DIR__ . '/foot.php'; ?>
