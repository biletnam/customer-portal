<?php

namespace Oro\Bundle\CustomerBundle\Controller\Frontend;

use Symfony\Component\Routing\Annotation\Route;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\CustomerBundle\Controller\AbstractAjaxCustomerUserController;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;

class AjaxCustomerUserController extends AbstractAjaxCustomerUserController
{
    /**
     * @Route("/get-customer/{id}",
     *      name="oro_customer_frontend_customer_user_get_customer",
     *      requirements={"id"="\d+"}
     * )
     * @AclAncestor("oro_customer_frontend_customer_user_view")
     *
     * {@inheritdoc}
     */
    public function getCustomerIdAction(CustomerUser $customerUser)
    {
        return parent::getCustomerIdAction($customerUser);
    }
}
