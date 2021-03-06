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
 * Topic object handler class.
 * @param CLASS_PREFIX variable prefix for the class name
 * @author    D.J. (phppp)
 * @copyright copyright &copy; 2005 XOOPS Project
 *
 * {@link \XoopsPersistableObjectHandler}
 *
 * @package   module::article
 *
 */
//art_parse_class('
class TopicHandler extends \XoopsPersistableObjectHandler
{
    /**
     * Constructor
     *
     * @param \XoopsDatabase $db reference to the {@link XoopsDatabase}
     *                           object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('topic', true), Topic::class, 'top_id', 'top_title');
    }

    /**
     * get a list of topics including a specified article and matching a condition
     *
     * {@link Permission}
     *
     * @param int    $art_id   article ID
     * @param object $criteria {@link CriteriaElement} to match
     * @return array  of topics {@link Xtopoic}
     */
    public function &getByArticle($art_id, $criteria = null)
    {
        $_cachedTop = [];
        $ret        = null;
        if (empty($art_id)) {
            return $ret;
        }

        $sql = 'SELECT t.top_id, t.top_title FROM ' . art_DB_prefix('topic') . ' AS t';
        $sql .= ' LEFT JOIN ' . art_DB_prefix('arttop') . ' AS at ON at.top_id=t.top_id';
        $sql .= ' WHERE at.art_id =' . (int)$art_id;
        mod_loadFunctions('user', $GLOBALS['artdirname']);
        if (!art_isAdministrator()) {
            $permissionHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Permission', $GLOBALS['artdirname']);
            $allowed_cats      = &$permissionHandler->getCategories();
            if (0 == count($allowed_cats)) {
                return null;
            }
            $sql .= ' AND t.cat_id IN (' . implode(',', $allowed_cats) . ')';
        }
        $limit = $start = null;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
            if ('' != $criteria->getSort()) {
                $sql      .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                $orderSet = true;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (empty($orderSet)) {
            $sql .= ' ORDER BY t.cat_id, t.top_order, t.top_time DESC';
        }
        if (!$result = $this->db->query($sql, $limit, $start)) {
            return $ret;
        }
        while (false !== ($row = $this->db->fetchArray($result))) {
            $topic = $this->create(false);
            $topic->assignVars($row);
            $_cachedTop[$topic->getVar('top_id')] = $topic;
            unset($topic);
        }

        return $_cachedTop;
    }

    /**
     * get a list of topics matching a condition of a category
     *
     * @param mixed  $cat_id   category ID(s)
     * @param int    $limit    Max number of objects to fetch
     * @param int    $start    Which record to start at
     * @param object $criteria {@link CriteriaElement} to match
     * @param array  $tags     variables to fetch
     * @param bool   $asObject flag indicating as object, otherwise as array
     * @return array  of topics {@link Xtopoic}
     */
    public function &getByCategory($cat_id = 0, $limit = 0, $start = 0, $criteria = null, $tags = null, $asObject = true)
    {
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
        } else {
            $criteria = new \CriteriaCompo();
        }
        $criteria->setLimit($limit);
        $criteria->setStart($start);

        if ($cat_id && is_array($cat_id)) {
            $cat_id = array_map('intval', $cat_id);
            $criteria->add(new \Criteria('cat_id', '(' . implode(',', $cat_id) . ')', 'IN'));
        } elseif ((int)$cat_id) {
            $criteria->add(new \Criteria('cat_id', (int)$cat_id));
        }
        $ret = $this->getAll($criteria, $tags, $asObject);

        return $ret;
    }

    /**
     * count topics matching a condition of a category (categories)
     *
     * @param object $criteria {@link CriteriaElement} to match
     * @param mixed  $cat_id
     * @return int    count of topics
     */
    public function getCountByCategory($cat_id = 0, $criteria = null)
    {
        $sql = 'SELECT COUNT(*) AS count FROM ' . art_DB_prefix('topic');
        if (is_array($cat_id) && count($cat_id) > 0) {
            $cat_id = array_map('intval', $cat_id);
            $sql    .= ' WHERE cat_id IN (' . implode(',', $cat_id) . ')';
        } elseif ((int)$cat_id) {
            $sql .= ' WHERE cat_id = ' . (int)$cat_id;
        } else {
            $sql .= ' WHERE 1=1';
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $myrow = $this->db->fetchArray($result);

        return (int)$myrow['count'];
    }

    /**
     * count topics matching a condition grouped by category
     *
     * @param object $criteria {@link CriteriaElement} to match
     * @return array  associative array of count of topics and category ID
     */
    public function getCountsByCategory($criteria = null)
    {
        $sql = 'SELECT cat_id, COUNT(*) as count FROM ' . art_DB_prefix('topic') . ' GROUP BY cat_id';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['cat_id']] = $myrow['count'];
        }

