<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\Customer;
use Adlarge\FixturesDocumentationBundle\Service\FixturesDocumentationManager;

class AppFixtures extends Fixture
{
    /**
      * @var FixturesDocumentationManager
    */
    private $documentationManager;

    /**
     * AppFixtures constructor.
     *
     * @param FixturesDocumentationManager $documentationManager
     */
    public function __construct(FixturesDocumentationManager $documentationManager)
    {
        $this->documentationManager = $documentationManager;
    }
    
    public function load(ObjectManager $manager)
    {
        $doc = $this->documentationManager->getDocumentation();
        
        $customer = (new Customer())
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setEmail('john.doe@test.fr');

        $fixtureCustomer = $doc->addFixture('Customers', [
            'id' => 1,
            'first name' => 'John',
            'last name' => 'Doe',
            'email' => 'john.doe@test.fr'
        ]);

        $manager->persist($customer);
        $manager->flush();
        
        $product = (new Product())
            ->setName("Product 1")
            ->setCategory("Category 1");

        $manager->persist($product);
        
        $productFixture = $doc->addFixture('Products', [
            'id' => 1,
            'name' => 'Product 1',
            'owner' => 'John Doe'
        ]);
        $productFixture->addLink('owner', $fixtureCustomer);

        $product = (new Product())
            ->setName("Product 2")
            ->setCategory("Category 2");

        $manager->persist($product);
        
        $productFixture = $doc->addFixture('Products', [
            'id' => 2,
            'name' => 'Product 2',
            'owner' => 'John Doe'
        ]);

        $productFixture->addLink('owner', $fixtureCustomer);

        
    }
}
