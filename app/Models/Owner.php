<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Owner::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Owner::class, 'parent_id');
    }

    public function grandchildren()
    {
        return $this->hasManyThrough(Owner::class, Owner::class, 'parent_id', 'parent_id');
    }

    /**
     * Get trilevel hierarchy (parent -> current -> children)
     */
    public function getTrilevelHierarchy()
    {
        $hierarchy = [
            'parent' => null,
            'current' => $this,
            'children' => []
        ];

        // Get parent
        if ($this->parent_id && $this->parent_id != 0) {
            $hierarchy['parent'] = $this->parent;
        }

        // Get children
        $hierarchy['children'] = $this->children;

        return $hierarchy;
    }

    /**
     * Get complete hierarchy with grandchildren
     */
    public function getCompleteHierarchy()
    {
        $hierarchy = [
            'grandparent' => null,
            'parent' => null,
            'current' => $this,
            'children' => [],
            'grandchildren' => []
        ];

        // Get parent and grandparent
        if ($this->parent_id && $this->parent_id != 0) {
            $hierarchy['parent'] = $this->parent;
            
            if ($this->parent->parent_id && $this->parent->parent_id != 0) {
                $hierarchy['grandparent'] = $this->parent->parent;
            }
        }

        // Get children with their children (grandchildren)
        $hierarchy['children'] = $this->children()->with('children')->get();

        // Extract all grandchildren
        $grandchildren = [];
        foreach ($hierarchy['children'] as $child) {
            $grandchildren = array_merge($grandchildren, $child->children->all());
        }
        $hierarchy['grandchildren'] = $grandchildren;

        return $hierarchy;
    }

    /**
     * Get all descendants (children + grandchildren + etc)
     */
    public function getAllDescendants()
    {
        return $this->children()->with('allDescendants')->get();
    }

    /**
     * Recursive relationship for all descendants
     */
    public function allDescendants()
    {
        return $this->children()->with('allDescendants');
    }

    /**
     * Get hierarchy as flattened array for display
     */
    public function getHierarchyForDisplay()
    {
        $hierarchy = $this->getCompleteHierarchy();
        
        $displayData = [];

        // Grandparent level
        if ($hierarchy['grandparent']) {
            $displayData['grandparent'] = [
                'id' => $hierarchy['grandparent']->id,
                'name' => $hierarchy['grandparent']->owner_name,
                'name_en' => $hierarchy['grandparent']->owner_name_1,
                'level' => 'Grand Parent'
            ];
        }

        // Parent level
        if ($hierarchy['parent']) {
            $displayData['parent'] = [
                'id' => $hierarchy['parent']->id,
                'name' => $hierarchy['parent']->owner_name,
                'name_en' => $hierarchy['parent']->owner_name_1,
                'level' => 'Parent'
            ];
        }

        // Current level
        $displayData['current'] = [
            'id' => $hierarchy['current']->id,
            'name' => $hierarchy['current']->owner_name,
            'name_en' => $hierarchy['current']->owner_name_1,
            'level' => 'Current'
        ];

        // Children level
        if ($hierarchy['children']->isNotEmpty()) {
            $displayData['children'] = $hierarchy['children']->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->owner_name,
                    'name_en' => $child->owner_name_1,
                    'level' => 'Child',
                    'has_children' => $child->children->isNotEmpty()
                ];
            })->toArray();
        }

        return $displayData;
    }
}