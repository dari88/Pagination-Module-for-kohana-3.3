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
 * @version    $Id: Paginator.php 23775 2011-03-01 17:25:24Z ralph $
 */
/**
 * @category   Zend
 * @package    Paginator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   kohana
 * @package    Paginator modified by dari88
 * @copyright  Copyright (c) dari88
 * @license    New BSD License
 */
class Paginator implements Countable, IteratorAggregate {
    /**
     * The cache tag prefix used to namespace Paginator results in the cache
     *
     */

    const CACHE_TAG_PREFIX = 'Paginator_';

    /**
     * Default scrolling style
     *
     * @var string
     */
    protected static $_default_Scrolling_Style = 'Sliding';

    /**
     * Default item count per page
     *
     * @var int
     */
    protected static $_default_Item_Count_Per_Page = 10;

    /**
     * Default number of local pages (i.e., the number of discretes
     * page numbers that will be displayed, including the current
     * page number)
     *
     * @var int
     */
    protected static $_default_Page_Range = 10;

    /**
     * Cache object
     *
     * @var Cache_Core
     */
    protected static $_cache;

    /**
     * Enable or disable the cache by Paginator instance
     *
     * @var bool
     */
    protected $_cache_Enabled = true;

    /**
     * Adapter
     *
     * @var Paginator_Interface
     */
    protected $_adapter = null;

    /**
     * Number of items in the current page
     *
     * @var integer
     */
    protected $_current_Item_Count = null;

    /**
     * Current page items
     *
     * @var Traversable
     */
    protected $_current_Items = null;

    /**
     * Current page number (starting from 1)
     *
     * @var integer
     */
    protected $_current_Page_Number = 1;

    /**
     * Number of items per page
     *
     * @var integer
     */
    protected $_item_Count_Per_Page = null;

    /**
     * Number of pages
     *
     * @var integer
     */
    protected $_page_Count = null;

    /**
     * Number of local pages (i.e., the number of discrete page numbers
     * that will be displayed, including the current page number)
     *
     * @var integer
     */
    protected $_page_Range = null;

    /**
     * Pages
     *
     * @var array
     */
    protected $_pages = null;

    /**
     * Default url, page option's name, url options.
     *
     * @var array
     */
    protected $_default_Url = '';
    protected $_default_Page_Query_Name = 'page';
    protected $_default_Option_Query = '';

    /**
     * url, page option's name, url options.
     *
     * @var array
     */
    protected $_url = null;
    protected $_page_Query_Name = null;
    protected $_option_Query = null;

    /**
     * Constructor.
     *
     * @param Paginator_Interface|Paginator_AdapterAggregate $adapter
     */
    public function __construct($adapter)
    {
        if ($adapter instanceof Paginator_Iterator)
        {
            $this->_adapter = $adapter;
        } else
        {
            throw new Exception(
                    'Paginator only accepts instances of the type ' .
                    'Paginator_Iterator.'
            );
        }
    }

    /**
     * Factory.
     *
     * @param  mixed $data
     * @param  string $adapter
     * @param  array $prefixPaths
     * @return Paginator
     */
    public static function factory($data)
    {
        return new self(new Paginator_Iterator($data));
    }

    /**
     * Returns the default scrolling style.
     *
     * @return  string
     */
    public static function get_Default_Scrolling_Style()
    {
        return self::$_default_Scrolling_Style;
    }

    /**
     * Get the default item count per page
     *
     * @return int
     */
    public static function get_Default_Item_Count_Per_Page()
    {
        return self::$_default_Item_Count_Per_Page;
    }

    /**
     * Set the default item count per page
     *
     * @param int $count
     */
    public static function set_Default_Item_Count_Per_Page($count)
    {
        self::$_default_Item_Count_Per_Page = (int) $count;
    }

    /**
     * Get the default page range
     *
     * @return int
     */
    public static function get_Default_Page_Range()
    {
        return self::$_default_Page_Range;
    }

    /**
     * Set the default page range
     *
     * @param int $count
     */
    public static function set_Default_Page_Range($count)
    {
        self::$_default_Page_Range = (int) $count;
    }

