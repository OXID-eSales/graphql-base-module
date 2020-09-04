Sorting data
==============

Sorting is currently intended to be used the same way as :doc:`filters <./filtering>`,
just in separate argument, near by the filters.

We provide basic abstract sorting DataType implementation that should be extended. It
can be configured with specific protected fields you would like to use for sorting
your data:

.. code-block:: php

    use OxidEsales\GraphQL\Base\DataType\Sorting as BaseSorting;

    final class CountrySorting extends BaseSorting
    {
        /**
         * @Factory()
         */
        public static function fromUserInput(
            ?string $title = null,
            ?string $iso = null
        ): self {
            return new self([
                'OXTITLE' => $title,
                'OXISOALPHA2' => $iso,
            ]);
        }
    }

Now in our controller we would like to ask for sorting parameter, as we did with
filters earlier:

.. code-block:: php

    public function exampleCountryQuery(
        ?CountryFilterList $filterList = null
        ?CountrySorting $sorting = null
    ): array {
        ...
        if ($sorting) {
            $sorting->addToQuery($queryBuilder);
        }
        ...
    }

Simple as that! Now we can add sorting to our query:

.. code-block:: graphql

    query {
        exampleCountryQuery (
            filterList: {
                title: {
                    beginsWith: "D"
                }
            },
            sorting: {
                iso: "DESC"
            }
        )
    }

And the result will be sorted by iso descending:

.. code-block:: json

    {
        "data": {
            "exampleCountryQuery": [
                [
                    "8f241f11095825bf2.61063355",
                    "Dominikanische Republik",
                    "DO"
                ], [
                    "8f241f11095811ea5.84717844",
                    "Dominica",
                    "DM"
                ], [
                    "8f241f110957e6ef8.56458418",
                    "Dänemark",
                    "DK"
                ], [
                    "8f241f110957fd356.02918645",
                    "Dschibuti",
                    "DJ"
                ], [
                    "a7c40f631fc920687.20179984",
                    "Deutschland",
                    "DE"
                ]
            ]
        }
    }

Some of the lists have default sorting depending on their position. In case you want to sort by other field you need to reset the default sorting. Let's say you want to sort the categories by title and not by position. You will have to set the position to an empty string otherwise its default value will be taken into account. Here is an example:

.. code-block:: graphql

    query {
        categories (
            sort: {
                position: "",
                title: "ASC"
            }
        ) {
            title
        }
    }
