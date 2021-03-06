<?php

namespace PodcastSite\Entity\Traits;

/**
 * Trait GetExplicit
 * As both the Show and Episode entities use this function,
 * it's being shared via a trait.
 *
 * @package PodcastSite\Entity\Traits
 */
trait GetExplicit
{
    /** @var string */
    protected $explicit;

    /**
     * Is the item of an explicit nature or not.
     *
     * @return string
     */
    public function getExplicit()
    {
        return $this->explicit;
    }
}