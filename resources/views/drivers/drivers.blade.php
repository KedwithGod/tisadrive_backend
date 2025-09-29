   <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Transport Type</th>
                <th>Vehicle Type</th>
                <th>Status</th>
                <th>signup Date</th>

            </tr>
        </thead>
        <tbody>
            @php $i= 1; @endphp

            @forelse($results as $key => $driver)
            @php
            $email = $driver->email;
            $dial = $driver->countryDetail ? $driver->countryDetail->dial_code : '0';
            $mobile = $dial. " ".$driver->mobile;
            if(env('APP_FOR') == 'demo'){
                $mobile = "**********";
                $email = "**********";
            }
            @endphp
                <tr>
                    <td>{{ $i++ }} </td>
                    <td>{{ $driver->id }}</td>
                    <td>{{ $driver->name }}</td>
                    <td>{{ $email }}</td>
                    <td>{{ $mobile }}</td>
                    <td>{{ $driver->transport_type }}</td>
                    <td>
                        @foreach($driver->driverVehicleTypeDetail as $vehicleType)
                        {{ $vehicleType->vehicleType->name.',' }}
                        @endforeach
                    </td>
    
                    @if ($driver->approve)
                        <td><span class="label label-success">Approved</span></td>
                    @else
                        <td><span class="label label-danger">Disapproved</span></td>
                    @endif
                    <td>{{ $driver->getConvertedCreatedAtAttribute() }}</td>

                </tr>
                @empty
                <tr>
                    <td colspan="11">
                        <h4 class="text-center" style="color:#333;font-size:25px;">No Data Found</h4>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
