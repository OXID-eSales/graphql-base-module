use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Logged;

final class Product
{
    // ...

    /**
     * @Mutation()
     * @Logged()
     */
    public function productTitleUpdate(ProductDataType $product): ProductDataType
    {
        $this->productService->store($product);

        return $product;
    }
}
