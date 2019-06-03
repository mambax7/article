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
class PingbackHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('pingback', true), Pingback::class, 'pb_id', 'pb_url');
    }

    public function &getByArticle($art_id)
    {
        $sql    = 'SELECT * FROM ' . art_DB_prefix('pingback') . ' WHERE art_id = ' . (int)$art_id;
        $result = $this->db->query($sql);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $pingback = $this->create(false);
            $pingback->assignVars($myrow);
            $ret[$myrow['pb_id']] = $pingback;
            unset($pingback);
        }

        return $ret;
    }

    public function deleteByArticle($art_id)
    {
        $pingbacks = $this->getByArticle($art_id);
        if (count($pingbacks) > 0) {
            foreach ($pingbacks as $pb_id => $pingback) {
                $this->delete($pingback);
            }
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
