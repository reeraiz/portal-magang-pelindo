@extends('layouts.app')
@section('header', 'Pengaturan Profil')

@section('content')
<div class="space-y-6">
    <!-- Header Area -->
    <div class="bg-[#0A1128] rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center text-4xl font-bold border-4 border-white/20 overflow-hidden">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    {{ substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div>
                <h2 class="text-3xl font-bold mb-1">{{ auth()->user()->name }}</h2>
                <p class="text-blue-200">{{ auth()->user()->email }}</p>
                <div class="mt-3 flex gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">{{ ucfirst(auth()->user()->role ?? 'Intern') }}</span>
                    @if(auth()->user()->division)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-white backdrop-blur-sm">{{ auth()->user()->division }}</span>
                    @endif
                </div>
            </div>
        </div>
        <!-- Decoration -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500 rounded-full mix-blend-screen opacity-20 blur-3xl"></div>
        <div class="absolute right-40 -top-20 w-48 h-48 bg-purple-500 rounded-full mix-blend-screen opacity-20 blur-3xl"></div>
    </div>

    <!-- Forms Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Profile Info & Password -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(auth()->user()->role === 'intern' && auth()->user()->internshipType && in_array(strtolower(auth()->user()->internshipType->name), ['magenta', 'kemnaker']))
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.upload-skripsi-form')
                </div>
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.update-email-settings-form')
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Danger Zone -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-red-50 p-6 sm:p-8 rounded-2xl border border-red-100 relative overflow-hidden">
                <div class="relative z-10">
                    @include('profile.partials.delete-user-form')
                </div>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-500 rounded-full mix-blend-multiply opacity-5 blur-xl"></div>
            </div>
        </div>
    </div>
</div>
@endsection
