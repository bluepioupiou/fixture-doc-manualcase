<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\Customer;
use Adlarge\FixturesDocumentationBundle\Service\FixturesDocumentationManager;
use Exception;

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

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $doc = $this->documentationManager->getDocumentation();

        $john = (new Customer())
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setEmail('john.doe@test.fr');

        $manager->persist($john);

        $johnFixture = $doc->addFixture('Customer', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@test.fr'
        ]);

        $susan = (new Customer())
            ->setFirstname('Susan')
            ->setLastname('Doyle')
            ->setEmail('susan.doyle@test.com');

        $manager->persist($susan);

        $susanFixture = $doc->addFixture('Customer', [
            'firstname' => 'Susan',
            'lastname' => 'Doyle',
            'email' => 'susan.doyle@test.com'
        ]);

        $product = (new Product())
            ->setName("Product 1")
            ->setCategory("Category 1")
            ->setOwner($john)
            ->setTags(['tag1', 'tag2']);

        $manager->persist($product);

        $productFixture = $doc->addFixture('Products', [
            'name' => 'Product 1',
            'owner' => 'John Doe'
        ]);
        $productFixture->addLink('owner', $johnFixture);

        $product = (new Product())
            ->setName("Product 2")
            ->setCategory("Category 2")
            ->setOwner($john)
            ->setTags(['tag2', 'tag2', 'tag3']);

        $manager->persist($product);

        $productFixture = $doc->addFixture('Products', [
            'name' => 'Product 2',
            'owner' => 'John Doe'
        ]);
        $productFixture->addLink('owner', $johnFixture);

        $product = (new Product())
            ->setName("Product 3")
            ->setCategory("Category 2")
            ->setOwner($susan)
            ->setTags(['tag3']);

        $manager->persist($product);

        $productFixture = $doc->addFixture('Products', [
            'name' => 'Product 3',
            'owner' => 'John Doe'
        ]);
        $productFixture->addLink('owner', $susanFixture);


        $manager->flush();

        $customerFixtures = [];
        $customers = [];
        for ($i = 0; $i < 100 ; $i++) {
            $customer = (new Customer())
                ->setFirstname('CustomerFirstname' . $i)
                ->setLastname('CustomerLastname' . $i)
                ->setEmail('email' . $i . '@test.com');

            $manager->persist($customer);

            $customerFixture = $doc->addFixture('Customer', [
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'email' => $customer->getEmail()
            ]);
            array_push($customers, $customer);
            array_push($customerFixtures, $customerFixture);
        }
        $manager->flush();

        for ($i = 0; $i < 1000 ; $i++) {
            $whichCustomer = random_int(0, 99);
            $product = (new Product())
                ->setName("Product" . $i)
                ->setCategory("Category " . random_int(1, 10))
                ->setOwner($customers[$whichCustomer])
                ->setTags(['tag3']);

            $manager->persist($product);

            $productFixture = $doc->addFixture('Products', [
                'name' => $product->getName(),
                'owner' => $product->getOwner()->getEmail()
            ]);
            $productFixture->addLink('owner', $customerFixtures[$whichCustomer]);

        }
        $manager->flush();
    }
}
