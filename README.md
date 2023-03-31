# Redaxo GraphQL

This is an addon for the [Redaxo CMS](https://redaxo.org) that provides an
extensible [GraphQL](https://graphql.org) endpoint that allows you to create
your own API endpoints easily
using [GraphQLite](https://graphqlite.thecodingmachine.io/). By default, the
addon also ships
many useful types, queries and mutations that allow you to use REDAXO as a
headless CMS.

## Usage

Once the addon is installed, you can access the GraphQL endpoint
at `https://yourdomain.com/index.php?graphql-api=1&clang-id=<clang-id>`.
The `clang-id` parameter is optional and defaults to the default language of
your REDAXO installation. You can also include the following Line in
your `.htaccess` file to make the endpoint available
at `https://yourdomain.com/graphql/<clang-id>`:

```
RewriteRule ^graphql/([0-9]+)$ index.php?graphql-api=1&clang-id=$1 [L]
```

### Queries

The addon provides the following queries:

- `article(id: ID!): Article`: Returns the article with the given id.
- `rootArticles: [Article]`: Returns all root articles.
- `articleByPath(path: String!): Article`: Returns the article with the given
  path. The path is the path of the article in the REDAXO structure using
  yrewrite. For example, `path: /` returns the start article, `path: /about`
  returns the article with the path `about` and so on.
- `siteStartArticle: Article`: Returns the start article of the current site.
- `articleSlices(articleId: ID!): [ArticleSlice]`: Returns all slices of the
  article with the given id.
- `articleSlice(id: ID!): ArticleSlice`: Returns the slice with the given id.
- `rootCategories: [Category]`: Returns all root categories.
- `clangs(articleId: ID!): [Clang]`: Returns all languages of the article with
  the given id.
- `media(name: String!, mediaType: String): Media`: Returns the media with the
  given name. It uses the `mediaType` parameter from the `media_manager` addon
  to execute the correct media effect.
- `rootNavigation(depth: Int!, articleId: ID!): [NavigationItem]`: Returns the
  navigation tree of REDAXO with the given depth. The `articleId` is used to
  determine the active navigation item.

### Create your own Types

Before creating your own queries and mutations, you need to create your own
types. You can do this by creating a class that uses a namespace that starts
with `GraphQL\Type`. After that, you need to annotate with the `#[Type]`
annotation.
For example:

```php
<?php

namespace GraphQL\Type\MyType;

use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type]
class MyType
{
    // ...
}
```

After that, you need to add Fields to your type. You can do this by annotating
getter methods with the `#[Field]` annotation. For example:

```php
<?php

namespace GraphQL\Type;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type]
class MyType
{
    #[Field]
    public function getMyField(): string
    {
        return 'Hello World';
    }
}
```

The `myField` field can now be queried from the MyType type.

### Create your own Queries/Mutations

Queries and Mutations reside in `Controller` classes. For creating a controller,
you need to create a class that uses a namespace that starts with
`GraphQL\Controller`. After that, you need to annotate each method, that should
be a query or mutation, with the `#[Query]` or `#[Mutation]` annotation. For
example:

```php
<?php

namespace GraphQL\Controller;

use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;

class MyController
{
    #[Query]
    public function myQuery(): string
    {
        return 'Hello World';
    }

    #[Mutation]
    public function myMutation(): string
    {
        return 'Hello World';
    }
}
```

The `myQuery` query and the `myMutation` mutation can now be queried from the
GraphQL endpoint.

The return type of the query or mutation is automatically converted to a GraphQL
type. For example, if the return type is a `string`, the GraphQL type will be
`String`. If the return type is a class, the class must be a valid GraphQL
type (as described above).
If the return type is an array, it is necessary to specify the type of the
array. For example, if the return type is `string[]`, the GraphQL type will be
`[String]`. If the return type is `MyType[]`, the GraphQL type will be
`[MyType]`:

```php
<?php

namespace GraphQL\Controller;

use GraphQL\Type\MyType;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;

class MyController
{
    #[Query]
    public function myQuery(): string
    {
        return 'Hello World';
    }

    #[Mutation]
    public function myMutation(): string
    {
        return 'Hello World';
    }

    /**
     * @return string[]
     */
    #[Query]
    public function myQueryWithArray(): array
    {
        return ['Hello', 'World'];
    }

    /**
     * @return MyType[]
     */
    #[Query]
    public function myQueryWithMyTypeArray(): array
    {
        return [new MyType()];
    }
}
```

Queries and mutations can also have parameters. For example:

```php
<?php

namespace GraphQL\Controller;

use GraphQL\Type\MyType;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;

class MyController
{
    #[Query]
    public function myQuery(string $myParam): string
    {
        return 'Hello World';
    }

    #[Mutation]
    public function myMutation(string $myParam): string
    {
        return 'Hello World';
    }

    /**
     * @return string[]
     */
    #[Query]
    public function myQueryWithArray(string $myParam): array
    {
        return ['Hello', 'World'];
    }

    /**
     * @return MyType[]
     */
    #[Query]
    public function myQueryWithMyTypeArray(string $myParam): array
    {
        return [new MyType()];
    }
}
```

For more information about the possibilities of GraphQLite, please refer to
the [GraphQLite documentation](https://graphqlite.thecodingmachine.io/docs/).



