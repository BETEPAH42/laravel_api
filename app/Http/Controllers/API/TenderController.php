<?php

namespace App\Http\Controllers\API;

use App\Models\Tender;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Carbon\Carbon;

class TenderController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenders = [];
        if (($open = fopen(database_path() . "/migrations/test_task_data.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($open)) !== FALSE) {
                if(in_array('Внешний код',$data)) {
                    continue;
                }
                echo "<pre>" . print_r(  [Tender::class,
                        'xml_id' => trim($data[0]),
                        'number' => trim($data[1]),
                        'status' => trim($data[2]) == 'Открыто',
                        'name' => trim($data[3]),
                        'update' => Carbon::parse($data[4])->timestamp,
                    ],true) . "</pre>";
                $test = Tender::create([
                    'xml_id' => $data[0],
                    'number' => $data[1],
                    'status' => trim($data[2]) == 'Открыто',
                    'name' => $data[3],
                    'update' => Carbon::parse($data[4])->timestamp
                ]);

                die();
//                $tenders[] = [
//                    'xml_id' => $data[0],
//                    'number' => $data[1],
//                    'status' => trim($data[2]) == 'Открыто',
//                    'name' => $data[3],
//                    'update' => Carbon::parse($data[4])->timestamp
//                ];
            }
            fclose($open);
        }
        $tenders = Tender::all();
        return $this->sendResponse($tenders->toArray(), 'Tenders retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'xml_id' => 'required',
            'name' => 'required',
            'number' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $tender = Tender::create($input);
        return $this->sendResponse($tender->toArray(), 'Tender created successfully.');
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tender = Product::find($id);
        if (is_null($tender)) {
            return $this->sendError('Tender not found.');
        }
        return $this->sendResponse($tender->toArray(), 'Tender retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tender $tender)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'xml_id' => 'required',
            'name' => 'required',
            'number' => 'required',
            'status' => 'boolean'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if(isset($input['status'])) {
            $tender->status = $input['status'];
        }
        $tender->name = $input['name'];
        $tender->status = $input['number'];
        $tender->status = $input['name'];
        $tender->save();
        return $this->sendResponse($tender->toArray(), 'Tender updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param Tender $tender
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tender $tender)
    {
        $tender->delete();
        return $this->sendResponse($tender->toArray(), 'Tender deleted successfully.');
    }
}
