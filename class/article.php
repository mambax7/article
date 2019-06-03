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
 * Article
 *
 * @author    D.J. (phppp)
 * @copyright copyright &copy; 2005 XoopsForge.com
 * @package   module::article
 *
 * {@link XoopsObject}
 **/
if (!class_exists('Article')) {
    class Article extends \XoopsObject
    {
        /**
         * @var array
         */
        public $headings = [];
        /**
         * @var array
         */
        public $notes = [];

        /**
         * Constructor
         */
        public function __construct()
        {
            //$this->ArtObject();
            //$this->table = art_DB_prefix("article");
            $this->initVar('art_id', XOBJ_DTYPE_INT, null, false);                // auto_increment unique ID
            $this->initVar('cat_id', XOBJ_DTYPE_INT, 0, true);                    // base category ID

            $this->initVar('uid', XOBJ_DTYPE_INT, 0);                            // submitter's UID
            $this->initVar('writer_id', XOBJ_DTYPE_INT, 0);                         // Original writer's ID
            //$this->initVar("art_author",         XOBJ_DTYPE_TXTBOX, "");
            //$this->initVar("art_profile",        XOBJ_DTYPE_TXTAREA, "");
            $this->initVar('art_source', XOBJ_DTYPE_TXTBOX, '');                     // Original URL or resource

            $this->initVar('art_title', XOBJ_DTYPE_TXTBOX, '');                        // article title
            $this->initVar('art_keywords', XOBJ_DTYPE_TXTBOX, '');                        // keywords, in raw format
            $this->initVar('art_summary', XOBJ_DTYPE_TXTAREA, '');                    // article summary

            $this->initVar('art_image', XOBJ_DTYPE_ARRAY, '');                        // head image: file name, caption
            $this->initVar('art_template', XOBJ_DTYPE_TXTBOX, '');                        // specified article template, overwriting the module and category -wide setting

            $this->initVar('art_pages', XOBJ_DTYPE_ARRAY, '');                        // associative array of pages: text ID, papage title
            $this->initVar('art_categories', XOBJ_DTYPE_ARRAY, '');                        // categories ID
            $this->initVar('art_topics', XOBJ_DTYPE_ARRAY, '');                        // topics ID
            $this->initVar('art_elinks', XOBJ_DTYPE_TXTAREA, '');                     // external links, in raw format
            $this->initVar('art_forum', XOBJ_DTYPE_INT, 0);                            // forum ID the comments will be located

            $this->initVar('art_time_create', XOBJ_DTYPE_INT);                            // time of creation
            $this->initVar('art_time_submit', XOBJ_DTYPE_INT);                            // time of submission

            $this->initVar('art_time_publish', XOBJ_DTYPE_INT);                            // time of publish
            $this->initVar('art_counter', XOBJ_DTYPE_INT, 0);                            // click count
            $this->initVar('art_rating', XOBJ_DTYPE_INT);                            // rating value, in sum
            $this->initVar('art_rates', XOBJ_DTYPE_INT, 0);                            // rating count
            $this->initVar('art_comments', XOBJ_DTYPE_INT, 0);                            // comment count
            $this->initVar('art_trackbacks', XOBJ_DTYPE_INT, 0);                            // trackback count

            /*
             * For summary
             *
             */
            $this->initVar('dohtml', XOBJ_DTYPE_INT, 1);
        }

        /**
         * get a list of categories
         *
         * @return array of category ID
         */
        public function &getCategories()
        {
            $categories = $this->getVar('art_categories');
            if (!in_array($this->getVar('cat_id'), $categories)) {
                array_unshift($categories, $this->getVar('cat_id'));
            }

            return $categories;
        }

        /**
         * get verified image of the article: url, caption
         *
         * @param bool $complete flag for retrieving image url
         * @return mixed array or null
         */
        public function getImage($complete = true)
        {
            $image = $this->getVar('art_image');
            if (!empty($image['file'])) {
                if (!empty($complete)) {
                    mod_loadFunctions('url', $GLOBALS['artdirname']);
                    $image['url'] = art_getImageUrl($image['file']);
                }
            } else {
                $image = null;
            }

            return $image;
        }

        /**
         * get writer info of the article
         *
         * @return array associative array of writer name, avatar and his profile
         */
        public function &getWriter()
        {
            $helper = \XoopsModules\Article\Helper::getInstance();
            $writer = [];
            if ($writer_id = $this->getVar('writer_id')) {
                $writerHandler  = $helper->getHandler('Writer', $GLOBALS['artdirname']);
                $writer_obj     = $writerHandler->get($writer_id);
                $writer['name'] = $writer_obj->getVar('writer_name');
                mod_loadFunctions('url', $GLOBALS['artdirname']);
                $writer['avatar']  = art_getImageLink($writer_obj->getVar('writer_avatar'));
                $writer['profile'] = $writer_obj->getVar('writer_profile');
                unset($writer_obj);
            }

            return $writer;
        }

        /**
         * get author info of the article
         *
         * {@link XoopsUser}
         *
         * @param bool $retrieveUname flag for retrieving user name based on user ID
         * @return array associative array of registered author id and his name
         */
        public function &getAuthor($retrieveUname = false)
        {
            /*
             $author["author"] = $this->getVar("art_author");
             $author["profile"] = $this->getVar("art_profile");
             */
            $author['uid'] = $this->getVar('uid');
            if ($retrieveUname) {
                $author['name'] = \XoopsUser::getUnameFromId($author['uid']);
            }

            return $author;
        }

        /**
         * get formatted publish time of the article
         *
         * {@link Config}
         *
         * @param string $format format of time
         * @return string
         */
        public function getTime($format = 'c')
        {
            mod_loadFunctions('time', $GLOBALS['artdirname']);
            $time = art_formatTimestamp($this->getVar('art_time_publish'), $format);

            return $time;
        }

        /**
         * get summary of the article
         *
         * @param bool $actionOnEmpty flag for truncating content if summary is empty
         * @param bool $dohtml
         * @return string
         */
        public function getSummary($actionOnEmpty = false, $dohtml = true)
        {
            $myts    = \MyTextSanitizer::getInstance();
            $helper  = \XoopsModules\Article\Helper::getInstance();
            $summary = $this->getVar('art_summary', 'n');
            if (empty($summary) && !empty($actionOnEmpty)) {
                $pages       = $this->getPages();
                $textHandler = $helper->getHandler('Text', $GLOBALS['artdirname']);
                if (count($pages) > 1) {
                    $texts   = array_filter($textHandler->getList(new \Criteria('text_id', '(' . implode(',', $pages) . ')', 'IN')), 'trim'); // fixed by Steven Chu
                    $summary = implode($dohtml ? '<br>' : '. ', $texts);
                } else {
                    $text_obj = $textHandler->get($pages[0]);
                    $summary  = $text_obj->getVar('text_body');
                    mod_loadFunctions('render', $GLOBALS['artdirname']);
                    $summary = art_html2text($summary);
                    $length  = empty($GLOBALS['xoopsModuleConfig']['length_excerpt']) ? 255 : $GLOBALS['xoopsModuleConfig']['length_excerpt'];
                    $summary = $myts->htmlSpecialChars(xoops_substr($summary, 0, $length));
                }
            } else {
                $summary = $myts->displayTarea($summary, 1);
                if (!$dohtml) {
                    mod_loadFunctions('render', $GLOBALS['artdirname']);
                    $summary = art_html2text($summary);
                }
            }

            return $summary;
        }

        /**
         * get the text ID of a specified page of the article
         *
         * @param int  $page truncate content if summary is empty
         * @param bool $searchAll
         * @return int page ID (text_id)
         */
        public function getPage($page = 0, $searchAll = false)
        {
            if (0 == $this->getVar('art_id')) {
                return null;
            }
            $pages = $this->getPages(false, $searchAll);
            $page  = isset($pages[(int)$page]) ? $pages[(int)$page] : null;

            return $page;
        }

        /**
         * get array of text IDs and titles  of the article
         *
         * @param bool $withTitle
         * @param bool $searchAll
         * @return array associative array of ID and title
         */
        public function getPages($withTitle = false, $searchAll = false)
        {
            $helper = \XoopsModules\Article\Helper::getInstance();
            $ret    = [];
            if (0 == $this->getVar('art_id')) {
                return $ret;
            }
            $pages_id = $this->getVar('art_pages');
            if (empty($withTitle) && empty($searchAll)) {
                return $pages_id;
            }
            $textHandler = $helper->getHandler('Text', $GLOBALS['artdirname']);
            if ($searchAll) {
                $criteria_pages = new \Criteria('art_id', $this->getVar('art_id'));
            } else {
                $criteria_pages = new \Criteria('text_id', '(' . implode(',', $pages_id) . ')', 'IN');
            }
            if (empty($withTitle)) {
                $textHandler->identifierName = false;
            }
            $pages = $textHandler->getList($criteria_pages);
            foreach ($pages_id as $id) {
                if (!isset($pages[$id])) {
                    continue;
                }
                if (empty($withTitle)) {
                    $ret[] = $id;
                } else {
                    $ret[] = ['id' => $id, 'title' => $pages[$id]];
                }
            }
            foreach (array_keys($pages) as $id) {
                if (in_array($id, $pages_id)) {
                    continue;
                }
                if (empty($withTitle)) {
                    $ret[] = $id;
                } else {
                    $ret[] = ['id' => $id, 'title' => $pages[$id]];
                }
            }
            unset($criteria_pages, $pages);

            return $ret;
        }

        /**
         * pages count of the article
         *
         * @param bool $searchAll
         * @return int
         */
        public function getPageCount($searchAll = false)
        {
            $helper = \XoopsModules\Article\Helper::getInstance();
            if (0 == $this->getVar('art_id')) {
                return 0;
            }
            if (empty($searchAll)) {
                return count($this->getVar('art_pages'));
            }
            $textHandler    = $helper->getHandler('Text', $GLOBALS['artdirname']);
            $criteria_pages = new \Criteria('art_id', $this->getVar('art_id'));
            $count          = $textHandler->getCount($criteria_pages);
            unset($criteria_pages);

            return $count;
        }

        /**
         * get rating average of the article
         *
         * @param int $decimals decimal length
         * @return numeric
         */
        public function getRatingAverage($decimals = 2)
        {
            $ave = 0;
            if ($this->getVar('art_rates')) {
                $ave = number_format($this->getVar('art_rating') / $this->getVar('art_rates'), $decimals);
            }

            return $ave;
        }

        /**
         * get text content of a specified page of the article
         *
         * @param int    $page   page no
         * @param string $format text format
         * @return array
         */
        public function &gettext($page = -1, $format = 's')
        {
            global $xoopsModuleConfig;

            $format = mb_strtolower($format);
            $text   = $this->_getText($page, $format);
            if (empty($text)) {
                return $text;
            }
            if ('e' === $format || 'edit' === $format || 'n' === $format || 'none' === $format) {
                return $text;
            }
            if ('raw' === $format) {
                mod_loadFunctions('render', $GLOBALS['artdirname']);
                $ret = [
                    'title' => art_htmlSpecialChars($text['title']),
                    'body'  => art_displayTarea($text['body']),
                ];

                return $ret;
            }

            $body = &$text['body'];
            $body = $this->parseNotes($body);
            $body = $this->parseHeadings($body);

            $ret = ['title' => $text['title'], 'body' => $body];

            return $ret;
        }

        /**
         * Generate sanitized text and headings of the article
         *
         * @param string $text text content
         * @return string
         */
        public function &parseHeadings(&$text)
        {
            $this->headings = [];
            if (empty($GLOBALS['xoopsModuleConfig']['do_heading']) || empty($text)) {
                return $text;
            }
            $text = preg_replace_callback("/<h([1-7])>(.*)<\/h\\1>/isU", [&$this, '_convertHeadings'], $text);

            return $text;
        }

        /**
         * Generate heading of the article
         *
         * @param array $matches matched items
         * @return string
         */
        public function _convertHeadings($matches)
        {
            static $ii = 0;
            ++$ii;
            $this->headings[] = '<a href="#heading' . $ii . '">' . $matches[2] . '</a>';

            return '<a name="heading' . $ii . '" id="heading' . $ii . '"></a><h' . $matches[1] . '>' . $matches[2] . '</h' . $matches[1] . '>';
        }

        /**
         * Generate sanitized text and footnotes of the article
         *
         * @param string $text text content
         * @return string
         */
        public function &parseNotes(&$text)
        {
            if (empty($GLOBALS['xoopsModuleConfig']['do_footnote']) || empty($text)) {
                return $text;
            }
            $text = preg_replace_callback("/\(\((.*)\)\)/U", [&$this, '_convertNotes'], $text);

            return $text;
        }

        /**
         * Generate footnote of the article
         *
         * @param array $matches matched items
         * @return string
         */
        public function _convertNotes($matches)
        {
            static $ii = 0;
            ++$ii;
            $this->notes[] = '<a name="footnote_content' . $ii . '" id="footnote_content' . $ii . '"></a><a href="#footnote_index' . $ii . '"><span class="noteitem">' . $matches[1] . '</span></a>';

            return '<a name="footnote_index' . $ii . '" id="footnote_index' . $ii . '"></a>[<a href="#footnote_content' . $ii . '">' . $ii . '</a>]';
        }

        /**
         * get text raw content of a specified page of the article
         *
         * {@link Text}
         *
         * @param int    $page   page no
         * @param string $format text format
         * @return array
         */
        public function &_getText($page = -1, $format = 's')
        {
            $helper = \XoopsModules\Article\Helper::getInstance();
            $page   = $this->getPage((int)$page);
            if (empty($page)) {
                $ret = null;

                return $ret;
            }
            $textHandler = $helper->getHandler('Text', $GLOBALS['artdirname']);
            $text        = $textHandler->get($page);
            if ('raw' === $format) {
                $res = [
                    'title' => $text->vars['text_title']['value'],
                    'body'  => $text->vars['text_body']['value'],
                ];
            } elseif (empty($format)) {
                $res = ['title' => $text->getVar('text_title'), 'body' => $text->getVar('text_body')];
            } else {
                $res = ['title' => $text->getVar('text_title'), 'body' => $text->getVar('text_body', $format)];
            }

            return $res;
        }
    }
}
