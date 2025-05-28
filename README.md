# Laravel Removable Global Scopes

A Laravel package that adds the ability to dynamically remove global scopes from Eloquent models.

## Installation

You can install the package via composer:

```bash
composer require kesoji/laravel-removable-global-scopes
```

## Usage

Add the `RemovableGlobalScopes` trait to your Eloquent model:

```php
use Illuminate\Database\Eloquent\Model;
use Kesoji\RemovableGlobalScopes\RemovableGlobalScopes;

class User extends Model
{
    use RemovableGlobalScopes;
    
    // Your model code...
}
```

### Removing a Single Global Scope

You can remove a global scope by its name, class, or instance:

```php
// Remove by name
User::removeGlobalScope('active');

// Remove by class
User::removeGlobalScope(ActiveScope::class);

// Remove by instance
User::removeGlobalScope(new ActiveScope);
```

### Removing Multiple Global Scopes

Remove multiple global scopes at once:

```php
User::removeGlobalScopes(['active', 'verified', TenantScope::class]);
```

### Important Notes

⚠️ **Warning**: The `removeGlobalScope()` and `removeGlobalScopes()` methods permanently remove global scopes from the model class. This affects all subsequent queries on that model.

If you need to temporarily remove a global scope for a specific query, use Laravel's built-in `withoutGlobalScope()` method instead:

```php
// Temporary removal for a single query
User::withoutGlobalScope('active')->get();

// Temporary removal of multiple scopes
User::withoutGlobalScopes(['active', 'verified'])->get();
```

### Return Value

The `removeGlobalScope()` method returns the original global scopes array before removal, allowing you to potentially restore them later using Laravel's `addGlobalScope()` method:

```php
// Remove and store original scopes
$originalScopes = User::removeGlobalScope('active');

// Do something without the scope...

// Restore if needed
if (isset($originalScopes[User::class]['active'])) {
    User::addGlobalScope('active', $originalScopes[User::class]['active']);
}
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.