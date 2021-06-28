<?php

namespace App\Notifications;

use App\Services\NotificationDriver\LinkedinPosterDriver;
use App\Services\LinkedinPoster;
use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use GuzzleHttp\Client;
use Exception;

class TodoPostToLinkedin extends Notification
{
    use Queueable;

    public $todo;
    private $redirect_uri;
    private $client_id;
    private $client_secret;
    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
        $config = config('services.linkedin');
        $this->redirect_uri = $config['redirect'];
        $this->client_id = $config['client_id'];
        $this->client_secret = $config['client_secret'];
    }

    public function via($notifiable)
    {
        return [LinkedinPosterDriver::class];
    }

    public function toLinkedin($notifiable)
    {

        $code=$notifiable->token('linkedin')->secret;
        $access_type = 'code';
        $client = new Client();
        $access_token = ($access_type === 'code') ? $this->getAccessToken($code) : $code;
        $personURN = $this->getProfile($access_token)['id'];
        return $client->request('POST', 'https://api.linkedin.com/v2/ugcPosts', [
            'headers' => [
                'Authorization'             => 'Bearer '.$access_token,
                'Connection'                => 'Keep-Alive',
                'Content-Type'              => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0',
            ],
            'json' => [
                'author'          => 'urn:li:person:'.$personURN,
                'lifecycleState'  => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => 'text !!',
                        ],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ],
        ]);
    
       
    }
    public function getAccessToken($code)
    {
        $client = new Client();
        $response = $client->request('POST', 'https://www.linkedin.com/oauth/v2/accessToken', [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $this->redirect_uri,
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
            ],
        ]);

        $object = json_decode($response->getBody()->getContents(), true);
        $access_token = $object['access_token'];

        return $access_token;
    }

    public function getProfile($access_token)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.linkedin.com/v2/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$access_token,
                'Connection'    => 'Keep-Alive',
            ],
        ]);
        $object = json_decode($response->getBody()->getContents(), true);

        return $object;
    }
}
