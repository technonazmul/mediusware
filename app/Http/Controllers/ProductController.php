<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\ProductImage;
use App\Models\Variant;
use Illuminate\Http\Request;
use Image;
use File;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $product = new Product;
        $product->title = $request->title;
        $product->sku = $request->sku;
        $product->description = $request->description;
        //$product->description = json_encode($request->product_variant) . json_encode($request->product_variant_prices);
        $product->save();
        if (!empty($request->product_variant)) {
            $count = count($request->product_variant);
            $product_variants = $request->product_variant;
            $product_variants_collection = [];
            for ($i = 0; $i < $count; $i++) {
                $product_variant_single = $product_variants[$i];
                $count_tags = count($product_variant_single['tags']);

                for ($j = 0; $j < $count_tags; $j++) {
                    $productVarient = new ProductVariant;
                    $productVarient->variant = $product_variant_single['tags'][$j];
                    $productVarient->variant_id = $product_variant_single['option'];
                    $productVarient->product_id = $product->id;
                    $productVarient->save();
                    $product_variants_collection[$productVarient->id] = $productVarient->variant;
                }
            }
        }

        if (!empty($request->product_variant_prices)) {
            $count = count($request->product_variant_prices);
            $product_variant_pricess = $request->product_variant_prices;
            for ($i = 0; $i < $count; $i++) {
                $product_variant_price_new = new ProductVariantPrice;
                $product_variant_prices_single = $product_variant_pricess[$i];
                $product_variant_prices_title_array = explode('/', $product_variant_prices_single['title'], -1);
                $product_variant_prices_count = count($product_variant_prices_title_array);
                for ($k = 0; $k < $product_variant_prices_count; $k++) {
                    $product_variant_prices_title_single = $product_variant_prices_title_array[$k];
                    $variants_id_find = array_search($product_variant_prices_title_single, $product_variants_collection);

                    if ($k == 0) {
                        $product_variant_price_new->product_variant_one = $variants_id_find;
                    } elseif ($k == 1) {
                        $product_variant_price_new->product_variant_two = $variants_id_find;
                    } elseif ($k == 2) {
                        $product_variant_price_new->product_variant_three = $variants_id_find;
                    }
                }
                $product_variant_price_new->price = $product_variant_prices_single['price'];
                $product_variant_price_new->stock = $product_variant_prices_single['stock'];
                $product_variant_price_new->product_id  = $product->id;
                $product_variant_price_new->save();
            }
        }

        if (isset($request->product_image) && count($request->product_image) > 0) {
            $i = 1;
            foreach ($request->product_image as $image) {
                //make image name
                $img = $image->getClientOriginalName() . '-' . time() . $i . '.' . $image->getClientOriginalExtension();
                //image location
                $location = public_path('images/' . $img);

                Image::make($image)->save($location);

                $product_image = new ProductImage;

                $product_image->file_path = $img;
                $product_image->product_id = $product->id;

                $product_image->save();
                $i++;
            }
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
    public function filter(Request $request)
    {
        //$products = Product::rightJoin('product_variants', 'product_variants.product_id', '=', 'products.id')->orWhere('product_variants.variant', 'LIKE', '%' . $request->variant . '%')->orWhere('products.title', 'LIKE', '%' . $request->title . '%')->orwhere('products.created_at', $request->date)->orderBy('products.id', 'desc')->paginate(12);

        $products = Product::join('product_variants', 'product_variants.product_id', '=', 'products.id')
            ->join('product_variant_prices', 'product_variant_prices.product_id', '=', 'products.id')->where('product_variants.variant', 'LIKE', '%' . $request->variant . '%')
            ->where('products.title', 'LIKE', '%' . $request->title . '%')->orwhere('products.created_at', $request->date)
            ->paginate(10);

        return view('products.index', compact('products'));
    }
}
