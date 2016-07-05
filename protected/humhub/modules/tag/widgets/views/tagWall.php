<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="panel panel-default" id="tag-wall-<?php echo $tag->id; ?>">
    <div class="panel-body">
        <div class="media">
            <div class="media-body">
                <?php echo Html::a(Html::encode($tag->name), Url::to(['/directory/directory/tags', 'keyword' =>  $tag->name]), array('class' => 'label label-default')); ?>
            </div>
        </div>
    </div>
</div>
