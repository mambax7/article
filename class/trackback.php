<?php
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
require_once  dirname(__DIR__) . '/include/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

if (!class_exists('Trackback')) {
    class Trackback extends \XoopsObject
    {
        public function __construct($id = null)
        {
            //$this->ArtObject();
            //$this->table = art_DB_prefix("trackback");
            $this->initVar('tb_id', XOBJ_DTYPE_INT, null);
            $this->initVar('art_id', XOBJ_DTYPE_INT, 0, true);
            $this->initVar('tb_status', XOBJ_DTYPE_INT, 0);
            $this->initVar('tb_time', XOBJ_DTYPE_INT);
            $this->initVar('tb_title', XOBJ_DTYPE_TXTBOX);
            $this->initVar('tb_url', XOBJ_DTYPE_TXTBOX);
            $this->initVar('tb_excerpt', XOBJ_DTYPE_TXTBOX);
            $this->initVar('tb_blog_name', XOBJ_DTYPE_TXTBOX);
            $this->initVar('tb_ip', XOBJ_DTYPE_INT);
        }

        public function getTime($format = 's')
        {
            mod_loadFunctions('time', $GLOBALS['artdirname']);
            $time = art_formatTimestamp($this->getVar('tb_time'), $format);

            return $time;
        }

        public function getIp()
        {
            return long2ip($this->getVar('tb_ip'));
        }
    }
}

art_parse_class('
class [CLASS_PREFIX]TrackbackHandler extends \XoopsPersistableObjectHandler
{
    function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, art_DB_prefix("trackback", true), "Trackback", "tb_id", "tb_url");
    }

    function &getByArticle($art_id, $isApproved = true)
    {
        $sql = "SELECT * FROM " . art_DB_prefix("trackback") . " WHERE art_id = ". (int)($art_id);
        if ($isApproved) {
            $sql .= " AND tb_status > 0";
        }
        $result = $this->db->query($sql);
        $ret = array();
       while (false !== ($myrow = $this->db->fetchArray($result))) {
            $trackback = $this->create(false);
            $trackback->assignVars($myrow);
            $ret[$myrow["tb_id"]] = $trackback;
            unset($trackback);
        }

        return $ret;
    }

    function deleteIds($ids)
    {
        $ids = array_map("intval", $ids);

        $sql = "DELETE FROM " . art_DB_prefix("trackback") . " WHERE tb_id IN (" . implode(",", $ids) . ")";
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("delete trackbacks error:" . $sql);
            return false;
        }

        return true;
    }

    function approveIds($ids)
    {
        $ids = array_map("intval", $ids);

        $sql = "UPDATE " . art_DB_prefix("trackback") . " SET tb_status=1 WHERE tb_id IN (" . implode(",", $ids) . ")";
        if (!$result = $this->db->queryF($sql)) {
            //xoops_error("approve trackback error:" . $sql);
            return false;
        }

        return true;
    }

    function deleteByArticle($art_id)
    {
        $trackbacks = $this->getByArticle($art_id);
        if (count($trackbacks) > 0) {
            $this->deleteIds(array_keys($trackbacks));
        }

        return true;
    }

    function getStatus()
    {
        return 1;
    }

    /**
     * clean orphan items from database
     *
     * @return bool true on success
     */
    function cleanOrphan($table_link = null, $field_link = null, $field_object =null)
    {
        parent::cleanOrphan(art_DB_prefix("article"), "art_id");
    }
}
');
