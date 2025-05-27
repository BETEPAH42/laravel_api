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
    public function index(Request $request)
    {
        $tenders= Tender::query();
        $tenders->select(['id', 'xml_id', 'number', 'status', 'name', 'update']);
//        if ($request->has('status')) {
//            $tenders->where('status', $request->status);
//        }
//        if ($request->has('xml_id')) {
//            $tenders->where('xml_id', $request->status);
//        }
        if ($request->has('name')) {
            $tenders->where('name', 'like', '%' . $request->name . '%');
        }
//        if ($request->has('number')) {
//            $tenders->where('number', 'like', '%' . $request->number . '%');
//        }
        if ($request->has('date')) {
            $tenders->whereDate('update', Carbon::parse($request->date)->format('Y-m-d') );
        }
        if ($request->has('date_to')) {
            $tenders->whereDate('update', '<', Carbon::parse($request->date)->format('Y-m-d') );
        }
        if ($request->has('date_from')) {
            $tenders->whereDate('update', '>',Carbon::parse($request->date)->format('Y-m-d') );
        }

        return $this->sendResponse($tenders->get(), 'Tenders retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        if ($request->has('update')) {
            $input['update'] =  Carbon::parse($request->update)->format('Y-m-d H:i:s');
        }
        if ($request->has('status')) {
            $input['status'] = in_array($request->status,[1,'true']);
        }
        $validator = Validator::make($input, [
            'xml_id' => 'required|integer|unique:tenders,xml_id',
            'name' => 'required|string|min:10|max:250',
            'number' => 'required|string|min:1|max:50',
            'status' => 'boolean',
            'update' => 'string',
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
    public function show(Request $request, string $id)
    {

        $tender = Tender::find($id);
        if (is_null($tender)) {
            return $this->sendError('Tender not found.');
        }
        return $this->sendResponse($tender, 'Tender retrieved successfully.');
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
