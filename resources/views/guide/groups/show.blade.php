@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Group Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-t-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-white">{{ $group->name }}</h1>
                    <span class="ml-3 bg-indigo-900/50 text-xs text-white px-3 py-1 rounded-full">
                        {{ $group->trip->name }}
                    </span>
                </div>
                <p class="text-indigo-100 mt-2">Groep beheer en ledenlijst</p>
            </div>
            <div>
                <a href="{{ route('groups.edit', $group) }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-transparent rounded-lg font-semibold text-sm text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow transition-colors">
                    <i class="fas fa-edit mr-2"></i> Bewerken
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
        
        <!-- Group Details Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Groepsinformatie</h2>
            
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Beschrijving</h3>
                        <p class="text-gray-800">{{ $group->description ?: 'Geen beschrijving beschikbaar' }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Leden</h3>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-16 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $group->isFull() ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ min(100, ($group->getMemberCount() / $group->max_members) * 100) }}%"></div>
                                </div>
                                <span class="ml-3 text-gray-900 font-medium">
                                    {{ $group->getMemberCount() }}/{{ $group->max_members }}
                                    <span class="text-sm text-gray-500">
                                        ({{ min(100, ($group->getMemberCount() / $group->max_members) * 100) }}%)
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Aangemaakt door</h3>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-indigo-600"></i>
                                </div>
                                <span class="ml-3 text-gray-900">{{ $group->creator->name ?? 'Onbekend' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Group Members Section -->
        <div>
            <div class="flex flex-wrap justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Groepsleden beheren</h2>
                <div class="flex items-center mt-2 sm:mt-0">
                    <button id="toggleSelectAll" class="text-sm text-indigo-600 hover:text-indigo-900">
                        Selecteer alles
                    </button>
                </div>
            </div>
            
            @if ($members->isEmpty())
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <i class="fas fa-user-friends fa-lg"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Geen groepsleden</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Deze groep heeft nog geen leden. Studenten kunnen zelf lid worden van deze groep.
                    </p>
                </div>
            @else
                <form action="{{ route('groups.remove-traveller', ['group' => $group->id, 'traveller' => ':traveller_id']) }}" 
                      method="POST" 
                      id="removeMemberForm"
                      onsubmit="return confirm('Weet je zeker dat je de geselecteerde leden wilt verwijderen?');">
                    @csrf
                    @method('DELETE')
                    
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="relative w-12 px-6 sm:w-16 sm:px-8">
                                            <input type="checkbox" id="selectAll" class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Telefoon</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($members as $member)
                                        <tr class="{{ $member->user && $member->user->canManageGroups() ? 'bg-blue-50' : '' }} hover:bg-gray-50 transition-colors">
                                            <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                                                @if (!($member->user && $member->user->canManageGroups()))
                                                    <input type="checkbox" name="selected_members[]" value="{{ $member->id }}" class="member-checkbox absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-full {{ $member->user && $member->user->canManageGroups() ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }}">
                                                        <i class="fas {{ $member->user && $member->user->canManageGroups() ? 'fa-user-tie' : 'fa-user' }}"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</div>
                                                        @if ($member->user && $member->user->canManageGroups())
                                                            <div class="text-xs text-blue-600">Begeleider</div>
                                                        @endif
                                                        <div class="text-sm text-gray-500 md:hidden">{{ $member->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                                <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                                <div class="text-sm text-gray-500">{{ $member->phone }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if (!($member->user && $member->user->canManageGroups()))
                                                    <button type="button" onclick="removeMember({{ $member->id }})" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-user-minus mr-1"></i> Verwijderen
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">
                                                        <i class="fas fa-lock mr-1"></i> Begeleider
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <button type="submit" id="removeSelectedBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg font-semibold text-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-colors disabled:opacity-50 disabled:hover:bg-red-600" disabled>
                            <i class="fas fa-trash mr-2"></i> Geselecteerde leden verwijderen
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    
    <!-- Navigation Footer -->
    <div class="mt-8 text-center">
        <div class="inline-flex rounded-md shadow-sm">
            <a href="{{ route('guide.groups.index') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-700 rounded-lg font-semibold text-sm text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Terug naar groepenoverzicht
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle individual member removal
    function removeMember(travellerId) {
        if (confirm('Weet je zeker dat je dit lid wilt verwijderen uit de groep?')) {
            const form = document.getElementById('removeMemberForm');
            form.action = form.action.replace(':traveller_id', travellerId);
            form.submit();
        }
    }
    
    // Handle checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.member-checkbox');
        const removeSelectedBtn = document.getElementById('removeSelectedBtn');
        const toggleSelectAllBtn = document.getElementById('toggleSelectAll');
        
        function updateRemoveButtonState() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            removeSelectedBtn.disabled = !anyChecked;
        }
        
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
                updateRemoveButtonState();
            });
        }
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateRemoveButtonState();
                
                // Update "select all" checkbox
                if (!this.checked) {
                    selectAll.checked = false;
                } else {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    selectAll.checked = allChecked;
                }
            });
        });
        
        // Toggle select all button text
        if (toggleSelectAllBtn) {
            toggleSelectAllBtn.addEventListener('click', function() {
                const allSelected = selectAll.checked;
                selectAll.checked = !allSelected;
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allSelected;
                });
                updateRemoveButtonState();
                this.textContent = allSelected ? 'Selecteer alles' : 'Deselecteer alles';
            });
        }
    });
</script>
@endpush
@endsection
