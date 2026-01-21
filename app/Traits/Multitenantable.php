<?php
namespace App\Traits;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait Multitenantable
{
    /**
     * The "Boot" method of the trait.
     * Laravel automatically calls this when a model uses the trait.
     */
    protected static function bootMutitenantable()
    {
        // 1. The Reading Logic (Global Scope)
        // This runs on every select query (User::all(), Student::find(1), etc.)
        static::addGlobalScope(new SchoolScope);

        // 2. The Writing Logic (Auto-Injection)
        // This runs before a record is created in the database.
        static::creating(function (Model $model) {
            $activeSchool = session('active_school');

            // If we are currently "inside" a school context, assign the ID automatically.
            // This prevents developers from forgetting to add 'school_id' => $id
            if ($activeSchool) {
                $model->school_id = $activeSchool;
            }
        });
    }
}
