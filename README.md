Very convenient Pagination Module for kohana 3.3
================================================

This VPM is codes modification of Zend Paginator. It's input is Database_MySQL_Result, output is paginator and rendered view of pagination. It's very convenient!

Most simple usage:
------------------
    // Model
    $select = DB::select('*')
    return $select->execute();
    
    // Controller
    $model = Model::factory('test12_posts');
    $select = $model->selectblogs($array);
    $paginator = Paginator::factory($select);
    $paginator->set_Current_Page_Number($page);
    $view = View::factory('test12/edit/posts');
    $view->data = $paginator;
    $view->pagination = $paginator->render();
    
    // View
    <?PHP
           foreach ($data as $d) {
               echo $d['post_title']."<br />";
           }
           echo $pagination;
    ?>

Options for example:
--------------------
    $paginator->set_Option_Queries('http://example.com/kohana', 'PageNumber', 'option=draft');
    $paginator->set_Item_Count_Per_Page(30);  // Default = 10
    $paginator->render('Elastic');  // Scrolling style
    $pagecount = $paginator->count();

Install:
--------
* Download and copy paginator folder under `kohana/modules/` folder.
* Edit your `bootstrap.php` file and add next line.
* `'paginator'  => MODPATH.'paginator',`

View file can be modified:
--------------------------
1. Copy `modules/paginator/views/paginator/pagination.php` under `application/views/paginator/` folder.
2. Modify it as you like.

Methods:
--------
    clear_Page_Item_Cache($page_Number = null)
    count()
    factory($data)
    get_Absolute_Item_Number($relative_Item_Number, $page_Number = null)
    get_Adapter()
    get_Current_Item_Count()
    get_Current_Items()
    get_Current_Page_Number()
    get_Default_Item_Count_Per_Page()
    get_Default_Page_Range()
    get_Default_Scrolling_Style()
    get_Item($item_Number, $page_Number = null)
    get_Item_Count($items)
    get_Item_Count_Per_Page()
    get_Items_By_Page($page_Number)
    get_Page_Item_Cache()
    get_Page_Range()
    get_Pages($scrolling_Style = null)
    get_Pages_In_Range($lower_Bound, $upper_Bound)
    get_Total_Item_Count()
    getIterator()
    normalize_Item_Number($item_Number)
    normalize_Page_Number($page_Number)
    render($scrolling_Style = null)
    set_Cache(Cache_Core $cache)
    set_Cache_Enabled($enable)
    set_Current_Page_Number($page_Number)
    set_Default_Item_Count_Per_Page($count)
    set_Default_Page_Range($count)
    set_Default_Scrolling_Style($scrolling_Style = 'Sliding')
    set_Item_Count_Per_Page($item_Count_Per_Page = -1)
    set_Option_Queries($url = null, $page_Query_Name = null, $option_Query = null)
    set_Page_Range($page_Range)