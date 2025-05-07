@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-indigo-600 p-6">
                <h1 class="text-2xl font-bold text-white">Groep Bewerken</h1>
                <p class="text-indigo-100 mt-2">{{ $group->name }}</p>
            </div>
            
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Fout!</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('groups.update', $group) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Groep naam</label>
                        <input type="text" name="name" id="name" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('name', $group->name) }}" required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschrijving</label>
                        <textarea name="description" id="description" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $group->description) }}</textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label for="max_members" class="block text-sm font-medium text-gray-700 mb-1">Maximum aantal leden</label>
                        <input type="number" name="max_members" id="max_members" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" min="{{ $group->getMemberCount() }}" value="{{ old('max_members', $group->max_members) }}" required>
                        <p class="text-xs text-gray-500 mt-1">Kan alleen verhoogd worden als er al leden in deze groep zitten.</p>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <a href="{{ route('guide.groups.show', $group) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                            Annuleren
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Opslaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
