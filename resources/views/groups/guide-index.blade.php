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
            
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Groepen Beheren</h5>
                    <a href="{{ route('groups.create') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-plus me-1"></i> Nieuwe Groep
                    </a>
                </div>
                <div class="card-body">
                    @if(count($groups) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Naam</th>
                                        <th>Beschrijving</th>
                                        <th>Leden</th>
                                        <th>Status</th>
                                        <th>Begeleiders</th>
                                        <th>Acties</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groups as $group)
                                        @php
                                            $guideMembers = $group->members->filter(function($member) {
                                                return $member->user && $member->user->role === 'guide';
                                            });
                                        @endphp
                                        <tr>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ Str::limit($group->description, 50) }}</td>
                                            <td>{{ $group->memberCount }}/{{ $group->max_members }}</td>
                                            <td>
                                                @if($group->isLocked())
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-lock me-1"></i> Vergrendeld
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-lock-open me-1"></i> Open
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($guideMembers->count() > 0)
                                                    @foreach($guideMembers as $guide)
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-user-tie me-1"></i>
                                                            {{ $guide->first_name }} {{ $guide->last_name }}
                                                        </span>
                                                        @if(!$loop->last) &nbsp; @endif
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-secondary">Geen begeleiders</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('groups.edit', $group) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('groups.toggle-lock', $group) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning">
                                                            @if($group->isLocked())
                                                                <i class="fas fa-lock-open"></i>
                                                            @else
                                                                <i class="fas fa-lock"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                        onclick="if(confirm('Weet je zeker dat je deze groep wilt verwijderen?')) document.getElementById('delete-form-{{ $group->id }}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $group->id }}" action="{{ route('groups.destroy', $group) }}" method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <p class="mb-0">Er zijn nog geen groepen aangemaakt voor deze reis.</p>
                            <a href="{{ route('groups.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-1"></i> Nieuwe Groep Aanmaken
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
