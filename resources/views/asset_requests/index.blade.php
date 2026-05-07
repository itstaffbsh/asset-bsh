<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Permintaan Aset') }}</h2>
            @if(auth()->user()->hasPermission('requests.create'))
            <a href="{{ route('asset-requests.create') }}" class="bg-[#a47b53] hover:bg-[#8b6540] text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                + {{ __('Request Baru') }}
            </a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm border border-[#e5e0d8] rounded-xl">
            <div class="p-6 text-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f4f1ea] text-[#4a554a] font-serif uppercase text-xs">
                                <th class="p-4 border-b border-[#e5e0d8]">{{ __('Tgl Pengajuan') }}</th>
                                <th class="p-4 border-b border-[#e5e0d8]">{{ __('Pemohon') }}</th>
                                <th class="p-4 border-b border-[#e5e0d8]">{{ __('Departemen') }}</th>
                                <th class="p-4 border-b border-[#e5e0d8]">{{ __('Criticality') }}</th>
                                <th class="p-4 border-b border-[#e5e0d8]">{{ __('Status') }}</th>
                                <th class="p-4 border-b border-[#e5e0d8] text-center">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($requests as $req)
                                <tr class="hover:bg-[#faf9f6] transition border-b border-[#f0eee9]">
                                    <td class="p-4">{{ $req->created_at->format('d M Y') }}</td>
                                    <td class="p-4 font-bold text-[#4a554a]">{{ $req->user->name }}</td>
                                    <td class="p-4">{{ $req->department->nama_departemen }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                            {{ $req->criticality_level == 'top priority' ? 'bg-red-100 text-red-600' : ($req->criticality_level == 'urgent' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') }}">
                                            {{ __($req->criticality_level) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs">
                                        @php
                                            $statusLabel = [
                                                'pending_hr' => ['text' => __('Menunggu HR'), 'class' => 'bg-gray-100 text-gray-600'],
                                                'pending_dept' => ['text' => __('Menunggu Manager Dept'), 'class' => 'bg-yellow-100 text-yellow-700'],
                                                'pending_it' => ['text' => __('Menunggu Manager IT'), 'class' => 'bg-blue-100 text-blue-700'],
                                                'pending_md' => ['text' => __('Menunggu Managing Director'), 'class' => 'bg-purple-100 text-purple-700'],
                                                'pending_fulfillment' => ['text' => __('Menunggu IT Fulfillment'), 'class' => 'bg-teal-100 text-teal-700'],
                                                'approved' => ['text' => __('Selesai / Approved'), 'class' => 'bg-green-100 text-green-700'],
                                                'rejected' => ['text' => __('Ditolak'), 'class' => 'bg-red-100 text-red-700'],
                                            ];
                                            $s = $statusLabel[$req->status] ?? ['text' => $req->status, 'class' => 'bg-gray-100'];
                                        @endphp
                                        <span class="px-2 py-1 rounded-lg {{ $s['class'] }} font-semibold">
                                            {{ $s['text'] }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('asset-requests.show', $req) }}" class="text-[#a47b53] hover:underline font-bold">
                                            {{ __('Detail') }}
                                        </a>
                                        @if($req->status == 'approved')
                                            <a href="{{ route('asset-requests.print', $req) }}" target="_blank" class="ml-2 text-green-700 hover:underline font-bold text-xs">
                                                🖨️ {{ __('Cetak EWTR') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-gray-400 italic">{{ __('Belum ada permintaan aset.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
