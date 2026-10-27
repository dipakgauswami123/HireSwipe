        </main>
    </div>
</div>
<nav class="tabbar" aria-label="Main">
    <?php $renderNav('tab-link'); ?>
</nav>
<script src="assets/js/menu.js" defer></script>
<?php foreach (($scripts ?? []) as $src): ?>
    <script src="<?= e($src) ?>" defer></script>
<?php endforeach; ?>
<?php include __DIR__ . '/foot.php'; ?>
