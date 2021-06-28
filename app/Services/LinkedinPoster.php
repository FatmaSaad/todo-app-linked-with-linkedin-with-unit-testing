<?php


namespace App\Services;


use App\Models\User;
use LinkedIn\Client;
use LinkedIn\AccessToken;

class LinkedinPoster
{
    private $linkedIn;

    
    
    public function forUser(User $user)
    {
        $config = $user->token('linkedin');
        // load token from the file
        
        // instantiate access token object from stored data
        $accessToken = new AccessToken($config['token'], $config['secret']);

        // set token for client
        $this->linkedIn->setAccessToken($accessToken);
        return new self($config['token']);
    }

    
    public function __construct()
    {
        
        $config = config('services.linkedin');
         $this->linkedIn =  new Client(
            $config['client_id'],
             $config['client_secret'],
        );
        
    }

    public function post($message)
    {
        $profile = $this->linkedIn->get(
            'me',
            ['fields' => 'id,firstName,lastName']
        );
        $share = $this->linkedIn->post(                 
            'ugcPosts',                         
            [                                   
                'author' => 'urn:li:person:' . $profile['id'],
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [          
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => 'Checkout this amazing PHP SDK for LinkedIn!'
                        ],
                        'shareMediaCategory' => 'ARTICLE',
                        'media' => [
                            [
                                'status' => 'READY',
                                'description' => [
                                    'text' => 'OAuth 2 flow, composer Package.'
                                ],
                                'originalUrl' => 'https://github.com/zoonman/linkedin-api-php-client',
                                'title' => [
                                    'text' => 'PHP Client for LinkedIn API'
                                ]
                            ]
                        ]
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'CONNECTIONS'
                ]
            ]
        );
print_r($share);
    }
}
