<?php

namespace App\Http\Controllers;

use App\Models\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DatabaseController extends Controller
{
    protected $model;
    public function __construct()
    {
        $this->model =new Database();
    }

    public function index()
    {


        return view('welcome');
    }

    public function show_tables($db_name)
    {


        $tables = \DB::select('SHOW TABLES FROM ' . $db_name);


        return view('partials.tables', compact('db_name', 'tables'))->render();


    }


    public function show_columns($db_name, $table_name)
    {


        //dd($db_name,$table_name);
        $columns = \DB::select('SHOW COLUMNS FROM ' . $db_name . '.' . $table_name);
        $dataes = \DB::select('SELECT * FROM ' . $db_name . '.' . $table_name);

        //dd($columns);
        return view('partials.columns', compact('columns', 'dataes','table_name','db_name'))->render();


    }


    public function create_database(Request $request)
    {
        $db_name = $request->db_name;

        $database = \DB::select('CREATE DATABASE IF NOT EXISTS ' . $db_name);

        return view('partials.create_columns', compact('db_name'))->render();



    }

    public function show_create_table($db_name)
    {
        // $db=$db_name;
        return view('partials.create_columns', compact('db_name'))->render();


    }

    public function show_create_database()
    {

        return view('partials.create_database')->render();


    }


    public function create_table(Request $request, $db_name)
    {
        $data = $request->all();
        $columns = $data['columns'];
        //dd($columns);
        $sql = "CREATE TABLE IF NOT EXISTS " . $db_name . "." . $data['table_name'] . "(";

        foreach ($columns as $column) {
            if (!empty($column['field_name'])) {

                if ($column['field_type'] == "INT") {

                        $sql .= " " . $column['field_name'] . " " . $column['field_type'] . "(" . $column['field_length'] . ") " . " " . $column['field_default_type'] . ",";

                } elseif ($column['field_type'] == "DATE") {
                    $sql .= " " . $column['field_name'] . " " . $column['field_type']  . " " . $column['field_default_type'] . ",";

                } else {
                    $characterset_grup = explode('-', $column['field_collation']);
                    $characterset = $characterset_grup[0];
                    $collate = $characterset_grup[1];

                        $sql .= " " . $column['field_name'] . " " . $column['field_type'] . "(" . $column['field_length'] . ") " . " " . "CHARACTER SET" . " " . $characterset . " " . "COLLATE" . " " . $collate . " " . $column['field_default_type'] .
                            ",";

                }
            }

        }
        $sql = rtrim($sql, ",");

        $sql .= ")";

        // dd($db_name,$table,$column_type);


        $table = \DB::statement($sql);

        return response()->json(['success' => 'Table is created']);

        //return redirect('/');


    }

    public function delete_table($table_name,$db_name)
    {
        $this->model->delete_table($table_name,$db_name);
    }


//    protected function validator(array $data)
//    {
//        return Validator::make($data, [
//            'name' => ['required', 'string', 'max:255'],
//            'password' => ['required', 'string',],
//        ]);
//    }
//
//    /**
//     * Create a new user instance after a valid registration.
//     *
//     * @param  array  $data
//     * @return \App\Models\User
//     */
//    protected function create_user(array $data)
//    {
////        CREATE USER 'sammy'@'localhost' IDENTIFIED BY 'password';
////$password= Hash::make($data['password']);
//        $user = \DB::select('CREATE USER '. "'".$data['name']."'".'@'.'localhost '.'IDENTIFIED BY '. "'".$data['password']."'");
//        $grant = \DB::select('GRANT ALL PRIVILEGES ON *.* TO  '. "'".$data['name']."'".'@'.'localhost '.'IDENTIFIED BY '. "'".$data['password']."'"
//
//        );
//    }
}

