<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Driver Name</th>
            <th>Total Logged in Hours</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; @endphp

        @forelse($results as $requests)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $requests->date }}</td>
                <td>{{  $requests->driver_name }}</td>
                <td>
                    @if(isset($requests->total_duration_hours))
                        @php
                            // Get the total hours and minutes
                            $totalHours = floor($requests->total_duration_hours); // Whole hours
                            $totalMinutes = ($requests->total_duration_hours - $totalHours) * 60; // Convert decimal part to minutes
                        @endphp
                        {{ $totalHours }} hour{{ $totalHours !== 1 ? 's' : '' }} {{ floor($totalMinutes) }} minute{{ floor($totalMinutes) !== 1 ? 's' : '' }}
                    @else
                        -
                    @endif
                </td>
           </tr>
        @empty
            <tr>
                <td colspan="4">
                    <h4 class="text-center" style="color:#333; font-size:25px;">No Data Found</h4>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>


