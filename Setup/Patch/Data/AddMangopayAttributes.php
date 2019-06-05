<?php
declare(strict_types=1);

namespace Empisoft\Mangopay\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddMangopayAttributes
 * @package Empisoft\Customer\Setup\Patch\Data
 */
class AddMangopayAttributes implements DataPatchInterface
{
    /** @var \Magento\Customer\Setup\CustomerSetupFactory  */
    protected $customerSetupFactory;

    /** @var \Magento\Eav\Model\Entity\Attribute\SetFactory  */
    protected $attributeSetFactory;

    /** @var \Magento\Framework\Setup\ModuleDataSetupInterface */
    protected $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
    /**
     * Run code inside patch
     * If code fails, patch must be reverted, in case when we are speaking about schema - than under revert
     * means run PatchInterface::revert()
     *
     * If we speak about data, under revert means: $transaction->rollback()
     *
     * @return $this
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'mangopay_id',
            [
                'type' => 'varchar',
                'label' => 'Mangopay ID',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'user_defined' => true,
                'is_required' => false,
                'is_used_in_grid' => true,
                'unique' => false,
                'system' => false

            ]
        );

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'wallet_id',
            [
                'type' => 'varchar',
                'label' => 'Wallet ID',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'user_defined' => true,
                'is_required' => false,
                'is_used_in_grid' => true,
                'unique' => false,
                'system' => false

            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'mangopay_id'
        );

        $attribute->addData(
            [
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            ]
        );

        $attribute->save();

        $attribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'wallet_id'
        );

        $attribute->addData(
            [
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            ]
        );

        $attribute->save();

        return $this;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * example of implementation:
     *
     * [
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch1::class,
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch2::class
     * ]
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }
}
