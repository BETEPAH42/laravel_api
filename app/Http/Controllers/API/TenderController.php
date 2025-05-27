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
     * Вывод списка с фильтром по наименованию и дате + дата до и после (можно включить и остальные параметры).
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     * Создание сущности
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
     * Получение конкретной записи по id
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {

        $tender = Tender::find($id);
        if (is_null($tender)) {
            return $this->sendError('Tender not found.');
        }
        return $this->sendResponse($tender, 'Tender retrieved successfully.');
    }
}
