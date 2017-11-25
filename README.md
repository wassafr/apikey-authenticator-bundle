# Wassa API Key Authenticor Bundle
The Symfony website has a great tutorial on how to [create an API token authentication system](https://symfony.com/doc/current/security/guard_authentication.html). It's crystal clear and very easy to reproduce.
From our point of view though, it has 2 main issues:

1) If you have a lot of projects (which is our case at Wassa), it can be quite time consuming to reproduce it for every single project
2) The tutorial is more targeted toward what we call "multi-users" environment, in which a single API is linked to a single user. 

This bundle aims to address these 2 issues.

## Requirements

- Symfony 2.8

## Installation
First run:

```
composer require wassa/apikey-authenticator-bundle
```

Then update `AppKernel.php`:

```
...
new Wassa\ApiKeyAuthenticatorBundle\WassaApiKeyAuthenticatorBundle(),
...
```

The API key must be stored in `var/private/api.key`. You can create the file yourself or use the built-in command to create a key:

```
mkdir -p var/private
php bin/console apikey-authenticator:create-key [apiKey] [-s size]
```

If you don't provide an API key, the command will use the default generator (see "Creating your own generator") to create a random key.
The default size for the random key is 32 chars but can be configured (see "Configuration") or manually set it with the `-s` switch.

Remember to set correct permissions on the key file so that it is readable only by the webserver.

## Configuration
The bundle works without any special configuration. Add and edit the following block to `config.yml` if necessary:

```
wassa_api_key_authenticator:
    role: 'ROLE_API'                                            # Role that will be assigned to authenticated requests
    name: 'x-api-key'                                           # Name of the HTTP request header that must contain the API key
    location: 'all'                                             # Where to look for the API key in the request
    generator: 'wassa_api_key_authenticator.random_generator'   # Generator to use to generate the API key
    key_size: 32                                                # Size of the generated API key
```

The `name` parameter specify the name of the "field" in the request containing the API key.

The `location` parameter specifies which "field" to look for the API key in the request:

* `headers`: look for an HTTP header
* `query`: look for a query parameter
* `body`: look for a POST data parameter
* `path`: look in the path
* `all`: look for all above and returns the first found (in order above)

`location` can be a combination of different values, for example `'headers&query'` to look in headers and query but not in the body.

Then edit `security.yml`:

```
    ...
    providers:
        api_key_provider:
            id: wassa_api_key_authenticator.user_provider
    ...
    firewalls:
        ...
        main:
            ...
            guard:
                authenticators:
                    - wassa_api_key_authenticator.authenticator
    ...
    access_control:
        ...
        - { path: ^/api, roles: ROLE_API }
        ...
```

Be sure that `access_control` is configured with the same role as in `config.yml`.

## Creating your own API key generator
If you need your API key to be a bit more complex than a series of X chars, you can create your own generator.

In order to do that, just create a service that implements `ApiKeyGeneratorInterface` and write your own logic in the `generate()` method.

Then configure the bundle in `config.yml`:

```
wassa_api_key_authenticator:
    ...
    generator: 'app.my_generator'
```

## Composer post-install script
You can automate the creation of the API key by including the built-in post-install script in your `composer.json`:

```
"scripts": {
    "post-install-cmd": [
        ...
        "Wassa\\ApiKeyAuthenticatorBundle\\Composer\\ScriptHandler::generateApiKey"
    ]
},
"extra": {
    ...
    "apikey-size": 128
}
```

`apikey-size` is optional, if you don't specify it, the key will be created with the configured key size.

Also, if an API key already exists, it will not be overriden.

## Managing multiple API keys
For now our bundle handles only one API key, that's what it was made for in the first place so it's OK. If you want to handle multiple keys, then you can just follow these easy steps:

* Create your own User class that implements our `ApiKeyUserInterface` and provide an implementation for `getApiKey()`
* Create your own UserProvider class or use one that suits you (like FOSUserBundle).

[This part](https://symfony.com/doc/current/security/guard_authentication.html#create-a-user-and-a-user-provider) of the tutorial can help you to do that.