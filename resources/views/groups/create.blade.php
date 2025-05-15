@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guide.groups.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
            <i class="fas fa-arrow-left mr-2"></i> Terug naar groepen
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 p-6">
            <h1 class="text-3xl font-bold text-white">Nieuwe Groep Aanmaken</h1>
            <p class="text-indigo-100 mt-2">Maak een nieuwe groep voor deze reis</p>
        </div>
        
        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                    <p class="font-semibold">Er zijn fouten gevonden:</p>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('groups.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Groepsnaam *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschrijving</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">Optioneel. Geef een korte beschrijving van de groep.</p>
                </div>
                
                <div class="mb-4">
                    <label for="max_members" class="block text-sm font-medium text-gray-700 mb-1">Maximum aantal leden *</label>
                    <input type="number" name="max_members" id="max_members" value="{{ old('max_members', 10) }}" required min="1" max="50"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus-circle mr-2"></i> Groep Aanmaken
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
