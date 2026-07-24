@extends('layouts.app')
@section('header', 'Kelola Data Master')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Kelola Data Master</h1>
    <p class="text-gray-600 mt-1">Tambah atau hapus data referensi seperti Divisi, Universitas, Fakultas, dll.</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Card: Divisi -->
    <x-master-data-card title="Divisi" type="division" placeholder="Tambah Divisi baru..." :data="$divisions" />

    <!-- Card: Departemen -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col" 
         @php 
            $allDepts = collect();
            foreach($divisions as $div) {
                foreach($div->departments as $dept) {
                    $allDepts->push((object)['id' => $dept->id, 'name' => $dept->name, 'division_name' => $div->name, 'division_id' => $div->id]);
                }
            }
            $allDepts = $allDepts->sortBy('name')->values();
         @endphp
         x-data="masterCard({{ Js::from($allDepts) }})">
        
        <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-3 gap-2">
            <h3 class="font-bold text-gray-900 whitespace-nowrap">Departemen</h3>
            <input type="text" x-model="search" placeholder="Cari..." class="text-xs border-gray-200 rounded-lg py-1.5 px-3 w-28 sm:w-36 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 transition-colors">
        </div>
        
        <div class="flex-grow overflow-hidden mb-4 relative min-h-[160px]">
            <table class="w-full text-sm text-left">
                <tbody>
                    <template x-for="item in paginatedData" :key="item.id">
                        <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                            <td class="py-2 px-2">
                                <span x-text="item.name"></span>
                                <span class="block text-[10px] text-gray-400" x-text="item.division_name"></span>
                            </td>
                            <td class="py-2 px-2 text-right align-top">
                                <form :action="`{{ url('admin/master-data/department') }}/${item.id}`" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
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

        <div class="flex justify-between items-center mb-4 text-xs" x-show="totalPages > 1" x-cloak>
            <button type="button" @click="prevPage" :disabled="currentPage === 1" class="px-2 py-1 text-gray-500 hover:text-blue-600 disabled:opacity-30 disabled:hover:text-gray-500 font-medium transition-colors">&laquo; Prev</button>
            <span class="text-gray-400 font-medium"><span x-text="currentPage" class="text-gray-700"></span> / <span x-text="totalPages"></span></span>
            <button type="button" @click="nextPage" :disabled="currentPage === totalPages" class="px-2 py-1 text-gray-500 hover:text-blue-600 disabled:opacity-30 disabled:hover:text-gray-500 font-medium transition-colors">Next &raquo;</button>
        </div>

        <form action="{{ route('admin.master-data.store', 'department') }}" method="POST" class="mt-auto w-full">
            @csrf
            <div class="flex flex-col gap-2">
                <select name="division_id" x-model="selectedFilter" required class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" required class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Tambah Departemen...">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah
                </button>
            </div>
        </form>
    </div>

    <!-- Card: Jenis Magang -->
    <x-master-data-card title="Jenis Magang" type="internship-type" placeholder="Tambah Jenis Magang..." :data="$internshipTypes" />

    <!-- Card: Jenjang Pemagangan -->
    <x-master-data-card title="Jenjang Pemagangan" type="education-level" placeholder="Tambah Jenjang..." :data="$educationLevels" />

    <!-- Card: Universitas / Sekolah -->
    <x-master-data-card title="Universitas / Sekolah" type="university" placeholder="Tambah Universitas..." :data="$universities" />

    <!-- Card: Fakultas -->
    <x-master-data-card title="Fakultas" type="faculty" placeholder="Tambah Fakultas..." :data="$faculties" />

    <!-- Card: Jurusan -->
    <x-master-data-card title="Jurusan" type="major" placeholder="Tambah Jurusan..." :data="$majors" />

    <!-- Card: Program Studi -->
    <x-master-data-card title="Program Studi (Prodi)" type="study-program" placeholder="Tambah Prodi..." :data="$studyPrograms" />

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('masterCard', (initialData) => ({
            search: '',
            selectedFilter: '',
            currentPage: 1,
            itemsPerPage: 5,
            items: initialData,

            get filteredData() {
                let result = this.items;

                if (this.selectedFilter !== '') {
                    result = result.filter(item => item.division_id == this.selectedFilter);
                }

                if (this.search.trim() !== '') {
                    const query = this.search.toLowerCase();
                    result = result.filter(item => {
                        return item.name.toLowerCase().includes(query) || 
                               (item.division_name && item.division_name.toLowerCase().includes(query));
                    });
                }
                
                return result;
            },

            get totalPages() {
                return Math.ceil(this.filteredData.length / this.itemsPerPage) || 1;
            },

            get paginatedData() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredData.slice(start, start + this.itemsPerPage);
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },

            init() {
                this.$watch('search', () => {
                    this.currentPage = 1;
                });
            }
        }));
    });
</script>
@endsection
