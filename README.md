FeedBundle - A bundle to build RSS feeds from entities
=========================================================

CURRENTLY IN DEVELOPMENT, CAN'T BE USED.

Version 1.0

Features
--------

 * Generate RSS feed
 * Based on your entities
 * Easy to use

Configuration
-------------

### Edit app/config.yml

The following configuration lines are required:

```yaml
ekofeed:
    feeds:
        article:
            title:       'My articles/posts'
            description: 'Latests articles'
            link:        'http://vincent.composieux.fr'
            entity:      MyBundle\Entity\Article
```

### Implement the ItemInterface

Each entities you will use to generate an RSS feed needs to implement the `Eko\FeedBundle\Item\ItemInterface` interface as follow in this example for an `Article` entity of a blog:

```php
<?php
namespace Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eko\FeedBundle\Item\ItemInterface;

/**
 * Bundle\BlogBundle\Entity\Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity
 */
class Article implements ItemInterface
{
```

In this same entity, just implement those required methods:

`public function getFeedItemTitle();` : This method returns entity item title

`public function getFeedItemDescription();` : This method returns entity item description (or content)

`public function getFeedItemLink();` : This method returns entity item link (URL)

`public function getFeedItemPubdate();` : This method returns entity item publication date

### Generate the feed!

...

Author :
--------

 * Vincent Composieux <vincent.composieux@gmail.com> (Twitter: @vcomposieux)