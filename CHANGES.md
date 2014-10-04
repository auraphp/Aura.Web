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
