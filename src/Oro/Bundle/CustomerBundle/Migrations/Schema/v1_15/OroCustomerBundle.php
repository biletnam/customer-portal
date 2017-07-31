<?php

namespace Oro\Bundle\CustomerBundle\Migrations\Schema\v1_15;

use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCustomerBundle implements Migration, ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->updateCustomerVisitorTable($schema);
    }

    /**
     * Update oro_customer_visitor table
     *
     * @param Schema $schema
     */
    protected function updateCustomerVisitorTable(Schema $schema)
    {
        $table = $schema->getTable('oro_customer_visitor');
        $table->addColumn('customer_user_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_customer_user'),
            ['customer_user_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addUniqueIndex(['customer_user_id'], 'idx_customer_visitor_id_customer_user_id');
    }
}
