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
class TextHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('text', true), Text::class, 'text_id', 'text_title');
    }

    public function &getByArticle($art_id, $page = 0, $tags = null)
    {
        $text = false;
        $page = (int)$page;
        if ($tags && is_array($tags)) {
            if (!in_array('text_id', $tags)) {
                $tags[] = 'text_id';
            }
            $select = implode(',', $tags);
        } else {
            $select = '*';
        }

        if ($page) {
            $sql    = "SELECT $select FROM " . art_DB_prefix('text') . ' WHERE art_id = ' . (int)$art_id . ' ORDER BY text_id';
            $result = $this->db->query($sql, 1, $page - 1);
            if ($result && $myrow = $this->db->fetchArray($result)) {
                $text = $this->create(false);
                $text->assignVars($myrow);

                return $text;
            }
            //xoops_error($this->db->error());
            return $text;
        }
        $sql    = "SELECT $select FROM " . art_DB_prefix('text') . ' WHERE art_id = ' . (int)$art_id . ' ORDER BY text_id';
        $result = $this->db->query($sql);
        $ret    = [];
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $text = $this->create(false);
            $text->assignVars($myrow);
            $ret[$myrow['text_id']] = $text;
            unset($text);
        }

        return $ret;
    }

    /*
    function getIdByArticle($art_id, $page = 0)
    {
        $page = (int)($page);
        if ($page) {
            $sql = "SELECT text_id FROM " . art_DB_prefix("text") . " WHERE art_id = ". (int)($art_id) ." ORDER BY text_id LIMIT ".((int)($page)-1).", 1";
            $result = $this->db->query($sql);
           while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret = $myrow["text_id"];

                return $ret;
            }
            $ret = null;

            return $ret;
        } else {
            $sql = "SELECT text_id FROM " . art_DB_prefix("text") . " WHERE art_id = ". (int)($art_id) ." ORDER BY text_id";
            $result = $this->db->query($sql);
            $ret = array();
           while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[] = $myrow["text_id"];
                unset($text);
            }

            return $ret;
        }
    }

    function getForPDF(&$text)
    {
        return $text->getBody(true);
    }

    function getForPrint(&$text)
    {
        return $text->getBody();
    }

    function deleteByArticle($art_id)
    {
        $sql = "DELETE FROM ".art_DB_prefix("text")." WHERE art_id = ".(int)($art_id);
        if (!$result = $this->db->queryF($sql)) {
              //xoops_error($this->db->error());
            return false;
        }

        return true;
    }
    */

    /**
     * clean orphan text from database
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
