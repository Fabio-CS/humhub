<?php

/**
 * This View replaces a input with an tag picker
 *
 * @property String $inputId is the ID of the input HTML Element
 * @property Int $maxTags the maximum of tags for this input
 * @property String $tagSearchUrl the url of the search, to find the tags
 * @property String $currentValue is the current value of the parent field.
 *
 * @package humhub.modules_core.tag
 * @since 0.5
 */
use \humhub\modules\tag\models\Tag;
use \yii\helpers\Html;

$this->registerJsFile("@web/js/jquery.highlight.min.js");
$this->registerJsFile("@web/resources/tag/tagpicker.js");
?>

<?php
// Resolve ids to tags
$newValue = "";

foreach (explode(",", $currentValue) as $id) {
    $tag = Tag::findOne(['id' => trim($id)]);
    if ($tag != null) {
        $name = Html::encode($tag->name);
        $newValue .= '<li class="tagInput" id="' . $tag->id . '">' . $name . '<i class="fa fa-times-circle"></i></li>';
    }
}
?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#<?php echo $inputId; ?>').tagpicker({
            searchUrl: '<?php echo $tagSearchUrl; ?>',
            inputId: '#<?php echo $inputId; ?>',
            maxTags: '<?php echo $maxTags; ?>',
            currentValue: '<?php echo $newValue; ?>',
            focus: '<?php echo $focus; ?>',
            tagId: '<?php echo $tagId; ?>',
            data: <?php echo $data ?>,
            placeholderText: '<?php echo $placeholderText; ?>'
        });
    });
</script>