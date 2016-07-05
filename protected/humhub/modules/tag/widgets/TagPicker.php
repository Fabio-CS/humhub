<?php

namespace humhub\modules\tag\widgets;

use Yii;
use yii\helpers\Html;
use \yii\helpers\Url;
use humhub\modules\tag\models\TagFilter;

/**
 * TagPickerWidget displays a tag picker instead of an input field.
 *
 * To use this widget, you may insert the following code in a view:
 * <pre>
 * $this->widget('application.modules_core.tag.widgets.TagPickerWidget',array(
 *     'name'=>'tags',
 * ));
 * </pre>
 *
 * By configuring the {@link options} property, you may specify the options
 * that need to be passed to the tagpicker plugin. Please refer to
 * the documentation for possible options (name-value pairs).
 *
 * @package humhub.modules_core.tag.widgets
 * @since 0.5
 * @author Fabio
 */
class TagPicker extends \yii\base\Widget
{

    /**
     * Id of input element which should replaced
     *
     * @var type
     */
    public $inputId = "";

    /**
     * JSON Search URL - defaults: search/json
     *
     * The token -keywordPlaceholder- will replaced by the current search query.
     *
     * @var String Url with -keywordPlaceholder-
     */
    public $tagSearchUrl = "";

    /**
     * Maximum tags
     *
     * @var type
     */
    public $maxTags = 10;

    /**
     * Set id for the current tag
     *
     * @var type string
     */
    public $tagId = "";

    /**
     * Set focus to input or not
     *
     * @var type boolean
     */
    public $focus = false;

    /**
     * @var CModel the data model associated with this widget.
     */
    public $model = null;

    /**
     * @var string the attribute associated with this widget.
     * The name can contain square brackets (e.g. 'name[1]') which is used to collect tabular data input.
     */
    public $attribute = null;

    /**
     * @var string for input placeholder attribute.
     */
    public $placeholderText = "";
    
    /**
     * Used to transfer additional data to the server
     * @var type 
     */
    public $data = null;

    /**
     * Inits the Tag Picker
     *
     */
    public function init()
    {
        // Default tag search for all tags
        if ($this->tagSearchUrl == "") {
            $this->tagSearchUrl = Url::toRoute(['/tag/search/json', 'keyword' => '-keywordPlaceholder-']);
        }
    }

    /**
     * Displays / Run the Widgets
     */
    public function run()
    {

        // Try to get current field value, when model & attribute attributes are specified.
        $currentValue = "";
        if ($this->model != null && $this->attribute != null) {
            $attribute = $this->attribute;
            $currentValue = $this->model->$attribute;
        }

        return $this->render('tagPicker', [
                    'tagSearchUrl' => $this->tagSearchUrl,
                    'maxTags' => $this->maxTags,
                    'currentValue' => $currentValue,
                    'inputId' => $this->inputId,
                    'focus' => $this->focus,
                    'tagId' => $this->tagId,
                    'data' => json_encode($this->data),
                    'placeholderText' => $this->placeholderText,
        ]);
    }
    
    /**
     * Creates a json tag array used in the tagpicker js frontend.
     * The $cfg is used to specify the filter values the following values are available:
     * 
     * query - (ActiveQuery) The initial query which is used to append additional filters. - default = Tag Friends if friendship module is enabled else Tag::find()
     * 
     * active - (boolean) Specifies if only active tag should be included in the result - default = true
     * 
     * maxResults - (int) The max number of entries returned in the array - default = 10
     * 
     * keyword - (string) A keyword which filters tag by tagname, firstname, lastname, email and title
     * 
     * permission - (BasePermission) An additional permission filter
     * 
     * fillQuery - (ActiveQuery) Can be used to fill the result array if the initial query does not return the maxResults, these results will have a lower priority
     * 
     * fillTag - (boolean) When set to true and no fillQuery is given the result is filled with Tag::find() results
     * 
     * disableFillTag - Specifies if the results of the fillQuery should be disabled in the tagpicker results - default = true
     * 
     * @param type $cfg filter configuration
     * @return type json representation used by the tagpicker
     */
    public static function filter($cfg = null)
    {
        $defaultCfg = [
            'maxResult' => 10,
            'keyword' => null,
        ];
        
        $cfg = ($cfg == null) ? $defaultCfg : array_merge($defaultCfg, $cfg);
        $cfg['query'] = TagFilter::find();
        //Filter the initial query and disable tag without the given permission
        $tag = TagFilter::filter($cfg['query'], $cfg['keyword'], $cfg['maxResult']);
        $jsonResult = self::asJSON($tag);
        
        if(self::isNewTag($jsonResult, $cfg['keyword'])){
            array_unshift($jsonResult, self::addNewTag($cfg['keyword']));
        }
        
        return $jsonResult;
    }
    
    /**
     * Assambles all tag Ids of the given $tags into an array
     * 
     * @param array $tags array of tag models
     * @return array tag id array
     */
    private static function getTagIdArray($tags)
    {
        $result = [];
        foreach($tags as $tag) {
            $result[] = $tag->id;
        }
        return $result;
    }
    
    /**
     * Creates an json result with tag information arrays. A tag will be marked
     * as disabled, if the permission check fails on this tag.
     * 
     * @param type $tags
     * @param type $permission
     * @return type
     */
    public static function asJSON($tags)
    {
        if (is_array($tags)) {
            $result = [];
            foreach ($tags as $tag) {
                if ($tag != null) {
                    $result[] = self::createJSONTagInfo($tag);
                }
            }
            return $result;
        } else {
            return self::createJsonTagInfo($tags);
        }
    }

    /**
     * Creates an single tag-information array for a given tag. 
     * 
     * @param type $tag
     * @return type
     */
    private static function createJSONTagInfo($tag)
    { 
        $tagInfo = [];
        $tagInfo['id'] = $tag->id;
        $tagInfo['name'] = $tag->name;
        return $tagInfo;
    }
    
    private static function isNewTag($jsonResult, $keyword)
    {
        foreach($jsonResult as $tag){
            if($tag['name'] === $keyword){
                return false;
            }
        }
        return true;
    }
    
    private static function addNewTag($keyword)
    {
        $tagInfo = [];
        $tagInfo['id'] = str_replace(" ", "_", strtoupper($keyword));
        $tagInfo['name'] = strtoupper($keyword);
        return $tagInfo;
    }
}

?>
