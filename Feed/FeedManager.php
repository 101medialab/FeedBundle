<?php
/*
 * This file is part of the EkoFeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Feed;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Manager
 *
 * This class manage feeds specified in configuration file
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedManager extends ContainerAware
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $feeds;

    /**
     * @param array $config  Configuration settings
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Check if feed exists in configuration under 'feeds' node
     *
     * @param string $feed  Feed name
     * @return bool
     */
    public function has($feed) {
        return isset($this->config['feeds'][$feed]);
    }

    /**
     * Return specified Feed instance if exists
     *
     * @param string $feed  Feed name
     * @return Feed
     *
     * @throws \InvalidArgumentException
     */
    public function get($feed)
    {
        if (!$this->has($feed)) {
            throw new \InvalidArgumentException(
                sprintf("Specified feed '%s' is not defined in your configuration.", $feed)
            );
        }

        if (!isset($this->feeds[$feed])) {
            $this->feeds[$feed] = new Feed($this->config['feeds'][$feed]);
        }

        return $this->feeds[$feed];
    }
}