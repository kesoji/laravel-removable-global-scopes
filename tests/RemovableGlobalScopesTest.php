<?php

namespace Kesoji\RemovableGlobalScopes\Tests;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Kesoji\RemovableGlobalScopes\RemovableGlobalScopes;

class RemovableGlobalScopesTest extends TestCase
{
    public function test_remove_named_global_scope()
    {
        TestModel::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', true);
        });

        $this->assertTrue(TestModel::hasGlobalScope('active'));
        
        // Verify both static and instance methods before removal
        $allScopes = TestModel::getAllGlobalScopes();
        $this->assertArrayHasKey(TestModel::class, $allScopes);
        $this->assertArrayHasKey('active', $allScopes[TestModel::class]);
        
        $model = new TestModel;
        $this->assertArrayHasKey('active', $model->getGlobalScopes());

        $original = TestModel::removeGlobalScope('active');

        $this->assertFalse(TestModel::hasGlobalScope('active'));
        $this->assertIsArray($original);
        
        // Verify both static and instance methods after removal
        $allScopesAfter = TestModel::getAllGlobalScopes();
        $this->assertArrayNotHasKey('active', $allScopesAfter[TestModel::class] ?? []);
        $this->assertArrayNotHasKey('active', $model->getGlobalScopes());
    }

    public function test_remove_class_based_global_scope()
    {
        TestModel::addGlobalScope(new ActiveScope);

        $this->assertTrue(TestModel::hasGlobalScope(ActiveScope::class));

        $original = TestModel::removeGlobalScope(ActiveScope::class);

        $this->assertFalse(TestModel::hasGlobalScope(ActiveScope::class));
        $this->assertIsArray($original);
    }

    public function test_remove_closure_global_scope()
    {
        $closure = function (Builder $builder) {
            $builder->where('active', true);
        };

        TestModel::addGlobalScope($closure);

        // Verify using getAllGlobalScopes (static)
        $allScopes = TestModel::getAllGlobalScopes();
        $this->assertArrayHasKey(TestModel::class, $allScopes);
        $this->assertCount(1, $allScopes[TestModel::class]);
        
        // Verify using getGlobalScopes (instance)
        $model = new TestModel;
        $this->assertCount(1, $model->getGlobalScopes());

        TestModel::removeGlobalScope($closure);

        // Verify removal with both methods
        $allScopesAfter = TestModel::getAllGlobalScopes();
        $this->assertCount(0, $allScopesAfter[TestModel::class] ?? []);
        $this->assertCount(0, $model->getGlobalScopes());
    }

    public function test_remove_multiple_global_scopes()
    {
        TestModel::addGlobalScope('scope1', function (Builder $builder) {
            $builder->where('field1', 'value1');
        });

        TestModel::addGlobalScope('scope2', function (Builder $builder) {
            $builder->where('field2', 'value2');
        });

        TestModel::addGlobalScope(new ActiveScope);

        $model = new TestModel;
        $this->assertCount(3, $model->getGlobalScopes());

        TestModel::removeGlobalScopes(['scope1', 'scope2', ActiveScope::class]);

        $this->assertCount(0, $model->getGlobalScopes());
    }

    public function test_remove_non_existent_scope_does_not_throw_exception()
    {
        $model = new TestModel;
        $this->assertCount(0, $model->getGlobalScopes());

        $original = TestModel::removeGlobalScope('non-existent');

        $this->assertIsArray($original);
        $this->assertCount(0, $model->getGlobalScopes());
    }

    public function test_original_scopes_are_returned()
    {
        TestModel::addGlobalScope('test', function (Builder $builder) {
            $builder->where('test', true);
        });

        $original = TestModel::removeGlobalScope('test');

        $this->assertArrayHasKey(TestModel::class, $original);
        $this->assertArrayHasKey('test', $original[TestModel::class]);
    }

    protected function tearDown(): void
    {
        TestModel::setAllGlobalScopes([]);
        parent::tearDown();
    }
}

class TestModel extends Model
{
    use RemovableGlobalScopes;

    protected $table = 'test_models';
    protected $guarded = [];
}

class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('active', true);
    }
}