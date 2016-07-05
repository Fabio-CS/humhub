<?php

use humhub\modules\search\engine\Search;
use humhub\modules\tag\Events;

return [
    'id' => 'tag',
    'class' => \humhub\modules\tag\Module::className(),
    'isCoreModule' => true,
    'events' => [
        ['class' => Search::className(), 'event' => Search::EVENT_ON_REBUILD, 'callback' => array(Events::className(), 'onSearchRebuild')],
    ]
];
?>