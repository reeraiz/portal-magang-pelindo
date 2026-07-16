<section class="space-y-6">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-red-600">
            Hapus Akun
        </h2>

        <p class="mt-1 text-sm text-red-800/70 font-medium">
            Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.
        </p>
    </header>

    <button type="button" onclick="document.getElementById('confirm-user-deletion').classList.remove('hidden')" class="px-6 py-2.5 bg-red-600 text-white font-bold rounded-xl text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm">
        Hapus Akun Permanen
    </button>

    <!-- Modal Delete -->
    <div id="confirm-user-deletion" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('confirm-user-deletion').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Apakah Anda yakin ingin menghapus akun?
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Harap masukkan kata sandi Anda untuk mengonfirmasi.
                                    </p>
                                </div>

                                <div class="mt-4">
                                    <label for="password" class="sr-only">Password</label>
                                    <input id="password" name="password" type="password" class="w-full text-sm border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="Masukkan Password Anda" required />
                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-sm text-red-600" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Hapus Akun
                        </button>
                        <button type="button" onclick="document.getElementById('confirm-user-deletion').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
