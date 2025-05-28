<?php

namespace Kesoji\RemovableGlobalScopes\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Kesoji\RemovableGlobalScopes\RemovableGlobalScopesServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            RemovableGlobalScopesServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('test_models', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
}