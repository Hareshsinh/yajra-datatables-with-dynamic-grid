<?php

namespace App\Http\Controllers;

use App\TableColumnsList;
use App\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columnsData = TableColumnsList::where('slug','users')->where('user_id','1')->first();
        $checkedFields = explode(',',$columnsData->fields);
        if ($request->ajax()) {
            if(!empty($checkedFields)) {
                $checkedFields = array_diff($checkedFields,['DT_RowIndex','action']);
            }
            $data = User::select($checkedFields)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('users',compact('checkedFields'));
    }
}
