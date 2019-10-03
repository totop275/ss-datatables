# Server Side Datatables Handler for Laravel
A handler for datatables server side's request

## About ss-datatables
ss-datatables contains php traits which contain a view loader for server side datatables requests. A helper to handle ajax requests from [datatables](https://datatables.net).

## What ss-datatables do?
Basically, ss-datatables only add a method named `list` to the controller that is used to respond to datatables ajax request.

## Working Feature
* List data
* Pagination
* Multiple/single field filter/search
* Multiple/single field short
* Search
* Data limit
* Dinamic column request

## Installation

Use the depency manager [composer](https://getcomposer.org) to install ss-datatables.

```bash
composer require totop275/ss-datatables
```

## Usage

1. Include Datatables trait to your controller
```php
...
use Kitablog\Traits\Datatables;
...
class MyController extends Controller{
  use Datatables;
...
```
2. Define shared table data
ss-datatables uses object properties to define columns and tables used to serve datatables. Below are the properties used by ss-datatables.

* **column** : 
  > Defines the tables, columns, and table relationships that are used.
  * example:
    * ```php
      $this->column=[ 
       'alias' => ['item_name'=>'items.name','item_unitime'=>'concat(items.uq,UNIX_TIMESTAMP())'],
       'available' => ['name','gender','item_name','item_unique'],
       'searchable' => ['name','item_name'],
       'table' => '['items'=>['users.item','=','items.id'],'address'=>['address.user','=','users.id']]'
      ]```
  * **column[alias]**
    * Defines a column name alias. `key` for alias, `value` for original references
    * rule
      * `[alias1=>reference1,alias2=>reference2,....]`
    * example
      * `['item_name'=>'items.name','item_unitime'=>'concat(items.uq,UNIX_TIMESTAMP())']`
  * **column[available]**
    * Defines a list of columns that can be used
    * rule
      * `[column1,column2,column3,...]`
    * example
      * `['name','gender','item_name','item_unique']`
  * **column[searchable]**
    * Defines a list of columns that can be searched when doing a global search
    * rule
      * `[column1,colum2,column3,...]`
    * example
      * `['name','item_name']`
  * **column[table]**
    * Defines table relationship that required for data request
    * rule
      * `[table_alias=>[origin_reference_column,operator,target_reference_column],...]`
    * example
      * `['items'=>['users.item','=','items.id'],'address'=>['address.user','=','users.id']]`

* **model** 
  * Defines main eloquent model of requested data
  * example
    * `$this->model=\App\User::class`
* **shouldSelect**
  * Defines a list of columns that will always be returned on every request
  * example
    * `$this->shouldSelect=['users.id']`
* **actionBtn**
  * Defines the action buttons on the table (if any).
  * example
    ```php
      $this->actionBtn=[
        ['<a href="'.url('user/item').'/%s/edit" class="btn btn-sm btn-flat btn-info">Edit</a>','id'],
        [' <btn class="btn btn-sm btn-flat btn-danger btnDelete" data-id="%s">Delete</btn>','id']
      ];```

3. Create a route for ss-datatables
  **create a route for ss-datatables**
  `Route::get('ajax','YourController@list')`
  **or branching on some route**
  ```php
  ...
  use Kitablog\Traits\Datatables;
  ...
  class MyController extends Controller{
    use Datatables;
  ...
  public function showAll(Request $request){
    if($request->input('ajax')){
      return $this->list($request);
    }
    ...
  }
  ```

## Client side usage
Read detail on [datatables documentation](https://datatables.net/manual/server-side).

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[GPL-3.0](https://www.gnu.org/licenses/gpl-3.0.html)
