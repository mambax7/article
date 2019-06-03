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

require_once __DIR__ . '/vars.php';
define($GLOBALS['artdirname'] . '_FUNCTIONS_TIME_LOADED', true);

if (!defined('ART_FUNCTIONS_TIME')):
    define('ART_FUNCTIONS_TIME', 1);

    /**
     * Function to convert UNIX time to formatted time string
     * @param        $time
     * @param string $format
     * @param null   $timeoffset
     * @return string
     */
    function art_formatTimestamp($time, $format = 'c', $timeoffset = null)
    {
        $artConfig = art_load_config();

        if ('reg' === mb_strtolower($format) || '' == mb_strtolower($format)) {
            $format = 'c';
        }
        if (('custom' === mb_strtolower($format) || 'c' === mb_strtolower($format))
            && !empty($artConfig['formatTimestamp_custom'])) {
            $format = $artConfig['formatTimestamp_custom'];
        }

        xoops_load('xoopslocal');

        return \XoopsLocal::formatTimestamp($time, $format, $timeoffset);
    }
endif;
