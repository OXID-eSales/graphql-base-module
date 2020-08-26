use RuntimeException;

final class ProductRepository
{
    // ..

    /**
     * @throws RuntimeException
     * @return true
     */
    public function saveProduct(ProductDataType $product): bool
    {
        if (!$product->getEshopModel()->save()) {
            throw new RuntimeException('Object save failed');
        }

        return true;
    }
}
