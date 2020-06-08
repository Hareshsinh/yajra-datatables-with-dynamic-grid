# yajra-datatables-with-dynamic-grid

##Introduction
This application helps to show your yajra datatable dynamic table row ordering, resizing and grid show hide  

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.5/installation#installation)

Clone the repository

    git clone https://github.com/Hareshsinh/yajra-datatables-with-dynamic-grid.git

Switch to the repo folder

    cd yajra-datatables-with-dynamic-grid
   
 If you have linux system, you can execute below command only in your project root
 


Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Install all the dependencies using composer

    composer install

Generate a new application key

    php artisan key:generate

Run migration for add database tables

    php artisan migrate --seed

for run the project

    php artisan serve

Start the local development server

    http://127.0.0.1::[your_port] (By default show dynamic grid page) 
    http://127.0.0.1::[your_port]/users - dynamic grid with export information
    http://127.0.0.1::[your_port]/drags - dynamic grid with ordering and resizing
    
 ###Kye points before using this feature
 1. This migration function used to make column table where we store table column table slug,order,name,width and user id  
        
         public function up()
            {
                Schema::create('table_columns_lists', function (Blueprint $table) {
                    $table->id();
                    $table->string('slug',50);
                    $table->string('fields',255);
                    $table->string('fieldname',255);
                    $table->string('fieldwidth',255);
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('id')->on('users');
                    $table->tinyInteger('status')->comment("0=> InActive ,1=> Active")->default(0);
                    $table->timestamps();
                });
            }
       

2.controller & blade file predefine column list which we need to hide show..
  
            //data-id = define table header name 
            //value = define database columns name which using for getting database information 
            //$checkedFields  = database value
            
            if not added then please add predefined value in controller like below              
            
            //predefined value for checkbox
             $config = [
                        'DT_RowIndex' => 'No',
                        'name'=>'Name',
                        'email'=> 'Email',
                        'action' => 'Action',
             ];
             
             //check this table is already added or not for this user
             $columnsData = TableColumnsList::where('slug','users')->where('user_id','1')->first();
             if(empty($columnsData)) {
                 $checkedFields = ['DT_RowIndex','name','email','action'];// for checkbox field value
                 $fieldwidth = '118,118,118,118'; // field width
             }
             
             //define column list which we need to hide show
            <span>Show/Hide Columns</span>
            <input type="checkbox" data-id="No" name='hide_columns[]' {{ (in_array('DT_RowIndex',$checkedFields)) ? 'checked': ''}} value='DT_RowIndex'> No
            <input type="checkbox" data-id="Name" name='hide_columns[]' {{ (in_array('name',$checkedFields)) ? 'checked': '' }} value='name'> Name
            <input type="checkbox" data-id="Action" name='hide_columns[]' {{ (in_array('action',$checkedFields)) ? 'checked': '' }} value='action'> Action
            <input type="button" class="btn btn-success btn-sm" id="but_showhide" value='save grids'>// using this id = but_showhide call the function and store the information

3.before get result please remove the information extra value like 
   
        if(!empty($checkedFields)) {
            $checkedFields = array_diff($checkedFields,['DT_RowIndex','action']);
        }
   *important note*: array of $checkedFields value name should be same as select query
        
        $data = User::select($checkedFields)->latest()->get();
     
     
4. Hide & show columns script
       // Hide & show columns
          $('#but_showhide').click(function(){
             var checked_arr = [];var unchecked_arr = [];
        
             // Read all checked checkboxes
             $.each($('input[type="checkbox"]:checked'), function (key, value) {
                checked_arr.push(this.value);
             });
        
             // Read all unchecked checkboxes
             $.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
                unchecked_arr.push(this.value);
             });
        
             // Hide the checked columns
             empDataTable.columns(checked_arr).visible(false);
        
             // Show the unchecked columns
             empDataTable.columns(unchecked_arr).visible(true);
          })
        })
5. resize and drag and drop columns function example 
    please add one library more detail click here (https://github.com/jeffreydwalter/ColReorderWithResize)
    
        <script src="https://cdn.jsdelivr.net/gh/jeffreydwalter/ColReorderWithResize@9ce30c640e394282c9e0df5787d54e5887bc8ecc/ColReorderWithResize.js"></script>
        $(document).ready(function (){
            var table = $('#example').DataTable({
                'ajax': 'your path',
                'dom': 'Rlfrtip'
            });
        });
  
## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email vishal@viitorcloud.com or haresh.sisodiya@viitor.cloud instead of using the issue tracker.

## Credits

- [Haresh Sisodiya](https://github.com/Hareshsinh)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
