# KleijnWeb\SwaggerBundle Example 
[![Build Status](https://travis-ci.org/kleijnweb/swagger-bundle-example.svg?branch=master)](https://travis-ci.org/kleijnweb/swagger-bundle-example)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/?branch=master)

"Kitchen Sink" example for [SwaggerBundle](https://github.com/kleijnweb/swagger-bundle) with [support for E-Tags](https://github.com/kleijnweb/rest-e-tag-bundle) and [JWT](https://github.com/kleijnweb/jwt-bundle).

## Run The Example

Download and install [docker-compose](https://docs.docker.com/compose/install/). 

Then run the following command to start all the containers and import data fixtures:

```
make dev
```

## Explore the API

Open the Swagger editor with `make doc`.  

In the **Security** section click on `Authenticate` and enter the following token:

```
Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImRlZmF1bHQifQ.eyJpc3MiOiJodHRwOi8vYXBpLnNlcnZlcjIuY29tL29hdXRoMi90b2tlbiIsInBybiI6ImFwaSJ9.TpL9LHFleMFwTHQARqW1WunJcHqd7MQKMA_YjhMwjUA
```
## About the project

To play around with JWT tokens, use [jwt.io](http://jwt.io/).

This example includes two examples of a fictional "Service Desk API".

1. v1: Basic example using JMS serializer and a basic REST API
2. v2: Simplified example of combining JMS serializer with tobscure/json-api for JSON-API responses (not a fully compliant server, pretty close though)

Example 2 adds a lot of boilerplate, negating some of the benefits of using SwaggerBundle. Future versions will have integrated (fully compliant) JSON-API support.

In general, anything not in a controller or a service has a good change to make it into a future release of SwaggerBundle.

## License

MIT
