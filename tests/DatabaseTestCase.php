<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DatabaseTestCase extends WebTestCase
{
    /**
     * @var EntityManager $manager
     */
    private $manager;
    /**
     * @var ORMExecutor $executor
     */
    private $executor;

    protected function setUp(): void
    {
        $this->manager = self::bootKernel()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');
        $this->executor = new ORMExecutor($this->manager, new ORMPurger());

        $schemaTool = new SchemaTool($this->manager);
        $schemaTool->updateSchema(
            $this->manager->getMetadataFactory()->getAllMetadata()
        );
    }

    public function tearDown(): void
    {
        (new SchemaTool($this->manager))->dropDatabase();
        self::ensureKernelShutdown();
    }

    /**
     * @param object|string $fixture
     */
    protected function loadFixture($fixture): void
    {
        $loader = new Loader();
        $loader->addFixture(is_object($fixture) ? $fixture : new $fixture);
        $this->executor->execute($loader->getFixtures());
    }
}
