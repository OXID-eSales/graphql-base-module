CustomerAdministration
======================

.. important::
   To place an order you need the  `GraphQL Storefront module
   <https://github.com/OXID-eSales/graphql-storefront-module/>`_ installed and activated.

The Storefront module comes with all queries and mutations to create and maintain a customer account.
    * Customer Registration
    * Customer account data update (password/email change, setting /changing of invoice address , create/remove delivery address)
    * Forgotten password recovery
    * Subscribe/unsubscribe for newsletter
    * Contact
    * And of course queries to get information about your own account

To create a customer account, we start with

.. code-block:: graphql
   :caption: call to ``customerRegister`` mutation

    mutation customerRegister {
        customerRegister (
            customer: {
                email: "newuser@oxid.de",
                password: "password",
            }
        ) {
            id
            email
        }
    }

.. code-block:: json
   :caption: ``customerRegister`` mutation response

    {
        "data": {
            "customerRegister": {
                "id": "9a2a70af72bfe20b64413d6ad15a4256",
                "email": "newuser@oxid.de"
            }
        }
    }


Once you have this 'naked' account created, you need to identify with a JWT to
access and further update your account

.. code-block:: graphql
   :caption: call to ``token`` query

    query token {
        token(
            username: "newuser@oxid.de"
            password: "password"
        )
    }

.. code-block:: json
   :caption: ``token`` query response

    {
        "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QubG9jYWwvIn0.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0LmxvY2FsLyIsImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3QubG9jYWwvIiwiaWF0IjoxNjUzNDgwMjI4LjIyMjQwOCwibmJmIjoxNjUzNDgwMjI4LjIyMjQwOCwiZXhwIjoxNjUzNTA5MDI4LjIyNzkwNSwic2hvcGlkIjoxLCJ1c2VybmFtZSI6InVzZXJAb3hpZC1lc2FsZXMuY29tIiwidXNlcmlkIjoiZTdhZjFjM2I3ODZmZDAyOTA2Y2NkNzU2OThmNGU2YjkiLCJ1c2VyYW5vbnltb3VzIjpmYWxzZSwidG9rZW5pZCI6Ijc5ZTEwMjVjNjJhOWFiNTY2YzZmMzdlYzliMDlhYjJlIn0.JrfP112Th23o4sPB22w7Bq0eYISy_9A7zvYmjOMcvqcBpyt5QibeBSPUCtk2-PFLJ2bWZwGepDOG-gy8-cLunw"
        }
    }

Send this token in every request to the API in the Authorization header (Bearer <JWT>) where it is
necessary to be logged in and be identifiable as the current customer.

Here are some examples how to update the birthday date, set the invoice address and create a delivery address.

.. code-block:: graphql
   :caption: call to ``customerBirthdateUpdate`` mutation

    mutation customerBirthdateUpdate {
        customerBirthdateUpdate ( birthdate: "2001-04-01") {
            email
            birthdate
        }
    }

.. code-block:: json
   :caption: ``customerBirthdateUpdate`` mutation response

    {
        "data": {
            "customerBirthdateUpdate": {
            "email": "newuser@oxid.de",
            "birthdate": "2001-04-01T00:00:00+02:00"
            }
        }
    }


.. code-block:: graphql
   :caption: call to ``customerInvoiceAddressSet`` mutation

    mutation customerInvoiceAddressSet {
        customerInvoiceAddressSet (
            invoiceAddress: {
                salutation: "MRS"
                firstName: "Jane"
                lastName: "Doe"
                company: "Some GmbH"
                additionalInfo: "Invoice address"
                street: "Bertoldstrasse"
                streetNumber: "48"
                zipCode: "79098"
                city: "Freiburg"
                countryId: "a7c40f631fc920687.20179984"
                phone: "123456"
                mobile: "12345678"
                fax: "555"
            }
        ){
            salutation
            firstName
            lastName
            company
            additionalInfo
            street
            streetNumber
            zipCode
            city
            phone
            mobile
            fax
        }
    }

.. code-block:: json
   :caption: ``customerInvoiceAddressSet`` mutation response

    {
        "data": {
            "customerInvoiceAddressSet": {
                "salutation": "MRS",
                "firstName": "Jane",
                "lastName": "Doe",
                "company": "Some GmbH",
                "additionalInfo": "Invoice address",
                "street": "Bertoldstrasse",
                "streetNumber": "48",
                "zipCode": "79098",
                "city": "Freiburg",
                "phone": "123456",
                "mobile": "12345678",
                "fax": "555"
            }
        }
    }


.. code-block:: graphql
   :caption: call to ``customerDeliveryAddressAdd`` mutation

    mutation customerDeliveryAddressAdd {
        customerDeliveryAddressAdd (
            deliveryAddress: {
                salutation: "MRS"
                firstName: "Jane"
                lastName: "Dodo"
                company: "NoNo GmbH"
                additionalInfo: "Delivery address"
                street: "OtherStreet"
                streetNumber: "22"
                zipCode: "22547"
                city: "Bremen"
                countryId: "a7c40f631fc920687.20179984"
                phone: "123456"
                fax: "555"
            }
        ){
            id
            salutation
            firstName
            lastName
            company
            additionalInfo
            street
            streetNumber
            zipCode
            city
            phone
            fax
        }
    }

.. code-block:: json
   :caption: ``customerDeliveryAddressAdd`` mutation response

    {
        "data": {
            "customerDeliveryAddressAdd": {
                "id": "e71cd1bf4f292f70862a3cf1121a72a8",
                "salutation": "MRS",
                "firstName": "Jane",
                "lastName": "Dodo",
                "company": "NoNo GmbH",
                "additionalInfo": "Delivery address",
                "street": "OtherStreet",
                "streetNumber": "22",
                "zipCode": "22547",
                "city": "Bremen",
                "phone": "123456",
                "fax": "555"
            }
        }
    }

Now we can query the customer details. Don't foget to send the JWT, a customer can only query
its own information.

.. code-block:: graphql
   :caption: call to ``customerDetails`` query

    query customerDetails {
        customer {
            id
            email
            customerNumber
            newsletterStatus {
                status
            }
            invoiceAddress {
                salutation
                firstName
                lastName
                street
                company
                streetNumber
                zipCode
                city
                country {
                    isoAlpha2
                }
            }
            deliveryAddresses {
                id
                city
            }
            orders {
                id
                orderNumber
            }
        }
    }

.. code-block:: json
   :caption: ``customerDetails`` query response

    {
        "data": {
            "customer": {
                "id": "9cc65d910c7b385ad004d4eac21fa040",
                "email": "newuser@oxid.de",
                "customerNumber": "8",
                "newsletterStatus": {
                    "status": "UNSUBSCRIBED"
                },
                "invoiceAddress": {
                    "salutation": "MRS",
                    "firstName": "Jane",
                    "lastName": "Doe",
                    "street": "Bertoldstrasse",
                    "company": "Some GmbH",
                    "streetNumber": "48",
                    "zipCode": "79098",
                    "city": "Freiburg",
                    "country": {
                        "isoAlpha2": "DE"
                    }
                },
                "deliveryAddresses": [
                    {
                        "id": "e71cd1bf4f292f70862a3cf1121a72a8",
                        "city": "Bremen"
                    }
                ],
                "orders": []
            }
        }
    }
