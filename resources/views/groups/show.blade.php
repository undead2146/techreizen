@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="mb-4">
                <a href="{{ $isGuide ? route('guide.groups.index') : route('groups.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Terug naar groepen
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info" role="alert">
                    {{ session('info') }}
                </div>
            @endif
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i> {{ $group->name }}
                        @if($group->isLocked())
                            <span class="badge bg-danger ms-2">
                                <i class="fas fa-lock me-1"></i> Vergrendeld
                            </span>
                        @endif
                    </h5>
                    
                    @if($isGuide)
                        <div>
                            <a href="{{ route('groups.edit', $group) }}" class="btn btn-sm btn-light">
                                <i class="fas fa-edit me-1"></i> Bewerken
                            </a>
                            <form action="{{ route('groups.toggle-lock', $group) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $group->isLocked() ? 'warning' : 'light' }} ms-1">
                                    @if($group->isLocked())
                                        <i class="fas fa-lock-open me-1"></i> Ontgrendelen
                                    @else
                                        <i class="fas fa-lock me-1"></i> Vergrendelen
                                    @endif
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                    <p class="mb-4">{{ $group->description }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0">Groepsleden ({{ $group->getMemberCount() }}/{{ $group->max_members }})</h5>
                        </div>
                        
                        @if (!$isGuide && !$isMember && !$group->isFull() && !$group->isLocked())
                            <form action="{{ route('groups.join', $group) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i> Word lid
                                </button>
                            </form>
                        @elseif (!$isGuide && !$isMember && ($group->isFull() || $group->isLocked()))
                            <button class="btn btn-secondary" disabled>
                                @if($group->isLocked())
                                    <i class="fas fa-lock me-1"></i> Vergrendeld
                                @else
                                    <i class="fas fa-users-slash me-1"></i> Groep vol
                                @endif
                            </button>
                        @elseif (!$isGuide && $isMember)
                            @if($group->isLocked())
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-lock me-1"></i> Vergrendelde groep kan niet worden verlaten
                                </button>
                            @else
                                <form action="{{ route('groups.leave', $group) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Weet je zeker dat je deze groep wilt verlaten?')">
                                        <i class="fas fa-user-minus me-1"></i> Verlaat groep
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                    
                    @if ($group->members->count() > 0)
                        <div class="row">
                            @foreach ($group->members as $member)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title">
                                                        {{ $member->first_name }} {{ $member->last_name }}
                                                        @php
                                                            $isGuideMember = $member->user && $member->user->role === 'guide';
                                                        @endphp
                                                        @if($isGuideMember)
                                                            <span class="badge bg-info ms-1">
                                                                <i class="fas fa-user-tie me-1"></i> Begeleider
                                                            </span>
                                                        @endif
                                                    </h5>
                                                    <h6 class="card-subtitle mb-2 text-muted">{{ $member->email }}</h6>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="avatar bg-light d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px;">
                                                        @if($isGuideMember)
                                                            <i class="fas fa-user-tie text-info"></i>
                                                        @else
                                                            <i class="fas fa-user text-secondary"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <p class="mb-1">
                                                    <i class="fas fa-graduation-cap me-2 text-muted"></i> 
                                                    @if($isGuideMember)
                                                        Begeleider
                                                    @else
                                                        {{ $member->major ? $member->major->name : 'Onbekend' }}
                                                    @endif
                                                </p>
                                                <p class="mb-1"><i class="fas fa-phone me-2 text-muted"></i> {{ $member->phone }}</p>
                                            </div>
                                            
                                            @if($isGuide)
                                                <div class="mt-3">
                                                    <form action="{{ route('groups.remove-traveller', ['group' => $group->id, 'traveller' => $member->id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Weet je zeker dat je deze persoon wilt verwijderen uit de groep?')">
                                                            <i class="fas fa-user-minus me-1"></i> Verwijderen uit groep
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <p class="mb-0">Deze groep heeft nog geen leden.</p>
                        </div>
                    @endif

                    @if($isGuide)
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Voeg leden toe aan groep</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('groups.add-member', $group) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="traveller_ids" class="form-label">
                                                <i class="fas fa-info-circle me-1 text-info"></i>
                                                Selecteer reizigers (houd CTRL ingedrukt om meerdere te selecteren)
                                            </label>
                                            <select class="form-select" id="traveller_ids" name="traveller_ids[]" multiple size="6" required>                                
                                                @if($availableTravellers->where('group_id', null)->count() > 0)
                                                <optgroup label="Beschikbare reizigers">
                                                    @foreach($availableTravellers->where('group_id', null) as $traveller)
                                                        @php
                                                            $isTravellerGuide = $traveller->user && $traveller->user->role === 'guide';
                                                        @endphp
                                                        <option value="{{ $traveller->id }}">
                                                            {{ $traveller->first_name }} {{ $traveller->last_name }}
                                                            @if($isTravellerGuide)
                                                                (Begeleider)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                @endif
                                                
                                                @php
                                                    $otherGroupTravellers = $availableTravellers->whereNotNull('group_id');
                                                @endphp
                                                
                                                @if($otherGroupTravellers->count() > 0)
                                                <optgroup label="Reizigers in andere groepen">
                                                    @foreach($otherGroupTravellers as $traveller)
                                                        @php
                                                            $otherGroup = App\Models\Group::find($traveller->group_id);
                                                            $groupName = $otherGroup ? $otherGroup->name : 'Onbekende groep';
                                                            $isTravellerGuide = $traveller->user && $traveller->user->role === 'guide';
                                                        @endphp
                                                        <option value="{{ $traveller->id }}">
                                                            {{ $traveller->first_name }} {{ $traveller->last_name }}
                                                            @if($isTravellerGuide)
                                                                (Begeleider)
                                                            @endif
                                                            ({{ $groupName }})
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                @endif
                                            </select>
                                            <small class="form-text text-muted">
                                                <ul class="mt-2">
                                                    <li>Je kunt meerdere reizigers selecteren door CTRL ingedrukt te houden tijdens het klikken.</li>
                                                    <li>Op Mac, gebruik CMD in plaats van CTRL.</li>
                                                    <li>Deze groep heeft ruimte voor {{ $group->max_members - $group->getMemberCount() }} nieuwe leden.</li>
                                                </ul>
                                            </small>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="selection-count" class="alert alert-info d-none">
                                                Je hebt <span id="selected-count">0</span> reizigers geselecteerd.
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-user-plus me-2"></i> Voeg geselecteerde reizigers toe aan groep
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const selectElement = document.getElementById('traveller_ids');
                                const selectionCountEl = document.getElementById('selection-count');
                                const selectedCountEl = document.getElementById('selected-count');
                                
                                selectElement.addEventListener('change', function() {
                                    const selectedOptions = selectElement.selectedOptions;
                                    const count = selectedOptions.length;
                                    
                                    selectedCountEl.textContent = count;
                                    
                                    if (count > 0) {
                                        selectionCountEl.classList.remove('d-none');
                                    } else {
                                        selectionCountEl.classList.add('d-none');
                                    }
                                });
                            });
                        </script>
                        @endpush
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
