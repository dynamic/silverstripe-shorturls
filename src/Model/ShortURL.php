<?php

namespace Dynamic\ShortURL\Model;

use GuzzleHttp\Client;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Validation\ValidationResult;

class ShortURL extends DataObject
{
    /**
     * @var string
     */
    private static $singular_name = 'Short URL';

    /**
     * @var string
     */
    private static $plural_name = 'Short URLs';

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'URL' => 'Varchar(255)',
        'CampaignSource' => 'Varchar(255)',
        'CampaignMedium' => 'Varchar(255)',
        'CampaignName' => 'Varchar(255)',
        'CampaignTerm' => 'Varchar(255)',
        'CampaignContent' => 'Varchar(255)',
        'ShortURL' => 'Varchar(255)',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title',
        'URL',
        'ShortURL',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title',
        'URL',
        'CampaignSource',
        'CampaignMedium',
        'CampaignName',
        'CampaignTerm',
        'CampaignContent',
        'ShortURL',
    ];

    /**
     * @var string
     */
    private static $table_name = 'ShortURL';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('URL')
            ->setDescription('URL to shorten and tag');

        $fields->dataFieldByName('CampaignSource')
            ->setDescription(
                'Use to identify a search engine, newsletter name, or other source. Example: google'
            );

        $fields->dataFieldByName('CampaignMedium')
            ->setDescription(
                'Use utm_medium to identify a medium such as email or cost-per- click. Example: cpc'
            );

        $fields->dataFieldByName('CampaignName')
            ->setDescription(
                'Used for keyword analysis. Use utm_campaign to identify a specific product promotion or
                strategic campaign. Example: utm_campaign=spring_sale'
            );

        $fields->dataFieldByName('CampaignTerm')
            ->setDescription(
                'Used for paid search. Use utm_term to note the keywords for this ad. Example: running+shoes'
            );

        $fields->dataFieldByName('CampaignContent')
            ->setDescription(
                'Used for A/B testing and content-targeted ads. Use utm_content to differentiate ads or links
                that point to the same URL. Examples: logolink or textlink'
            );

        $fields->addFieldToTab('Root.Main', ReadonlyField::create('LongURL', 'Long URL', $this->getLongURL()));

        $short = $fields->dataFieldByName('ShortURL');
        $short = $short->performReadonlyTransformation();
        $fields->addFieldToTab('Root.Main', $short);

        return $fields;
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if (!$this->Title) {
            $result->addError('A Title is required before you can save');
        }

        if (!$this->URL) {
            $result->addError('A URL is required before you can save');
        }

        if (!$this->CampaignSource) {
            $result->addError('A Campaign Source is required before you can save');
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return Config::inst()->get(ShortURL::class, 'bitly_token');
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return Config::inst()->get(ShortURL::class, 'bitly_domain');
    }

    /**
     * @return string
     */
    public function getLongURL()
    {
        $vars = [
            'utm_source' => $this->CampaignSource,
            'utm_medium' => $this->CampaignMedium,
            'utm_campaign' => $this->CampaignName,
            'utm_term' => $this->CampaignTerm,
            'utm_content' => $this->CampaignContent,
        ];
        $LongURL = $this->URL . '?' . http_build_query($vars);
        return $LongURL;
    }

    /**
     * @return bool
     */
    public function getLinkChanged()
    {
        $isChanged = false;

        if ($this->isChanged('URL', self::CHANGE_VALUE) ||
            $this->isChanged('CampaignMedium', self::CHANGE_VALUE) ||
            $this->isChanged('CampaignName', self::CHANGE_VALUE) ||
            $this->isChanged('CampaignTerm', self::CHANGE_VALUE) ||
            $this->isChanged('CampaignContent', self::CHANGE_VALUE)
        ) {
            $isChanged = true;
        }

        $this->extend('updateIsLinkChanged', $isChanged);
        return $isChanged;
    }

    /**
     *
     */
    public function onBeforeWrite()
    {
        // do not call api if link is not changed
        if (!$this->getLinkChanged()) {
            return parent::onBeforeWrite();
        }

        if ($token = $this->getToken()) {
            $client = new Client([
                'base_uri' => 'https://api-ssl.bitly.com/v4/',
                'timeout' => $this->config()->get('timeout'),
                'http_errors' => false,
                'verify' => true,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getToken(),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = [
                'long_url' => $this->getLongURL(),
            ];

            if ($domain = $this->getDomain()) {
                $data['domain'] = $domain;
            }

            $response = $client->post('shorten', [
                'json' => $data,
            ]);

            $responseData = json_decode($response->getBody(), true);
            if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201) {
                throw new \Exception($responseData['message'] . ' : ' . $responseData['description']);
            }

            $this->ShortURL = $responseData['link'];
        }

        parent::onBeforeWrite();
    }
}
