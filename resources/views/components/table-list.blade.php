@props(['columns','rows'])

<div class="overflow-x-auto bg-white rounded-2xl shadow mb-6">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-gray-100">
            <tr>
                @foreach($columns as $col)
                    <th class="border px-4 py-2 text-left">{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $rows }}
        </tbody>
    </table>
</div>
