<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JavierEguiluz\Bundle\EasyAdminBundle\Tests\Router;

use AppTestBundle\Entity\FunctionalTests\Product;
use JavierEguiluz\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use JavierEguiluz\Bundle\EasyAdminBundle\Tests\Fixtures\AbstractTestCase;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EasyAdminRouterTest extends AbstractTestCase
{
    /**
     * @var EasyAdminRouter
     */
    private $router;

    public function setUp()
    {
        parent::setUp();

        $this->initClient(array('environment' => 'default_backend'));

        $this->router = $this->client->getContainer()->get('easyadmin.router');
    }

    /**
     * @dataProvider provideEntities
     */
    public function testRouter($entity, $action, $expectEntity, array $parameters, array $expectParameters = array())
    {
        $url = $this->router->generate($entity, $action, $parameters);

        self::assertContains('entity='.$expectEntity, $url);
        self::assertContains('action='.$action, $url);

        foreach (array_merge($parameters, $expectParameters) as $key => $value) {
            self::assertContains($key.'='.$value, $url);
        }
    }

    public function provideEntities()
    {
        $product = new Product();
        $ref = new \ReflectionClass($product);
        $refPropertyId = $ref->getProperty('id');
        $refPropertyId->setAccessible(true);
        $refPropertyId->setValue($product, 1);

        return array(
            array('AppTestBundle\Entity\FunctionalTests\Category', 'new', 'Category', array('modal' => 1)),
            array('Product', 'new', 'Product', array('entity' => 'Category'), array('entity' => 'Product')),
            array($product, 'show', 'Product', array(), array('id' => 1)),
        );
    }
}
