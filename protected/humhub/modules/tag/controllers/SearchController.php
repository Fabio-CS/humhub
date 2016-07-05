<?php

namespace humhub\modules\tag\controllers;

use Yii;
use yii\web\Controller;

/**
 * Search Controller provides action for searching tags.
 *
 * @author Fabio
 * @package humhub.modules_core.tag.controllers
 * @since 0.5
 */
class SearchController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
       
        ];
    }

    /**
     * JSON Search for Tags
     *
     * Returns an array of tags with fields:
     *  - id
     *  - name
     */
    public function actionJson()
    {
        Yii::$app->response->format = 'json';
        return \humhub\modules\tag\widgets\TagPicker::filter([
            'keyword' => Yii::$app->request->get('keyword'),
        ]);
    }

}

?>
