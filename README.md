# DbEscaper
[![Build Status](https://travis-ci.org/neoparla/db_escaper.svg?branch=master)](https://travis-ci.org/neoparla/db_escaper)

Database wrapper to escape properly, allowing meaningful queries with parametrized values
## Getting started
Install it through `composer` with
```
composer require neoparla/dbescaper
```

## First step: connect
To create an instance just initialize it with connection data.
```
$db_escaper = DbEscaper::init(
    array(
        'host' => 'host',
        'user'  => 'user',
        'pass'  => 'pass',
        'schema'    => 'schema',
        // 'port' => 3306
    )
);
```
By default it will connect through port 3306.

## Basic queries
To run a basic query, just `DbEscaper::query`.
```
$db_escaper->query('show tables');
```

## Statements
To avouid unwanted queries to be executed (aka SQLInjection) use `DbEscaper::prepare()`.
```
$statement = $db_escaper->prepare($sql, $query_label);
```

You can bind following types of data.
* **Double** *No transform*
* **Integer** *No transform*
* **String**
* **Field**
* **Tuple**

### Binding::String
It'll escape strings (such as quotes) and wrapp it with quotes
```php
$value = "string with quotes (') and slashes (\)";
DbStatement->bindParam(':binding', $value, Binding::String);
// Real query: 'string with quotes (\') and slashes (\\)'
```

### Binding::Field
It'll ensure valid MySQL field name and wrap it with backtips
```php
$value = "field_name";
DbStatement->bindParam(':binding', $value, Binding::Field);
// Real query: `field_name`
```

### Binding::Tuple
It'll ensure all values are valid and will transform them if needed.
```php
$value = new DbTuple(Binding::PARAM_STRING, array('string 1', 'string 2'), DbTuple::WITH_PARENTHESIS);;
DbStatement->bindParam(':binding', $value, Binding::Tuple);
// Real query: ( 'string 1', 'string 2' )
```

#### DbTuple class
To bind tuples you must use `DbTuple` class.

### Binding::Double and Binding::Integer
These kind of bindings won't perform any transformation. It'll just check correct data type.

