<?php

namespace XoopsModules\Article;

/**
 * Article module for XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         article
 * @since           1.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once dirname(__DIR__) . '/include/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

/**
 * Article object handler class.
 * @param CLASS_PREFIX variable prefix for the class name
 * @author    D.J. (phppp)
 * @copyright copyright &copy; 2000 XOOPS Project
 *
 * {@link \XoopsPersistableObjectHandler}
 *
 * @package   module::article
 *
 */
//art_parse_class('
class ArticleHandler extends \XoopsPersistableObjectHandler
{
    /**
     * Constructor
     *
     * @param \XoopsDatabase $db reference to the {@link XoopsDatabase}
     *                           object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('article', true), Article::class, 'art_id', 'art_title');
    }

    /**
     * Get the previous article and the next article of an articles
     *
     * @param object $article  reference to the article
     * @param array  $category article scope
     * @return array
     **/
    // To be optimized
    public function &getSibling(&$article, &$category)
    {
        $ret = [];

        $sql_cat = '';
        if ($category && is_array($category)) {
            $category = array_map('intval', $category);
            $sql_cat  = ' AND ac.cat_id IN (' . implode(',', $category) . ')';
        } else {
            $cat_id = is_object($category) ? $category->getVar('cat_id') : (int)$category;
            if ($cat_id > 0) {
                $sql_cat = ' AND ac.cat_id = ' . $cat_id;
            }
        }

        $sql    = '
            SELECT a.art_id as prev_id, a.art_title
                FROM ' . art_DB_prefix('article') . ' AS a
                LEFT JOIN ' . art_DB_prefix('artcat') . ' AS ac
                ON ac.art_id=a.art_id
                WHERE a.art_id < ' . $article->getVar('art_id') . '
                ' . $sql_cat . '
                ORDER BY a.art_id DESC LIMIT 1
        ';
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);
        if (!empty($myrow['prev_id'])) {
            $ret['previous'] = ['id' => $myrow['prev_id'], 'title' => $myrow['art_title']];
        }

