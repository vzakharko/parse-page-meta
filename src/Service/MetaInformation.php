<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 14.5.18
 * Time: 13.31
 */

namespace Service;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MetaInformation
 */
class MetaInformation
{
    private $crawler;

    private $raw_url;

    private $data = [];

    private $rawData = [];

    private $header = [];

    private $url;

    /**
     * @param $url
     *
     * @return array|bool
     */
    function getUrlContent($url)
    {
        $result = $this->setUrl($url);

        if ($result === false) {
            return false;
        }

        if ($this->isImage()) {
            return [
                'site'        => parse_url($url, PHP_URL_HOST),
                'title'       => '',
                'image'       => $this->url,
                'description' => '',
                'url'         => $this->url,
            ];
        }

        if ($this->isYoutube()) {
            ;
        }
        $this->prepareRawData();

        return [
            'site'        => parse_url($url, PHP_URL_HOST),
            'image'       => $this->get('image'),
            'title'       => $this->get('title'),
            'description' => $this->get('description'),
            'url'         => $url,
        ];
    }

    function setUrl($url)
    {
        $this->raw_url = $url;
        $this->url = $url;
        $this->data = [];

        $content = $this->getContent($url);

        if ($content === false) {
            return false;
        }

        $this->crawler = new Crawler();
        $this->crawler->addHTMLContent($content, $this->getHeaderCharset());

        $metas = $this->crawler->filter('meta')->each(
            function (Crawler $node) {
                $property = trim($node->attr('property'));
                $name = trim($node->attr('name'));
                $content = trim($node->attr('content'));
                $key = $property != '' ? $property : $name;

                return ['key' => $key, 'content' => $content];
            }
        );

        $links = $this->crawler->filter('links')->each(
            function (Crawler $node) {
                $rel = $node->attr('rel');
                $content = $node->attr('href');

                return ['key' => $rel, 'content' => $content];
            }
        );

        $title = $this->crawler->filter('title')->first();
        if (count($title) > 0) {
            $metas [] = ['key' => 'title', 'content' => trim($title->text())];
        }

        $metas += $links;

        foreach ($metas as $meta) {
            $this->data[$meta['key']] = $meta['content'];
        }
    }

    function getYoutube()
    {
    }

    /**
     * @return bool
     */
    private function isYoutube()
    {
        if (strpos($this->url, 'youtube') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isImage()
    {
        $imgExts = ["gif", "jpg", "jpeg", "png", "webp", "apng", "tiff", "tif"];

        $urlExt = pathinfo($this->url, PATHINFO_EXTENSION);
        if (in_array($urlExt, $imgExts)) {
            return true;
        }

        return false;
    }

    function detectType()
    {
    }

    function get($key)
    {
        $result = '';

        foreach (['og:', 'twitter:', 'article:', ''] as $prefix) {
            if (isset($this->data[$prefix.$key])) {
                $result = $this->data[$prefix.$key];

                break;
            }
        }

        return $result;
    }

    function prepareRawData()
    {
        foreach ($this->data as $key => $data) {
            print_r($data);
            echo "\n";
        }
    }

    function getContent($url)
    {
        $content_params = [
            "ssl"  => [
                "verify_peer"       => false,
                "verify_peer_name"  => false,
                "allow_self_signed" => true,
            ],
            'http' => [
                'follow_location' => true,
                'header'          => "accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8
accept-encoding:gzip, deflate
accept-language:en-US,en;q=0.8
cache-control:no-cache
pragma:no-cache
referer:https://www.google.com/
user-agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36
",
            ],
        ];

        $context = stream_context_create($content_params);
        stream_context_set_default($content_params);

        $http_response_header_test = get_headers($url);

        foreach ($http_response_header_test as $item) {
            $value = explode(':', $item);
            if (isset($value[1])) {
                $this->header [trim($value[0])] = trim($value[1]);
            }
        }
        if (
            isset($this->header['Content-Type']) &&
            strpos($this->header['Content-Type'], 'audio/') !== false
        ) {
            return false;
        }

        $content = @file_get_contents($url, false, $context);
        //'compress.zlib://'.
        $len = strlen($content);
        if (!($len < 18 || strcmp(substr($content, 0, 2), "\x1f\x8b"))) {
            $content = gzdecode($content);
        }

        // foreach ($http_response_header as $item) {
        //      $value = explode(':', $item);
        //      if (isset($value[1]))
        //          $this->header [trim($value[0])] = trim($value[1]);
        //  }

        //dump($this->header);
        // dump($content);
        // data-testid="follow_link" facae book redirect link

        // header ?
        // refresh:1;URL=https://ofigenno.com/kupalniki-princess

        return $content;
    }

    function getHeaderCharset()
    {
        if (!isset($this->header['Content-Type'])) {
            return false;
        }

        $ct = $this->header['Content-Type'];

        $res = explode('=', $ct);
        if (isset($res[1])) {
            return $res[1];
        }

        return false;
    }
}
