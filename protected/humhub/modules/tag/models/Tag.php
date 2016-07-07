<?php

/**
 */

namespace humhub\modules\tag\models;

use Yii;
use humhub\modules\tag\components\ActiveQueryTag;
use humhub\modules\content\models\Content;
use humhub\modules\contentrating\models\Rating;

/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property string $name
 */
class Tag extends \yii\db\ActiveRecord implements \humhub\modules\search\interfaces\Searchable
{
    
    public $rating;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50, 'min' => 2],
            [['name'], 'unique'],
            [['id'], 'integer'],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tag'
        ];
    }

    /**
     * @inheritdoc
     *
     * @return ActiveQueryContent
     */
    public static function find()
    {
        return Yii::createObject(ActiveQueryTag::className(), [get_called_class()]);
    }

    public function getId()
    {
        return $this->id;
    }
    
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getRatings(){
        return $this->hasMany(Rating::className(), ['tag_id' => 'id']);
    }
    
    public function getContents()
    {
        return $this->hasMany(Content::className(), ['id' => 'content_id'])->viaTable('content_tag', ['tag_id' => 'id']);
    }
    
    public function getWallOut(){
        return \humhub\modules\tag\widgets\TagWall::widget(['tag' => $this]);
    }

    /**
     * Returns an array of informations used by search subsystem.
     * Function is defined in interface ISearchable
     *
     * @return Array
     */
    public function getSearchAttributes()
    {
        $attributes = array(
            'id' => $this->id,
            'name' => $this->name,
        );

        $this->trigger(self::EVENT_SEARCH_ADD, new \humhub\modules\search\events\SearchAddEvent($attributes));

        return $attributes;
    }

    public function createUrl($route = null, $params = array(), $scheme = false)
    {
        if ($route === null) {
            $route = '/tag';
        }

        array_unshift($params, $route);
        if (!isset($params['id'])) {
            $params['id'] = $this->id;
        }

        return \yii\helpers\Url::toRoute($params, $scheme);
    }
    

}
