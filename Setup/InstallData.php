<?php
/**
 * @category   Divalto
 * @package    Divalto_ExtraPrice
 * @subpackage Setup
 */

namespace Divalto\ExtraPrice\Setup;


use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class InstallData implements InstallDataInterface

{
   private $eavSetupFactory;


   public function __construct(EavSetupFactory $eavSetupFactory)
   {
       $this->eavSetupFactory = $eavSetupFactory;
   }

 

   public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
   {

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(
          \Magento\Catalog\Model\Product::ENTITY,
           'extra_price_unit_amount');

       $eavSetup->addAttribute(
           \Magento\Catalog\Model\Product::ENTITY,
           'extra_price_unit_amount',
            [
                'group' => 'General',
                'attribute_set_id' => 'Default',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Extra Price Unit Amount',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'sort_order' => 101
            ]
       );

   }

}
