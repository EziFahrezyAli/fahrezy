<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->sendResponse(Product::with('category')->get());
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
            $requestData = $request->only(['name', 'description', 'price', 'image', 'category_id', 'expired_at']);

            $validator = Validator::make($requestData, [
                'name' => 'required',
                'description' => 'nullable',
                'price' => 'required|integer|gt:0',
                'image' => 'nullable|image|max:2048|mimes:png,jpg,jpeg',
                'category_id' => 'required',
                'expired_at' => 'required|date',
            ]);
            if ($validator->fails()) throw new ValidationException($validator);

            $checkCategory = Category::find($request->category_id);
            if (!$checkCategory) throw ValidationException::withMessages(['category_id' => 'Category not found!']);

            $expiredAt = Carbon::parse($request->expired_at, 'Asia/Jakarta')->format('Y-m-d');
            $requestData['expired_at'] = $expiredAt;

            $imageName = null;
            if ($request->hasFile('image')) {
                $path = $request->image->store('product-images', 'public');
                $imageName = basename($path);
            }

            $product = new Product();
            $product->fill($requestData);
            $product->image = $imageName;
            $product->modified_by = auth()->user()->email;
            $product->save();

            DB::commit();
            return $this->sendResponse($product->load('category'), 'Success create Products');
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
    public function show(Product $product)
    {
        return $this->sendResponse($product->load('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only(['name', 'description', 'price', 'image', 'category_id', 'expired_at']);

            $validator = Validator::make($requestData, [
                'name' => 'required',
                'description' => 'nullable',
                'price' => 'required|integer|gt:0',
                'image' => 'nullable|image|max:2048|mimes:png,jpg,jpeg',
                'category_id' => 'required',
                'expired_at' => 'required|date',
            ]);
            if ($validator->fails()) throw new ValidationException($validator);

            $checkCategory = Category::find($request->category_id);
            if (!$checkCategory) throw ValidationException::withMessages(['category_id' => 'Category not found!']);

            $expiredAt = Carbon::parse($request->expired_at, 'Asia/Jakarta')->format('Y-m-d');
            $requestData['expired_at'] = $expiredAt;

            $imageName = null;
            if ($request->hasFile('image')) {
                if (!is_null($product->image)) unlink(storage_path("app/public/product-images/" . $product->image));
                $path = $request->image->store('product-images', 'public');
                $imageName = basename($path);
            } else {
                $imageName = $product->image;
            }

            $product->fill($requestData);
            $product->image = $imageName;
            $product->modified_by = auth()->user()->email;
            $product->save();

            DB::commit();
            return $this->sendResponse($product->load('category'), 'Success update Products');
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
    public function destroy(Product $product)
    {
        if (!is_null($product->image)) unlink(storage_path("app/public/product-images/" . $product->image));
        $product->delete();

        return $this->sendResponse($product->load('category'), 'Success delete Products');
    }
}
