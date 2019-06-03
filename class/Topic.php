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

if (!class_exists('Topic')) {
    class Topic extends \XoopsObject
    {
        /**
         * Constructor
         */
        public function __construct()
        {
            //$this->ArtObject();
            //$this->table = art_DB_prefix("topic");
            $this->initVar('top_id', XOBJ_DTYPE_INT, null, false);
            $this->initVar('cat_id', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('top_title', XOBJ_DTYPE_TXTBOX, '', true);
            $this->initVar('top_description', XOBJ_DTYPE_TXTAREA);
            $this->initVar('top_template', XOBJ_DTYPE_SOURCE);
            $this->initVar('top_time', XOBJ_DTYPE_INT);
            $this->initVar('top_expire', XOBJ_DTYPE_INT);
            $this->initVar('top_order', XOBJ_DTYPE_INT, 1);
            $this->initVar('top_sponsor', XOBJ_DTYPE_TXTAREA);
        }

        /**
         * get a list of parsed sponsors of the topic
         *
         * @return array
         */
        public function &getSponsor()
        {
            $ret = art_parseLinks($this->getVar('top_sponsor', 'e'));

            return $ret;
        }

        /**
         * get formatted creation time of the topic
         *
         * @param string $format format of time
         * @return string
         */
        public function getTime($format = '')
        {
            mod_loadFunctions('time', $GLOBALS['artdirname']);
            $time = art_formatTimestamp($this->getVar('top_time'), $format);

            return $time;
        }

        /**
         * get formatted expiring time of the topic
         *
         * @param string $format format of time
         * @return string
         */
        public function getExpire($format = '')
        {
            mod_loadFunctions('time', $GLOBALS['artdirname']);
            $time = art_formatTimestamp($this->getVar('top_expire'), $format);

            return $time;
        }
    }
}
