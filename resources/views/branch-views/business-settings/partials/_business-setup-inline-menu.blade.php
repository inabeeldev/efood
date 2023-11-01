<div class="mt-5 mb-5">
    <div class="inline-page-menu my-4">
        <ul class="list-unstyled">
            <li class="{{Request::is('branch/business-settings/restaurant/restaurant-setup')? 'active': ''}}"><a href="{{route('branch.business-settings.restaurant.restaurant-setup')}}">{{translate('Business_Settings')}}</a></li>
            <li class="{{Request::is('branch/business-settings/restaurant/time-schedule')? 'active' : ''}}"><a href="{{route('branch.business-settings.restaurant.time_schedule_index')}}">{{translate('Restaurant_Availabilty_Time_Slot')}}</a></li>

        </ul>
    </div>
</div>
