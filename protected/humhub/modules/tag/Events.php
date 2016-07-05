<?php

namespace humhub\modules\tag;

use Yii;
use humhub\modules\tag\models\Tag;

/**
 * Events provides callbacks for all defined module events.
 * 
 * @author fabio
 */
class Events extends \yii\base\Object
{

    /**
     * On rebuild of the search index, rebuild all user records
     *
     * @param \yii\base\Event $event
     */
    public static function onSearchRebuild($event)
    {
        foreach (models\Tag::find()->all() as $obj) {
            \Yii::$app->search->add($obj);
        }
    }

}
