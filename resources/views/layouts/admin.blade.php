<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Farmer\'s Basket') }}</title>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="author" content="Farmer's Basket" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/animation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('font/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('icon/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">

    @stack("styles")
</head>
<body class="body">
    <div id="wrapper">
        <div id="page" class="">
            <div class="layout-wrap">

                <!-- <div id="preload" class="preload-container">
    <div class="preloading">
        <span></span>
    </div>
</div> -->

                <div class="section-menu-left">
                    <div class="box-logo">
                        <a href="{{route('admin.index')}}" id="site-logo-inner">
                            <img class="" id="logo_header" alt="" src="{{ asset('images/logo/logo.png') }}"
                                data-light="{{ asset('images/logo/logo.png') }}" data-dark="{{ asset('images/logo/logo.png') }}">
                        </a>
                        <div class="button-show-hide">
                            <i class="icon-menu-left"></i>
                        </div>
                    </div>
                    <div class="center">
                        <div class="center-item">
                            <div class="center-heading">Main Home</div>
                            <ul class="menu-list">
                                <li class="menu-item">
                                    <a href="{{route('admin.index')}}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Dashboard</div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="center-item">
                            <ul class="menu-list">
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-shopping-cart"></i></div>
                                        <div class="text">Products</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.product.add') }}" class="">
                                                <div class="text">Add Product</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.products') }}" class="">
                                                <div class="text">Products</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-layers"></i></div>
                                        <div class="text">Brand</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.brand.add') }}" class="">
                                                <div class="text">New Brand</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.brands') }}" class="">
                                                <div class="text">Brands</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-layers"></i></div>
                                        <div class="text">Category</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.category.add') }}" class="">
                                                <div class="text">New Category</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.categories') }}" class="">
                                                <div class="text">Categories</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-file-plus"></i></div>
                                        <div class="text">Order</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.orders') }}" class="">
                                                <div class="text">Orders</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="order-tracking.html" class="">
                                                <div class="text">Order tracking</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item">
                                    <a href="{{ route('admin.slides') }}" class="">
                                        <div class="icon"><i class="icon-image"></i></div>
                                        <div class="text">Slider</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="{{ route('admin.coupons') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Coupons</div>
                                    </a>
                                </li>
                                 <li class="menu-item">
                                    <a href="{{ route('admin.contacts') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Messages</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="{{ route('admin.reviews') }}" class="">
                                        <div class="icon"><i class="icon-star"></i></div>
                                        <div class="text">Reviews</div>
                                    </a>
                                </li>

                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-package"></i></div>
                                        <div class="text">Inventory</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.inventory') }}" class="">
                                                <div class="text">Stock Levels</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.warehouses') }}" class="">
                                                <div class="text">Warehouses</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.stock.transfers') }}" class="">
                                                <div class="text">Stock Transfers</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-truck"></i></div>
                                        <div class="text">Shipping</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.dispatch.index') }}" class="{{ request()->routeIs('admin.dispatch.*') ? 'active' : '' }}">
                                                <div class="text">Dispatch Board</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.shipments.index') }}" class="{{ request()->routeIs('admin.shipments.*') ? 'active' : '' }}">
                                                <div class="text">All Shipments</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.couriers.index') }}" class="{{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
                                                <div class="text">Courier Services</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.riders.index') }}" class="{{ request()->routeIs('admin.riders.*') ? 'active' : '' }}">
                                                <div class="text">Riders</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-monitor"></i></div>
                                        <div class="text">POS Management</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.branches') }}" class="">
                                                <div class="text">Branches</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.cashiers') }}" class="">
                                                <div class="text">Cashiers</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.pos.sessions') }}" class="">
                                                <div class="text">POS Sessions</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.login.activity') }}" class="">
                                                <div class="text">Login Activity</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.customers') }}" class="{{ request()->routeIs('admin.customers','admin.customer.detail') ? 'active' : '' }}">
                                        <div class="icon"><i class="icon-user"></i></div>
                                        <div class="text">Customers</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                                        <div class="icon"><i class="icon-settings"></i></div>
                                        <div class="text">Settings</div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="section-content-right">

                    <div class="header-dashboard">
                        <div class="wrap">
                            <div class="header-left">
                                <a href="{{ route('admin.index') }}">
                                    <img class="" id="logo_header_mobile" alt="" src="{{ asset('images/logo/logo.png') }}"
                                        data-light="{{ asset('images/logo/logo.png') }}" data-dark="{{ asset('images/logo/logo.png') }}"
                                        data-width="154px" data-height="52px" data-retina="{{ asset('images/logo/logo.png') }}">
                                </a>
                                <div class="button-show-hide">
                                    <i class="icon-menu-left"></i>
                                </div>


                                <form class="form-search flex-grow">
                                    <fieldset class="name">
                                        <input type="text" placeholder="Search here..." class="show-search" name="name" id="search-input"
                                            tabindex="2" value="" aria-required="true" required="" autocomplete="off">
                                    </fieldset>
                                    <div class="button-submit">
                                        <button class="" type="submit"><i class="icon-search"></i></button>
                                    </div>
                                    <div class="box-content-search" >
                                      <ul id="box-content-search">

                                      </ul>
                                    </div>
                                </form>

                            </div>
                            <div class="header-grid">

                                <div class="popup-wrap message type-header">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false"
                                            onclick="loadNotifications()">
                                            <span class="header-item" style="position:relative;">
                                                <span id="notif-badge" class="text-tiny"
                                                      style="display:none;position:absolute;top:-6px;right:-6px;background:#e74c3c;color:#fff;border-radius:50%;width:16px;height:16px;font-size:9px;display:none;align-items:center;justify-content:center;font-weight:700;">0</span>
                                                <i class="icon-bell" id="notif-bell"></i>
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end has-content"
                                            id="notif-dropdown"
                                            aria-labelledby="dropdownMenuButton2"
                                            style="min-width:320px;max-height:480px;overflow-y:auto;padding:0;">
                                            <li style="padding:10px 16px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:1;">
                                                <h6 style="margin:0;font-size:14px;font-weight:700;">Notifications</h6>
                                                <button id="btn-mark-all" onclick="markAllRead()"
                                                        style="display:none;background:none;border:none;color:#2ecc71;font-size:11px;font-weight:600;cursor:pointer;padding:0;">
                                                    Mark all read
                                                </button>
                                            </li>
                                            <div id="notif-list" style="min-height:80px;">
                                                <div style="text-align:center;padding:24px;color:#aaa;font-size:13px;">Loading...</div>
                                            </div>
                                            <li style="padding:8px 12px;border-top:1px solid #eee;position:sticky;bottom:0;background:#fff;">
                                                <a href="{{ route('admin.notifications.page') }}" class="tf-button w-full" style="display:block;text-align:center;">View all notifications</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>




                                <div class="popup-wrap user type-header">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="header-user wg-user">
                                                <span class="image">
                                                    <img src="images/avatar/user-1.png" alt="">
                                                </span>
                                                <span class="flex flex-column">
                                                    <span class="body-title mb-2">{{ Auth::user()->name }}</span>
                                                    <span class="text-tiny">Admin</span>
                                                </span>
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end has-content"
                                            aria-labelledby="dropdownMenuButton3">
                                            <li>
                                                <a href="{{ route('admin.settings') }}" class="user-item">
                                                    <div class="icon">
                                                        <i class="icon-settings"></i>
                                                    </div>
                                                    <div class="body-title-2">Account Settings</div>
                                                </a>
                                            </li>
                                           
                                            <li>
                                                <form action="{{route('logout')}}" method="POST" id="logout-form">
                                                    @csrf
                                                    <a href="{{route('logout')}}" class="user-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                                        <div class="icon">
                                                            <i class="icon-log-out"></i>
                                                        </div>
                                                        <div class="body-title-2">Log out</div>
                                                </a>
                                                </form>
                                              
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="main-content">
                        @yield('content')
                    


                        <div class="bottom-page">
                            <div class="body-text">Copyright © 2026 Farmer's Basket</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>   
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>    
    <script src="{{ asset('js/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @stack('scripts')
    <script>
    // ── Notifications ─────────────────────────────────────────────────────────
    var notifLoaded    = false;
    var notifLastCount = 0;

    var notifIcons  = { new_order:'icon-shopping-bag', order_canceled:'icon-x-circle', new_contact:'icon-mail', new_customer:'icon-user', low_stock:'icon-alert-triangle' };
    var notifColors = { new_order:'#2ecc71', order_canceled:'#e74c3c', new_contact:'#3498db', new_customer:'#9b59b6', low_stock:'#f39c12' };

    function loadNotifications() {
        $.getJSON('{{ route('admin.notifications.fetch') }}', function(data) {
            notifLoaded = true;
            var count = data.unread_count;
            var badge = document.getElementById('notif-badge');
            badge.textContent = count > 99 ? '99+' : count;
            if (count > 0) {
                badge.style.display = 'inline-flex';
                document.getElementById('btn-mark-all').style.display = 'inline';
            } else {
                badge.style.display = 'none';
                document.getElementById('btn-mark-all').style.display = 'none';
            }

            if (count > notifLastCount && notifLastCount > 0) {
                ringBell();
            }
            notifLastCount = count;

            var html = '';
            if (data.notifications.length === 0) {
                html = '<div style="text-align:center;padding:32px;color:#aaa;font-size:13px;"><i class="icon-bell" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3;"></i>No notifications</div>';
            } else {
                data.notifications.forEach(function(n) {
                    var icon  = notifIcons[n.type]  || 'icon-bell';
                    var color = notifColors[n.type] || '#888';
                    var bg    = n.is_read ? '#fff' : '#f0fdf4';
                    var item  = '<div style="display:flex;align-items:flex-start;gap:10px;padding:10px 16px;border-bottom:1px solid #f5f5f5;background:' + bg + ';cursor:pointer;" onclick="notifClick(' + n.id + ',\'' + (n.url || '') + '\')">';
                    item += '<div style="width:36px;height:36px;border-radius:50%;background:' + color + '20;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">';
                    item += '<i class="' + icon + '" style="color:' + color + ';font-size:15px;"></i></div>';
                    item += '<div style="flex:1;min-width:0;">';
                    item += '<div style="font-size:13px;font-weight:' + (n.is_read ? '500' : '700') + ';color:#1a1f2e;line-height:1.3;">' + escHtml(n.title) + '</div>';
                    item += '<div style="font-size:11px;color:#888;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escHtml(n.message) + '</div>';
                    item += '<div style="font-size:10px;color:#bbb;margin-top:3px;">' + escHtml(n.time_ago) + '</div>';
                    item += '</div>';
                    if (!n.is_read) {
                        item += '<span style="width:8px;height:8px;border-radius:50%;background:#2ecc71;flex-shrink:0;margin-top:6px;"></span>';
                    }
                    item += '</div>';
                    html += item;
                });
            }
            document.getElementById('notif-list').innerHTML = html;
        });
    }

    function notifClick(id, url) {
        $.post('{{ url('/admin/notifications') }}/' + id + '/read', function() {
            if (url) window.location.href = url;
            else loadNotifications();
        });
    }

    function markAllRead() {
        $.post('{{ route('admin.notifications.read.all') }}', function() {
            loadNotifications();
        });
    }

    function ringBell() {
        var bell = document.getElementById('notif-bell');
        bell.classList.add('bell-ring');
        setTimeout(function() { bell.classList.remove('bell-ring'); }, 600);
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Poll every 30 seconds for new notifications
    function pollNotifications() {
        $.getJSON('{{ route('admin.notifications.fetch') }}', function(data) {
            var count = data.unread_count;
            var badge = document.getElementById('notif-badge');
            badge.textContent = count > 99 ? '99+' : count;
            if (count > 0) {
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
            if (count > notifLastCount && notifLastCount >= 0) {
                ringBell();
            }
            notifLastCount = count;
        });
    }

    $(function() {
        // Initial badge fetch
        pollNotifications();
        // Poll every 30s
        setInterval(pollNotifications, 30000);
        // Reload list when dropdown opens
        $('#dropdownMenuButton2').on('click', function() {
            loadNotifications();
        });
    });
    </script>
    <style>
    @keyframes bell-ring {
        0%,100% { transform: rotate(0); }
        20%,60%  { transform: rotate(-18deg); }
        40%,80%  { transform: rotate(18deg); }
    }
    #notif-bell.bell-ring { animation: bell-ring 0.5s ease; display:inline-block; }
    </style>
     <script>
    $(function(){
      $("#search-input").on("keyup", function(){
        var searchQuery = $(this).val();
        if(searchQuery.length > 2){
          $.ajax({
            type: "GET",
            url: "{{ route('admin.search') }}",
            data: {query: searchQuery},
            dataType: "json",
            success: function(data){
              $("#box-content-search").html('');
              $.each(data, function(index,item){
                var url = "{{route('admin.product.edit',['id'=>'product_id_pls'])}}";
                var link = url.replace('product_id_pls',item.id);
                $("#box-content-search").append(`
                  <li>
                    <ul>
                  <li class="product-item gap14 mb-10">
                    <div class="image no-bg">
                      <img src= "{{asset('uploads/products/thumbnails')}}/${item.image}" alt="${item.name}">
                    </div>
                    <div class="flex items-center justify-between gap20 flex-grow">
                      <div class="name">
                        <a href="${link}" class="body-text">${item.name}</a>
                      </div>
                    </div>

                  </li>
                  <li class="mb-10">
                    <div class="divider"></div>
                  </li>


                    </ul>
                    </li>
            
                `);
              });
            }
          });
        } 
      });
    });
</script>
   
     @stack("scripts")
</body>

</html>
