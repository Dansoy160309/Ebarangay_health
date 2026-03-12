@extends('layouts.app')

@section('title', 'Available Slots')

@section('content')
<div class="container mx-auto">

    <h1 class="text-2xl font-semibold mb-4">Available Appointment Slots</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($slots->isEmpty())
        <p class="text-gray-600">No available slots at the moment. Please check back later.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2 border-b">Date</th>
                        <th class="px-4 py-2 border-b">Time</th>
                        <th class="px-4 py-2 border-b">Service</th>
                        <th class="px-4 py-2 border-b">Available Spots</th>
                        <th class="px-4 py-2 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slots as $slot)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($slot->date)->format('M d, Y') }}</td>
                            <td class="px-4 py-2 border-b">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</td>
                            <td class="px-4 py-2 border-b">{{ $slot->service }}</td>
                            <td class="px-4 py-2 border-b">{{ $slot->available_spots }}</td>
                            <td class="px-4 py-2 border-b">
                                @if($slot->available_spots > 0)
                                    <form action="{{ route('patient.appointments.book', $slot->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white px-3 py-1 rounded">
                                            Book
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">Full</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection
