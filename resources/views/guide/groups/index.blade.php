@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Guide Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-t-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-white">Groepen Beheren</h1>
                <p class="text-indigo-100 mt-2 text-lg">Beheer alle groepen voor deze reis</p>
            </div>
            <div>
                <a href="{{ route('groups.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-transparent rounded-lg font-semibold text-sm text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow transition-colors">
                    <i class="fas fa-plus mr-2"></i> Nieuwe Groep
                </a>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-b-lg shadow-lg p-6">
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle mt-1"></i>
                    </div>
                    <div class="ml-3">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle mt-1"></i>
                    </div>
                    <div class="ml-3">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Groups Dashboard -->
        <div class="mb-6">
            <div class="flex flex-wrap items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Groepen Overzicht</h2>
                <div class="mt-2 md:mt-0">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Beheer en organiseer alle groepen voor deze reis
                    </span>
                </div>
            </div>
            
            @if ($managedGroups->isEmpty())
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Geen groepen beschikbaar</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">
                        Er zijn nog geen groepen aangemaakt voor deze reis.
                    </p>
                    <a href="{{ route('groups.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Nieuwe Groep Aanmaken
                    </a>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Beschrijving</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leden</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Gemaakt door</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Aangemaakt</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($managedGroups as $group)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $group->name }}</div>
                                            <div class="text-sm text-gray-500 md:hidden">
                                                {{ Str::limit($group->description, 30) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 hidden md:table-cell">
                                            <div class="text-sm text-gray-500">{{ Str::limit($group->description, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $group->isFull() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $group->getMemberCount() }}/{{ $group->max_members }}
                                                </span>
                                                
                                                @if($group->getMemberCount() >= $group->max_members)
                                                    <span class="ml-2 text-xs text-red-600">Vol</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                            <div class="text-sm text-gray-500">{{ $group->creator->name ?? 'Onbekend' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                            <div class="text-sm text-gray-500">
                                                <time datetime="{{ $group->created_at->format('Y-m-d') }}" title="{{ $group->created_at->format('d-m-Y H:i') }}">
                                                    {{ $group->created_at->format('d-m-Y') }}
                                                </time>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <a href="{{ route('guide.groups.show', $group) }}" class="text-indigo-600 hover:text-indigo-900" title="Bekijken">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="sr-only">Bekijken</span>
                                                </a>
                                                <a href="{{ route('groups.edit', $group) }}" class="text-blue-600 hover:text-blue-900" title="Bewerken">
                                                    <i class="fas fa-edit"></i>
                                                    <span class="sr-only">Bewerken</span>
                                                </a>
                                                <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Verwijderen" onclick="return confirm('Weet je zeker dat je deze groep wilt verwijderen?')">
                                                        <i class="fas fa-trash"></i>
                                                        <span class="sr-only">Verwijderen</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
