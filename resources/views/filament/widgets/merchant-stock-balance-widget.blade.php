<x-filament-widgets::widget>
    <x-filament::section heading="Stock Balance">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-gray-600">
                        <th class="px-3 py-2 font-medium">Merchant</th>
                        <th class="px-3 py-2 font-medium">Item</th>
                        <th class="px-3 py-2 font-medium">Unit</th>
                        <th class="px-3 py-2 font-medium">Incoming</th>
                        <th class="px-3 py-2 font-medium">Dispatched</th>
                        <th class="px-3 py-2 font-medium">Remaining</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows as $row)
                        <tr>
                            <td class="px-3 py-2">{{ $row->merchant_name }}</td>
                            <td class="px-3 py-2">{{ $row->item_name }}</td>
                            <td class="px-3 py-2">{{ $row->unit }}</td>
                            <td class="px-3 py-2">{{ number_format((float) $row->incoming_quantity, 2) }}</td>
                            <td class="px-3 py-2">{{ number_format((float) $row->dispatched_quantity, 2) }}</td>
                            <td class="px-3 py-2 font-semibold {{ (float) $row->remaining_quantity < 0 ? 'text-danger-600' : 'text-success-600' }}">
                                {{ number_format((float) $row->remaining_quantity, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">No stock balance data yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
