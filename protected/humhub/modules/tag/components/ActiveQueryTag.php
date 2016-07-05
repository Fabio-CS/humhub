<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tag\components;

use yii\db\ActiveQuery;

/**
 * Description of ActiveQueryUser
 *
 * @author luke
 */
class ActiveQueryTag extends ActiveQuery
{

    public function init()
    {
        parent::init();
    }

    public function active()
    {
        return $this;
    }

}
