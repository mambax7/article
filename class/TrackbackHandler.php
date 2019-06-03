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
class TrackbackHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('trackback', true), Trackback::class, 'tb_id', 'tb_url');
    }

    public function &getByArticle($art_id, $isApproved = true)
    {
        $sql = 'SELECT * FROM ' . art_DB_prefix('trackback') . ' WHERE art_id = ' . (int)$art_id;
        if ($isApproved) {
            $sql .= ' AND tb_status > 0';
        }
        $result = $this->db->query($sql);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $trackback = $this->create(false);
            $trackback->assignVars($myrow);
            $ret[$myrow['tb_id']] = $trackback;
            unset($trackback);
        }

        return $ret;
    }

    public function deleteIds($ids)
    {
        $ids = array_map('intval', $ids);

        $sql = 'DELETE FROM ' . art_DB_prefix('trackback') . ' WHERE tb_id IN (' . implode(',', $ids) . ')';
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("delete trackbacks error:" . $sql);
            return false;
        }

        return true;
    }

    public function approveIds($ids)
    {
        $ids = array_map('intval', $ids);

        $sql = 'UPDATE ' . art_DB_prefix('trackback') . ' SET tb_status=1 WHERE tb_id IN (' . implode(',', $ids) . ')';
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("approve trackback error:" . $sql);
            return false;
        }

        return true;
    }

    public function deleteByArticle($art_id)
    {
        $trackbacks = $this->getByArticle($art_id);
        if (count($trackbacks) > 0) {
            $this->deleteIds(array_keys($trackbacks));
        }

        return true;
    }

    public function getStatus()
    {
        return 1;
    }

    /**
     * clean orphan items from database
     *
     * @param null|mixed $table_link
     * @param null|mixed $field_link
     * @param null|mixed $field_object
     * @return void true on success
     */
    public function cleanOrphan($table_link = null, $field_link = null, $field_object = null)
    {
        parent::cleanOrphan(art_DB_prefix('article'), 'art_id');
    }
}
//');
