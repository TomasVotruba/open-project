<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObject;

final class Video
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $videoId;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $thumbnail;

    /**
     * @var string
     */
    private $kind;

    /**
     * @var string
     */
    private $speaker;

    /**
     * @var string
     */
    private $slides;

    /**
     * @var string
     */
    private $month;

    /**
     * @var string
     */
    private $link;

    public function __construct(
        string $title,
        string $speaker,
        string $description,
        string $slug,
        string $thumbnail,
        string $kind,
        string $slides,
        string $month,
        // youtube specific
        string $videoId = '',
        // facebook
        string $link = ''
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->slug = $slug;
        $this->thumbnail = $thumbnail;
        $this->kind = $kind;

        $this->month = $month;
        $this->speaker = $speaker;
        $this->slides = $slides;

        // youtube specific
        $this->videoId = $videoId;

        // facebook specific
        $this->link = $link;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVideoId(): string
    {
        return $this->videoId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getMonth(): string
    {
        return $this->month;
    }

    public function getSpeaker(): string
    {
        return $this->speaker;
    }

    public function getSlides(): string
    {
        return $this->slides;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
