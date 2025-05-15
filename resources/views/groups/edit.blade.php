@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Terug naar groep
                </a>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Groep Bewerken</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('groups.update', $group) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Naam</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $group->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Beschrijving</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $group->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="max_members" class="form-label">Maximaal aantal leden</label>
                            <input type="number" class="form-control @error('max_members') is-invalid @enderror" id="max_members" name="max_members" value="{{ old('max_members', $group->max_members) }}" min="{{ $group->getMemberCount() }}" max="50" required>
                            <div class="form-text">Het huidige aantal leden is {{ $group->getMemberCount() }}.</div>
                            @error('max_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="locked" name="locked" value="1" {{ $group->isLocked() ? 'checked' : '' }}>
                                <label class="form-check-label" for="locked">
                                    Groep vergrendelen
                                </label>
                                <div class="form-text">Vergrendelde groepen kunnen geen nieuwe leden accepteren.</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Opslaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
