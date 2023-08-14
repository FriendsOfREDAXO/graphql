<?php
$sliceId = $this->getVar('slice_id');
$clangId = $this->getVar('clang_id');
$slice = rex_article_slice::getArticleSliceById($sliceId, $clangId);
$values = json_decode($slice->getValue(1), true);
$mblock = json_decode($slice->getValue(2), true);
$image = $slice->getMedia(1);

?>

<div class="panel-body">
    <?php
    $fragment = new \rex_fragment();
    $fragment->setVar('values', $values, false);
    $fragment->setVar('image', $image, false);
    echo $fragment->parse('graphql/preview/image_text.php');
    ?>
    <?php if($mblock && $mblock[0]): ?>
        <?php
        $fragment = new \rex_fragment();
        $fragment->setVar('values', $mblock[0], false);
        $fragment->setVar('image', $mblock[0]['REX_MEDIA_1'], false);
        echo $fragment->parse('graphql/preview/image_text.php');
        ?>
    <?php endif; ?>
</div>
