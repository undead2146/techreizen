<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Trip;
use App\Models\Traveller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class GroupController extends Controller
{
    /**
     * Display a listing of the groups for travelers
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get traveler record associated with current user
        $traveller = $user->traveller;
        
        if (!$traveller) {
            return redirect()->route('traveller.home')
                ->with('error', 'Je hebt nog geen reizigers profiel.');
        }
        
        // Get all groups for the traveler's trip
        $allGroups = Group::where('trip_id', $traveller->trip_id)->get();
        
        // Get the group this traveler belongs to
        $myGroup = $traveller->group;
        $myGroups = $myGroup ? collect([$myGroup]) : collect();
        
        // Available groups are those in the trip that the traveler is NOT in
        $availableGroups = $allGroups->filter(function($group) use ($traveller) {
            return $traveller->group_id != $group->id;
        });
        
        return view('groups.index', [
            'myGroups' => $myGroups,
            'availableGroups' => $availableGroups,
            'isGuide' => false
        ]);
    }

    /**
     * Display a list of groups for guides
     */
    public function guideIndex()
    {
        $user = Auth::user();
        
        if (!$user->canManageGroups()) {
            return redirect()->route('groups.index')
                ->with('error', 'Je hebt geen toestemming om groepen te beheren.');
        }
        
        // Get the trip ID the guide is assigned to
        $tripId = $user->getTripId();
        
        if (!$tripId) {
            return redirect()->route('guide.home')
                ->with('error', 'Je bent niet toegewezen aan een reis.');
        }
        
        // Get all groups for this trip
        $groups = Group::where('trip_id', $tripId)->get();
        
        // Add member count for each group
        foreach ($groups as $group) {
            $group->memberCount = $group->getMemberCount();
        }
        
        return view('groups.guide-index', [
            'groups' => $groups,
            'isGuide' => true
        ]);
    }

    /**
     * Show the form for creating a new group.
     * Only accessible by guides.
     */
    public function create()
    {
        // Make sure only guides can access this
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')
                ->with('error', 'Je hebt geen toestemming om groepen aan te maken.');
        }
        
        return view('groups.create');
    }

    /**
     * Store a newly created group in storage.
     */
    public function store(Request $request)
    {
        // Ensure only guides can create groups
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')
                ->with('error', 'Je hebt geen toestemming om groepen aan te maken.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_members' => 'required|integer|min:1|max:50',
        ]);
        
        $group = new Group();
        $group->name = $validated['name'];
        $group->description = $validated['description'] ?? '';
        $group->max_members = $validated['max_members'];
        $group->trip_id = Auth::user()->getTripId();
        $group->created_by = Auth::id();
        
        // Check if columns exist before setting them
        if (Schema::hasColumn('groups', 'is_open')) {
            $group->is_open = $request->has('is_open');
        }
        
        if (Schema::hasColumn('groups', 'current_members')) {
            $group->current_members = 0;
        }
        
        $group->save();
        
        return redirect()->route('guide.groups.index')
            ->with('success', 'Groep succesvol aangemaakt!');
    }

    /**
     * Display the specified group.
     * Works for both guides and travelers.
     */
    public function show(Group $group)
    {
        $user = Auth::user();
        $isGuide = $user->canManageGroups();
        
        // For guides, check if the group is in their trip
        if ($isGuide) {
            $tripId = $user->getTripId();
            
            if (!$tripId || $group->trip_id != $tripId) {
                return redirect()->route('guide.groups.index')
                    ->with('error', 'Deze groep behoort niet tot jouw reis.');
            }
            
            // Get all travelers for this trip who are not in this group
            $availableTravellers = Traveller::where('trip_id', $tripId)
                ->where(function($query) use ($group) {
                    $query->whereNull('group_id')
                        ->orWhere('group_id', '!=', $group->id);
                })
                ->orderBy('group_id', 'asc') // Group by membership status
                ->orderBy('last_name', 'asc') // Then by last name
                ->get();
            
            return view('groups.show', [
                'group' => $group,
                'isGuide' => true,
                'isMember' => false,
                'availableTravellers' => $availableTravellers
            ]);
        }
        
        // For travelers, check if they have access to this group
        $traveller = $user->traveller;
        
        if (!$traveller) {
            return redirect()->route('traveller.home')
                ->with('error', 'Je hebt nog geen reizigers profiel.');
        }
        
        if ($group->trip_id != $traveller->trip_id) {
            return redirect()->route('groups.index')
                ->with('error', 'Deze groep behoort niet tot jouw reis.');
        }
        
        // Check if traveler is a member of this group
        $isMember = $traveller->group_id == $group->id;
        
        return view('groups.show', [
            'group' => $group,
            'isGuide' => false,
            'isMember' => $isMember
        ]);
    }

    /**
     * Show the form for editing the specified group.
     */
    public function edit(Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')->with('error', 'Je hebt geen toestemming om groepen te bewerken.');
        }
        
        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified group in storage.
     */
    public function update(Request $request, Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')->with('error', 'Je hebt geen toestemming om groepen te wijzigen.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_members' => 'nullable|integer|min:' . $group->getMemberCount(),
        ]);
        
        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? $group->description,
            'max_members' => $validated['max_members'] ?? $group->max_members,
            'locked' => $request->has('locked'),
        ]);
        
        return redirect()->route('groups.show', $group)->with('success', 'Groep succesvol bijgewerkt!');
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')->with('error', 'Je hebt geen toestemming om groepen te verwijderen.');
        }
        
        // Remove all travellers from this group first
        Traveller::where('group_id', $group->id)->update(['group_id' => null]);
        
        // Now delete the group
        $group->delete();
        
        return redirect()->route('guide.groups.index')
            ->with('success', 'Groep succesvol verwijderd!');
    }
    
    /**
     * Join a group
     */
    public function join(Group $group)
    {
        $user = Auth::user();
        $traveller = $user->traveller;
        
        if (!$traveller) {
            return redirect()->route('home')->with('error', 'Reizigers profiel niet gevonden.');
        }
        
        // Check if group is locked
        if ($group->isLocked()) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Deze groep is vergrendeld door een begeleider.');
        }
        
        // Check if traveller is already in a group
        if ($traveller->hasGroup()) {
            return redirect()->route('groups.show', $group)
                ->with('info', 'Je bent al lid van een groep. Verlaat eerst je huidige groep.');
        }
        
        // Check if group is full
        if ($group->isFull()) {
            return redirect()->route('groups.index')
                ->with('error', 'Deze groep is vol.');
        }
        
        // Add traveller to group
        if ($traveller->joinGroup($group)) {
            return redirect()->route('groups.show', $group)
                ->with('success', 'Je bent nu lid van deze groep!');
        }
        
        return redirect()->route('groups.index')
            ->with('error', 'Kon niet deelnemen aan de groep.');
    }
    
    /**
     * Leave a group
     */
    public function leave(Group $group)
    {
        $user = Auth::user();
        $traveller = $user->traveller;
        
        if (!$traveller) {
            return redirect()->route('home')->with('error', 'Reizigers profiel niet gevonden.');
        }
        
        // Check if group is locked
        if ($group->isLocked()) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Deze groep is vergrendeld. Je kunt een vergrendelde groep niet verlaten.');
        }
        
        // Check if traveller is in this group
        if ($traveller->group_id !== $group->id) {
            return redirect()->route('groups.index')->with('error', 'Je bent geen lid van deze groep.');
        }
        
        // Remove traveller from group
        if ($traveller->leaveGroup()) {
            return redirect()->route('groups.index')->with('success', 'Je hebt de groep verlaten.');
        }
        
        return redirect()->route('groups.show', $group)->with('error', 'Kon de groep niet verlaten.');
    }
    
    /**
     * Remove a traveller from a group (admin/guide only)
     */
    public function removeTraveller(Group $group, Traveller $traveller)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')
                ->with('error', 'Je hebt geen toestemming om leden te beheren.');
        }
        
        // Check if traveller is in this group
        if ($traveller->group_id !== $group->id) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Deze reiziger is geen lid van deze groep.');
        }
        
        // Remove traveller from group
        $traveller->leaveGroup();
        
        return redirect()->route('groups.show', $group)
            ->with('success', 'Lid succesvol verwijderd uit de groep.');
    }

    /**
     * Add travellers to a group (admin/guide only)
     */
    public function addMember(Request $request, Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')
                ->with('error', 'Je hebt geen toestemming om leden toe te voegen.');
        }
        
        $validated = $request->validate([
            'traveller_ids' => 'required|array',
            'traveller_ids.*' => 'exists:travellers,id'
        ]);
        
        // Check if group has enough space for the new members
        $currentMemberCount = $group->getMemberCount();
        $selectedCount = count($validated['traveller_ids']);
        $newTravellerCount = 0; // Count of travelers not already in this group
        
        foreach ($validated['traveller_ids'] as $travellerId) {
            $traveller = Traveller::find($travellerId);
            if (!$traveller) continue;
            
            if ($traveller->group_id != $group->id) {
                $newTravellerCount++;
            }
        }
        
        // Check if adding new travelers would exceed max members
        if ($currentMemberCount + $newTravellerCount > $group->max_members) {
            return redirect()->route('groups.show', $group)
                ->with('error', "Deze groep heeft slechts ruimte voor {$group->max_members} leden. Je hebt {$selectedCount} reizigers geselecteerd, maar er is alleen ruimte voor " . ($group->max_members - $currentMemberCount) . " nieuwe leden.");
        }
        
        $addedCount = 0;
        $movedCount = 0;
        
        foreach ($validated['traveller_ids'] as $travellerId) {
            $traveller = Traveller::find($travellerId);
            if (!$traveller) continue;
            
            // Get old group info for the success message
            if ($traveller->group_id && $traveller->group_id != $group->id) {
                $movedCount++;
                // Remove from old group first
                $traveller->leaveGroup();
            }
            
            // Add traveller to new group
            if ($traveller->joinGroup($group)) {
                $addedCount++;
            }
        }
        
        $successMessage = '';
        if ($addedCount > 0) {
            $successMessage = "{$addedCount} leden succesvol toegevoegd aan de groep.";
            if ($movedCount > 0) {
                $successMessage = "{$addedCount} leden toegevoegd aan de groep, waarvan {$movedCount} verplaatst van andere groepen.";
            }
        } else {
            $successMessage = "Geen nieuwe leden toegevoegd aan de groep.";
        }
        
        return redirect()->route('groups.show', $group)
            ->with('success', $successMessage);
    }

    /**
     * Toggle the locked status of a group
     */
    public function toggleLock(Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')
                ->with('error', 'Je hebt geen toestemming om groepen te vergrendelen/ontgrendelen.');
        }
        
        $group->toggleLock();
        $lockStatus = $group->isLocked() ? 'vergrendeld' : 'ontgrendeld';
        
        return redirect()->route('guide.groups.index')
            ->with('success', "Groep \"{$group->name}\" is nu {$lockStatus}.");
    }
}
