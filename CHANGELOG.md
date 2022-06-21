# CHANGELOG

# 2.2.0

- Added allowing dot notation to gain access to arrays in Values objects by @proggeler in https://github.com/auraphp/Aura.Web/pull/59

# 2.1.2

- Fixed : Passing null to parameter type string is deprecated for strtolower by @zkwbbr in https://github.com/auraphp/Aura.Web/pull/60

- Continous Itegration moved from Travis to Github actions. Tests are running from PHP 5.4 - 8.1.

# 2.1.1

- Fixed parsing for urlencoded Request content body

- Added support for HTTP/2 as a response version

- Added support for "421 Misdirected Request" response code

## 2.1.0

Add support for setting, and sending, multiple Response header values.

## 2.0.4

Hygiene release: update license year, and remove branch alias.

## 2.0.3

This release modifies the testing structure and updates other support files.

## 2.0.2

This is a hygiene release to update support files. In particular, composer.json now adds `tests/` to "autoload-dev" so that Aura\Web\FakeResponse is available for bundle, kernel, and project tests.

## 2.0.1

- DOC: Update README and docblocks.

- ADD: Request\Content::getCharset() functionality; fixes #45

- CHG: Disable auto-resolve for container tests and make config more explicit

## 2.0.0

- Convert to PSR-4 autoloading.

- Add back PHP 5.3 compatibility.

- Honor host from 'HTTP_HOST' and 'SERVER_NAME'.

- Converted to class-based configuration for Aura projects, and use the updated
  service naming rules.

- Add a ResponseSender class for easy sending of Response objects: after sending
  headers and cookies, if the content is a callable, the callable is invoked and
  echoed, otherwise the content is just echoed.

- Deal with a bug in the PHP built-in server to determine content type and
  length.

- Use the proper content-type for the .csv extension.

- The Request Values object now has getBool(), getInt(), and getFloat(); these
  cast the request value appropriately.

- Extract Accept-related functionality to Aura.Accept package.

- README and docblock updates.

## 2.0.0-beta1

Initial 2.0 beta release.

