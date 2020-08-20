final class Product
{
    // ...

    /**
     * @return true
     */
    public function store(ProductDataType $product): bool
    {
        return $this->productRepository->saveProduct(
            $product
        );
    }
}
