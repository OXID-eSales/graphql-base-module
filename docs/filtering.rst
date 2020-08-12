Filtering data
==============

One of the most important actions in development is getting the right
set of data by given criteria. The "base module" gives some most common
filters you might need to use, out of the box:

* Bool filter
    - equals
        get rows where field is equal to the value given;
        example: ``{equals: true}``
* Date filter
    - equals
        get rows where field is equal to the value given;
        example: ``{equals: '2020-01-30 12:37:21'}``
    - between
        get rows where field value is between given values;
        example: ``{between: ['2020-01-30 12:37:21', '2020-02-30 10:20:30']}``
* Integer filter
    - equals
        get rows where field is equal to the value given;
        example: ``{equals: 5}``
    - lowerThen
        get rows where field is lower then the value given;
        example: ``{lowerThen: 5}``
    - greaterThen
        get rows where field is greater then the value given;
        example: ``{greaterThen: 5}``
    - between
        get rows where field value is between given values;
        example: ``{between: [1, 5]}``
* Float filter
    This filter takes the same configuration as Integer filter, but float values are used!
* String filter
    - equals
        get rows where field is equal to the string given;
        example: ``{equals: 'some text'}``
    - contains
        get rows where field contains string given;
        example: ``{contains: 'me tex'}``
    - beginsWith
        get rows where field begins with string given
        example: ``{beginsWith: 'some'}``

And several more specific ones:

* Pagination filter
    - offset
        set the row number to start listing from;
        example: ``{offset: 10, limit: 10}``
    - limit
        set the count of rows we would like to get;
        example: ``{offset: 0, limit: 100}``

* ID filter
    - Florian will write something about this :D lol.

* :doc:`Sorting <./sorting>`

Main idea
---------

Via the graphql query we provide arguments for filter constructor and later
on we apply created preconfigured filter on the current query builder object
instance by passing it to addToQuery method of the filter and modifying the
builder by this selected specific filter configuration.

Simple direct filter usage example
----------------------------------

In this example, we create a country query with possibility to filter
countries by title and we will use our String filter for this:

::

    /**
     * @Query()
     *
     * @return string[][]
     */
    public function exampleCountryQuery(?StringFilter $titleFilter = null): array
    {
        // Get the query builder and write main query.
        // This is usually done in service, not directly in the controller.
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('OXID,OXTITLE,OXISOALPHA2')->from('oxcountry');

        // Apply our filter on the query builder.
        $titleFilter->addToQuery($queryBuilder, 'oxtitle');

        // Execute a query and give results.
        // This part is usually more complex as well - We want some DataType as result, not just array of strings.
        $queryBuilder->getConnection()->setFetchMode(PDO::FETCH_ASSOC);
        return $queryBuilder->execute()->fetchAll();
    }

Later on, we can apply the "title" filter on the query via the GraphQL interface.
As there are several variants possible for String filter (equals, contains, beginsWith),
we could use any combination of those, but as example, simple "equals" usage is shown:

::

    query{
      exampleCountryQuery(titleFilter: {equals: "Deutschland"})
    }

Result of this query will be one country data with specific name:

::

    {
      "data": {
        "exampleCountryQuery": [
          [
            "a7c40f631fc920687.20179984",
            "Deutschland",
            "DE"
          ]
        ]
      }
    }


.. important::
    The example do not provide you best practices architecture for your application - this is just an example to understand the main idea.

Filtering by multiple fields
----------------------------

Often we need more then one field with more then one filter at the time. For
this reason, we could wrap several filters in some filter list DataType:

::

    final class CountryFilterList
    {
        /** @var ?StringFilter */
        private $title;

        /** @var ?StringFilter */
        private $iso;

        public function __construct(
            ?StringFilter $title = null,
            ?StringFilter $iso = null
        ) {
            $this->title  = $title;
            $this->iso  = $iso;
        }

        /**
         * @return array{
         *   oxtitle: ?StringFilter
         *   oxisoalpha2: ?StringFilter
         * }
         */
        public function getFilters(): array
        {
            return [
                'OXTITLE' => $this->title,
                'OXISOALPHA2' => $this->iso
            ];
        }

        /**
         * @Factory(name="CountryFilterList")
         */
        public static function createCountryFilterList(
            ?StringFilter $title = null,
            ?StringFilter $iso = null
        ): self {
            return new self(
                $title,
                $iso
            );
        }
    }

While having this filter list, we will require this DataType in place of our single filter
in controller query from simple filter example, and just apply multiple filters to our
query builder instead of previously used one:

::

    public function exampleCountryQuery(?CountryFilterList $filterList = null): array
    {
        ...

        /** @var FilterInterface[] $filters */
        $filters = $filterList->getFilters();

        foreach ($filters as $field => $fieldFilter) {
            $fieldFilter->addToQuery($queryBuilder, $field);
        }

        ...
    }

Now our filter list can be used in a query:

::

    query{
      exampleCountryQuery(filterList:{
        title: {beginsWith: "D"}
        iso: {beginsWith: "DM"}
      })
    }

Gives us a country that was filtered by our conditions:

::

    {
      "data": {
        "exampleCountryQuery": [
          [
            "8f241f11095811ea5.84717844",
            "Dominica",
            "DM"
          ]
        ]
      }
    }

How to add your own filters
---------------------------

You can easily add your own filters by implementing your new filter class
in your module DataType folder.

There are no hard requirements on implementing any interfaces or implementing those
new filters by any rules - everything is up to you! But, you could go together
with us, and try reusing our interfaces and structures for making some standards
for us and everyone in the future!

Feel free to make some pull requests with your great filters that others could
get benefit from!