        return $ret;
    }

    /**
     * get articles matching a condition of a topic
     *
     * {@link Article}
     *
     * @param mixed  $topic    topic ID or {@link Xtopic}
     * @param int    $limit    Max number of objects to fetch
     * @param int    $start    Which record to start at
     * @param object $criteria {@link CriteriaElement} to match
     * @param array  $tags     variables to fetch
     * @return array  of articles {@link Article}
     */
    public function &getArticles($topic, $limit = 0, $start = 0, $criteria = null, $tags = null)
    {
        $top_id         = is_object($topic) ? $topic->getVar('top_id') : (int)$topic;
        $articleHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Article', $GLOBALS['artdirname']);
        $ret            = &$articleHandler->getByTopic($top_id, $limit, $start, $criteria, $tags);

        return $ret;
    }

    /**
     * count articles matching a condition of a cateogy (categories)
     *
     * @param mixed  $top_id   array or {@link Xtopic}
     * @param object $criteria {@link CriteriaElement} to match
     * @return int    count of articles
     */
    public function getArticleCount($top_id, $criteria = null)
    {
        $sql = 'SELECT COUNT(*) as count FROM ' . art_DB_prefix('arttop');
        if ($top_id && is_array($top_id)) {
            $sql .= ' WHERE top_id IN (' . implode(',', $top_id) . ')';
        } elseif ((int)$top_id) {
            $sql .= ' WHERE top_id = ' . (int)$top_id;
        } else {
            $sql .= ' WHERE 1=1';
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
        }
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return (int)$myrow['count'];
    }

    /**
     * count articles matching a condition of a list of topics, respectively
     *
     * @param mixed  $top_id   array or {@link Xtopic}
     * @param object $criteria {@link CriteriaElement} to match
     * @return array  associative array topic ID and article count
     */
    public function getArticleCounts($top_id, $criteria = null)
    {
        $sql = 'SELECT top_id, COUNT(*) as count FROM ' . art_DB_prefix('arttop');
        if ($top_id && is_array($top_id)) {
            $sql .= ' WHERE top_id IN (' . implode(',', $top_id) . ')';
        } elseif ((int)$top_id) {
            $sql .= ' WHERE top_id = ' . (int)$top_id;
        } else {
            $sql .= ' WHERE 1=1';
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND ' . $criteria->render();
        }
        $sql    .= ' GROUP BY top_id';
        $result = $this->db->query($sql);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['top_id']] = $myrow['count'];
        }

        return $ret;
    }

    /**
     * check permission of the topic
     *
     * {@link Xcategory}
     *
     * @param object $topic {@link Xtopic}
     * @param string $type  permission type
     * @return bool   true on accessible
     */
    public function getPermission(&$topic, $type = 'access')
    {
        if (!is_object($topic)) {
            $topic = &$this->get((int)$topic);
        }
        $categoryHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Category', $GLOBALS['artdirname']);
        $category_obj    = $categoryHandler->get($topic->getVar('cat_id'));

        return $categoryHandler->getPermission($category_obj, $type);
    }

    /**
     * clean orphan topics from database
     *
     * @param null|mixed $table_link
     * @param null|mixed $field_link
     * @param null|mixed $field_object
     * @return bool true on success
     */
    public function cleanOrphan($table_link = null, $field_link = null, $field_object = null)
    {
        parent::cleanOrphan(art_DB_prefix('category'), 'cat_id');

        /* for MySQL 4.1+ */
        if (version_compare(mysqli_get_server_info(), '4.1.0', 'ge')):
            $sql = 'DELETE FROM ' . art_DB_prefix('arttop') . ' WHERE (top_id NOT IN ( SELECT DISTINCT top_id FROM ' . art_DB_prefix('topic') . ') )';
        else:
            $sql = 'DELETE ' . art_DB_prefix('arttop') . ' FROM ' . art_DB_prefix('arttop') . ' LEFT JOIN ' . art_DB_prefix('topic') . ' AS aa ON ' . art_DB_prefix('arttop') . '.top_id = aa.top_id ' . ' WHERE (aa.top_id IS NULL)';
        endif;
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("cleanOrphan: ". $sql);
            //return false;
        }

        return true;
    }
}
//');
