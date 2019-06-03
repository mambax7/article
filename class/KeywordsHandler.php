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

// The solution is not ready for HTML yet.
// Another trial: http://aidan.dotgeek.org/lib/?file=function.str_highlight.php
// -- D.J.

/*
 * Adapted from
 * ------------
 * @description     Advanced keyword highlighter, keep HTML tags safe.
 * @author(s)    Bojidar Naydenov a.k.a Bojo (bojo2000@mail.bg) & Antony Raijekov a.k.a Zeos (dev@strategma.bg)
 * @country         Bulgaria
 * @copyright    GPL
 * @access       public
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once dirname(__DIR__) . '/include/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

//art_parse_class('
class KeywordsHandler extends \XoopsPersistableObjectHandler
{
    public $keywords;
    public $skip_tags = [
        'A',
        'IMG',
        'PRE',
        'QUOTE',
        'CODE',
        'H1',
        'H2',
        'H3',
        'H4',
        'H5',
        'H6',
    ];    //add here more, if you want to filter them


    public function init()
    {
        $this->getKeywords();
        if (0 == count($this->keywords)) {
            return false;
        }

        return true;
    }

    public function getKeywords()
    {
        global $xoopsModuleConfig;
        static $keywords = [];
        if (count($keywords) > 0) {
            return $keywords;
        }
        $_keywords = art_parseLinks($xoopsModuleConfig['keywords']);

        foreach ($_keywords as $_keyword) {
            $this->keywords[mb_strtolower($_keyword['title'])] = $_keyword['url'];
        }
    }

    public function highlighter($matches)
    {
        if (!in_array(mb_strtoupper($matches[2]), $this->skip_tags)) {
            $replace = '<a href="' . $this->keywords[mb_strtolower($matches[3])] . '">' . $matches[3] . '</a>';
            $proceed = preg_replace("#\b(" . $matches[3] . ")\b#si", $replace, $matches[0]);
        } else {
            $proceed = $matches[0];
        }

        return stripslashes($proceed);
    }

    public function &process(&$text)
    {
        foreach ($this->keywords as $keyword => $rep) {
            $text = preg_replace_callback("#(<([A-Za-z]+)[^>]*[\>]*)*\s(" . $keyword . ")\s(.*?)(<\/\\2>)*#si", [&$this, 'highlighter'], $text);
        }

        return $text;
    }
}
//');
