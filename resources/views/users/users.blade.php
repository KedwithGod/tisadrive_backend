   <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Status</th>
                <th>signup Date</th>

            </tr>
        </thead>
        <tbody>
            @php $i= 1; @endphp

            @forelse($results as $key => $user)
            @php
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
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $email }}</td>
                    <td>{{ $mobile }}</td>
                    @if ($user->active)
                        <td><span class="label label-success">Active</span></td>
                    @else
                        <td><span class="label label-danger">InActive</span></td>
                    @endif
                    <td>{{ $user->getConvertedCreatedAtAttribute() }}</td>

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
