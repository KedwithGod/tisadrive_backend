   <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Request Number</th>
                <th>Trip Start Time</th>
                <th> Trip End Time</th>
                <th> User</th>
                <th> Driver</th>
                <th> Owner</th>
                <th> Trip Status</th>
                <th> Payment Status</th>
                <th> Payment Option</th>
                <th> Vehicle Type</th>
                <th> Ride Type</th>
                <th> Trip Time</th>
                <th> Trip Distance</th>
                <th> Driver Commission</th>
                <th> Admin Commission</th>
                <th> Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $i= 1; @endphp

            @forelse($results as $key => $requests)
                <tr>
                    <td>{{ $i++ }} </td>
                    <td>{{ $requests->created_at->format("m/d/Y") }} </td>
                    <td>{{$requests->request_number}}</td>
                    <td>{{ $requests->converted_trip_start_time ?? '-' }}</td>
                    <td>{{ $requests->converted_completed_at ?? '-' }}</td>
                    <td>{{$requests->userDetail ? $requests->userDetail->name : '-'}}</td>
                    <td>{{$requests->driverDetail ? $requests->driverDetail->name : '-'}}</td>
    
                    @if($requests->owner_id)
                    <td>{{$requests->ownerDetail ? $requests->ownerDetail->owner_name : '-'}}</td>
                    @else
                    <td>{{"Individual"}}</td>
                    @endif
    
                    @if($requests->is_cancelled == 1)
                        <td><span class="label label-danger">cancelled</span></td>
                    @elseif($requests->is_completed == 1)
                        <td><span class="label label-success">completed</span></td>
                    @elseif($requests->is_trip_start == 0 && $requests->is_cancelled == 0)
                        <td><span class="label label-warning">not_started</span></td>
                    @else
                        <td>-</td>
                    @endif
    
                    @if ($requests->is_paid)
                        <td><span class="label label-success">paid</span></td>
                    @else
                        <td><span class="label label-danger">not_paid</span></td>
                    @endif
    
                    @if ($requests->payment_opt == 0)
                        <td><span class="label label-danger">card</span></td>
                    @elseif($requests->payment_opt == 1)
                        <td><span class="label label-primary">cash</span></td>
                    @elseif($requests->payment_opt == 2)
                        <td><span class="label label-warning">wallet</span></td>
                    @else
                        <td><span class="label label-info">cash_wallet</span></td>
                    @endif
    
                    <td>{{ $requests->vehicle_type_name }}</td>
    
    
                @php
                   $later = $requests->is_later;
                   $rental = $requests->is_rental;
                 @endphp
                 @if($later == 0)
    
                    @if(($later == 0) &&  ($rental == 0))
                    <td><span class="label label-success">regular_instant</span> </td>
                    @else(($later == 0) &&  ($rental == 1))
                    <td><span class="label label-success"> rental_instant</span> </td>
                    @endif
    
                @else($later == 1)
    
                    @if(($later == 1) &&  ($rental == 0))
                    <td><span class="label label-success">  regular_scheduled</span></td>
                    @else(($later == 1) &&  ($rental == 1 ))
                    <td><span class="label label-success"> rental_scheduled</span></td>
                    @endif
    
                @endif
    
    
                    <td>{{ $requests->total_time .' Mins' }}</td>
                    <td>{{ $requests->total_distance .'  '. $requests->request_unit}}</td>
                    <td>{{ $requests->requestBill ? $requests->currency .' '. $requests->requestBill->driver_commision : '-' }}</td>
                    <td>{{ $requests->requestBill ? $requests->currency .' '. $requests->requestBill->admin_commision_with_tax : '-' }}</td>
                    <td>{{ $requests->requestBill ? $requests->currency .' '. $requests->requestBill->total_amount : '-' }}</td>                </tr>
                @empty
                <tr>
                    <td colspan="11">
                        <h4 class="text-center" style="color:#333;font-size:25px;">No Data Found</h4>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
