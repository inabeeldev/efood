<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    <!-- Logo -->
                    @php($restaurant_logo=\App\Model\BusinessSetting::where(['key'=>'logo', 'branch_id' => auth('branch')->id()])->first()->value ?? "")
                    <a class="navbar-brand" href="{{route('branch.dashboard')}}" aria-label="Front">
                        <img logo-ab="{{asset('storage/app/public/restaurant/'.$restaurant_logo)}}" class="navbar-brand-logo" style="object-fit: contain;"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/app/public/restaurant/'.$restaurant_logo)}}"
                             alt="Logo">
                        <img class="navbar-brand-logo-mini" style="object-fit: contain;"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/app/public/restaurant/'.$restaurant_logo)}}" alt="Logo">
                    </a>

                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align" data-template="<div class=&quot;tooltip d-none d-sm-block&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><div class=&quot;tooltip-inner&quot;></div></div>" data-toggle="tooltip" data-placement="right" title="" data-original-title="Expand"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->

                    <div class="navbar-nav-wrap-content-left d-none d-xl-block">
                        <!-- Navbar Vertical Toggle -->
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                            <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                            <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                        </button>
                        <!-- End Navbar Vertical Toggle -->
                    </div>

                    <!-- Navbar Vertical Toggle -->
                    <!-- <button type="button"
                            class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button> -->
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content text-capitalize">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input" placeholder="Search Menu...">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('branch.dashboard')}}" title="{{translate('Dashboards')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('dashboard')}}
                                </span>
                            </a>
                        </li>
                        <!-- End Dashboards -->

                        <li class="nav-item">
                            <small
                                class="nav-subtitle">{{translate('pos')}} {{translate('system')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- POS -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/pos/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('POS')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/pos/*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('branch/pos/')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.pos.index')}}"
                                       title="{{translate('pos')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('new_sale')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/pos/orders')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.pos.orders')}}" title="{{translate('orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('order')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::where('branch_id', auth('branch')->id())->Pos()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End POS -->
                        <li class="nav-item">
                            <small class="nav-subtitle" title="{{translate('Pages')}}">{{translate('order')}} {{translate('section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/orders/list*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                               title="{{translate('order')}}">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('order')}}
                                </span>
                            </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/orders/list*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('branch/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('all')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notDineIn()->where(['branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where(['order_status'=>'pending','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('confirmed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where('order_type', '!=' , 'dine_in')->where(['order_status'=>'confirmed','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['processing'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('processing')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where(['order_status'=>'processing','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['out_for_delivery'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('out_for_delivery')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where(['order_status'=>'out_for_delivery','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('delivered')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where(['order_status'=>'delivered','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('returned')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where(['order_status'=>'returned','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('failed_to_deliver')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where(['order_status'=>'failed','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('branch/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('canceled')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->notSchedule()->where(['order_status'=>'canceled','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('branch/orders/list/schedule')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['schedule'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            {{translate('scheduled')}}
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::notPos()->schedule()->where(['branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                            
 <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('product')}} {{translate('management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

 <!-- Pages -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/category*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                >
                                    <i class="tio-category nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('category')}} {{translate('setup')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('branch/category*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('branch/category/add')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.category.add')}}"
                                           title="{{translate('add new category')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('category')}}</span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('branch/category/add-sub-category')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.category.add-sub-category')}}"
                                           title="{{translate('add new sub category')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('sub_category')}}</span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                            <!-- End Pages -->
                            <!-- Pages -->
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('branch/addon*') ||Request::is('branch/product*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                >
                                    <i class="tio-premium-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('product')}} {{translate('setup')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('branch/product*') || Request::is('branch/addon*') || Request::is('branch/attribute*') || Request::is('branch/reviews*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('branch/attribute*')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.attribute.add-new')}}"
                                           title="{{translate('Add product attribute')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{translate('Product_Attributes')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/addon*')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.addon.add-new')}}"
                                           title="{{translate('add addon')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{translate('Product_Addon')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/product/list') || Request::is('branch/product/edit*') ?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.product.list')}}" title="{{translate('product_list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('product_list')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/product/bulk-import')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.product.bulk-import')}}" title="{{translate('bulk import')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('bulk_import')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/product/bulk-export')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.product.bulk-export')}}" title="{{translate('bulk export')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('bulk_export')}}</span>
                                        </a>
                                    </li>
                                    <!-- REVIEWS -->
                                    <li class="navbar-vertical-aside-has-menu {{Request::is('branch/reviews*')?'active':''}}">
                                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.reviews.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('product')}} {{translate('reviews')}}
                                    </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End Pages -->
                            
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('promotion')}} {{translate('management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <!-- BANNER -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/banner*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.banner.list')}}"
                                >
                                    <i class="tio-image nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('banner')}}</span>
                                </a>
                                {{--                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"--}}
                                {{--                                    style="display: {{Request::is('branch/banner*')?'block':'none'}}">--}}
                                {{--                                    <li class="nav-item {{Request::is('branch/banner/add-new')?'active':''}}">--}}
                                {{--                                        <a class="nav-link " href="{{route('branch.banner.add-new')}}">--}}
                                {{--                                            <span class="tio-circle nav-indicator-icon"></span>--}}
                                {{--                                            <span--}}
                                {{--                                                class="text-truncate">{{translate('add')}} {{translate('new')}}</span>--}}
                                {{--                                        </a>--}}
                                {{--                                    </li>--}}
                                {{--                                    <li class="nav-item {{Request::is('branch/banner/list')?'active':''}}">--}}
                                {{--                                        <a class="nav-link " href="{{route('branch.banner.list')}}">--}}
                                {{--                                            <span class="tio-circle nav-indicator-icon"></span>--}}
                                {{--                                            <span class="text-truncate">{{translate('list')}}</span>--}}
                                {{--                                        </a>--}}
                                {{--                                    </li>--}}
                                {{--                                </ul>--}}
                            </li>

                            <!-- COUPON -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/coupon*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.coupon.add-new')}}">
                                    <i class="tio-gift nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('coupon')}}</span>
                                </a>
                            </li>

                            <!-- NOTIFICATION -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/notification*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.notification.add-new')}}">
                                    <i class="tio-notifications nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('Emergency Alerts')}}
                                    </span>
                                </a>
                            </li>
                            <!--<li class="navbar-vertical-aside-has-menu {{Request::is('branch/package*') || Request::is('branch/package*')?'active':''}}">-->
                            <!--    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('membership')}}">-->
                            <!--        <i class="tio-incognito nav-icon"></i>-->
                            <!--        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">-->
                            <!--                {{translate('Packages')}}-->
                            <!--            </span>-->
                            <!--    </a>-->
                            <!--    <ul class="js-navbar-vertical-aside-submenu nav nav-sub " style="display: {{Request::is('branch/package*') || Request::is('branch/package*')?'block':''}}">-->

                            <!--        <li class="nav-item {{Request::is('branch/package/add-new')? 'active': ''}}">-->
                            <!--            <a class="nav-link" href="{{route('branch.package.add-new')}}" title="{{translate('app_package')}}">-->
                            <!--                <span class="tio-circle nav-indicator-icon"></span>-->
                            <!--                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">-->
                            <!--                        {{translate('add_package')}}</span>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--        <li class="nav-item {{Request::is('branch/package/list')? 'active': ''}}">-->
                            <!--            <a class="nav-link" href="{{route('branch.package.list')}}" title="{{translate('list')}}">-->
                            <!--                <span class="tio-circle nav-indicator-icon"></span>-->
                            <!--                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">-->
                            <!--                        {{translate('list')}}</span>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--    </ul>-->
                            <!--</li>-->
                            
                            {{--                        REPORT & ANALYTICS MANAGEMENT--}}
                        {{-- @if(Helpers::module_permission_check(MANAGEMENT_SECTION['report_and_analytics_management'])) --}}
                            <li class="nav-item">
                                <small class="nav-subtitle"
                                       title="{{translate('report and analytics')}}">{{translate('report_and_analytics')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <!-- Pages -->
                                    <li class="nav-item {{Request::is('branch/report/earning')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.report.earning')}}">
                                            <i class="tio-chart-pie-1 nav-icon"></i>
                                            <span
                                                class="text-truncate">{{translate('earning')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/report/order')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.report.order')}}"
                                        >
                                            <i class="tio-chart-bar-2 nav-icon"></i>
                                            <span
                                                class="text-truncate">{{translate('order')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/report/deliveryman-report')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.report.deliveryman_report')}}"
                                        >
{{--                                            <i class="tio-chart-bar-3 nav-icon"></i>--}}
                                            <i class="tio-chart-donut-2 nav-icon"></i>
                                            <span
                                                class="text-truncate">{{translate('Earrand Guy Report')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/report/product-report')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.report.product-report')}}"
                                        >
                                            <i class="tio-chart-bubble nav-icon"></i>
                                            <span
                                                class="text-truncate">{{translate('product')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/report/sale-report')?'active':''}}">
                                        <a class="nav-link " href="{{route('branch.report.sale-report')}}">
                                            <i class="tio-chart-bar-1 nav-icon"></i>
                                            <span class="text-truncate">{{translate('sale')}} {{translate('report')}}</span>
                                        </a>
                                    </li>
{{--                                </ul>--}}
{{--                            </li>--}}
                            <!-- End Pages -->
                        {{-- @endif --}}
{{--                        REPORT & ANALYTICS MANAGEMENT END--}}
                            
                        <!-- End Pages -->
                        
                        <!-- <li class="nav-item">-->
                        <!--        <small class="nav-subtitle"-->
                        <!--               title="Layouts">{{translate('Help_&_Support_Section')}}</small>-->
                        <!--        <small class="tio-more-horizontal nav-subtitle-replacer"></small>-->
                        <!--</li>-->
                        <!-- MESSAGE -->
                        <!--<li class="navbar-vertical-aside-has-menu {{Request::is('branch/message*')?'active':''}} {{auth('branch')->user()->plan_id == 1 ? 'getMembership' : ''}}">-->
                        <!--    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{auth('branch')->user()->plan_id == 2 ? route('branch.message.list') : '#'}}">-->
                        <!--        <i class="tio-messages nav-icon"></i>-->
                        <!--        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">-->
                        <!--            {{translate('messages')}}-->
                        <!--        </span>-->
                        <!--    </a>-->
                        <!--</li>-->

                         <li class="nav-item">
                                <small class="nav-subtitle"
                                       title="Layouts">{{translate('Help_&_Support_Section')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- MESSAGE -->
                        <!--<li class="navbar-vertical-aside-has-menu {{Request::is('branch/customer*')?'active':''}} {{auth('branch')->user()->plan_id == 1 ? '' : ''}}">-->
                        <!--    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{auth('branch')->user()->plan_id == 2 ? route('branch.customer.list') : route('branch.customer.list')}}">-->
                        <!--        <i class="tio-poi-user nav-icon"></i>-->
                        <!--        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">-->
                        <!--            {{translate('Customer')}}-->
                        <!--        </span>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/message*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.message.list')}}">
                                <i class="tio-messages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages')}}
                                </span>
                            </a>
                        </li>
                        
                        
                        
                         <li class="nav-item {{(Request::is('branch/employee*') || Request::is('branch/custom-role*'))?'scroll-here':''}}">
                                <small class="nav-subtitle">{{translate('user_management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/customer/transaction') || Request::is('branch/customer/list') || Request::is('branch/customer/view*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-poi-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('customer')}}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('branch/customer/transaction') || Request::is('branch/customer/list')  || Request::is('branch/customer/view*')?'block':''}}; top: 831.076px;">
                                    <li class="nav-item {{Request::is('branch/customer/list') || Request::is('branch/customer/view*') ? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('branch.customer.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('list')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/customer/transaction')? 'active':''}}">
                                        <a class="nav-link" href="{{route('branch.customer.transaction')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('point_history')}}</span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/customer/subscribed-email*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                   href="{{route('branch.customer.subscribed_emails')}}">
                                    <i class="tio-email-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('Subscribed Emails')}}
                                    </span>
                                </a>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/delivery-man*')? 'active' : ''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('Earrand Guy')}}
                                        </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:  {{Request::is('branch/delivery-man*')? 'block' : ''}}; top: 831.076px;">
                                    <li class="nav-item {{Request::is('branch/delivery-man/list')? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('branch.delivery-man.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('Earrand_Guy_List')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/delivery-man/add')? 'active' : ''}}">
                                        <a class="nav-link " href="{{route('branch.delivery-man.add')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('Add_New_Earrand_Guy')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('branch/delivery-man/reviews/list')? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('branch.delivery-man.reviews.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('Earrand_Guy_Reviews')}}</span>
                                        </a>
                                    </li>
                                    
                                    

                                </ul>
                            </li>
                            <!-- Pages -->
                            
                             <li class="navbar-vertical-aside-has-menu {{Request::is('branch/reservation*') || Request::is('branch/reservation*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('Reservations')}}">
                                    <i class="tio-incognito nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('Reservations')}}
                                        </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub " style="display: {{Request::is('branch/reservation*') || Request::is('branch/reservation*')?'block':''}}">
                                    <li class="nav-item {{Request::is('branch/reservation/list')? 'active': ''}}">
                                        <a class="nav-link" href="{{route('branch.reservation.list')}}" title="{{translate('list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                                    {{translate('list')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        <li class="nav-item">
                            <small
                                class="nav-subtitle">{{translate('table')}} {{translate('order')}} {{translate('section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/table/order/list/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('table order')}}
                                </span>
                                <label class="badge badge-danger">{{translate('addon')}}</label>
{{--                                <label class="badge badge-danger">{{translate('addon')}}</label>--}}
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/table/order*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('branch/table/order/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.table.order.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                                {{translate('all')}}
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::dineIn()->where(['branch_id' => auth('branch')->id()])->count()}}
                                                </span>
                                            </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/table/order/list/confirmed')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.table.order.list',['confirmed'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                                {{translate('confirmed')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::dineIn()->where(['order_status'=>'confirmed'])->where(['branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/table/order/list/cooking')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.table.order.list',['cooking'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                                {{translate('cooking')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                    {{\App\Model\Order::dineIn()->where(['order_status'=>'cooking'])->where(['branch_id' => auth('branch')->id()])->count()}}
                                                </span>
                                            </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/table/order/list/done')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.table.order.list',['done'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                                {{translate('Ready For Serve')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                    {{\App\Model\Order::dineIn()->where(['order_status'=>'done'])->where(['branch_id' => auth('branch')->id()])->count()}}
                                                </span>
                                            </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/table/order/list/completed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.table.order.list',['completed'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                                {{translate('completed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{\App\Model\Order::dineIn()->where(['order_status'=>'completed'])->where(['branch_id' => auth('branch')->id()])->count()}}
                                                </span>
                                            </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/table/order/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.table.order.list',['canceled'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                                {{translate('canceled')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                    {{\App\Model\Order::dineIn()->where(['order_status'=>'canceled'])->where(['branch_id' => auth('branch')->id()])->count()}}
                                                </span>
                                            </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/table/order/running')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.table.order.running')}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                                {{translate('On Table')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{\App\Model\Order::with('table_order')->whereHas('table_order', function($q){
                                                                    $q->where('branch_table_token_is_expired', 0);
                                                                })->where(['branch_id' => auth('branch')->id()])->count()}}
                                                </span>
                                            </span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('table')}} {{translate('section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/table/list') || Request::is('branch/branch-promotion/*') || Request::is('branch/table/index') ?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-gift nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('table')}}</span>
                                <label class="badge badge-danger">{{translate('addon')}}</label>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('branch/table/*') || Request::is('branch/promotion*')? 'block' : ''}}">
                                <li class="nav-item ">
                                    <a class="nav-link {{Request::is('branch/table/list')? 'active' : ''}}" href="{{route('branch.table.list')}}" title="{{translate('list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('list')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link {{Request::is('branch/table/index')? 'active' : ''}}" href="{{route('branch.table.index')}}" title="{{translate('list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('availability')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link  {{Request::is('branch/promotion/*')? 'active' : ''}}" href="{{route('branch.promotion.create')}}" title="List">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('table_promotion')}}
                                    </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        

                        <li class="nav-item">
                            <small
                                class="nav-subtitle">{{translate('kitchen')}} {{translate('section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/kitchen/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('chef')}}</span>
                                <label class="badge badge-danger">{{translate('addon')}}</label>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/kitchen/*')? 'block' : ''}}">
                                <li class="nav-item {{Request::is('branch/kitchen/add-new')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.kitchen.add-new')}}"
                                       title="{{translate('add new')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('add new')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/kitchen/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.kitchen.list')}}" title="{{translate('list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('list')}}
                                    </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('system')}} {{translate('setting')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Business_Setup -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/business-settings/restaurant*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.business-settings.restaurant.restaurant-setup')}}">
                                <i class="tio-settings nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Business_Setup')}}</span>
                            </a>
                        </li>
                            <li class="navbar-vertical-aside-has-menu {{Request::is('branch/business-settings/web-app/system-setup*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.business-settings.web-app.system-setup.language.index')}}">
                                <i class="tio-security-on-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('System Setup')}}</span>
                            </a>
                        </li>
                        <li class="nav-item pt-10">
                            <div class="row justify-content-center p-3">
                                <a target="_BLANK" href="#" class="p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm-48.9 158.8c.2 2.8.2 5.7.2 8.5 0 86.7-66 186.6-186.6 186.6-37.2 0-71.7-10.8-100.7-29.4 5.3.6 10.4.8 15.8.8 30.7 0 58.9-10.4 81.4-28-28.8-.6-53-19.5-61.3-45.5 10.1 1.5 19.2 1.5 29.6-1.2-30-6.1-52.5-32.5-52.5-64.4v-.8c8.7 4.9 18.9 7.9 29.6 8.3a65.447 65.447 0 0 1-29.2-54.6c0-12.2 3.2-23.4 8.9-33.1 32.3 39.8 80.8 65.8 135.2 68.6-9.3-44.5 24-80.6 64-80.6 18.9 0 35.9 7.9 47.9 20.7 14.8-2.8 29-8.3 41.6-15.8-4.9 15.2-15.2 28-28.8 36.1 13.2-1.4 26-5.1 37.8-10.2-8.9 13.1-20.1 24.7-32.9 34z"/></svg>
                                </a>
                                <a target="_BLANK" href="#" class="p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>                        </a>
                                </a>
                                <a target="_BLANK" href="#" class="p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"/></svg>
                                </a>
                                
                            </div>
                            <!--<div class="row justify-content-center p-3"><a target="_BLANK" href="google.com">-->
                            <!--    <i class="fa fa-facebook"></i>-->
                            <!--</div>-->
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>


{{--<script>
    $(document).ready(function () {
        $('.navbar-vertical-content').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 'slow');
    });
</script>--}}

@push('script_2')
    <script>
        $(window).on('load' , function() {
            if($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        //Sidebar Menu Search
        var $rows = $('.navbar-vertical-content .navbar-nav > li');
        $('#search-bar-input').keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });
    </script>
@endpush
