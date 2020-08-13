final class controller
{
    /**
     * @Query()
     * @Logged()
     * @Right('SEE_BASKET')
     */
    public function basket(): Basket {
        // ...
    }
}
