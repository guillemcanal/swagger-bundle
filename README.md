# KleijnWeb\SwaggerBundle Example 
[![Build Status](https://travis-ci.org/kleijnweb/swagger-bundle-example.svg?branch=master)](https://travis-ci.org/kleijnweb/swagger-bundle-example) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kleijnweb/swagger-bundle-example/?branch=master)

"Kitchen Sink" example for [SwaggerBundle](https://github.com/kleijnweb/swagger-bundle) with [support for E-Tags](https://github.com/kleijnweb/rest-e-tag-bundle) and [JWT](https://github.com/kleijnweb/jwt-bundle).

## Run The Example

Download and install docker-compose. After `docker compose up`, setup the database:

```bash
docker-compose run app bash -c 'app/console doctrine:database:create --no-interaction && app/console doctrine:migrations:migrate --no-interaction && app/console doctrine:fixtures:load --no-interaction'
```

Go to `http://localhost:8000/#/?import=/swagger/service-desk/v1.yml` to get a Swagger Editor where yoou can try out the API.

You'll need the following token (prefix with "Bearer "):

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImRlZmF1bHQifQ.eyJpc3MiOiJodHRwOi8vYXBpLnNlcnZlcjIuY29tL29hdXRoMi90b2tlbiIsInBybiI6ImFwaSJ9.TpL9LHFleMFwTHQARqW1WunJcHqd7MQKMA_YjhMwjUA
```

To play around with JWT tokens, use [jwt.io](http://jwt.io/).


## License

MIT
