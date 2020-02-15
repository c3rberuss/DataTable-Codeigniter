# # Configurations

~~~
$config = array(  
   "table" => "your table name",  
   "primary_key" => "primary key of the table",  
   "fields" => array(   //the order is very very important!!!
          "name of field" => function ($value) {  
		 return $value;  //return value of the field
	   },  
          "permission" => function ($value) {  
	         return $value;  
	   },  
 ),  
   "actions" => array(  //Actions for the rows
	      array(  
		 "url" => "delete/{:id}",   //url action and don't replace {:id} 
		 "text" => "Eliminar",     //text show 
		 "permission" => "delete_permission",  //permission required
	      ),  
	      array(  //another config for action
		 "url" => "editar/{:id}",  
		 "text" => "Editar",  
		 "permission" => "edit_permission",  
		 "field" => "id_permission", 
		 "formatter" => function($value){  
		     return $value % 2 == 0 ? "ONE" : "TWO";  
	         }  
	      ),  
   )  
);  
~~~

## For generate the DataTable use

~~~
Dt::generate($config); //call in end of function of your controller
~~~
