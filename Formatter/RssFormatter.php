<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Formatter;

use Eko\FeedBundle\Feed\Feed;
use Eko\FeedBundle\Item\Field;
use Eko\FeedBundle\Item\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * RSS formatter
 *
 * This class provides an RSS formatter
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class RssFormatter extends Formatter implements FormatterInterface
{
    private $request;

    /**
     * Construct a formatter with given feed
     *
     * @param Feed $feed A feed instance
     */
    public function __construct(Feed $feed, Request $request)
    {
        $this->request = $request;

        $this->fields = array(
            new Field(
                'title',
                'getFeedItemTitle',
                array('cdata' => true)
            ),
            new Field(
                'description',
                'getFeedItemDescription',
                array('cdata' => true)
            ),
            new Field(
                'link',
                'getFeedItemLink'
            ),
            new Field(
                'pubDate',
                'getFeedItemPubDate',
                array('date_format' => \DateTime::RSS)
            ),
        );

        parent::__construct($feed);

        $this->initialize();
    }

    /**
     * Initialize XML DOMDocument nodes and call addItem on all items
     */
    public function initialize()
    {
        $encoding = $this->feed->get('encoding');

        $this->dom = new \DOMDocument('1.0', $encoding);

        $root = $this->dom->createElement('rss');
        $root->setAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
        $root->setAttribute('xmlns:wfw', 'http://wellformedweb.org/CommentAPI/');
        $root->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $root->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
        $root->setAttribute('xmlns:sy', 'http://purl.org/rss/1.0/modules/syndication/');
        $root->setAttribute('xmlns:slash', 'http://purl.org/rss/1.0/modules/slash/');
        $root->setAttribute('version', '2.0');
        $root = $this->dom->appendChild($root);

        $channel = $this->dom->createElement('channel');
        $channel = $root->appendChild($channel);

        $fields = array('title', 'description', 'link');

        foreach ($fields as $field) {
            $element = $this->dom->createElement($field, $this->feed->get($field));
            $channel->appendChild($element);
        }

        $element = $this->dom->createElement('atom:link');
        $element->setAttribute('href', $this->request->getUri());
        $element->setAttribute('rel', 'self');
        $element->setAttribute('type', 'application/rss+xml');
        $channel->appendChild($element);

        $channel->appendChild($this->dom->createElement('language', 'en-US'));
        $channel->appendChild($this->dom->createElement('sy:updatePeriod', 'hourly'));
        $channel->appendChild($this->dom->createElement('sy:updateFrequency', '1'));

        $date = new \DateTime();
        $lastBuildDate = $this->dom->createElement('lastBuildDate', $date->format(\DateTime::RSS));

        $channel->appendChild($lastBuildDate);

        $items = $this->feed->getItems();

        foreach ($items as $item) {
            $this->addItem($channel, $item);
        }
    }

    /**
     * Add an entity item to the feed
     *
     * @param \DOMElement   $channel The channel DOM element
     * @param ItemInterface $item    An entity object
     */
    public function addItem(\DOMElement $channel, ItemInterface $item)
    {
        $node = $this->dom->createElement('item');
        $node = $channel->appendChild($node);

        foreach ($this->fields as $field) {
            $element = $this->format($field, $item);
            $node->appendChild($element);
        }
    }
}
