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
require_once XOOPS_ROOT_PATH . '/class/tree.php';

if (!class_exists('ArtTree')) {
    class ArtTree extends XoopsObjectTree
    {
        public function __construct(&$objectArr, $rootId = null)
        {
            parent::__construct($objectArr, 'cat_id', 'cat_pid', $rootId);
        }

        /**
         * Make options for a select box from
         *
         * @param int    $key         ID of the object to display as the root of select options
         * @param string $ret         (reference to a string when called from outside) Result from previous recursions
         * @param string $prefix_orig String to indent items at deeper levels
         * @param string $prefix_curr String to indent the current item
         * @param null   $tags
         * @internal  param string $fieldName Name of the member variable from the
         *                            node objects that should be used as the title for the options.
         * @internal  param string $selected Value to display as selected
         * @access    private
         */
        public function _makeTreeItems($key, &$ret, $prefix_orig, $prefix_curr = '', $tags = null)
        {
            if ($key > 0) {
                if (count($tags) > 0) {
                    foreach ($tags as $tag) {
                        $ret[$key][$tag] = $this->tree[$key]['obj']->getVar($tag);
                    }
                } else {
                    $ret[$key]['cat_title'] = $this->tree[$key]['obj']->getVar('cat_title');
                }
                $ret[$key]['prefix'] = $prefix_curr;
                $prefix_curr         .= $prefix_orig;
            }
            if (isset($this->tree[$key]['child']) && !empty($this->tree[$key]['child'])) {
                foreach ($this->tree[$key]['child'] as $childkey) {
                    $this->_makeTreeItems($childkey, $ret, $prefix_orig, $prefix_curr, $tags);
                }
            }
        }

        /**
         * Make a select box with options from the tree
         *
         * @param  string  $prefix         String to indent deeper levels
         * @param  integer $key            ID of the object to display as the root of select options
         * @param null     $tags
         * @return array HTML select box
         * @internal param string $name Name of the select box
         * @internal param string $fieldName Name of the member variable from the
         *                                 node objects that should be used as the title for the options.
         * @internal param string $selected Value to display as selected
         * @internal param bool $addEmptyOption Set TRUE to add an empty option with value "0" at the top of the hierarchy
         */
        public function &makeTree($prefix = '-', $key = 0, $tags = null)
        {
            //art_message($prefix);
            $ret = [];
            $this->_makeTreeItems($key, $ret, $prefix, '', $tags);

            return $ret;
        }

        /**
         * Make a select box with options from the tree
         *
         * @param  string  $name           Name of the select box
         * @param  string  $prefix         String to indent deeper levels
         * @param  string  $selected       Value to display as selected
         * @param bool     $EmptyOption
         * @param  integer $key            ID of the object to display as the root of select options
         * @return string HTML select box
         * @internal param string $fieldName Name of the member variable from the
         *                                 node objects that should be used as the title for the options.
         * @internal param bool $addEmptyOption Set TRUE to add an empty option with value "0" at the top of the hierarchy
         */
        public function &makeSelBox($name, $prefix = '-', $selected = '', $EmptyOption = false, $key = 0)
        {
            $ret = '<select name=' . $name . '>';
            if (!empty($addEmptyOption)) {
                $ret .= '<option value="0">' . (is_string($EmptyOption) ? $EmptyOption : '') . '</option>';
            }
            $this->_makeSelBoxOptions('cat_title', $selected, $key, $ret, $prefix);
            $ret .= '</select>';

            return $ret;
        }

        /**
         * Make a tree for the array of a given category
         *
         * @param  string  $key   top key of the tree
         * @param  array   $ret   the tree
         * @param  array   $tags  fields to be used
         * @param  integer $depth level of subcategories
         * @return void
         */
        public function getAllChild_array($key, &$ret, $tags = [], $depth = 0)
        {
            if (0 == --$depth) {
                return;
            }

            if (isset($this->tree[$key]['child'])) {
                foreach ($this->tree[$key]['child'] as $childkey) {
                    if (isset($this->tree[$childkey]['obj'])):
                        if (count($tags) > 0) {
                            foreach ($tags as $tag) {
                                $ret['child'][$childkey][$tag] = $this->tree[$childkey]['obj']->getVar($tag);
                            }
                        } else {
                            $ret['child'][$childkey]['cat_title'] = $this->tree[$childkey]['obj']->getVar('cat_title');
                        }
                    endif;

                    $this->getAllChild_array($childkey, $ret['child'][$childkey], $tags, $depth);
                }
            }
        }

        /**
         * Make a tree for the array
         *
         * @param int|string $key   top key of the tree
         * @param  array     $tags  fields to be used
         * @param  integer   $depth level of subcategories
         * @return array
         */
        public function &makeArrayTree($key = 0, $tags = null, $depth = 0)
        {
            $ret = [];
            if ($depth > 0) {
                ++$depth;
            }
            $this->getAllChild_array($key, $ret, $tags, $depth);

            return $ret;
        }
    }
}
