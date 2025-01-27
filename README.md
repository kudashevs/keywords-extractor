# Keywords Extractor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kudashevs/keywords-extractor.svg)](https://packagist.org/packages/kudashevs/keywords-extractor)
[![Run Tests](https://github.com/kudashevs/keywords-extractor/actions/workflows/run-tests.yml/badge.svg)](https://github.com/kudashevs/keywords-extractor/actions/workflows/run-tests.yml)
[![License MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

The `keywords-extractor` is a flexible and customizable PHP library that extracts relevant keywords from text.


## Installation

You can install the package via composer:
```bash
composer require kudashevs/keywords-extractor
```


## Usage

The key feature of the `keywords-extractor` library is the possibility to extract not just individual nouns, but the
meaningful sequences of words that make more sense in some contexts. This possibility is provided by the [RAKE PHP](https://github.com/kudashevs/rake-php)
library that is used as the default extraction algorithm. If this library doesn't suit your needs, it can be easily
substituted with something more appropriate or relevant.

The behavior of the `KeywordsExtractor` class is pretty straightforward. To extract keywords just call the `extract` method:
```php
use Kudashevs\KeywordsExtractor\KeywordsExtractor;

$extractor = new KeywordsExtractor([
    'add_words' => 'some',
    'remove_words' => 'interesting'
]);

$keywords = $extractor->extract('this is some interesting text');

print_r($keywords); // some, text
```

To make this library even more convenient to use in some specific cases, it provides a fluent interface. 
```php
use Kudashevs\KeywordsExtractor\KeywordsExtractor;

$extractor = new KeywordsExtractor();

$keywords = $extractor->addWords(['this', 'example'])
    ->extract('this is a usage example');
    
print_r($keywords); // usage example, this
```


## Options

The `KeywordsExtractor` class accepts some configuration options:
```
'extractor'                     # An Extractor instance that does all the extraction work.
'add_words' => value            # A string or an array of words to add to the result (if they are ignored by an Extractor).
'remove_words' => value         # A string or an array of words to remove from the result (if they are not ignored by an Extractor).
'limiter'                       # A Limiter instance that limits the length of the end result.
'limit_length'                  # An integer defines the maximum length of the result (is used only when a Limiter is not provided).
```

**Note:** At the moment of instantiation, the `KeywordsExtractor` class can throw a few exceptions: `InvalidOptionType`,
`InvalidOptionType`. These exceptions extend a built-in `InvalidArgumentException` class, so they are easy to deal with.


## Testing

```bash
composer test
```


## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

 **Note:** Please make sure to update tests as appropriate.


## License

The MIT License (MIT). Please see the [License file](LICENSE.md) for more information