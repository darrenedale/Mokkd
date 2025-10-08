# Mokkd

> Mokkd is pre-release software.

> Mokkd is licensed under the Apache License 2.0. See the LICENSE file.

Aims to do for free functions what Mockery does for classes. Test doubles for functions can be quickly built using a
fluent interface. For example, to mock the `time()` built-in function so that it returns 0, use:

```php
$time = Mokkd::func("time")
    ->returning(0);
```

---

Basic usage scenarios are covered in this readme. See the documentation for more.

### Return successive array elements

```php
$time = Mokkd::func("time")
    ->returningFrom([0, 1, 2])
```

### Return a mapped value

```php
$getEnv = Mokkd::func("getenv"
    ->returningMappedValueFrom(
        [
            "host" => "example.com",
            "secret" => "fake-secret"
        ],
        0,
    )
```
### Completely replace a function

```php
$time = Mokkd::func("time")
    ->returningUsing(static function(): int {
        static $count = 0;
        return 60 * $count++;
    })
```

## Setting expectations

### Control call count

```php
$time = Mokkd::func("time")
    ->times(3)
    ->returning(0);
```

### Convenience `once()` and `twice()`

```php
$time = Mokkd::func("time")
    ->once()
    ->returning(0);
```

```php
$time = Mokkd::func("time")
    ->twice()
    ->returning(0);
```

### Expect specified arguments

```php
$getEnv = Mokkd::func("getenv")
    ->expects("host")
    ->returning("example.com")
```

### Matching arguments

```php
$getEnv = Mokkd::func("substr")
    ->expects(Mokkd::isString(), 0, Mokkd::isIntGreaterThan(2))
    ->returning("leftmost")
```

### Allowing unmatched calls

```php
$getEnv = Mokkd::func("getenv")
    ->expects("host")
    ->returning("example.com")
    ->withoutBlocking()
```