        $sql    = '
            SELECT a.art_id as next_id, a.art_title
                FROM ' . art_DB_prefix('article') . ' AS a
                LEFT JOIN ' . art_DB_prefix('artcat') . ' AS ac
                ON ac.art_id=a.art_id
                WHERE a.art_id > ' . $article->getVar('art_id') . '
                ' . $sql_cat . '
                ORDER BY a.art_id ASC LIMIT 1
        ';
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);
        if (!empty($myrow['next_id'])) {
            $ret['next'] = ['id' => $myrow['next_id'], 'title' => $myrow['art_title']];
        }

        return $ret;
    }

    /**
     * count articles matching a condition of a category (categories)
     *
     * @param mixed  $category array or {@link Xcategory}
     * @param object $criteria {@link CriteriaElement} to match
     * @return int    count of articles
     */
    public function getCountByCategory(&$category, $criteria = null)
    {
        $sql = 'SELECT COUNT(DISTINCT a.art_id) as count FROM ' . art_DB_prefix('article') . ' AS a LEFT JOIN ' . art_DB_prefix('artcat') . ' AS ac ON a.art_id=ac.art_id WHERE 1=1 ';
        if ($category && is_array($category)) {
            $category = array_map('intval', $category);
            $sql      .= ' AND ac.cat_id IN (' . implode(',', $category) . ')';
        } else {
            $cat_id = is_object($category) ? $category->getVar('cat_id') : (int)$category;
            if ($cat_id > 0) {
                $sql .= ' AND ac.cat_id = ' . $cat_id;
            }
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
        }
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return (int)$myrow['count'];
    }

    /**
     * count articles matching a condition of a topic
     *
     * {@link Xtopic}
     *
     * @param mixed  $top_id   topic ID(s)
     * @param object $criteria {@link CriteriaElement} to match
     * @return int    count of articles
     */
    public function getCountByTopic($top_id, $criteria = null)
    {
        $topicHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Topic', $GLOBALS['artdirname']);

        return $topicHandler->getArticleCount($top_id, $criteria);
    }

    /**
     * get articles matching a condition of a topic
     *
     * @param int    $top_id   Topic ID
     * @param int    $limit    Max number of objects to fetch
     * @param int    $start    Which record to start at
     * @param object $criteria {@link CriteriaElement} to match
     * @param array  $tags     variables to fetch
     * @param bool   $asObject flag indicating as object, otherwise as array
     * @return array  of articles {@link Article}
     */
    public function &getByTopic($top_id, $limit = 0, $start = 0, $criteria = null, $tags = null, $asObject = true)
    {
        if ($tags && is_array($tags)) {
            if (!in_array('art_id', $tags) && !in_array('a.art_id', $tags)) {
                $tags[] = 'a.art_id';
            }
            $select = implode(',', $tags);
        } else {
            $select = '*';
        }
        $sql = "SELECT $select FROM " . art_DB_prefix('article') . ' AS a';
        $sql .= ' LEFT JOIN ' . art_DB_prefix('arttop') . ' AS at ON at.art_id=a.art_id';
        $sql .= ' WHERE a.art_time_submit > 0 AND a.art_time_publish > 0';
        if (is_array($top_id) && count($top_id) > 0) {
            $sql .= ' AND at.top_id IN (' . implode(',', $top_id) . ')';
        } elseif ((int)$top_id) {
            $sql .= ' AND at.top_id = ' . (int)$top_id;
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
            if ('' != $criteria->getSort()) {
                $sql      .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                $orderSet = true;
            }
        }
        if (empty($orderSet)) {
            $sql .= ' ORDER BY at.at_time DESC';
        }

        $result = $this->db->query($sql, (int)$limit, (int)$start);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $article = $this->create(false);
            $article->assignVars($myrow);
            if ($asObject) {
                $ret[$myrow['art_id']] = $article;
            } else {
                $ret[$myrow[$this->keyName]] = $article->getValues(array_keys($myrow));
            }
            unset($article);
        }

        return $ret;
    }

    /**
     * get articles matching a condition of a category (categories)
     *
     * @param mixed  $category Category ID or object
     * @param int    $limit    Max number of objects to fetch
     * @param int    $start    Which record to start at
     * @param object $criteria {@link CriteriaElement} to match
     * @param array  $tags     variables to fetch
     * @param bool   $asObject flag indicating as object, otherwise as array
     * @return array  of articles {@link Article}
     */
    public function &getByCategory($category, $limit = 0, $start = 0, $criteria = null, $tags = null, $asObject = true)
    {
        if ($tags && is_array($tags)) {
            $key_artid = array_search('art_id', $tags);
            if (is_numeric($key_artid)) {
                unset($tags[$key_artid]);
            }
            $key_artid = array_search('a.art_id', $tags);
            if (is_numeric($key_artid)) {
                unset($tags[$key_artid]);
            }
            if ('distinct a.art_id' !== mb_strtolower($tags[0])) {
                array_unshift($tags, 'DISTINCT a.art_id');
            }
            $select = implode(',', $tags);
        } else {
            $select = '*';
        }
        $sql = "SELECT {$select} FROM " . art_DB_prefix('article') . ' AS a';
        $sql .= ' LEFT JOIN ' . art_DB_prefix('artcat') . ' AS ac ON ac.art_id=a.art_id';
        $sql .= ' WHERE a.art_time_submit > 0';
        $sql .= ' AND (a.cat_id = ac.cat_id OR a.art_time_publish > 0)';
        if ($category && is_array($category)) {
            $category = array_map('intval', $category);
            $sql      .= ' AND ac.cat_id IN (' . implode(',', $category) . ')';
        } else {
            $cat_id = is_object($category) ? $category->getVar('cat_id') : (int)$category;
            if ($cat_id > 0) {
                $sql .= ' AND ac.cat_id = ' . $cat_id;
            }
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
            if ('' != $criteria->getSort()) {
                $sql      .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                $orderSet = true;
            }
        }
        if (empty($orderSet)) {
            $sql .= ' ORDER BY a.art_id DESC';
        }
        $result = $this->db->query($sql, (int)$limit, (int)$start);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $article = $this->create(false);
            $article->assignVars($myrow);
            if ($asObject) {
                $ret[$myrow['art_id']] = $article;
            } else {
                $ret[$myrow[$this->keyName]] = $article->getValues(array_keys($myrow));
            }
            unset($article);
        }

        return $ret;
    }

    /**
     * get IDs of articles matching a condition of a category (categories)
     *
     * @param mixed  $category Category ID or object
     * @param int    $limit    Max number of objects to fetch
     * @param int    $start    Which record to start at
     * @param object $criteria {@link CriteriaElement} to match
     * @return array  of article IDs
     */
    public function &getIdsByCategory($category, $limit = 0, $start = 0, $criteria = null)
    {
        $sql = 'SELECT DISTINCT a.art_id FROM ' . art_DB_prefix('article') . ' AS a LEFT JOIN ' . art_DB_prefix('artcat') . ' AS ac ON ac.art_id=a.art_id';
        $sql .= ' WHERE 1=1';
        if ($category && is_array($category)) {
            $category = array_filter(array_map('intval', $category));
            if (count($category) > 0) {
                $sql .= ' AND ac.cat_id IN (' . implode(',', $category) . ')';
            }
        } else {
            $cat_id = is_object($category) ? $category->getVar('cat_id') : (int)$category;
            if ($cat_id > 0) {
                $sql .= ' AND ac.cat_id = ' . $cat_id;
            }
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
            if ('' != $criteria->getSort()) {
                $sql      .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                $orderSet = true;
            }
        }
        if (empty($orderSet)) {
            $sql .= ' ORDER BY ac.ac_publish DESC';
        }
        $result = $this->db->query($sql, $limit, $start);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = $myrow['art_id'];
        }
        $ret = array_unique($ret);

        return $ret;
    }

    /**
     * Update category ids of the article
     *
     * {@link CriteriaCompo}
     * {@link Criteria}
     *
     * @param object $article {@link Article}
     * @return bool
     */
    public function updateCategories(&$article)
    {
        $criteria = new \CriteriaCompo(new \Criteria('ac_publish', 0, '>'));
        $ids_new  = $this->getCategoryIds($article, $criteria);
        $ids_curr = $article->getVar('art_categories');
        if (strcmp(serialize($ids_new), serialize($ids_curr))) {
            $article->setVar('art_categories', $ids_new, true);
            $this->insert($article);
        }

        return true;
    }

    /**
     * Update topic ids of the article
     *
     * @param object $article {@link Article}
     * @return bool
     */
    public function updateTopics(&$article)
    {
        $ids_new  = $this->getTopicIds($article);
        $ids_curr = $article->getVar('art_topics');
        if (strcmp(serialize($ids_new), serialize($ids_curr))) {
            $article->setVar('art_topics', $ids_new, true);
            $this->insert($article);
        }

        return true;
    }

    /**
     * get IDs of categories matching a condition of the article
     *
     * @param object $article  {@link Article} reference to Article
     * @param object $criteria {@link CriteriaElement} to match
     * @return array  array of category IDs
     */
    public function getCategoryIds($article, $criteria = null)
    {
        $ret    = [];
        $art_id = is_object($article) ? $article->getVar('art_id') : (int)$article;
        if (0 == $art_id) {
            return $ret;
        }

        $sql = 'SELECT cat_id FROM ' . art_DB_prefix('artcat') . ' WHERE art_id=' . $art_id;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
        }
        $result = $this->db->query($sql);
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = $myrow['cat_id'];
        }

        return $ret;
    }

    /**
     * get status of categories of the article
     *
     * @param object $article {@link Article} reference to Article
     * @return array  associative array of category IDs and status
     */
    public function &getCategoryArray(&$article)
    {
        $_cachedCats = [];
        $art_id      = is_object($article) ? $article->getVar('art_id') : (int)$article;
        $sql         = 'SELECT cat_id, ac_publish AS approved FROM ' . art_DB_prefix('artcat') . ' WHERE art_id =' . $art_id;
        if (!$result = $this->db->query($sql)) {
            return $_cachedCats;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $_cachedCats[$myrow['cat_id']] = $myrow['approved'];
        }

        return $_cachedCats;
    }

    /**
     * get IDs of active topics of the article
     *
     * @param object $article {@link Article} reference to Article
     * @param null   $criteria
     * @return array  array of topic IDs
     */
    public function &getTopicIds(&$article, $criteria = null)
    {
        $ret          = [];
        $art_id       = is_object($article) ? $article->getVar('art_id') : (int)$article;
        $topicHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Topic', $GLOBALS['artdirname']);
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
        } else {
            $criteria = new \CriteriaCompo(new \Criteria('t.top_expire', time(), '>'));
        }
        if (false !== ($topics_obj = $topicHandler->getByArticle($art_id, $criteria))) {
            $ret = array_keys($topics_obj);
        }

        return $ret;
    }

    /**
     * insert a new article into the database
     *
     * @param \XoopsObject $article {@link Article}     reference to Article
     * @param bool         $force   flag to force the query execution despite security settings
     * @return int    article ID
     */
    public function insert(\XoopsObject $article, $force = true)
    {
        if (!$art_id = parent::insert($article, $force)) {
            return false;
        }

        // placeholder for user stats - to be implemented
        if (!empty($article->vars['art_time_publish']['changed']) && $article->getVar('uid')) {
            /*
            $memberHandler = xoops_getHandler("member");
            $user = $memberHandler->getUser($article->getVar("uid"));
            if (is_object($user) && $user->isActive()) {
                $posts = $article->getVar("art_time_publish") ? $user->getVar("posts") + 1 : $user->getVar("posts") - 1;
             $memberHandler->updateUserByField($user, "posts", $posts);
            }
            */
        }

        if (!empty($article->vars['art_keywords']['changed'])) {
            $this->updateKeywords($article);
        }

        return $art_id;
    }

    /**
     * Update keyword-article links of the article
     *
     * @param object $article {@link Article} reference to Article
     * @return bool   true on success
     */
    public function updateKeywords(&$article)
    {
        if (!@require_once XOOPS_ROOT_PATH . '/modules/tag/include/functions.php') {
            return false;
        }
        if (!$tagHandler = tag_getTagHandler()) {
            return false;
        }
        $tagHandler->updateByItem($article->getVar('art_keywords', 'n'), $article->getVar('art_id'), $GLOBALS['artdirname']);

        return true;
    }

    /**
     * Delete keyword-article links of the article from database
     *
     * @param object $article {@link Article} reference to Article
     * @return bool   true on success
     */
    public function deleteKeywords(&$article)
    {
        if (!@require_once XOOPS_ROOT_PATH . '/modules/tag/include/functions.php') {
            return false;
        }
        if (!$tagHandler = tag_getTagHandler()) {
            return false;
        }
        $tagHandler->updateByItem([], $article->getVar('art_id'), $GLOBALS['artdirname']);

        return true;
    }

    /**
     * delete an article from the database
     *
     * {@link Text}
     *
     * @param \XoopsObject $article {@link Article}
     *                              reference to Article
     * @param bool         $force   flag to force the query execution despite security settings
     * @return bool   true on success
     */
    public function delete(\XoopsObject $article, $force = true)
    {
        if (empty($force) && xoops_comment_count($GLOBALS['xoopsModule']->getVar('mid'), $article->getVar('art_id'))) {
            return false;
        }

        // placeholder for files
        /*
        $fileHandler = \XoopsModules\Article\Helper::getInstance()->getHandler("File", $GLOBALS["artdirname"]);
        $fileHandler->deleteAll(new \Criteria("art_id", $article->getVar("art_id")));
        */

        $textHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Text', $GLOBALS['artdirname']);
        $textHandler->deleteAll(new \Criteria('art_id', $article->getVar('art_id')));

        $rateHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Rate', $GLOBALS['artdirname']);
        $rateHandler->deleteAll(new \Criteria('art_id', $article->getVar('art_id')));

        $this->terminateCategory($article, $article->getCategories(), false);
        $this->terminateTopic($article);

        $this->deleteKeywords($article);

        xoops_comment_delete($GLOBALS['xoopsModule']->getVar('mid'), $article->getVar('art_id'));
        xoops_notification_deletebyitem($GLOBALS['xoopsModule']->getVar('mid'), 'article', $article->getVar('art_id'));

        parent::delete($article, $force);

        $article = null;
        unset($article);

        return true;
    }

    /**
     * Set article-category status of the article
     *
     * {@link Xcategory}
     *
     * @param       $article
     * @param mixed $cat_id category ID
     * @param int   $status status value
     * @param int   $time   register, publish or feature time; 0 for time()
     * @return bool  true on success
     */
    public function setCategoryStatus(&$article, $cat_id, $status = 1, $time = 0)
    {
        if (!is_object($article)) {
            $art_id      = (int)$article;
            $article_obj = $this->get($article);
        } else {
            $article_obj = $article;
            $art_id      = $article->getVar('art_id');
        }
        if (empty($art_id)) {
            return false;
        }

        $where = ' WHERE ac.art_id = ' . $art_id;
        $table = art_DB_prefix('artcat') . ' AS ac';
        switch ($status) {
            case 2:
                $time = empty($time) ? time() : $time;
                // Multi-table update only supported by MySQL 4.0.4+
                // Version check and update by scottlai
                if (version_compare($this->mysqli_server_version(), '4.0.4', '<')) {
                    if ($article_obj->getVar('art_time_publish') > 0) {
                        $value = "ac_feature = {$time}, ac_publish = if ( ac_publish = 0, {$time}, ac_publish )";
                    } else {
                        $value = 'ac_feature = 0';
                    }
                } else {
                    $table .= ', ' . art_DB_prefix('article') . ' AS a';
                    $value = "ac.ac_feature = {$time}, ac.ac_publish = if ( ac.ac_publish = 0, {$time}, ac.ac_publish )";
                    $where .= ' AND a.art_id = ac.art_id AND a.art_time_publish > 0';
                }
                break;
            case 0:
                $value = 'ac.ac_feature = 0, ac.ac_publish = 0';
                break;
            default:
            case 1:
                $time_feature = empty($time) ? 0 : $time;
                $time_publish = empty($time) ? time() : $time;
                // Multi-table update only supported by MySQL 4.0.4+
                // Version check and update by scottlai
                if (version_compare($this->mysqli_server_version(), '4.0.4', '<')) {
                    $art_time_publish = $article_obj->getVar('art_time_publish');
                    $a_cat_id         = $article_obj->getVar('cat_id');
                    $value            .= " ac.ac_publish = if ( ac.ac_publish = 0, if ( {$a_cat_id} = ac.cat_id, {$time_publish}, if ( {$art_time_publish} > 0, {$time_publish}, 0 ) ), {$time_publish}),";
                    $value            .= " ac.ac_feature = if ( ac.ac_feature = 0, 0, {$time_feature} )";

                    /* Update art_time_publish manually instead of using multi-table update */
                    if (!$art_time_publish) {
                        $article_obj->setVar('art_time_publish', $time_publish);
                        $this->insert($article_obj, false);
                    }
                } else {
                    $table .= ', ' . art_DB_prefix('article') . ' AS a';
                    $value = "a.art_time_publish = if ( a.art_time_publish = 0, if ( a.cat_id = ac.cat_id, {$time_publish}, 0 ), a.art_time_publish ), ";
                    $value .= " ac.ac_publish = if ( ac.ac_publish = 0, if ( a.cat_id = ac.cat_id, {$time_publish}, if ( a.art_time_publish > 0, {$time_publish}, 0 ) ), {$time_publish} ),";
                    $value .= " ac.ac_feature = if ( ac.ac_feature = 0, 0, {$time_feature} )";
                    $where .= ' AND a.art_id = ac.art_id';
                }
                break;
        }
        if (!empty($cat_id)) {
            $cat_id = is_array($cat_id) ? $cat_id : [$cat_id];
            $where  .= ' AND ac.cat_id IN (' . implode(',', array_map('intval', $cat_id)) . ')';
        }

        $sql = 'UPDATE ' . $table . ' SET ' . $value . $where;

        /* filter all table short-name when mysql version below 4.0.4 */
        if (version_compare($this->mysqli_server_version(), '4.0.4', '<')) {
            $pattern = ['AS ac', 'AS a', 'ac.', 'a.'];
            $sql     = str_replace($pattern, [], $sql);
        }

        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("update article-category error:" . $sql);
            return false;
        }

        $categoryHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Category', $GLOBALS['artdirname']);
        $categoryHandler->setLastArticleIds($cat_id);

        return true;
    }

    /**
     * get article-category status of a list articles
     *
     * @param int   $cat_id category ID
     * @param mixed $art_id article ID
     * @return array associative array of article IDs and status
     */
    public function getCategoryStatus($cat_id, $art_id = null)
    {
        if (empty($cat_id)) {
            return null;
        }
        if (!is_array($art_id)) {
            $art_id   = [$art_id];
            $isSingle = true;
        }
        if (0 == count($art_id)) {
            return null;
        }
        $art_id = array_map('intval', $art_id);

        $sql = 'SELECT DISTINCT art_id, ac_register AS register, ac_publish AS publish, ac_feature AS feature FROM ' . art_DB_prefix('artcat') . ' WHERE art_id IN (' . implode(',', $art_id) . ')';
        if (!$result = $this->db->query($sql)) {
            //xoops_error("query article-category error: " . $sql);
            return null;
        }
        $ret = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $status = $myrow['feature'] ? 2 : ($myrow['publish'] ? 1 : ($myrow['register'] ? 0 : null));
            if (!empty($isSingle)) {
                return $status;
            }
            $ret[$myrow['art_id']] = $status;
        }

        return $ret;
    }

    /**
     * Set article-category status as published (value = 1)
     *
     * @param $article
     * @param $cats
     * @return bool  true on success
     */
    public function publishCategory(&$article, $cats)
    {
        if (!$ret = $this->setCategoryStatus($article, $cats, 1)) {
            return false;
        }

        if (!empty($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
            $categoryHandler     = \XoopsModules\Article\Helper::getInstance()->getHandler('Category', $GLOBALS['artdirname']);
            $notificationHandler = xoops_getHandler('notification');
            if (!is_object($article)) {
                $article_obj = $this->get($article);
            } else {
                $article_obj = $article;
            }
            $cats                   = is_array($cats) ? $cats : [$cats];
            $tags                   = [];
            $tags['ARTICLE_ACTION'] = art_constant('MD_NOT_ACTION_PUBLISHED');
            $tags['ARTICLE_TITLE']  = $article_obj->getVar('art_title');
            foreach ($cats as $id) {
                $tags['ARTICLE_URL']    = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article_obj->getVar('art_id') . '/c' . $id;
                $category_obj           = $categoryHandler->get($id);
                $tags['CATEGORY_TITLE'] = $category_obj->getVar('cat_title');
                $notificationHandler->triggerEvent('global', 0, 'article_new', $tags);
                $notificationHandler->triggerEvent('global', 0, 'article_monitor', $tags);
                $notificationHandler->triggerEvent('category', $id, 'article_new', $tags);
                $notificationHandler->triggerEvent('article', $article_obj->getVar('art_id'), 'article_approve', $tags);
                unset($category_obj);
            }
        }

        return true;
    }

    /**
     * Set article-category status as dismissed(pending) (value = 0)
     *
     * @param $article
     * @param $cats
     * @return bool  true on success
     */
    public function unPublishCategory(&$article, $cats)
    {
        return $this->setCategoryStatus($article, $cats, 0);
    }

    /**
     * Set article-category status as featured (value = 2)
     *
     * @param $article
     * @param $cats
     * @return bool  true on success
     */
    public function featureCategory(&$article, $cats)
    {
        return $this->setCategoryStatus($article, $cats, 2);
    }

    /**
     * Set article-category status as normal (value = 1)
     *
     * @param $article
     * @param $cats
     * @return bool  true on success
     */
    public function unFeatureCategory(&$article, $cats)
    {
        return $this->setCategoryStatus($article, $cats, 1);
    }

    /**
     * register an article to categories
     *
     * TODO: refactoring with publishCategory
     *
     * {@link Xcategory}
     *
     * @param object $article {@link Article} reference to Article
     * @param array  $cats    array of category IDs
     * @return bool   true on success
     */
    public function registerCategory(&$article, $cats)
    {
        $art_id = is_object($article) ? $article->getVar('art_id') : (int)$art_id;
        if (empty($art_id) || empty($cats)) {
            return false;
        }
        $cats_pub = [];
        $cat_str  = [];
        foreach ($cats as $id => $cat) {
            $status = @(int)$cat['status'];
            if ($status > 2) {
                $status = 2;
            }
            if ($status < 0) {
                $status = 0;
            }
            if ($status > 0) {
                $cats_pub[] = $id;
            }
            switch ($status) {
                // Publish and feature
                case 2:
                    $value = time() . ',' . time() . ',' . time();
                    break;
                // Register
                case 0:
                    $value = time() . ', 0, 0';
                    break;
                // Publish
                default:
                case 1:
                    $value = time() . ',' . time() . ', 0';
                    break;
            }

            $cat_str[] = '(' . $art_id . ', ' . (int)$id . ', ' . $value . ', ' . (int)$cat['uid'] . ')';
        }
        $values = implode(',', $cat_str);

        $sql = 'INSERT INTO ' . art_DB_prefix('artcat') . ' (art_id, cat_id, ac_register, ac_publish, ac_feature, uid) VALUES ' . $values;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("Insert article-category error:" . $sql);
            return false;
        }

        $categoryHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Category', $GLOBALS['artdirname']);
        if (!empty($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
            $notificationHandler = xoops_getHandler('notification');
            if (!is_object($article)) {
                $article_obj = $this->get($article);
            } else {
                $article_obj = $article;
            }
            $tags                  = [];
            $tags['ARTICLE_TITLE'] = $article_obj->getVar('art_title');
        }
        if (count($cats_pub) > 0) {
            $categoryHandler->setLastArticleIds($cats_pub);
            if (!empty($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
                $cats = $cats_pub;
                foreach ($cats as $id) {
                    $tags['ARTICLE_URL']    = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $article_obj->getVar('art_id') . '/c' . $id;
                    $category_obj           = $categoryHandler->get($id);
                    $tags['CATEGORY_TITLE'] = $category_obj->getVar('cat_title');
                    $notificationHandler->triggerEvent('global', 0, 'article_new', $tags);
                    $notificationHandler->triggerEvent('global', 0, 'article_monitor', $tags);
                    $notificationHandler->triggerEvent('category', $id, 'article_new', $tags);
                    $notificationHandler->triggerEvent('article', $article_obj->getVar('art_id'), 'article_approve', $tags);
                    unset($category_obj);
                }
            }
        }
        $cats_reg = array_diff(array_keys($cats), $cats_pub);
        if (count($cats_reg) > 0) {
            if (!empty($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
                $cats = $cats_reg;
                foreach ($cats as $id) {
                    $tags['ARTICLE_URL']    = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/edit.article.php?article=' . $article_obj->getVar('art_id') . '&category=' . $id;
                    $category_obj           = $categoryHandler->get($id);
                    $tags['CATEGORY_TITLE'] = $category_obj->getVar('cat_title');
                    $notificationHandler->triggerEvent('global', 0, 'article_submit', $tags);
                    $notificationHandler->triggerEvent('category', $id, 'article_submit', $tags);
                    unset($category_obj);
                }
            }
        }

        return true;
    }

    /**
     * terminate an article from categories
     *
     * {@link Xcategory}
     *
     * @param mixed $article array or {@link Article} reference to Article
     * @param mixed $cat_id  array of category IDs
     * @param bool  $check_basic
     * @return bool  true on success
     */
    public function terminateCategory(&$article, $cat_id = null, $check_basic = true)
    {
        if (empty($cat_id)) {
            return false;
        }
        if (!is_array($cat_id)) {
            $cat_id = [$cat_id];
        }
        if (is_object($article)) {
            $art_obj = $article;
            $art_id  = $article->getVar('art_id');
        } else {
            $art_id  = (int)$article;
            $art_obj = $this->get($art_id);
            if (!is_object($art_obj) || !$art_id = $art_obj->getVar('art_id')) {
                return false;
            }
        }
        if (empty($art_id)) {
            //xoops_error("empty art_id");
            return false;
        }
        $cat_id = array_map('intval', $cat_id);

        $remove_all = false;
        if ($check_basic):
            // The basic category is to remove
            if (in_array($art_obj->getVar('cat_id'), $cat_id)) {
                $remove_all = true;
            } else {
                $cats = $art_obj->getCategories();
                // Or all categories are to remove
                if (array_intersect($cat_id, $cats) == $cats) {
                    $remove_all = true;
                }
            }
        endif;

        $where = ' WHERE art_id = ' . $art_id;
        if (empty($remove_all)) {
            $where .= ' AND cat_id IN (' . implode(',', $cat_id) . ')';
        }

        // remove article-category links
        $sql = 'DELETE FROM ' . art_DB_prefix('artcat') . $where;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("terminate article-category error:" . $sql);
            return false;
        }
        // update last-articles for relevant categories
        $categoryHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Category', $GLOBALS['artdirname']);
        $categoryHandler->setLastArticleIds($cat_id);

        if (!empty($remove_all)) {
            $this->delete($art_obj, true);
        }

        return true;
    }

    /**
     * move an article=
     *
     * {@link Xcategory}
     *
     * @param object $article  {@link Article} reference to Article
     * @param int    $cat_to   destination category
     * @param int    $cat_from source category
     * @return bool   true on success
     */
    public function moveCategory(&$article, $cat_to, $cat_from)
    {
        if (in_array($cat_to, $article->getCategories())) {
            return true;
        }

        $sql = 'UPDATE ' . art_DB_prefix('artcat') . ' SET cat_id = ' . (int)$cat_to . ' WHERE art_id= ' . $article->getVar('art_id') . ' AND cat_id=' . (int)$cat_from;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("moveCategory error:" . $sql);
            return false;
        }

        return true;
    }

    /**
     * register an article to topics
     *
     * @param object $article {@link Article} reference to Article
     * @param mixed  $top_id  array of topic IDs
     * @return bool   true on success
     */
    public function registerTopic(&$article, $top_id)
    {
        if (is_array($top_id)) {
            if (count($top_id) > 0) {
                $top_str = [];
                $top_id  = array_map('intval', $top_id);
                foreach ($top_id as $top) {
                    $top_str[] = '(' . $article->getVar('art_id') . ', ' . $article->getVar('uid') . ", {$top}," . time() . ')';
                }
                $values       = implode(',', $top_str);
                $top_id_value = ' top_id IN (' . implode(',', $top_id) . ')';
            } else {
                return false;
            }
        } else {
            $values       = '(' . $article->getVar('art_id') . ', ' . $article->getVar('uid') . ', ' . (int)$top_id . ',' . time() . ')';
            $top_id_value = ' top_id =' . (int)$top_id;
        }

        $sql = 'INSERT INTO ' . art_DB_prefix('arttop') . ' (art_id, uid, top_id, at_time) VALUES ' . $values;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("Insert article-topic error:" . $sql);
            return false;
        }

        $sql = 'UPDATE ' . art_DB_prefix('topic') . ' SET top_time=' . time() . ' WHERE ' . $top_id_value;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("Update topic time error:" . $sql);
            return false;
        }

        return true;
    }

    /**
     * terminate an article from topics
     *
     * @param mixed $article array or {@link Article} reference to Article
     * @param mixed $top_id  array of topic IDs
     * @return bool  true on success
     */
    public function terminateTopic(&$article, $top_id = null)
    {
        if (is_object($article)) {
            $art_id = $article->getVar('art_id');
        } else {
            $art_id = (int)$article;
        }
        if (empty($art_id)) {
            return false;
        }
        $where = ' WHERE art_id =' . $art_id;

        if (!is_array($top_id) && !empty($top_id)) {
            $top_id = [$top_id];
        }
        if (count($top_id) > 0) {
            $top_id = array_map('intval', $top_id);
            $where  .= ' AND top_id IN (' . implode(',', $top_id) . ')';
        }

        $sql = 'DELETE FROM ' . art_DB_prefix('arttop') . $where;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("terminate article-topic error:" . $sql);
            return false;
        }

        return true;
    }

    /**
     * retrieve global article stats of the module
     *
     * @return array array of authors, article view count, article rates
     */
    public function &getStats()
    {
        $sql    = 'SELECT COUNT(DISTINCT uid) AS authors, SUM(art_counter) AS views, SUM(art_rates) AS rates FROM ' . art_DB_prefix('article');
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return $myrow;
    }

    /**
     * insert a trackback item of the article into database
     *
     * @param object $article   {@link Article} reference to Article
     * @param array  $trackback associative array of time, url
     * @return bool   true on success
     */
    public function addTracked(&$article, $trackback)
    {
        $sql = 'INSERT INTO ' . art_DB_prefix('tracked') . ' (art_id, td_time, td_url) VALUES (' . $article->getVar('art_id') . ', ' . $trackback['time'] . ', ' . $this->db->quoteString($trackback['url']) . ')';
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("Add tracked error:" . $sql);
            return false;
        }

        return true;
    }

    /**
     * update tracked trackbacks of the article
     *
     * @param object $article {@link Article} reference to Article
     * @param mixed  $td_id   trackback id
     * @return bool   true on success
     */
    public function updateTracked(&$article, $td_id)
    {
        if ($tb_id && is_array($tb_id)) {
            $id = 'td_id IN (' . implode(',', $td_id) . ')';
        } else {
            $id = 'td_id = ' . $td_id;
        }
        $sql = 'UPDATE ' . art_DB_prefix('tracked') . ' SET td_time=' . time() . ' WHERE art_id = ' . $article->getVar('art_id') . ' AND ' . $id;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("update tracked error:" . $sql);
            return false;
        }

        return true;
    }

    /**
     * retrieve trackbacks of the article from database
     *
     * @param object $article {@link Article} reference to Article
     * @param string $type    type of trackbacks
     * @return array  associative array of trackback ID and values
     */
    public function getTracked(&$article, $type = 'all')
    {
        $sql = 'SELECT * FROM ' . art_DB_prefix('tracked') . ' WHERE art_id = ' . $article->getVar('art_id');
        if ('tracked' === $type) {
            $sql .= 'AND td_time > 0';
        }
        if ('untracked' === $type) {
            $sql .= 'AND td_time = 0';
        }
        $result = $this->db->query($sql);
        $res    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $res[$myrow['td_id']] = $myrow;
        }

        return $res;
    }

    /**
     * clean expired articles from database
     *
     * @param int $expire time limit for expiration
     * @return bool true on success
     */
    public function cleanExpires($expire)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE art_time_create < ' . (time() - (int)$expire) . ' AND art_time_submit =0';
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("cleanExpires error:". $sql);
            return false;
        }

        return true;
    }

    /**
     * clean orphan articles from database
     *
     * @param null $table_link
     * @param null $field_link
     * @param null $field_object
     * @return bool true on success
     */
    public function cleanOrphan($table_link = null, $field_link = null, $field_object = null)
    {
        /* for MySQL 4.1+ */
        if (version_compare(mysqli_get_server_info(), '4.1.0', 'ge')):
            $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . ' (' . $this->table . '.cat_id > 0 AND cat_id NOT IN ( SELECT DISTINCT cat_id FROM ' . art_DB_prefix('category') . ') )';
        else:
            $sql = 'DELETE ' . $this->table . ' FROM ' . $this->table . ' LEFT JOIN ' . art_DB_prefix('category') . ' AS aa ON ' . $this->table . '.cat_id = aa.cat_id ' . ' WHERE ' . ' (' . $this->table . '.cat_id>0 AND aa.cat_id IS NULL)';
        endif;

        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("cleanOrphan error: ". $sql);
        }

        /* for MySQL 4.1+ */
        if (version_compare(mysqli_get_server_info(), '4.1.0', 'ge')):
            $sql = 'DELETE FROM ' . art_DB_prefix('artcat') . ' WHERE ' . ' (' . art_DB_prefix('artcat') . '.art_id = 0 OR art_id NOT IN ( SELECT DISTINCT art_id FROM ' . $this->table . ') )';
        else:
            $sql = 'DELETE ' . art_DB_prefix('artcat') . ' FROM ' . art_DB_prefix('artcat') . ' LEFT JOIN ' . $this->table . ' AS aa ON ' . art_DB_prefix('artcat') . '.art_id = aa.art_id ' . ' WHERE ' . ' (' . art_DB_prefix('artcat') . '.art_id = 0 OR aa.art_id IS NULL)';
        endif;

        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("cleanOrphan error: ". $sql);
        }

        /* for MySQL 4.1+ */
        if (version_compare(mysqli_get_server_info(), '4.1.0', 'ge')):
            $sql = 'DELETE FROM ' . art_DB_prefix('arttop') . ' WHERE ' . ' (' . art_DB_prefix('arttop') . '.art_id = 0 OR art_id NOT IN ( SELECT DISTINCT art_id FROM ' . $this->table . ') )';
        else:
            $sql = 'DELETE ' . art_DB_prefix('arttop') . ' FROM ' . art_DB_prefix('arttop') . ' LEFT JOIN ' . $this->table . ' AS aa ON ' . art_DB_prefix('arttop') . '.art_id = aa.art_id ' . ' WHERE ' . ' (' . art_DB_prefix('arttop') . '.art_id = 0 OR aa.art_id IS NULL)';
        endif;

        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("cleanOrphan error:". $sql);
        }

        /* for MySQL 4.1+ */
        if (version_compare(mysqli_get_server_info(), '4.1.0', 'ge')):
            $sql = 'DELETE FROM ' . art_DB_prefix('tracked') . ' WHERE ' . ' (' . art_DB_prefix('tracked') . '.art_id = 0 OR art_id NOT IN ( SELECT DISTINCT art_id FROM ' . $this->table . ') )';
        else:
            $sql = 'DELETE ' . art_DB_prefix('tracked') . ' FROM ' . art_DB_prefix('tracked') . ' LEFT JOIN ' . $this->table . ' AS aa ON ' . art_DB_prefix('tracked') . '.art_id = aa.art_id ' . ' WHERE ' . ' (' . art_DB_prefix('tracked') . '.art_id = 0 OR aa.art_id IS NULL)';
        endif;

        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("cleanOrphan error:". $sql);
        }

        return true;
    }
}
//');
