<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\CatalogInventory\Test\Unit\Model\Plugin;

class AfterProductLoadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Plugin\AfterProductLoad
     */
    protected $plugin;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Catalog\Api\Data\ProductExtensionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productExtensionFactoryMock;

    /**
     * @var \Magento\Catalog\Api\Data\ProductExtensionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productExtensionMock;

    protected function setUp()
    {
        $stockRegistryMock = $this->createMock(\Magento\CatalogInventory\Api\StockRegistryInterface::class);
        $this->productExtensionFactoryMock = $this->getMockBuilder(
            \Magento\Catalog\Api\Data\ProductExtensionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new \Magento\CatalogInventory\Model\Plugin\AfterProductLoad(
            $stockRegistryMock,
            $this->productExtensionFactoryMock
        );

        $productId = 5494;
        $stockItemMock = $this->createMock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);

        $stockRegistryMock->expects($this->once())
            ->method('getStockItem')
            ->with($productId)
            ->willReturn($stockItemMock);

        $this->productExtensionMock = $this->getMockBuilder(\Magento\Catalog\Api\Data\ProductExtensionInterface::class)
            ->setMethods(['setStockItem'])
            ->getMockForAbstractClass();
        $this->productExtensionMock->expects($this->once())
            ->method('setStockItem')
            ->with($stockItemMock)
            ->willReturnSelf();

        $this->productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productMock->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($this->productExtensionMock)
            ->willReturnSelf();
        $this->productMock->expects(($this->once()))
            ->method('getId')
            ->will($this->returnValue($productId));
    }

    public function testAfterLoad()
    {
        $this->productMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($this->productExtensionMock);
        $this->productExtensionFactoryMock->expects($this->never())
            ->method('create');

        $this->assertEquals(
            $this->productMock,
            $this->plugin->afterLoad($this->productMock)
        );
    }
}
