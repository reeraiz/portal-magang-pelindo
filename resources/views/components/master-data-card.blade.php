@props(['title', 'type', 'placeholder', 'data'])

<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col" x-data="masterCard({{ Js::from($data) }})">
    <!-- Header with Search -->
    <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-3 gap-2">
        <h3 class="font-bold text-gray-900 whitespace-nowrap">{{ $title }}</h3>
        <input type="text" x-model="search" placeholder="Cari..." class="text-xs border-gray-200 rounded-lg py-1.5 px-3 w-28 sm:w-36 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 transition-colors">
    </div>

    <!-- Data Table -->
    <div class="flex-grow overflow-hidden mb-4 relative min-h-[160px]">
        <table class="w-full text-sm text-left">
            <tbody>
                <template x-for="item in paginatedData" :key="item.id">
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                        <td class="py-2.5 px-2" x-text="item.name"></td>
                        <td class="py-2.5 px-2 text-right">
                            <form :action="`{{ url('admin/master-data') }}/{{ $type }}/${item.id}`" method="POST" class="inline" onsubmit="return confirm('Hapus data ini? Semua data terkait mungkin akan terpengaruh.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-md transition-colors">Hapus</button>
                            </form>
                        </td>
                    </tr>
                </template>
                <tr x-show="paginatedData.length === 0" x-cloak>
                    <td colspan="2" class="py-6 text-gray-400 italic text-center text-xs">Belum ada data atau tidak ditemukan.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="flex justify-between items-center mb-4 text-xs" x-show="totalPages > 1" x-cloak>
        <button type="button" @click="prevPage" :disabled="currentPage === 1" class="px-2 py-1 text-gray-500 hover:text-blue-600 disabled:opacity-30 disabled:hover:text-gray-500 font-medium transition-colors">&laquo; Prev</button>
        <span class="text-gray-400 font-medium"><span x-text="currentPage" class="text-gray-700"></span> / <span x-text="totalPages"></span></span>
        <button type="button" @click="nextPage" :disabled="currentPage === totalPages" class="px-2 py-1 text-gray-500 hover:text-blue-600 disabled:opacity-30 disabled:hover:text-gray-500 font-medium transition-colors">Next &raquo;</button>
    </div>

    <!-- Add Form -->
    <form action="{{ route('admin.master-data.store', $type) }}" method="POST" class="mt-auto w-full">
        @csrf
        <div class="flex flex-col gap-2">
            <input type="text" name="name" required class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="{{ $placeholder }}">
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah
            </button>
        </div>
    </form>
</div>
