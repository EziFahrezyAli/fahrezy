<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->sendResponse(Category::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only(['name']);
            $validator = Validator::make($requestData, ['name' => 'required']);
            if ($validator->fails()) throw new ValidationException($validator);

            $category = new Category();
            $category->fill($requestData);
            $category->save();

            DB::commit();
            return $this->sendResponse($category, 'Success create Categories!');
        } catch (ValidationException $err) {
            DB::rollBack();
            return $this->sendError($err->validator->errors(), 'Validation Errors!');
        } catch (\Error $err) {
            DB::rollBack();
            return $this->sendError($err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->sendResponse($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only(['name']);
            $validator = Validator::make($requestData, ['name' => 'required']);
            if ($validator->fails()) throw new ValidationException($validator);

            $category->name = $request->name;
            $category->save();

            DB::commit();
            return $this->sendResponse($category, 'Success update Categories!');
        } catch (ValidationException $err) {
            DB::rollBack();
            return $this->sendError($err->validator->errors(), 'Validation Errors!');
        } catch (\Error $err) {
            DB::rollBack();
            return $this->sendError($err->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->sendResponse($category, 'Success delete Category');
    }
}
