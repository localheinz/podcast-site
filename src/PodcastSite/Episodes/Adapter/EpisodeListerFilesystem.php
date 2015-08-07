<?php

namespace PodcastSite\Episodes\Adapter;

use PodcastSite\Episodes\EpisodeListerInterface;
use PodcastSite\Iterator\ActiveEpisodeFilterIterator;
use PodcastSite\Sorter\SortByReverseDateOrder;
use PodcastSite\Entity\Episode;

/**
 * Class EpisodeListerFilesystem
 * @package PodcastSite\Episodes\Adapter
 */
class EpisodeListerFilesystem implements EpisodeListerInterface
{
    /**
     * @var string
     */
    const CACHE_KEY_EPISODES_LIST = 'episodes_';

    /**
     * @var string
     */
    protected $postDirectory;

    /**
     * @var object
     */
    protected $fileParser;

    /**
     * @var ActiveEpisodeFilterIterator
     */
    protected $episodeIterator;

    /**
     * @var object
     */
    protected $cache;

    /**
     * @param string $postDirectory
     * @param object $fileParser Yaml/Markdown parser
     */
    public function __construct($postDirectory, $fileParser, $cache = null)
    {
        $this->postDirectory = $postDirectory;
        $this->fileParser = $fileParser;
        if (!is_null($cache)) {
            $this->cache = $cache;
        }
        $this->episodeIterator = new ActiveEpisodeFilterIterator(
            new \DirectoryIterator($this->postDirectory)
        );
    }

    /**
     * Return the current available podcast episodes
     */
    public function getEpisodeList()
    {
        if ($this->cache) {
            $result = $this->cache->getItem(self::CACHE_KEY_EPISODES_LIST);
            if ($result) {
                return $result;
            } else {
                $result = $this->buildEpisodesList();
                $ret = $this->cache->setItem(self::CACHE_KEY_EPISODES_LIST, $result);
                return $result;
            }
        } else {
            return $this->buildEpisodesList();
        }
    }

    protected function buildEpisodesList()
    {
        $episodeListing = [];
        foreach ($this->episodeIterator as $file) {
            $episodeListing[] = $this->buildEpisode($file);
        }

        // Sort the records in reverse date order
        $sorter = new SortByReverseDateOrder();
        usort($episodeListing, $sorter);

        return $episodeListing;
    }

    /**
     * Get details of one episode
     *
     * @param \PodcastSite\Entity\Episode|null $episode
     */
    public function getEpisode($episodeSlug)
    {
        foreach ($this->episodeIterator as $file) {
            $fileContent = file_get_contents($file->getPathname());
            /** @var \Mni\FrontYAML\Document $document */
            $document = $this->fileParser->parse($fileContent);
            if ($document->getYAML()['slug'] === $episodeSlug) {
                return new Episode([
                    'publishDate' => $document->getYAML()['publish_date'],
                    'slug' => $document->getYAML()['slug'],
                    'title' => $document->getYAML()['title'],
                    'content' => $document->getContent(),
                    'link' => $document->getYAML()['link'],
                    'download' => $document->getYAML()['download'],
                    'guests' => (array_key_exists('guests', $document->getYAML())) ? $document->getYAML()['guests'] : ''
                ]);
            }
        }

        return null;
    }

    /**
     * Create an episode value object from the contents of an acceptable markdown file
     *
     * @param \SplFileInfo $file
     * @return \PodcastSite\Entity\Episode
     */
    public function buildEpisode(\SplFileInfo $file)
    {
        $fileContent = file_get_contents($file->getPathname());

        /** @var \Mni\FrontYAML\Document $document */
        $document = $this->fileParser->parse($fileContent, false);

        return new Episode([
            'publishDate' => (array_key_exists('publish_date', $document->getYAML())) ? $document->getYAML()['publish_date'] : '',
            'slug' => (array_key_exists('slug', $document->getYAML())) ? $document->getYAML()['slug'] : '',
            'title' => (array_key_exists('title', $document->getYAML())) ? $document->getYAML()['title'] : '',
            'content' => $document->getContent(),
            'link' => (array_key_exists('link', $document->getYAML())) ? $document->getYAML()['link'] : '',
            'download' => (array_key_exists('download', $document->getYAML())) ? $document->getYAML()['download'] : '',
            'guests' => (array_key_exists('guests', $document->getYAML())) ? $document->getYAML()['guests'] : ''
        ]);
    }

}