<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Every page reads the promotions table (the header's Loans menu and the
 * homepage placements), so every feature test gets a migrated in-memory
 * database -- SQLite, per phpunit.xml, never the MySQL development data.
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
}
