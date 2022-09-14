@extends('layouts.app')

@section('content')
@php

@endphp

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{route('product.filter')}}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                    @php
                        $variants = App\Models\Variant::orderBy('title')->get();
                    @endphp
                    
                    @foreach ($variants as $variant)
                    <optgroup label = "{{$variant->title}}">
                        @php
                            $variant_child = App\Models\ProductVariant::where('variant_id',$variant->id)->groupBy('variant')->get();
                        @endphp
                        @foreach ($variant_child as $child)
                        <option>{{$child->variant}}</option>
                        
                        @endforeach
                        
                        
                    @endforeach
                    
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($products as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$item->title}} <br> Created at : @php echo date('d-M-Y', strtotime($item->created_at));  @endphp</td>
                        <td>{{$item->description}}</td>
                        <td>
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                
                                    
                                    @if(isset($item->ProductVariantPrice))
                                    @php
                                        $count = count($item->ProductVariantPrice);
                                        $counter = 1;
                                    @endphp
                                    
                                    @foreach ($item->ProductVariantPrice as $VariantPrice)
                                    <dt class="col-sm-3 pb-0">
                                        @php
                                            if(!is_null($VariantPrice->product_variant_one)) {
                                                $variant_one = App\Models\ProductVariant::find($VariantPrice->product_variant_one);
                                                echo $variant_one->variant."/ ";
                                            }
                                            if(!is_null($VariantPrice->product_variant_two)) {
                                                $variant_two = App\Models\ProductVariant::find($VariantPrice->product_variant_two);
                                                echo $variant_two->variant."/ ";
                                            }
                                            if(!is_null($VariantPrice->product_variant_three)) {
                                                $variant_three = App\Models\ProductVariant::find($VariantPrice->product_variant_three);
                                                echo $variant_three->variant;
                                            }
                                            
                                        @endphp
                                       
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 pb-0">Price : {{ number_format($VariantPrice->price,2) }}</dt>
                                            <dd class="col-sm-8 pb-0">InStock : {{ number_format($VariantPrice->stock,2) }}</dd>
                                        </dl>
                                        
                                    </dd>
                                    @php
                                        $counter++;
                                    @endphp
                                    @endforeach
                                    @endif
                                    
                                
                                
                            </dl>
                            <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                    @endforeach
                    

                    </tbody>

                </table>
                {{ $products->links() }}
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing  {{($products->currentpage()-1)*$products->perpage()+1}} to {{$products->currentpage()*$products->perpage()}} out of {{$products->total()}}</p>
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
    </div>

@endsection
