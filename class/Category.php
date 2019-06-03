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
 * Xcategory
 *
 * @author    D.J. (phppp)
 * @copyright copyright &copy; 2005 XoopsForge.com
 * @package   module::article
 *
 * {@link XoopsObject}
 **/
if (!class_exists('Category')) {
    class Category extends \XoopsObject
    {
        /**
         * Constructor
         */
        public function __construct()
        {
            //$this->ArtObject();
            //$this->table = art_DB_prefix("category");
            $this->initVar('cat_id', XOBJ_DTYPE_INT, null, false);                // auto_increment unique ID
            $this->initVar('cat_title', XOBJ_DTYPE_TXTBOX, '', true);                    // category title
            $this->initVar('cat_pid', XOBJ_DTYPE_INT, 0, false);                     // parent category ID
            $this->initVar('cat_description', XOBJ_DTYPE_TXTAREA, '', false);                    // description
            $this->initVar('cat_image', XOBJ_DTYPE_SOURCE, '', false);                    // header graphic (unique)
            $this->initVar('cat_order', XOBJ_DTYPE_INT, 99, false);                    // display order
            $this->initVar('cat_entry', XOBJ_DTYPE_INT, 0, false);                     // entry article ID for the category. If cat_entry is set, the article will substitute the category index page
            // Feature designed by Skalpa
            $this->initVar('cat_template', XOBJ_DTYPE_SOURCE, 'default', false);            // category-wide template
            $this->initVar('cat_sponsor', XOBJ_DTYPE_TXTAREA, '', false);                    // sponsors: url[space]title

            $this->initVar('cat_moderator', XOBJ_DTYPE_ARRAY, serialize([]));        // moderators/editors
            $this->initVar('cat_track', XOBJ_DTYPE_ARRAY, serialize([]));         // track back to top category, for building Bread Crumbs
            $this->initVar('cat_lastarticles', XOBJ_DTYPE_ARRAY, serialize([]));         // last 10 article Ids
        }

        /**
         * get a list of parsed sponsors of the category
         *
         * @return array
         */
        public function &getSponsor()
        {
            $sponsors = art_parseLinks($this->getVar('cat_sponsor', 'e'));

            return $sponsors;
        }

        /**
         * get verified image url of the category
         *
         * @return string
         */
        public function getImage()
        {
            mod_loadFunctions('url', $GLOBALS['artdirname']);
            $image = art_getImageUrl($this->getVar('cat_image'));

            return $image;
        }
    }
}
