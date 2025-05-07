@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route($isGuide ? 'guide.groups.index' : 'groups.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
            <i class="fas fa-arrow-left mr-2"></i> Terug naar groepen
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
            <h1 class="text-3xl font-bold text-white">{{ $group->name }}</h1>
            <p class="text-blue-100 mt-2">Max. {{ $group->max_members }} leden</p>
        </div>
        
        <div class="p-6">
            <div class="prose max-w-none mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Beschrijving</h3>
                <p class="text-gray-600">{{ $group->description ?: 'Geen beschrijving beschikbaar.' }}</p>
            </div>
            
            <hr class="my-6">
            
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Groepsleden</h3>
                
                @if(isset($group->members) && count($group->members) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($group->members as $member)
                            <div class="bg-gray-50 rounded-lg p-4 flex items-center">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 mr-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $member->first_name }} {{ $member->last_name }}</p>
                                    @if($isGuide)
                                        <p class="text-sm text-gray-500">{{ $member->email }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">Deze groep heeft nog geen leden.</p>
                @endif
            </div>
            
            @if(!$isGuide)
                <div class="mt-6">
                    @if(!$isMember)
                        <form action="{{ route('groups.join', $group) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition">
                                <i class="fas fa-user-plus mr-2"></i> Lid worden
                            </button>
                        </form>
                    @else
                        <form action="{{ route('groups.leave', $group) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition">
                                <i class="fas fa-user-minus mr-2"></i> Groep verlaten
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            @if($isGuide)
                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('groups.edit', $group) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:border-yellow-600 focus:ring ring-yellow-300 disabled:opacity-25 transition">
                        <i class="fas fa-edit mr-2"></i> Bewerken
                    </a>
                    
                    <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Weet je zeker dat je deze groep wilt verwijderen?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition">
                            <i class="fas fa-trash mr-2"></i> Verwijderen
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
