@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
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
            
            <!-- My Groups Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Mijn Groepen</h5>
                </div>
                <div class="card-body">
                    @if(count($myGroups) > 0)
                        <div class="row">
                            @foreach($myGroups as $group)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 shadow-sm border-0">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title">
                                                    {{ $group->name }}
                                                    @if($group->isLocked())
                                                        <span class="badge bg-danger ms-2"><i class="fas fa-lock"></i></span>
                                                    @endif
                                                </h5>
                                            </div>
                                            <p class="card-text">{{ Str::limit($group->description, 100) }}</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-users me-1"></i> {{ $group->getMemberCount() }}/{{ $group->max_members }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-outline-primary">
                                                        Bekijk
                                                    </a>
                                                    <form action="{{ route('groups.leave', $group) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        @if(!$group->isFull() && !$group->isLocked())
                                                        <form action="{{ route('groups.join', $group) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Weet je zeker dat je deze groep wilt verlaten?')">
                                                                Verlaat
                                                            </button>
                                                        </form>
                                                        @elseif($group->isLocked())
                                                        <button class="btn btn-sm btn-secondary ms-1" disabled>
                                                            <i class="fas fa-lock me-1"></i> Vergrendeld
                                                        </button>
                                                        @endif
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <p class="mb-0">Je bent nog geen lid van een groep.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Available Groups Section -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Beschikbare Groepen</h5>
                </div>
                <div class="card-body">
                    @if(count($availableGroups) > 0)
                        <div class="row">
                            @foreach($availableGroups as $group)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 shadow-sm border-0">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title">
                                                    {{ $group->name }}
                                                    @if($group->isLocked())
                                                        <span class="badge bg-danger ms-2"><i class="fas fa-lock"></i></span>
                                                    @endif
                                                </h5>
                                            </div>
                                            <p class="card-text">{{ Str::limit($group->description, 100) }}</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-users me-1"></i> {{ $group->getMemberCount() }}/{{ $group->max_members }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-outline-primary">
                                                        Bekijk
                                                    </a>
                                                    
                                                    @if(!$group->isFull() && !$group->isLocked())
                                                        <form action="{{ route('groups.join', $group) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success ms-1">
                                                                Lid worden
                                                            </button>
                                                        </form>
                                                    @elseif($group->isLocked())
                                                        <button class="btn btn-sm btn-secondary ms-1" disabled>
                                                            <i class="fas fa-lock me-1"></i> Vergrendeld
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary ms-1" disabled>
                                                            <i class="fas fa-users-slash me-1"></i> Groep vol
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="mb-0">Er zijn geen beschikbare groepen om lid van te worden.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
