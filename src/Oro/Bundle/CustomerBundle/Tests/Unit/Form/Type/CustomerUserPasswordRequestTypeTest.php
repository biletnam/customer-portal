<?php

namespace Oro\Bundle\CustomerBundle\Tests\Unit\Form\Type;

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Validator\Validation;

use Oro\Bundle\CustomerBundle\Form\Type\CustomerUserPasswordRequestType;

class CustomerUserPasswordRequestTypeTest extends FormIntegrationTestCase
{
    /**
     * @var CustomerUserPasswordRequestType
     */
    protected $formType;

    protected function setUp()
    {
        parent::setUp();

        $this->formType = new CustomerUserPasswordRequestType();
    }

    protected function tearDown()
    {
        unset($this->formType);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtensions()
    {
        return [
            new ValidatorExtension(Validation::createValidator())
        ];
    }

    /**
     * @param array $options
     * @param mixed $defaultData
     * @param mixed $viewData
     * @param mixed $submittedData
     * @param mixed $expectedData
     * @dataProvider submitDataProvider
     */
    public function testSubmit(array $options, $defaultData, $viewData, $submittedData, $expectedData)
    {
        $form = $this->factory->create($this->formType, $defaultData, $options);

        $this->assertEquals($defaultData, $form->getData());
        $this->assertEquals($viewData, $form->getViewData());

        $form->submit($submittedData);
        $this->assertTrue($form->isValid());
        $this->assertEquals($expectedData, $form->getData());
    }

    /**
     * @return array
     */
    public function submitDataProvider()
    {
        return [
            'default' => [
                'options' => [],
                'defaultData' => [],
                'viewData' => [],
                'submittedData' => [
                    'email' => 'test@test.com'
                ],
                'expectedData' => [
                    'email' => 'test@test.com'
                ]
            ]
        ];
    }

    public function testGetName()
    {
        $this->assertInternalType('string', $this->formType->getName());
        $this->assertEquals(CustomerUserPasswordRequestType::NAME, $this->formType->getName());
    }
}
