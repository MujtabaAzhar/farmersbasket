@extends('layouts.app')

@section('content')
    <style>
    .pt-90 {
      padding-top: 90px !important;
    }

    .pr-6px {
      padding-right: 6px;
      text-transform: uppercase;
    }

    .my-account .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 40px;
      border-bottom: 1px solid;
      padding-bottom: 13px;
    }

    .my-account .wg-box {
      display: -webkit-box;
      display: -moz-box;
      display: -ms-flexbox;
      display: -webkit-flex;
      display: flex;
      padding: 24px;
      flex-direction: column;
      gap: 24px;
      border-radius: 12px;
      background: var(--White);
      box-shadow: 0px 4px 24px 2px rgba(20, 25, 38, 0.05);
    }

    .bg-success {
      background-color: #40c710 !important;
    }

    .bg-danger {
      background-color: #f44032 !important;
    }

    .bg-warning {
      background-color: #f5d700 !important;
      color: #000;
    }

    .table-transaction>tbody>tr:nth-of-type(odd) {
      --bs-table-accent-bg: #fff !important;

    }

    .table-transaction th,
    .table-transaction td {
      padding: 0.625rem 1.5rem .25rem !important;
      color: #000 !important;
    }
    .order-timeline { position: relative; padding-left: 2rem; }
    .order-timeline::before { content: ''; position: absolute; left: .55rem; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
    .order-timeline-item { position: relative; margin-bottom: 1.25rem; }
    .order-timeline-item::before { content: ''; position: absolute; left: -1.5rem; top: .3rem; width: 12px; height: 12px; border-radius: 50%; background: #6c757d; border: 2px solid #fff; box-shadow: 0 0 0 2px #6c757d; }
    .order-timeline-item.done::before { background: #40c710; box-shadow: 0 0 0 2px #40c710; }
    .order-timeline-item.canceled::before { background: #f44032; box-shadow: 0 0 0 2px #f44032; }
    .order-timeline-item.returned::before { background: #fd7e14; box-shadow: 0 0 0 2px #fd7e14; }

    .table> :not(caption)>tr>th {
      padding: 0.625rem 1.5rem .25rem !important;
      background-color: #6a6e51 !important;
    }

    .table-bordered>:not(caption)>*>* {
      border-width: inherit;
      line-height: 32px;
      font-size: 14px;
      border: 1px solid #e1e1e1;
      vertical-align: middle;
    }

    .table-striped .image {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      height: 50px;
      flex-shrink: 0;
      border-radius: 10px;
      overflow: hidden;
    }

    .table-striped td:nth-child(1) {
      min-width: 250px;
      padding-bottom: 7px;
    }

    .pname {
      display: flex;
      gap: 13px;
    }

    .table-bordered> :not(caption)>tr>th,
    .table-bordered> :not(caption)>tr>td {
      border-width: 1px 1px;
      border-color: #6a6e51;
    }
  </style>

    <main class="pt-90" style="padding-top: 0px;">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Orders</h2>
            <div class="row">
                <div class="col-lg-2">
                       @include('user.account-nav')
                </div>

                <div class="col-lg-10">
                    
                     <div class="wg-box">
                                    <div class="flex items-center justify-between gap10 flex-wrap">
                                        <div class="wg-filter flex-grow">
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
<h5>Order Details</h5>
                                            </div>
                                            <div class="col-6 text-right">
                                        <a class="btn btn-sm btn-danger" href="{{ route('user.orders') }}">Back</a>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                           @if(Session::has('success')) 
                                            <div class="alert alert-success text-center">{{ Session::get('success') }}</div>
                                        @endif
                                        <table class="table table-bordered table-striped table-transaction">
                                           
                                                <tr>
                                                    <th>Order No</th>
                                                    <td>{{ $order->order_number }}</td>
                                                      <th>Phone</th>
                                                    <td>{{ $order->phone }}</td>
                                                      <th>Zip Code</th>
                                                    <td>{{ $order->zip }}</td>
                                                 
                                                </tr>
                                                <tr>
                                                    <th>Order Date</th>
                                                    <td>{{ $order->created_at }}</td>
                                                      <th>Delivered Date</th>
                                                    <td>{{ $order->delivered_at }}</td>
                                                      <th>Cancel Date</th>
                                                    <td>{{ $order->canceled_at }}</td>
                                                 
                                                </tr>
                                                <tr>
                                                    <th>Order Status</th>
                                                    <td colspan="2">
                                                        @php
                                                            $statusLabels = ['ordered'=>'Pending','confirmed'=>'Confirmed','packed'=>'Packed','shipped'=>'Shipped','delivered'=>'Delivered','canceled'=>'Canceled','returned'=>'Returned'];
                                                            $statusColors = ['ordered'=>'warning','confirmed'=>'info','packed'=>'secondary','shipped'=>'primary','delivered'=>'success','canceled'=>'danger','returned'=>'dark'];
                                                        @endphp
                                                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span>
                                                    </td>
                                                    @if($order->tracking_number)
                                                    <th>Tracking #</th>
                                                    <td colspan="2">{{ $order->tracking_number }} @if($order->courier_name)<small class="text-muted">({{ $order->courier_name }})</small>@endif</td>
                                                    @else
                                                    <td colspan="3"></td>
                                                    @endif
                                                </tr>
                                                @if($order->estimated_delivery_date && !in_array($order->status, ['delivered','canceled','returned']))
                                                <tr>
                                                    <th>Est. Delivery</th>
                                                    <td colspan="5">{{ \Carbon\Carbon::parse($order->estimated_delivery_date)->format('d M Y') }}</td>
                                                </tr>
                                                @endif
                                           
                                   
                                        </table>
                                    </div>

                                   
                                </div>
                                
                                <div class="wg-box mt-5">
                                    <div class="flex items-center justify-between gap10 flex-wrap">
                                        <div class="wg-filter flex-grow">
                                            <h5>Items</h5>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th class="text-center">Price</th>
                                                    <th class="text-center">Quantity</th>
                                                    <th class="text-center">SKU</th>
                                                    <th class="text-center">Category</th>
                                                    <th class="text-center">Brand</th>
                                                    <th class="text-center">Options</th>
                                                    <th class="text-center">Return Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($orderItems as $item)
                                                <tr>

                                                    <td class="pname">
                                                        <div class="image">
                                                            <img src="{{asset('uploads/products/thumbnails/' . $item->product->image)}}" alt="{{ $item->product->name }}" class="image">
                                                        </div>
                                                        <div class="name">
                                                            <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}" target="_blank"
                                                                class="body-title-2">{{ $item->product->name }}</a>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">Rs {{ $item->price }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-center">{{ $item->product->sku }}</td>
                                                    <td class="text-center">{{ $item->product->category->name }}</td>
                                                    <td class="text-center">{{ $item->product->brand->name }}</td>
                                                    <td class="text-center">{{ $item->options }}</td>
                                                    <td class="text-center">{{ $item->rstatus == 0 ? 'No' : 'Yes' }}</td>
                                                    <td class="text-center">
                                                        <div class="list-icon-function view-icon">
                                                            <div class="item eye">
                                                                <i class="icon-eye"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="divider"></div>
                                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                                        {{ $orderItems->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                                    

                                <div class="wg-box mt-5">
                                    <h5>Address</h5>
                                    <div class="my-account__address-item col-md-6">
                                        <div class="my-account__address-item__detail">
                                            <p><b >Name:</b> {{ $order->name }}</p>
                                            <p><b >Address:</b> {{ $order->address }}</p>
                                            <p><b >Locality:</b> {{ $order->locality }}</p>
                                            <p><b >City:</b> {{ $order->city }}, <b >State:</b> {{ $order->state }}, <b >Country:</b> {{ $order->country }}</p>
                                            <p><b >Landmark:</b> {{ $order->landmark }}</p>
                                            <p><b >Zip Code:</b> {{ $order->zip }}</p>
                                            <br>
                                            <p><b >Mobile:</b> {{ $order->phone }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="wg-box mt-5">
                                    <h5>Transactions</h5>
                                    <table class="table table-striped table-bordered table-transaction">
                                        <tbody>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td>Rs {{ $order->subtotal }}</td>
                                                <th>Tax</th>
                                                <td>Rs {{ $order->tax }}</td>
                                                <th>Discount</th>
                                                <td>Rs {{ $order->discount }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td>Rs {{ $order->total }}</td>
                                                <th>Payment Mode</th>
                                                <td>{{ $transection->mode }}</td>
                                                <th>Status</th>
                                                <td>
                                                    @if($transection->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @elseif($transection->status == 'declined')
                                                        <span class="badge bg-danger">Declined</span>
                                                        @elseif($transection->status == 'refunded')
                                                        <span class="badge bg-secondary">Refunded</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                </td>
                                            </tr>
                                           
                                        </tbody>
                                    </table>
                                </div>
                                @if($order->histories->where('is_admin_note', false)->count())
                                <div class="wg-box mt-5">
                                    <h5>Order Timeline</h5>
                                    <div class="order-timeline mt-3">
                                        @foreach($order->histories->where('is_admin_note', false) as $history)
                                        @php
                                            $itemCls = in_array($history->status, ['delivered']) ? 'done'
                                                : ($history->status === 'canceled' ? 'canceled'
                                                : ($history->status === 'returned' ? 'returned' : ''));
                                        @endphp
                                        <div class="order-timeline-item {{ $itemCls }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <span class="fw-semibold">{{ $statusLabels[$history->status] ?? ucfirst($history->status) }}</span>
                                                <small class="text-muted ms-3 text-nowrap">{{ $history->created_at->format('d M Y H:i') }}</small>
                                            </div>
                                            @if($history->note)
                                                <p class="text-muted small mb-0 mt-1">{{ $history->note }}</p>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                    @if ($order->status == 'ordered')
                                <div class="wg-box mt-5 text-right">
                                    <form action="{{ route('user.order.cancel') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <button type="button" class="btn btn-danger cancel-order">Cancel Order</button>
                                    </form>
                                </div>
                                @endif

                </div>

            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        $(function(){
            $(`.cancel-order`).on('click',function(e){
                e.preventDefault();
                var selectedForm = $(this).closest('form');
                swal({
                    title: `Are you sure?`,
                    text: `You want to cancel this order?`,
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