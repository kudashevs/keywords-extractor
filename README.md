Keywords Extractor
==========================

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
'add_words' => value            # A string or an array of words to add to the result (if they are ignored by an Extractor).
'remove_words' => value         # A string or an array of words to remove from the result (if they are not ignored by an Extractor).
```

**Note:** At the moment of instantiation, the `KeywordsExtractor` class may throw an `InvalidOptionType` exception. This
exception extends a built-in `InvalidArgumentException` class, so it is easy to deal with.


## Testing

```bash
composer test
```


## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

 **Note:** Please make sure to update tests as appropriate.


## License

The MIT License (MIT). Please see the [License file](LICENSE.md) for more information