<?php

namespace Oro\Bundle\CustomerBundle\Entity;

use Symfony\Component\PropertyAccess\PropertyAccessor;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\CustomerBundle\Provider\CustomerUserRelationsProvider;
use Oro\Bundle\WebsiteBundle\Manager\WebsiteManager;

class GuestCustomerUserManager
{
    /**
     * @var WebsiteManager
     */
    protected $websiteManager;

    /**
     * @var CustomerUserManager
     */
    protected $customerUserManager;

    /**
     * @var CustomerUserRelationsProvider
     */
    protected $customerUserRelationsProvider;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param WebsiteManager $websiteManager
     * @param CustomerUserManager $customerUserManager
     * @param CustomerUserRelationsProvider $customerUserRelationsProvider
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(
        WebsiteManager $websiteManager,
        CustomerUserManager $customerUserManager,
        CustomerUserRelationsProvider $customerUserRelationsProvider,
        PropertyAccessor $propertyAccessor
    ) {
        $this->websiteManager = $websiteManager;
        $this->customerUserManager = $customerUserManager;
        $this->customerUserRelationsProvider = $customerUserRelationsProvider;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param array $properties
     *
     * @return CustomerUser
     */
    public function generateGuestCustomerUser(array $properties = [])
    {
        $customerUser = new CustomerUser();
        $customerUser->setIsGuest(true);
        $customerUser->setEnabled(false);
        $customerUser->setConfirmed(false);

        foreach ($properties as $propertyPath => $value) {
            if ($this->propertyAccessor->isWritable($customerUser, $propertyPath)) {
                $this->propertyAccessor->setValue($customerUser, $propertyPath, $value);
            }
        }

        $generatedPassword = $this->customerUserManager->generatePassword(10);
        $customerUser->setPlainPassword($generatedPassword);
        $this->customerUserManager->updatePassword($customerUser);

        $website = $this->websiteManager->getCurrentWebsite();
        $customerUser->setWebsite($website);

        // TODO move current organization to token storage BB-9269
        $customerUser->setOrganization($website->getOrganization());

        $anonymousGroup = $this->customerUserRelationsProvider->getCustomerGroup();
        // TODO owner should be fixed when current organization will be moved to token storage BB-9269
        $customerUser->setOwner($anonymousGroup->getOwner());

        $customerUser->createCustomer();

        $customerUser->getCustomer()->setGroup($anonymousGroup);

        return $customerUser;
    }

    /**
     * @param string|null $userName
     * @param AbstractAddress|null $address
     *
     * @return CustomerUser
     */
    public function createFromAddress($userName = null, AbstractAddress $address = null)
    {
        if ($userName === null) {
            $userName = $this->customerUserManager->generatePassword(10);
        }

        return $this->generateGuestCustomerUser([
            'username' => $userName,
            'name_prefix' => $address->getNamePrefix(),
            'first_name' => $address->getFirstName(),
            'middle_name' => $address->getMiddleName(),
            'last_name' => $address->getLastName(),
            'name_suffix' => $address->getNameSuffix()
        ]);
    }

    /**
     * @param CustomerUser $customerUser
     * @param string $userName
     * @param AbstractAddress $address
     *
     * @return CustomerUser
     */
    public function updateFromAddress(CustomerUser $customerUser, $userName, AbstractAddress $address)
    {
        $customerUser->setUsername($userName);
        $customerUser->setNamePrefix($address->getNamePrefix());
        $customerUser->setFirstName($address->getFirstName());
        $customerUser->setMiddleName($address->getMiddleName());
        $customerUser->setLastName($address->getLastName());
        $customerUser->setNameSuffix($address->getNameSuffix());
        $customerUser->fillCustomer();

        return $customerUser;
    }
}