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

// TODO: handle mysql version 4.1

//art_parse_class('
class FileHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('file', true), 'Xfile', 'file_id', 'file_name');
    }

    public function getCountOrphan()
    {
        $sql    = 'SELECT COUNT(*) as count FROM ' . art_DB_prefix('file') . ' WHERE art_id NOT IN ( SELECT DISTINCT art_id FROM ' . art_DB_prefix('article') . ')';
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return (int)$myrow['count'];
    }

    public function &getOrpan($criteria = null, $tags = false)
    {
        if ($tags && is_array($tags)) {
            if (!in_array('file_id', $tags)) {
                $tags[] = 'file_id';
            }
            $select = implode(',', $tags);
        } else {
            $select = '*';
        }
        $limit = $start = null;
        $sql   = "SELECT $select FROM " . art_DB_prefix('file') . ' WHERE art_id NOT IN ( SELECT DISTINCT art_id FROM ' . art_DB_prefix('article') . ')';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $file = $this->create(false);
            $file->assignVars($myrow);

            $ret[$myrow['file_id']] = $file;
            unset($file);
        }

        return $ret;
    }

    public function &getOrphanByLimit($limit = 1, $start = 0, $criteria = null, $tags = false)
    {
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
        } elseif (!empty($limit)) {
            $criteria = new \CriteriaCompo();
            $criteria->setLimit($limit);
            $criteria->setStart($start);
        }
        $ret = &$this->getAll($criteria, $tags);

        return $ret;
    }

    public function &getByArticle($art_id)
    {
        $sql    = 'SELECT * FROM ' . art_DB_prefix('file') . ' WHERE art_id = ' . (int)$art_id;
        $result = $this->db->query($sql);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $file = $this->create(false);
            $file->assignVars($myrow);
            $ret[$myrow['file_id']] = $file;
            unset($file);
        }

        return $ret;
    }

    public function delete(\XoopsObject $file)
    {
        global $xoopsModuleConfig;

        $sql = 'DELETE FROM ' . $file->table . ' WHERE file_id =' . $file->getVar('file_id');
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        @unlink($xoopsModuleConfig['path_file'] . '/' . $file->getVar('file_name'));
        unset($file);

        return true;
    }

    public function deleteByArticle($art_id)
    {
        $files = $this->getByArticle($art_id);
        if (count($files) > 0) {
            foreach ($files as $file_id => $file) {
                $this->delete($file);
            }
        }

        return true;
    }
}
//');
