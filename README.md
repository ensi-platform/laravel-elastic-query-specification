# Laravel Elastic Query Specification

Extension for [greensight/laravel-elastic-query](https://github.com/greensight/laravel-elastic-query/) to describe queries in a declarative way.

## Installation

1. Install [greensight/laravel-elastic-query](https://github.com/greensight/laravel-elastic-query/) https://github.com/greensight/laravel-elastic-query#installation
2. `composer require greensight/laravel-elastic-query-specification`

## Usage // TODO translate to english

Все виды декларативных запросов строятся на основе спецификации. В ней содержатся определения доступных фильтров, сортировок и
агрегатов.

```php
use Greensight\LaravelElasticQuery\Declarative\Agregating\AllowedAggregate;
use Greensight\LaravelElasticQuery\Declarative\Filtering\AllowedFilter;
use Greensight\LaravelElasticQuery\Declarative\Sorting\AllowedSort;
use Greensight\LaravelElasticQuery\Declarative\Specification\CompositeSpecification;
use Greensight\LaravelElasticQuery\Declarative\Specification\Specification;

class ProductSpecification extends CompositeSpecification
{
    public function __construct()
    {
        parent::__construct();
        
        $this->allowedFilters([
            'package',
            'active',
            AllowedFilter::exact('cashback', 'cashback.active')->default(true)
        ]);
        
        $this->allowedSorts(['name', 'rating']);
        
        $this->allowedAggregates([
            'package',
            AllowedAggregate::minmax('rating')
        ]);
        
        $this->whereNotNull('package');
        
        $this->nested('offers', function (Specification $spec) {
            $spec->allowedFilters(['seller_id', 'active']);
            
            $spec->allowedAggregates([
                'seller_id',
                AllowedAggregate::minmax('price')
            ]);
            
            $spec->allowedSorts([
                AllowedSort::field('price')->byMin()
            ]);
        });
    }
}
```

Примеры запросов для данной спецификации.
```json
{
 "sort": ["+price", "-rating"],
 "filter": {
    "active": true,
    "seller_id": 10
 }
}
```
```json
{
 "aggregate": ["price", "rating"],
 "filter": {
    "package": "bottle",
    "seller_id": 10
 }
}
```
Метод `nested` добавляет спецификации для вложенных документов. Имена фильтров, агрегатов и сортировок из них
экспортируются в глобальную область видимости без добавления каких-либо префиксов. Если для фильтров допустимо иметь
одинаковые имена, то для прочих компонентов нет.

```php
$this->nested('nested_field', function (Specification $spec) { ... })
$this->nested('nested_field', new SomeSpecificationImpl());
```

В спецификациях для вложенных документов могут использоваться только поля этих документов.

Допустимо добавлять несколько спецификаций для одного и того же поля типа `nested`.

Ограничения `where*` позволяют устанавливать дополнительные программные условия отбора, которые не могут быть изменены
клиентом. Ограничения, заданные в корневой спецификации, применяются всегда. Ограничения во вложенных спецификациях идут
только как дополнения к добавляемым в запрос фильтрам, агрегатам или сортировкам. Например, если во вложенной
спецификации нет ни одного активного фильтра, то в раздел фильтров запроса к Elasticsearch ограничения из этой
спецификации не попадут.

Метод `allowedFilters` определяет доступные для клиента фильтры. Каждый фильтр обязательно содержит уникальное в пределах
спецификации имя. В то же время, в корневой и вложенной спецификациях или в разных вложенных спецификациях, имена могут
повторяться. Все фильтры с одинаковыми именами будут заполнены одним значением из параметров запроса.

Кроме имени самого фильтра можно отдельно задать имя поля в индексе, для которого он применяется, и значение по умолчанию.

```php
$this->allowedFilters([AllowedFilter::exact('name', 'field')->default(500)]);

// the following statements are equivalent
$this->allowedFilters(['name']);
$this->allowedFilters([AllowedFilter::exact('name', 'name')]);
```

Виды фильтров

```php
AllowedFilter::exact('name', 'field');  // Значение поля проверяется на равенство одному из заданных
AllowedFilter::exists('name', 'field'); // Проверяется, что поле присутствует в документе и имеет ненулевое значение
```

Доступные клиенту сортировки добавляются методом `allowedSorts`. Направление сортировки задается в ее имени.
Знак `+` или отсутствие знака соответствует порядку по возрастанию, `-` - порядку по убыванию.
По умолчанию используется сортировка по возрастанию с выбором минимального, в случае нескольких значений в поле.

```php
$this->allowedSorts([AllowedSort::field('name', 'field')]);

// the following statements are equivalent
$this->allowedSorts(['name']);
$this->allowedSorts([AllowedSort::field('+name', 'name')]);
$this->allowedSorts([AllowedSort::field('+name', 'name')->byMin()]);

// set the sorting mode
$this->allowedSorts([AllowedSort::field('name', 'field')->byMin()]);
$this->allowedSorts([AllowedSort::field('name', 'field')->byMax()]);
$this->allowedSorts([AllowedSort::field('name', 'field')->byAvg()]);
$this->allowedSorts([AllowedSort::field('name', 'field')->bySum()]);
$this->allowedSorts([AllowedSort::field('name', 'field')->byMedian()]);
```

Для сортировки из вложенной спецификации учитываются все ограничения и активные фильтры из этой же спецификации.

Агрегаты объявляются методом `allowedAggregates`. Клиент в параметрах запроса указывает список имен агрегатов, результаты
которых он ожидает в ответе.

```php
$this->allowedAggregates([AllowedAggregate::terms('name', 'field')]);

// the following statements are equivalent
$this->allowedAggregates(['name']);
$this->allowedAggregates([AllowedAggregate::terms('name', 'name')]);
```

Виды агрегатов

```php
AllowedAggregate::terms('name', 'field');   // Get all variants of attribute values
AllowedAggregate::minmax('name', 'field');  // Get min and max attribute values
```

Агрегаты из вложенных спецификаций добавляются в запрос к Elasticsearch со всеми ограничениями и активными фильтрами.

## Поиск документов

```php
use Greensight\LaravelElasticQuery\Declarative\SearchQueryBuilder;
use Greensight\LaravelElasticQuery\Declarative\QueryBuilderRequest;

class ProductsSearchQuery extends SearchQueryBuilder
{
    public function __construct(QueryBuilderRequest $request)
    {
        parent::__construct(ProductsIndex::query(), new ProductSpecification(), $request);
    }
}
```

```php
class ProductsController
{
    // ...
    public function index(ProductsSearchQuery $query)
    {
        return ProductResource::collection($query->get());
    }
}
```

## Расчет сводных показателей

```php
use Greensight\LaravelElasticQuery\Declarative\AggregateQueryBuilder;
use Greensight\LaravelElasticQuery\Declarative\QueryBuilderRequest;

class ProductsAggregateQuery extends AggregateQueryBuilder
{
    public function __construct(QueryBuilderRequest $request)
    {
        parent::__construct(ProductsIndex::aggregate(), new ProductSpecification(), $request);
    }
}
```

```php
class ProductsController
{
    // ...
    public function totals(ProductsAggregateQuery $query)
    {
        return new ProductAggregateResource($query->get());
    }
}
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Testing

1. composer install
2. npm i
3. Start Elasticsearch in your preferred way.
4. Copy `phpunit.xml.dist` to `phpunit.xml` and set correct env variables there
6. composer test

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
