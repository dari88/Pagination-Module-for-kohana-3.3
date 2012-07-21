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
 * @version    $Id: Elastic.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * A Google-like scrolling style.  Incrementally expands the range to about
 * twice the given page range, then behaves like a slider.  See the example
 * link.
 *
 * @link       http://www.google.com/search?q=Zend+Framework
 * @category   Zend
 * @package    Paginator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Paginator_Scrollingstyle_Elastic extends Paginator_Scrollingstyle_Sliding
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

        $original_Page_Range = $page_Range;
        $page_Range         = $page_Range * 2 - 1;

        if ($original_Page_Range + $page_Number - 1 < $page_Range) {
            $page_Range = $original_Page_Range + $page_Number - 1;
        } else if ($original_Page_Range + $page_Number - 1 > count($paginator)) {
            $page_Range = $original_Page_Range + count($paginator) - $page_Number;
        }

        return parent::get_Pages($paginator, $page_Range);
    }
}