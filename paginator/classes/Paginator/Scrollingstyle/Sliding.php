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
 * @version    $Id: Sliding.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * A Yahoo! Search-like scrolling style.  The cursor will advance to
 * the middle of the range, then remain there until the user reaches
 * the end of the page set, at which point it will continue on to
 * the end of the range and the last page in the set.
 *
 * @link       http://search.yahoo.com/search?p=Zend+Framework
 * @category   Zend
 * @package    Paginator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Paginator_Scrollingstyle_Sliding implements Paginator_Scrollingstyle_Interface
{
    /**
     * Returns an array of "local" pages given a page number and range.
     *
     * @param  Paginator $paginator
     * @param  integer $page_Range (Optional) Page range
     * @return array
     */
    public function get_Pages(Paginator $paginator, $page_Range = null)
    {
        if ($page_Range === null) {
            $page_Range = $paginator->get_Page_Range();
        }

        $page_Number = $paginator->get_Current_Page_Number();
        $page_Count  = count($paginator);

        if ($page_Range > $page_Count) {
            $page_Range = $page_Count;
        }

        $delta = ceil($page_Range / 2);

        if ($page_Number - $delta > $page_Count - $page_Range) {
            $lower_Bound = $page_Count - $page_Range + 1;
            $upper_Bound = $page_Count;
        } else {
            if ($page_Number - $delta < 0) {
                $delta = $page_Number;
            }

            $offset     = $page_Number - $delta;
            $lower_Bound = $offset + 1;
            $upper_Bound = $offset + $page_Range;
        }

        return $paginator->get_Pages_In_Range($lower_Bound, $upper_Bound);
    }
}