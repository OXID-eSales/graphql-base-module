Browse the Storefront
=====================

.. important::
   To place an order you need the  `GraphQL Storefront module
   <https://github.com/OXID-eSales/graphql-storefront-module/>`_ installed and activated.

Introduction
------------

The OXID GraphQL Storefront Module covers the whole shop frontend functionality,
from browsing the product catalogue to creating/maintaining a customer account and
doing a checkout.

Still all GraphQL interacts with the OXID eShop core, so it won't be necessary
to fully convert from the standard shop as you know it to some headless shop
you need to rig up a frontend for from scratch.

Just pick the best of two worlds, for example browse the shop on a mobile device
via graphQL or have visitors sign up for a newsletter.

We'd like to quickly run you through the possibilities of our Storefront module.
We won't show every query and mutation there is in action but we'll
try to cover the relevant parts.

Browse the Shop
---------------

First thing we start with is the product catalogue/category tree.
You need to show off to potential customers right on the front page what
your shop has to offer. What we see here is the possibility to only fetch
selected data via graphQL. You only get what is asked for.

.. code-block:: graphql
   :caption: call to ``categories`` query

    query categoryTree {
        categories {
            title
            parent {
                title
            }
            children {
                title
            }
        }
    }

.. code-block:: json
   :caption: ``categories`` query response

   {
    "data": {
        "categories": [
            {
                "title": "Kiteboarding",
                "parent": null,
                "children": [
                    {
                        "title": "Kites"
                    },
                    {
                        "title": "Kiteboards"
                    },
                    {
                        "title": "Trapeze"
                    },
                    {
                        "title": "Zubehör"
                    }
                ]
            },
            {
                "title": "Wakeboarding",
                "parent": null,
                "children": [
                    {
                        "title": "Wakeboards"
                    },
                    {
                        "title": "Bindungen"
                    },
                    {
                        "title": "Sets"
                    }
                ]
            }
        ]
    }


We have the possibility built in to filter for specific categories e.g. by title:

.. code-block:: graphql
   :caption: call to ``categories`` query with filter and sorting and category products sorted by title

        query catsWithFilter {
            categories(
                filter: {
                    title: {
                        contains: "Kite"
                    }
                }
                sort: {
                    title: "ASC"
                }
            ) {
            title
            products (
                  sort: {
                      title: "DESC"
                  }
            ) {
               title
                }
            }
        }

.. code-block:: json
   :caption: ``categories`` query with filter response

        {
            "data": {
                "categories": [
                    {
                        "title": "Kiteboarding",
                        "products": []
                    },
                    {
                        "title": "Kites",
                        "products": [
                            {
                                "title": "Kite SPLEENE SP-X 2010"
                            },
                            {
                                "title": "Kite RRD PASSION 2010"
                            }
                    }
                ]
            }
        }


Or the all time favourite of every bargain hunter, get products sorted by price, lowest first:

.. code-block:: graphql
   :caption: call to ``products`` sorted by price

    query  productsByPrice {
        products (
              sort: {
                  price: "ASC"
              }
        ) {
           title
            id
             price {
                 price
                 currency {
                      name
                 }
            }
        }
    }


.. code-block:: json
   :caption: ``products`` query with sort by price response

    {
        "data": {
            "products": [
                {
                    "title": "Stickerset MIX",
                    "id": "fc71f70c3398ee4c2cdd101494087185",
                    "price": {
                        "price": 4.99,
                        "currency": {
                            "name": "EUR"
                        }
                     }
                },
                {
                    "title": "Klebeband DACRON KITEFIX",
                    "id": "0584e8b766a4de2177f9ed11d1587f55",
                    "price": {
                        "price": 7.99,
                        "currency": {
                            "name": "EUR"
                        }
                    }
                }
            ]
        }
    }

Of course it is possible to query one product for its details information:

.. code-block:: graphql
   :caption: call to ``product`` query

        query singleProduct {
            product (productId: "dc5ffdf380e15674b56dd562a7cb6aec")
            {
                title
                shortDescription
                seo {
                    url
                }
                price {
                    price
                    currency {
                        name
                    }
                }
            }
        }

.. code-block:: json
   :caption: ``product`` query response

    {
        "data": {
            "product": {
            "title": "Kuyichi Ledergürtel JEVER",
            "shortDescription": "Ledergürtel, unisex",
            "seo": {
                "url": "http://localhost.local/Bekleidung/Fashion/Accessoires/Kuyichi-Lederguertel-JEVER.html"
            },
            "price": {
                "price": 29.9,
                    "currency": {
                      "name": "EUR"
                    }
                }
            }
        }
    }
