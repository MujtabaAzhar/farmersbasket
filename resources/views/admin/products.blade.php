@extends('layouts.admin')

@section('content')
   
   <div class="main-content-inner">
                            <div class="main-content-wrap">
                                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                                    <h3>All Products</h3>
                                    <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                                        <li>
                                            <a href="{{ route('admin.index') }}">
                                                <div class="text-tiny">Dashboard</div>
                                            </a>
                                        </li>
                                        <li>
                                            <i class="icon-chevron-right"></i>
                                        </li>
                                        <li>
                                            <div class="text-tiny">All Products</div>
                                        </li>
                                    </ul>
                                </div>

                                <div class="wg-box">
                                    <div class="flex items-center justify-between gap10 flex-wrap">
                                        <div class="wg-filter flex-grow">
                                            <form class="form-search">
                                                <fieldset class="name">
                                                    <input type="text" placeholder="Search here..." class="" name="name"
                                                        tabindex="2" value="" aria-required="true" required="">
                                                </fieldset>
                                                <div class="button-submit">
                                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                        <a class="tf-button style-1 w208" href="{{ route('admin.product.add') }}"><i
                                                class="icon-plus"></i>Add New</a>
                                    </div>
                                  
                                    <div class="table-responsive">
                                            @if(Session::has('status')) 
                                            <div class="alert alert-success text-center">{{ Session::get('status') }}</div>
                                        @endif
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Price Range</th>
                                                    <th>Variants</th>
                                                    <th>Category</th>
                                                    <th>Brand</th>
                                                    <th>Featured</th>
                                                    <th>Stock</th>
                                                    <th>Total Qty</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($products as $product)
                                                <tr>
                                                    <td>{{ $product->id }}</td>
                                                    <td class="pname">
                                                        <div class="image">
                                                            <img src="{{ asset('uploads/products/thumbnails/'.$product->image) }}" alt="" class="image">
                                                        </div>
                                                        <div class="name">
                                                            <a href="#" class="body-title-2">{{ $product->name }}</a>
                                                            <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php $minP = $product->variants->min('price'); $maxP = $product->variants->max('price'); @endphp
                                                        @if($minP)
                                                            Rs {{ number_format($minP, 0) }}{{ $minP != $maxP ? ' – ' . number_format($maxP, 0) : '' }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $product->variants->count() }} variant{{ $product->variants->count() != 1 ? 's' : '' }}</td>
                                                    <td>{{ $product->category->name }}</td>
                                                    <td>{{ $product->brand->name }}</td>
                                                    <td>{{ $product->featured == 0 ? 'No' : 'Yes' }}</td>
                                                    <td>
                                                        <span class="badge {{ $product->stock_status === 'instock' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $product->stock_status === 'instock' ? 'In Stock' : 'Out of Stock' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $product->total_stock }}</td>
                                                    <td>
                                                        <div class="list-icon-function">
                                                            <a href="{{ route('admin.product.quantity') }}" title="Manage Quantities">
                                                                <div class="item text-primary edit">
                                                                    <i class="icon-plus"></i>
                                                                </div>
                                                            </a>
                                                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}" title="Edit Product">
                                                                <div class="item edit">
                                                                    <i class="icon-edit-3"></i>
                                                                </div>
                                                            </a>
                                                            <form action="{{ route('admin.product.delete', ['id' => $product->id]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <div class="item text-danger delete">
                                                                    <i class="icon-trash-2"></i>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="divider"></div>
                                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                                        {{ $products->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
@endsection

@push('scripts')
    <script>
        $(function(){
            $(`.delete`).on('click',function(e){
                e.preventDefault();
                var selectedForm = $(this).closest('form');
                swal({
                    title: `Are you sure?`,
                    text: `You want to delete this record?`,
                    type: `warning`,
                    buttons: [`No!`, `Yes!`],
                    confirmButtonColor: '#dc3545'
                }).then(function (result) {
                    if (result) {
                        selectedForm.submit();  
                    }
                });                             
            });
        });
    </script>
@endpush