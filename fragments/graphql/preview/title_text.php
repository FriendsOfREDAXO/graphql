<?php
$values = $this->getVar('values');
?>

<?php if ($values['title_large']): ?>
    <h2><?= $values['title_large'] ?></h2>
<?php endif; ?>
<?php if ($values['title']): ?>
    <h2><?= $values['title'] ?></h2>
<?php endif; ?>
<?php if ($values['title_small']): ?>
    <h3><?= $values['title_small'] ?></h3>
<?php endif; ?>
<?php if ($values['text']): ?>
    <?= $values['text'] ?>
<?php endif; ?>
