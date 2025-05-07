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
     * Display a listing of the groups.
     * Works for both travelers and guides.
     */
    public function index()
    {
        $user = Auth::user();
        $isGuide = $user->role === 'guide' || $user->role === 'admin';
        
        // Get the trip ID 
        $tripId = $user->getTripId();
        
        // Check which columns exist in the groups table
        $groupColumns = Schema::getColumnListing('groups');
        $hasIsOpen = in_array('is_open', $groupColumns);
        $hasCurrentMembers = in_array('current_members', $groupColumns);
        
        if ($isGuide) {
            // Guides see all groups for the current trip
            $groups = Group::where('trip_id', $tripId)->get();
            
            // Add virtual properties if columns don't exist
            if (!$hasIsOpen || !$hasCurrentMembers) {
                foreach ($groups as $group) {
                    if (!$hasIsOpen) {
                        $group->is_open = true; // Default all groups to open
                    }
                    if (!$hasCurrentMembers) {
                        // Count members directly from relationship
                        $group->current_members = $group->members()->count();
                    }
                }
            }
            
            return view('groups.index', [
                'groups' => $groups,
                'isGuide' => true
            ]);
        } else {
            // Travelers see their groups and available groups
            $traveller = $user->traveller;
            $joinedGroups = $traveller ? $traveller->groups : collect([]);
            
            // Get all groups without filtering by missing columns
            $availableGroups = Group::where('trip_id', $tripId)->get();
            
            // Filter manually if columns don't exist
            if (!$hasIsOpen || !$hasCurrentMembers) {
                $availableGroups = $availableGroups->filter(function ($group) use ($hasIsOpen, $hasCurrentMembers) {
                    // Count members if column doesn't exist
                    $memberCount = $hasCurrentMembers ? $group->current_members : $group->members()->count();
                    
                    // Consider all groups open if column doesn't exist
                    $isOpen = $hasIsOpen ? $group->is_open : true;
                    
                    return $isOpen && $memberCount < $group->max_members;
                });
            }
            
            return view('groups.index', [
                'joinedGroups' => $joinedGroups,
                'availableGroups' => $availableGroups,
                'isGuide' => false
            ]);
        }
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
        $isGuide = $user->role === 'guide' || $user->role === 'admin';
        
        // Check if the group belongs to the user's trip
        if ($group->trip_id != $user->getTripId()) {
            return redirect()->route('groups.index')
                ->with('error', 'Deze groep behoort niet tot jouw reis.');
        }
        
        // Load members relationship
        $group->load('members');
        
        return view('groups.show', [
            'group' => $group,
            'isGuide' => $isGuide,
            'isMember' => $user->traveller ? $user->traveller->groups->contains($group->id) : false,
        ]);
    }

    /**
     * Show the form for editing the specified group.
     */
    public function edit(Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')->with('error', 'You do not have permission to edit groups.');
        }
        
        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified group in storage.
     */
    public function update(Request $request, Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')->with('error', 'You do not have permission to update groups.');
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
        ]);
        
        return redirect()->route('guide.groups.show', $group)->with('success', 'Group updated successfully!');
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Group $group)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')->with('error', 'You do not have permission to delete groups.');
        }
        
        // Remove all travellers from this group first
        Traveller::where('group_id', $group->id)->update(['group_id' => null]);
        
        // Now delete the group
        $group->delete();
        
        return redirect()->route('guide.groups.index')->with('success', 'Group deleted successfully!');
    }
    
    /**
     * Join a group
     */
    public function join(Group $group)
    {
        $user = Auth::user();
        $traveller = $user->traveller;
        
        if (!$traveller) {
            return redirect()->route('home')->with('error', 'Traveller profile not found.');
        }
        
        // Check if traveller is already in a group
        if ($traveller->hasGroup()) {
            return redirect()->route('groups.show', $group)->with('info', 'You are already a member of a group. Leave your current group first.');
        }
        
        // Check if group is full
        if ($group->isFull()) {
            return redirect()->route('groups.index')->with('error', 'This group is full.');
        }
        
        // Add traveller to group
        if ($traveller->joinGroup($group)) {
            return redirect()->route('groups.show', $group)->with('success', 'You have joined the group successfully!');
        }
        
        return redirect()->route('groups.index')->with('error', 'Could not join the group.');
    }
    
    /**
     * Leave a group
     */
    public function leave(Group $group)
    {
        $user = Auth::user();
        $traveller = $user->traveller;
        
        if (!$traveller) {
            return redirect()->route('home')->with('error', 'Traveller profile not found.');
        }
        
        // Check if traveller is in this group
        if ($traveller->group_id !== $group->id) {
            return redirect()->route('groups.index')->with('error', 'You are not a member of this group.');
        }
        
        // Remove traveller from group
        if ($traveller->leaveGroup()) {
            return redirect()->route('groups.index')->with('success', 'You have left the group.');
        }
        
        return redirect()->route('groups.show', $group)->with('error', 'Could not leave the group.');
    }
    
    /**
     * Remove a traveller from a group (admin/guide only)
     */
    public function removeTraveller(Group $group, Traveller $traveller)
    {
        if (!Auth::user()->canManageGroups()) {
            return redirect()->route('groups.index')->with('error', 'You do not have permission to manage group members.');
        }
        
        // Check if traveller is in this group
        if ($traveller->group_id !== $group->id) {
            return redirect()->route('guide.groups.show', $group)->with('error', 'This traveller is not a member of this group.');
        }
        
        // Remove traveller from group
        $traveller->leaveGroup();
        
        return redirect()->route('guide.groups.show', $group)->with('success', 'Member removed from group successfully.');
    }
}
