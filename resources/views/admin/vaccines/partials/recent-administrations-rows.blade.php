@forelse($administrations as $admin)
    <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-5 py-3">
            <p class="font-black text-xs text-gray-900">{{ $admin->patient?->full_name ?? 'Unknown Patient' }}</p>
            <p class="text-[9px] font-bold text-gray-400">{{ $admin->patient?->patient_code ?? '—' }}</p>
        </td>
        <td class="px-5 py-3 text-xs font-bold text-gray-600">{{ $admin->administered_at?->format('M d, Y') ?? '—' }}</td>
        <td class="px-5 py-3 text-xs font-black text-gray-900">{{ $admin->batch?->batch_number ?? '—' }}</td>
        <td class="px-5 py-3 text-right text-xs font-black text-gray-900">{{ $admin->quantity }}</td>
        <td class="px-5 py-3 text-right text-xs font-bold text-gray-600">{{ $admin->administeredBy?->full_name ?? '—' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-5 py-10 text-center text-gray-400 font-medium italic text-xs">No recent administrations recorded.</td>
    </tr>
@endforelse