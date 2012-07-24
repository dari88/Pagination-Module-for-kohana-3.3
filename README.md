Very convenient Pagination Module for kohana 3.3
================================================

V1.1 (2012/07/24)
--------------------

- Followed kohana's coding style written in kohana 3.2 user guide "Conventions and Coding Style".
- The method name was changed to lower case except for the name derived from the core PHP.

This VPM is codes modification of Zend Paginator. It's input is `Database_MySQL_Result`, output is paginator and rendered view of pagination. It's very convenient!

Most simple usage:
------------------
    // Model
    $select = DB::select('*')
    return $select->execute();
    
    // Controller
    $model = Model::factory('test12_posts');
    $select = $model->selectblogs($array);
    $paginator = Paginator::factory($select);
    $paginator->set_current_page_number($page);
    $view = View::factory('test12/edit/posts');
    $view->data = $paginator;
    $view->pagination = $paginator->render();
    
    // View
    <?PHP
           foreach ($data as $d) {
               echo $d['post_title'];
           }
           echo $pagination;
    ?>

Options for example:
--------------------
    $paginator->set_option_queries('http://example.com/kohana', 'PageNumber', 'option=draft');
    $paginator->set_item_count_per_page(30);  // Default = 10
    $paginator->render('Elastic');  // Scrolling style
    $pagecount = $paginator->count();

Install:
--------
* Download and copy paginator folder under kohana/modules/ folder.
* Edit your bootstrap.php file and add next line.
* `'paginator'  => MODPATH.'paginator',`

View file can be modified:
--------------------------
1. Copy `modules/paginator/views/paginator/pagination.php` under `application/views/paginator/` folder.
2. Modify it as you like.

Methods:
--------
    clear_page_item_cache($page_number = null)
    count()
    factory($data)
    get_absolute_item_number($relative_item_number, $page_number = null)
    get_adapter()
    get_current_item_count()
    get_current_items()
    get_current_page_number()
    get_default_item_count_per_page()
    get_default_page_range()
    get_default_scrolling_style()
    get_item($item_number, $page_number = null)
    get_item_count($items)
    get_item_count_per_page()
    get_items_by_page($page_number)
    get_page_item_cache()
    get_page_range()
    get_pages($scrolling_style = null)
    get_pages_in_range($lower_bound, $upper_bound)
    get_total_item_count()
    getIterator()
    normalize_item_number($item_number)
    normalize_page_number($page_number)
    render($scrolling_style = null)
    set_cache(cache_core $cache)
    set_cache_enabled($enable)
    set_current_page_number($page_number)
    set_default_item_count_per_page($count)
    set_default_page_range($count)
    set_default_scrolling_style($scrolling_style = 'Sliding')
    set_item_count_per_page($item_count_per_page = -1)
    set_option_queries($url = null, $page_query_name = null, $option_query = null)
    set_page_range($page_range)

