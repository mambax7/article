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

//art_parse_class('
class RateHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('rate', true), Rate::class, 'rate_id');
    }

    public function &getByArticle($art_id, $criteria = null)
    {
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $criteria->add(new \Criteria('art_id', (int)$art_id), 'AND');
        } else {
            $criteria = new \CriteriaCompo(new \Criteria('art_id', (int)$art_id));
        }
        $ret = &$this->getAll($criteria);

        return $ret;
    }

    public function deleteByArticle($art_id)
    {
        if (is_array($art_id)) {
            if (count($art_id) > 0) {
                $art_id = array_map('intval', $art_id);
                $where  = ' WHERE art_id IN (' . implode(',', $art_id) . ')';
            } else {
                return false;
            }
        } elseif (!empty($art_id)) {
            $where = ' WHERE art_id= ' . (int)$art_id;
        } else {
            return false;
        }
        $sql = 'DELETE FROM ' . art_DB_prefix('rate') . $where;
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * clean orphan items from database
     *
     * @param null|mixed $table_link
     * @param null|mixed $field_link
     * @param null|mixed $field_object
     * @return bool true on success
     */
    public function cleanOrphan($table_link = null, $field_link = null, $field_object = null)
    {
        return parent::cleanOrphan(art_DB_prefix('article'), 'art_id');
    }
}
//');
