<?php

namespace humhub\modules\tag\widgets;

class TagWall extends \yii\base\Widget
{

    public $tag;

    public function run()
    {
        return $this->render('tagWall', array('tag' => $this->tag));
    }

}

?>