    /**
     * Sets a cache object
     *
     * @param Cache_Core $cache
     */
    public static function set_Cache(Cache_Core $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Sets the default scrolling style.
     *
     * @param  string $scrolling_Style
     */
    public static function set_Default_Scrolling_Style($scrolling_Style = 'Sliding')
    {
        self::$_default_Scrolling_Style = $scrolling_Style;
    }

    /**
     * Enables/Disables the cache for this instance
     *
     * @param bool $enable
     * @return Paginator
     */
    public function set_Cache_Enabled($enable)
    {
        $this->_cache_Enabled = (bool) $enable;
        return $this;
    }

    /**
     * Returns the number of pages.
     *
     * @return integer
     */
    public function count()
    {
        if (!$this->_page_Count)
        {
            $this->_page_Count = $this->_calculate_Page_Count();
        }

        return $this->_page_Count;
    }

    /**
     * Returns the total number of items available.
     *
     * @return integer
     */
    public function get_Total_Item_Count()
    {
        return count($this->get_Adapter());
    }

    /**
     * Clear the page item cache.
     *
     * @param int $page_Number
     * @return Paginator
     */
    public function clear_Page_Item_Cache($page_Number = null)
    {
        if (!$this->_cache_Enabled())
        {
            return $this;
        }

        if (null === $page_Number)
        {
            foreach (self::$_cache->getIdsMatchingTags(array($this->_get_Cache_Internal_Id())) as $id)
            {
                if (preg_match('|' . self::CACHE_TAG_PREFIX . "(\d+)_.*|", $id, $page))
                {
                    self::$_cache->remove($this->_get_Cache_Id($page[1]));
                }
            }
        } else
        {
            $cleanId = $this->_get_Cache_Id($page_Number);
            self::$_cache->remove($cleanId);
        }
        return $this;
    }

    /**
     * Returns the absolute item number for the specified item.
     *
     * @param  integer $relative_Item_Number Relative item number
     * @param  integer $page_Number Page number
     * @return integer
     */
    public function get_Absolute_Item_Number($relative_Item_Number, $page_Number = null)
    {
        $relative_Item_Number = $this->normalize_Item_Number($relative_Item_Number);

        if ($page_Number == null)
        {
            $page_Number = $this->get_Current_Page_Number();
        }

        $page_Number = $this->normalize_Page_Number($page_Number);

        return (($page_Number - 1) * $this->get_Item_Count_Per_Page()) + $relative_Item_Number;
    }

    /**
     * Returns the adapter.
     *
     * @return Paginator_Interface
     */
    public function get_Adapter()
    {
        return $this->_adapter;
    }

    /**
     * Returns the number of items for the current page.
     *
     * @return integer
     */
    public function get_Current_Item_Count()
    {
        if ($this->_current_Item_Count === null)
        {
            $this->_current_Item_Count = $this->get_Item_Count($this->get_Current_Items());
        }

        return $this->_current_Item_Count;
    }

    /**
     * Returns the items for the current page.
     *
     * @return Traversable
     */
    public function get_Current_Items()
    {
        if ($this->_current_Items === null)
        {
            $this->_current_Items = $this->get_Items_By_Page($this->get_Current_Page_Number());
        }

        return $this->_current_Items;
    }

    /**
     * Returns the current page number.
     *
     * @return integer
     */
    public function get_Current_Page_Number()
    {
        return $this->normalize_Page_Number($this->_current_Page_Number);
    }

    /**
     * Sets the current page number.
     *
     * @param  integer $page_Number Page number
     * @return Paginator $this
     */
    public function set_Current_Page_Number($page_Number)
    {
        $this->_current_Page_Number = (integer) $page_Number;
        $this->_current_Items = null;
        $this->_current_Item_Count = null;

        return $this;
    }

    /**
     * Returns an item from a page.  The current page is used if there's no
     * page sepcified.
     *
     * @param  integer $item_Number Item number (1 to item_Count_Per_Page)
     * @param  integer $page_Number
     * @return mixed
     */
    public function get_Item($item_Number, $page_Number = null)
    {
        if ($page_Number == null)
        {
            $page_Number = $this->get_Current_Page_Number();
        } else if ($page_Number < 0)
        {
            $page_Number = ($this->count() + 1) + $page_Number;
        }

        $page = $this->get_Items_By_Page($page_Number);
        $item_Count = $this->get_Item_Count($page);

        if ($item_Count == 0)
        {
            throw new Exception('Page ' . $page_Number . ' does not exist');
        }

        if ($item_Number < 0)
        {
            $item_Number = ($item_Count + 1) + $item_Number;
        }

        $item_Number = $this->normalize_Item_Number($item_Number);

        if ($item_Number > $item_Count)
        {
            throw new Exception('Page ' . $page_Number . ' does not'
                    . ' contain item number ' . $item_Number);
        }

        return $page[$item_Number - 1];
    }

    /**
     * Returns the number of items per page.
     *
     * @return integer
     */
    public function get_Item_Count_Per_Page()
    {
        if (empty($this->_item_Count_Per_Page))
        {
            $this->_item_Count_Per_Page = self::get_Default_Item_Count_Per_Page();
        }

        return $this->_item_Count_Per_Page;
    }

    /**
     * Sets the number of items per page.
     *
     * @param  integer $item_Count_Per_Page
     * @return Paginator $this
     */
    public function set_Item_Count_Per_Page($item_Count_Per_Page = -1)
    {
        $this->_item_Count_Per_Page = (integer) $item_Count_Per_Page;
        if ($this->_item_Count_Per_Page < 1)
        {
            $this->_item_Count_Per_Page = $this->get_Total_Item_Count();
        }
        $this->_page_Count = $this->_calculate_Page_Count();
        $this->_current_Items = null;
        $this->_current_Item_Count = null;

        return $this;
    }

    /**
     * Returns the number of items in a collection.
     *
     * @param  mixed $items Items
     * @return integer
     */
    public function get_Item_Count($items)
    {
        $item_Count = 0;

        if (is_array($items) || $items instanceof Countable)
        {
            $item_Count = count($items);
        } else
        { // $items is something like LimitIterator
            $item_Count = iterator_count($items);
        }

        return $item_Count;
    }

    /**
     * Returns the items for a given page.
     *
     * @return Traversable
     */
    public function get_Items_By_Page($page_Number)
    {
        $page_Number = $this->normalize_Page_Number($page_Number);

        if ($this->_cache_Enabled())
        {
            $data = self::$_cache->load($this->_get_Cache_Id($page_Number));
            if ($data !== false)
            {
                return $data;
            }
        }

        $offset = ($page_Number - 1) * $this->get_Item_Count_Per_Page();

        $items = $this->_adapter->get_Items($offset, $this->get_Item_Count_Per_Page());

        if (!$items instanceof Traversable)
        {
            $items = new ArrayIterator($items);
        }

        if ($this->_cache_Enabled())
        {
            self::$_cache->save($items, $this->_get_Cache_Id($page_Number), array($this->_get_Cache_Internal_Id()));
        }

        return $items;
    }

    /**
     * Returns a foreach-compatible iterator.
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->get_Current_Items();
    }

    /**
     * Returns the page range (see property declaration above).
     *
     * @return integer
     */
    public function get_Page_Range()
    {
        if (null === $this->_page_Range)
        {
            $this->_page_Range = self::get_Default_Page_Range();
        }

        return $this->_page_Range;
    }

    /**
     * Sets the page range (see property declaration above).
     *
     * @param  integer $page_Range
     * @return Paginator $this
     */
    public function set_Page_Range($page_Range)
    {
        $this->_page_Range = (integer) $page_Range;

        return $this;
    }

    /**
     * Returns the page collection.
     *
     * @param  string $scrolling_Style Scrolling style
     * @return array
     */
    public function get_Pages($scrolling_Style = null)
    {
        if ($this->_pages === null)
        {
            $this->_pages = $this->_create_Pages($scrolling_Style);
        }

        return $this->_pages;
    }

    /**
     * Returns a subset of pages within a given range.
     *
     * @param  integer $lower_Bound Lower bound of the range
     * @param  integer $upper_Bound Upper bound of the range
     * @return array
     */
    public function get_Pages_In_Range($lower_Bound, $upper_Bound)
    {
        $lower_Bound = $this->normalize_Page_Number($lower_Bound);
        $upper_Bound = $this->normalize_Page_Number($upper_Bound);

        $pages = array();

        for ($page_Number = $lower_Bound; $page_Number <= $upper_Bound; $page_Number++)
        {
            $pages[$page_Number] = $page_Number;
        }

        return $pages;
    }

    /**
     * Returns the page item cache.
     *
     * @return array
     */
    public function get_Page_Item_Cache()
    {
        $data = array();
        if ($this->_cache_Enabled())
        {
            foreach (self::$_cache->getIdsMatchingTags(array($this->_get_Cache_Internal_Id())) as $id)
            {
                if (preg_match('|' . self::CACHE_TAG_PREFIX . "(\d+)_.*|", $id, $page))
                {
                    $data[$page[1]] = self::$_cache->load($this->_get_Cache_Id($page[1]));
                }
            }
        }
        return $data;
    }

    /**
     * Brings the item number in range of the page.
     *
     * @param  integer $item_Number
     * @return integer
     */
    public function normalize_Item_Number($item_Number)
    {
        $item_Number = (integer) $item_Number;

        if ($item_Number < 1)
        {
            $item_Number = 1;
        }

        if ($item_Number > $this->get_Item_Count_Per_Page())
        {
            $item_Number = $this->get_Item_Count_Per_Page();
        }

        return $item_Number;
    }

    /**
     * Brings the page number in range of the paginator.
     *
     * @param  integer $page_Number
     * @return integer
     */
    public function normalize_Page_Number($page_Number)
    {
        $page_Number = (integer) $page_Number;

        if ($page_Number < 1)
        {
            $page_Number = 1;
        }

        $page_Count = $this->count();

        if ($page_Count > 0 && $page_Number > $page_Count)
        {
            $page_Number = $page_Count;
        }

        return $page_Number;
    }

    /**
     * Tells if there is an active cache object
     * and if the cache has not been desabled
     *
     * @return bool
     */
    protected function _cache_Enabled()
    {
        return ((self::$_cache !== null) && $this->_cache_Enabled);
    }

    /**
     * Makes an Id for the cache
     * Depends on the adapter object and the page number
     *
     * Used to store item in cache from that Paginator instance
     *  and that current page
     *
     * @param int $page
     * @return string
     */
    protected function _get_Cache_Id($page = null)
    {
        if ($page === null)
        {
            $page = $this->get_Current_Page_Number();
        }
        return self::CACHE_TAG_PREFIX . $page . '_' . $this->_get_Cache_Internal_Id();
    }

    /**
     * Get the internal cache id
     * Depends on the adapter and the item count per page
     *
     * Used to tag that unique Paginator instance in cache
     *
     * @return string
     */
    protected function _get_Cache_Internal_Id()
    {
        return md5(serialize(array(
                            $this->get_Adapter(),
                            $this->get_Item_Count_Per_Page()
                        )));
    }

    /**
     * Calculates the page count.
     *
     * @return integer
     */
    protected function _calculate_Page_Count()
    {
        return (integer) ceil($this->get_Adapter()->count() / $this->get_Item_Count_Per_Page());
    }

    /**
     * Creates the page collection.
     *
     * @param  string $scrolling_Style Scrolling style
     * @return stdClass
     */
    protected function _create_Pages($scrolling_Style = null)
    {
        $page_Count = $this->count();
        $current_Page_Number = $this->get_Current_Page_Number();

        $pages = new stdClass();
        $pages->page_Count = $page_Count;
        $pages->item_Count_Per_Page = $this->get_Item_Count_Per_Page();
        $pages->first = 1;
        $pages->current = $current_Page_Number;
        $pages->last = $page_Count;

        // Previous and next
        if ($current_Page_Number - 1 > 0)
        {
            $pages->previous = $current_Page_Number - 1;
        }

        if ($current_Page_Number + 1 <= $page_Count)
        {
            $pages->next = $current_Page_Number + 1;
        }

        // Pages in range
        $scrolling_Style = $this->_load_Scrolling_Style($scrolling_Style);
        $pages->pages_In_Range = $scrolling_Style->get_Pages($this);
        $pages->first_Page_In_Range = min($pages->pages_In_Range);
        $pages->last_Page_In_Range = max($pages->pages_In_Range);

        // Item numbers
        if ($this->get_Current_Items() !== null)
        {
            $pages->current_Item_Count = $this->get_Current_Item_Count();
            $pages->item_Count_Per_Page = $this->get_Item_Count_Per_Page();
            $pages->total_Item_Count = $this->get_Total_Item_Count();
            $pages->first_Item_Number = (($current_Page_Number - 1) * $this->get_Item_Count_Per_Page()) + 1;
            $pages->last_Item_Number = $pages->first_Item_Number + $pages->current_Item_Count - 1;
        }

        return $pages;
    }

    /**
     * Loads a scrolling style.
     *
     * @param string $scrolling_Style
     * @return Paginator_Scrollingstyle_Interface
     */
    protected function _load_Scrolling_Style($scrolling_Style = null)
    {
        if ($scrolling_Style === null)
        {
            $scrolling_Style = self::$_default_Scrolling_Style;
        }

        switch (strtolower($scrolling_Style))
        {
            case 'all':
            case 'elastic':
            case 'jumping':
            case 'sliding':
                $className = 'Paginator_Scrollingstyle_' . ucfirst($scrolling_Style);
                return new $className();

            case 'null':
            default:
                throw new Exception('Scrolling style must be a class ' .
                        'name or object implementing Paginator_Scrollingstyle_Interface');
        }
    }

    /**
     * Set URL and options
     * Default Page Query Name = 'page'
     * @param string $url
     * @param string $page_Query_Name
     * @param string $option_Query
     * @return boolean true
     */
    public function set_Option_Queries($url = null, $page_Query_Name = null, $option_Query = null)
    {
        $this->_url = $url ? $url : $this->_default_Url;
        $this->_page_Query_Name = $page_Query_Name ? $page_Query_Name : $this->_default_Page_Query_Name;
        $this->_option_Query = $option_Query ? $option_Query : $this->_default_Option_Query;
        return true;
    }

    /**
     * Render the pagination.
     * Scrolling style: Sliding(default), Elastic, Jumping, All
     * @param  string $scrolling_Style = null
     * @return rendered_View
     */
    public function render($scrolling_Style = null)
    {
        if ($this->_page_Query_Name == null)
        {
            $this->set_Option_Queries();
        }

        $pages = $this->get_Pages($scrolling_Style);
        if ($pages->page_Count > 0)
        {
            $url1 = $this->_url . '?' . $this->_page_Query_Name . '=';
            $url2 = $this->_option_Query ? '&' . $this->_option_Query : '';
            $first = ($pages->first == $pages->current) ? '' : $url1 . $pages->first . $url2;
            $previous = ($pages->first == $pages->current) ? '' : $url1 . $pages->previous . $url2;
            $next = ($pages->last == $pages->current) ? '' : $url1 . $pages->next . $url2;
            $last = ($pages->last == $pages->current) ? '' : $url1 . $pages->last . $url2;
            foreach ($pages->pages_In_Range as $key => $value)
            {
                $pages_In_Range[$value] = ($value == $pages->current) ? '' : $url1 . $value . $url2;
            }
        } else
        {
            $first = $previous = $pages_In_Range[1] = $next = $last = '';
        }

        $view = View::factory('paginator/pagination');
        $view->first = $first;
        $view->previous = $previous;
        $view->pages_In_Range = $pages_In_Range;
        $view->next = $next;
        $view->last = $last;

        return $view->render();
    }

}
