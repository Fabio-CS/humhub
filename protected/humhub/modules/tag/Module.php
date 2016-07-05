<?php

/**
 */

namespace humhub\modules\tag;

use Yii;

/**
 * User Module
 */
class Module extends \humhub\components\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'humhub\modules\tag\controllers';


    public function getName()
    {
        return 'tag';
    }

}
