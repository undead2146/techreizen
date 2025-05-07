@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">
                    @if($isGuide)
                        Groepen Beheren
                    @else
                        Mijn Groepen
                    @endif
                </h1>
                <p class="text-blue-100 mt-2">
                    @if($isGuide)
                        Bekijk en beheer alle groepen voor deze reis
                    @else
                        Bekijk je groepen en vind nieuwe groepen om aan deel te nemen
                    @endif
                </p>
            </div>
            
            @if($isGuide)
            <div class="mt-4 md:mt-0">
                <a href="{{ route('groups.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 hover:bg-indigo-50">
                    <i class="fas fa-plus mr-2"></i> Nieuwe Groep
                </a>
            </div>
            @endif
        </div>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if($isGuide)
        <!-- Guide View: All Groups Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beschrijving</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Leden</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($groups as $group)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $group->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ Str::limit($group->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $group->max_members }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('guide.groups.show', $group) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Bekijken</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Er zijn nog geen groepen aangemaakt voor deze reis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <!-- Traveller View: My Groups & Available Groups -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Mijn Groepen</h2>
            
            @if(count($joinedGroups) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($joinedGroups as $group)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $group->name }}</h3>
                            </div>
                            <div class="px-4 py-3">
                                <p class="text-gray-600 mb-3">{{ Str::limit($group->description, 100) }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-users mr-1"></i> {{ $group->max_members }} maximale leden
                                    </span>
                                    <a href="{{ route('groups.show', $group) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md text-sm text-white hover:bg-blue-700">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                    <p class="text-gray-500">Je bent nog geen lid van een groep.</p>
                </div>
            @endif
        </div>
            
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Beschikbare Groepen</h2>
            
            @if(count($availableGroups) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableGroups as $group)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $group->name }}</h3>
                            </div>
                            <div class="px-4 py-3">
                                <p class="text-gray-600 mb-3">{{ Str::limit($group->description, 100) }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-users mr-1"></i> {{ $group->max_members }} maximale leden
                                    </span>
                                    <a href="{{ route('groups.show', $group) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md text-sm text-white hover:bg-blue-700">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                    <p class="text-gray-500">Er zijn geen beschikbare groepen om lid van te worden.</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
