   <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Sevice Location</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Transport Type</th>
                <th>Status</th>
                {{-- <th>signup Date</th> --}}

            </tr>
        </thead>
        <tbody>
            @php $i= 1; @endphp

            @forelse($results as $key => $owner)
            @php
            $user = $owner->user;
            $email = $user->email;
            $dial = $user->countryDetail ? $user->countryDetail->dial_code : '0';
            $mobile = $dial. " ".$user->mobile;
            if(env('APP_FOR') == 'demo'){
                $mobile = "**********";
                $email = "**********";
            }
            @endphp
                <tr>
                    <td>{{ $i++ }} </td>
                    <td>{{ $owner->area->name }}</td>
                    <td>{{ $owner->name }}</td>
                    <td>{{ $email }}</td>
                    <td>{{ $mobile }}</td>
                    <td>{{ $owner->transport_type }}</td>
    
                    @if ($owner->approve)
                        <td><span class="label label-success">Approved</span></td>
                    @else
                        <td><span class="label label-danger">Disapproved</span></td>
                    @endif
                    {{-- <td>{{ $owner->getConvertedCreatedAtAttribute() }}</td> --}}

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
