@extends('layouts.app')

@section('content')
<div class="py-12" data-spa-content>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">Selamat datang, {{ Auth::user()->name }}!</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    @hasRole('gudang')
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 rounded-full">
                                <x-heroicon-o-users class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">Users</h2>
                                <p class="text-2xl font-bold text-blue-600">{{ App\Models\User::count() }}</p>
                            </div>
                        </div>
                    </div>
                    @endhasRole

                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-full">
                                <x-heroicon-o-check-circle class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">Status</h2>
                                <p class="text-2xl font-bold text-green-600">Aktif</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-500 rounded-full">
                                <x-heroicon-o-identification class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">Role</h2>
                                <p class="text-2xl font-bold text-purple-600 capitalize">{{ Auth::user()->role }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('admin.bahan_baku.index') }}" data-spa 
                           class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                            <x-heroicon-o-cube class="h-8 w-8 text-blue-500 mr-3" />
                            <div>
                                <h4 class="font-medium text-gray-900">Manajemen Bahan Baku</h4>
                                <p class="text-sm text-gray-500">Kelola bahan baku</p>
                            </div>
                        </a>

                        <a href="{{ route('profile') }}" data-spa 
                           class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                            <x-heroicon-o-user class="h-8 w-8 text-green-500 mr-3" />
                            <div>
                                <h4 class="font-medium text-gray-900">Profile</h4>
                                <p class="text-sm text-gray-500">Update informasi profil</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Timeline</h3>
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="p-6">
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <x-heroicon-s-check class="h-5 w-5 text-white" />
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Login berhasil sebagai <span class="font-medium text-gray-900">{{ Auth::user()->role }}</span></p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time>{{ now()->format('H:i') }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection