<?php

namespace PodcastSite\Entity;

/**
 * A simple value object for holding details about an Episode
 *
 * Class Episode
 * @package PodcastSite\Entity
 */
class Episode
{
    /**
     * @var string
     */
    protected $publishDate;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $download;

    /**
     * @param array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!empty($options)) {
            $memberVariables = get_class_vars(__CLASS__);
            foreach ($options as $key => $value) {
               if (array_key_exists($key, $memberVariables) && !empty($value)) {
                   $this->$key = $value;
               }
            }
        }
    }

    /**
     * Returns a \DateTime object, which can be used to determine the publish date
     *
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
       return $this->slug;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
       return $this->title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
       return $this->content;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getDownload()
    {
        return $this->download;
    }
}