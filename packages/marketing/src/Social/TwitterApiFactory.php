<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Social;

use TwitterAPIExchange;

final class TwitterApiFactory
{
    private string $twitterConsumerKey;

    private string $twitterConsumerSecret;

    private string $twitterOauthAccessToken;

    private string $twitterOauthAccessTokenSecret;

    public function __construct(
        string $twitterConsumerKey,
        string $twitterConsumerSecret,
        string $twitterOauthAccessToken,
        string $twitterOauthAccessTokenSecret
    ) {
        $this->twitterConsumerKey = $twitterConsumerKey;
        $this->twitterConsumerSecret = $twitterConsumerSecret;
        $this->twitterOauthAccessToken = $twitterOauthAccessToken;
        $this->twitterOauthAccessTokenSecret = $twitterOauthAccessTokenSecret;
    }

    public function create(): TwitterAPIExchange
    {
        return new TwitterAPIExchange([
            'consumer_key' => $this->twitterConsumerKey,
            'consumer_secret' => $this->twitterConsumerSecret,
            'oauth_access_token' => $this->twitterOauthAccessToken,
            'oauth_access_token_secret' => $this->twitterOauthAccessTokenSecret,
        ]);
    }
}
