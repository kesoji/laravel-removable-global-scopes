<?php

namespace Kesoji\RemovableGlobalScopes;

use Closure;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait RemovableGlobalScopes
{
    /**
     * Remove a registered global scope.
     *
     * Warning: This method permanently removes the global scope from the model class.
     * If you need to temporarily remove a global scope for a specific query,
     * use the withoutGlobalScope() method on the query builder instead.
     *
     * @param  \Illuminate\Database\Eloquent\Scope|(\Closure(\Illuminate\Database\Eloquent\Builder): mixed)|string  $scope
     * @return array Returns the original global scopes array before removal
     */
    public static function removeGlobalScope($scope)
    {
        $originals = static::getAllGlobalScopes();
        $next = $originals;
        
        if (is_string($scope)) {
            unset($next[static::class][$scope]);
        } elseif ($scope instanceof Closure) {
            unset($next[static::class][spl_object_hash($scope)]);
        } elseif ($scope instanceof Scope) {
            unset($next[static::class][get_class($scope)]);
        }
        
        static::setAllGlobalScopes($next);

        return $originals;
    }

    /**
     * Remove multiple registered global scopes.
     *
     * Warning: This method permanently removes the global scopes from the model class.
     * If you need to temporarily remove global scopes for a specific query,
     * use the withoutGlobalScopes() method on the query builder instead.
     *
     * @param  array  $scopes
     * @return void
     */
    public static function removeGlobalScopes(array $scopes)
    {
        foreach ($scopes as $key => $scope) {
            if (is_string($key)) {
                static::removeGlobalScope($key);
            } else {
                static::removeGlobalScope($scope);
            }
        }
    }
}