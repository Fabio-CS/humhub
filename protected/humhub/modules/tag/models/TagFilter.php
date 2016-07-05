<?php

/**
 */

namespace humhub\modules\tag\models;

/**
 * Special tag model class for the purpose of searching tags.
 *
 * @author Fabio Miranda
 */
class TagFilter extends Tag
{   
    /**
     * Searches for all active tags by the given keyword.
     * 
     * @param type $keywords
     * @param type $maxResults
     * @return type
     */
    public static function getTagByFilter($keywords = null, $maxResults = null)
    {
        return self::filter(Tag::find(), $keywords, $maxResults);
    }
    
    /**
     * Returns an array of user models filtered by a $keyword and $permission. These filters
     * are added to the provided $query. The $keyword filter can be used to filter the users
     * by email, username, firstname, lastname and title. By default this functions does not
     * consider inactive user.
     * 
     * @param type $query
     * @param type $keywords
     * @param type $maxResults
     * @return type
     */
    public static function filter($query, $keywords = null, $maxResults = null)
    {
        return $tag = self::addQueryFilter($query, $keywords, $maxResults)->all();
    }

    public static function addQueryFilter($query, $keywords = null, $maxResults = null)
    {
        
        if ($maxResults != null) {
            $query->limit($maxResults);
        }
        return $query;
    }

}
