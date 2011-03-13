Introduction
============


Aura Web Context
==================

Basic Usage
-----------

#### Instantiating a Web Context.

The easiest way to do this is to call the `aura.web/scripts/instance.php` script.

    $webcontext = require '/path/to/aura.web/scripts/instance.php';

**NOTE:** 
If the variables `$csrf_secret_key` and `$csrf_user_id` are not defined before calling `aura.web/scripts/instance.php` CSRF testing will not be avaliable. A call to `aura\web\Context->isCsrf()` will cause the exception `aura\web\Exception_Context`.

### Fetching data

**IMPORTANT:** 
All values returned by the get* methods and the public properties are from an untrusted source. These tainted values have not been filtered or sanitized in any way.

All get* methods have two arguments `$key` and `$alt`. If the `$key` is null the whole array is returned. `$alt` is returned if the `$key` does not exist and by default `$alt` is null.
    
#### get* methods:

    getQuery($key, $alt = null)
    getInput($key, $alt = null)
    getServer($key, $alt = null)
    getCookie($key, $alt = null)
    getEnv($key, $alt = null)
    getheader($key, $alt = null)
    getAccept($key, $alt = null)


#### Avaliable properties:

    get
    post
    cookies
    server
    env
    files
    header


#### Fetching a get value:

    // example query: ?id=101&sort=desc

    echo $webcontext->getQuery('id');
    
    // The page key does not exist, null is returned
    var_dump($webcontext->getQuery('page'));

    // The page key does not exist, alt is returned
    echo $webcontext->getQuery('page', 1);

    // fetch entire query
    print_r($webcontext->getQuery());

#### Results:

    101
    null
    1
    array('id' => 101, 'sort' => 'desc')

#### Fetching user submitted data:
User submitted data is a combination of `$post[key]` and `$files[key]` with files taking precedence over post.

    $comment = $webcontext->getInput('comment');

#### Fetching a header:
All header keys are lowercase with dashes.

    $type = $webcontext->getHeader('content-type');

#### Fetching an accept header.
If you want the content-type, ask for `type`; otherwise, if you want (e.g.) 'Accept-Language', ask for `language`.

    // Accept-Language: en;q=0.7, en-US
    $lang = $webcontext->getAccept('language');
    print_r($lang);

The result is an array; the array key is the accept-value and the array value is the quality factor. The results are sorted by the quality factor, the first result has the highest quality factor.

#### Example results:

    array(
        'en-US' => 1.0,
        'en'    => 0.7,
    )


Magic Quotes
------------
As of PHP 5.3 magic quotes have been deprecated and disabled by default. `aura\web\Context` does not test for or remove magic quotes.


Advanced Usage
--------------

#### Receiving a json, xml or other content type input

If the content-type is not `multipart/form-data` and `$key = null` the raw input from a POST or PUT request is returned without any processing as a string.

#### A POST or PUT example with a text/xml content-type:

    $xml = $webcontext->getInput();
    echo $xml;

#### Result:

    <xml>
        <value>Hello world!</value>
    </xml>


-----------------------------------------------

Aura Web Csrf
================
A library to generate and validate CSRF tokens.

`aura\web\Csrf` has two required __construct arguments:

  1. **$secret_key:** A random and project specific key. It should not change between requests.

  2. **$user_id:** Something unique to the user that does not change between requests. For example an email address or the primary key from the users table, anything that is unique to the user, **except** for passwords will do.

And two optional arguments:

  1. **$timeout:** The time in seconds before a token expires. The default is 1800 seconds.

  2. **$hash_algo:** Hashing algorithm see [hash_algos()](http://php.net/hash_algos) for a list of registered hashing algorithms. The default is sha1.

Usage
-----

#### Creating a standalone aura\web\Csrf instance:
    
    use aura\web\Csrf as Csrf;
    
    require '/path/to/aura.web/Csrf.php';
    require '/path/to/aura.web/Exception/MalformedToken.php';
    
    $server_secret = 'my-random-secret';
    $user_id       = $user->getEmail();

    $csrf = new Csrf($server_secret, $user_id);
    
#### Generating a token:

    $token = $csrf->generateToken();
    echo $token;

#### Example result:

    5199173921e7cc91dbee3145088af35e22df1d3|1299425613|2648677304d73a94de97218.48580521

#### Validating a token:

    try {
        if ($csrf->isValidToken($_POST['__csrf_token'])) {
            echo 'Not a CSRF attack.';
        } else {
            echo 'Invalid CSRF token.
            exit();
        }
    } catch (Exception_MalformedToken $e) {
        echo 'Malformed CSRF token.';
        exit();
    }
