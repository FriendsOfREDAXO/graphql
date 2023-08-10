<?php

$values = $this->getVar('values');
$image = $this->getVar('image');
?>
<div class="row">
    <?php if ($values['title_large'] || $values['title'] || $values['title_small']): ?>
        <div class="col-lg-8">
            <?php
            $fragment = new \rex_fragment();
            $fragment->setVar('values', $values, false);
            echo $fragment->parse('graphql/preview/title_text.php');
            ?>
        </div>
    <?php endif; ?>
    <?php if ($image): ?>
        <?php
        $media = rex_media::get($image);
        ?>
        <div class="col-lg-4">
            <img src="<?= $media->getUrl() ?>"
        </div>
    <?php endif; ?>
</div>
