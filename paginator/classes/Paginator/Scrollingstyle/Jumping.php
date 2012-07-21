<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Paginator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Jumping.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * A scrolling style in which the cursor advances to the upper bound
 * of the page range, the page range "jumps" to the next section, and
 * the cursor moves back to the beginning of the range.
 *
 * @category   Zend
 * @package    Paginator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Paginator_Scrollingstyle_Jumping implements Paginator_Scrollingstyle_Interface
{
    /**
     * Returns an array of "local" pages given a page number and range.
     *
     * @param  Paginator $paginator
     * @param  integer $page_Range Unused
     * @return array
     */
    public function get_Pages(Paginator $paginator, $page_Range = null)
    {
        $page_Range  = $paginator->get_Page_Range();
        $page_Number = $paginator->get_Current_Page_Number();

        $delta = $page_Number % $page_Range;

        if ($delta == 0) {
            $delta = $page_Range;
        }

        $offset     = $page_Number - $delta;
        $lower_Bound = $offset + 1;
        $upper_Bound = $offset + $page_Range;

        return $paginator->get_Pages_In_Range($lower_Bound, $upper_Bound);
    }
}