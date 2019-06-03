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
class SpotlightHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, art_DB_prefix('spotlight', true), Spotlight::class, 'sp_id');
    }

    public function &get($id = null, $as_object = true)
    {
        $spotlight = $this->create();
        $sql       = 'SELECT * FROM ' . art_DB_prefix('spotlight') . ' ORDER BY sp_id DESC LIMIT 1';
        if (!$result = $this->db->query($sql)) {
            return $spotlight;
        }
        $array = $this->db->fetchArray($result);
        if (empty($array)) {
            return $spotlight;
        }
        $spotlight->assignVars($array);
        $spotlight->unsetNew();

        return $spotlight;
    }

    /**
     * Get spotlight article
     *
     * {@link \XoopsPersistableObjectHandler}
     *
     * @param bool $asArticleId   retrun article ID
     * @param bool $specifiedOnly only return article market as spotlight by editors; in this case, null is returned if "recent article" is selected in spotlight admin
     * @return array spotlight content
     */
    public function &getContent($asArticleId = true, $specifiedOnly = false)
    {
        $content   = [];
        $spotlight = &$this->get();
        if (!is_object($spotlight) || !$spotlight->getVar('art_id')) {
            $content['sp_note'] = '';
            $content['image']   = null;
            $art_id             = 0;
            $categories         = null;
        } else {
            $content['sp_note'] = $spotlight->getVar('sp_note');
            $content['image']   = $spotlight->getImage();
            $art_id             = $spotlight->getVar('art_id');
            $categories         = $spotlight->getVar('sp_categories');
        }
        if (empty($art_id) && !empty($specifiedOnly)) {
            return $content;
        }

        $articleHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Article', $GLOBALS['artdirname']);
        if (empty($art_id)) {
            $criteria = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
            $arts     = &$articleHandler->getIdsByCategory($categories, 1, 0, $criteria);
            $art_id   = empty($arts[0]) ? 0 : $arts[0];
        }

        $content['art_id'] = $art_id;
        if ($asArticleId) {
        } elseif ($art_id > 0) {
            $article_obj = $articleHandler->get($art_id);
            if (!is_object($article_obj)) {
                unset($content['art_id']);

                return $content;
            }
            $content['image']     = empty($content['image']) ? $article_obj->getImage() : $content['image'];
            $content['title']     = $article_obj->getVar('art_title');
            $content['uid']       = $article_obj->getVar('uid');
            $content['writer_id'] = $article_obj->getVar('writer_id');
            $content['time']      = $article_obj->getTime();
            $content['views']     = $article_obj->getVar('art_counter');
            $content['comments']  = $article_obj->getVar('art_comments') + $article_obj->getVar('art_trackbacks');
            $content['summary']   = $article_obj->getSummary(true);
        } else {
            $content['summary'] = '';
        }

        return $content;
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
        return true; // skip this step since it will remove all spotlight with "art_id = 0";
        //return parent::cleanOrphan(art_DB_prefix("article"), "art_id");
    }
}
//');
