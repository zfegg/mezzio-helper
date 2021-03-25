<?php declare(strict_types = 1);

namespace ZfeggTest\MezzioHelper;

use Zfegg\MezzioHelper\Group;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{

    public function testGroup(): void
    {
        $group = Group::prefix('/api');

        $group->get('/test', 'test');
        $result = $group->getRoutes();

        $this->assertEquals(
            [
                [
                    'path' => '/api/test',
                    'middleware' => ['test',],
                    'allowed_methods' => ['GET',],
                    'name' => null,
                    'options' => [],
                ],
            ],
            $result
        );
    }
}
