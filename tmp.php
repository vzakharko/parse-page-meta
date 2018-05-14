<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 14.5.18
 * Time: 16.06
 */

function _oldGet()
{
    if ($this->isImage($this->url)) {
        // to base
        $card_content = [
            'image'        => $this->url,
            'title'        => '',
            'preview_text' => '',
            'url'          => $this->url,
        ];
    } elseif ($this->isYoutube($this->url)) {
        // to base

        $_img = $this->crawler->filter('head meta[property="og:image"]')->first();
        $_title = $this->crawler->filter('head meta[property="og:title"]')->first();
        $_description = $this->crawler->filter('head meta[property="og:description"]')->first();
        $_site = $this->crawler->filter('head meta[property="og:site_name"] ')->first();
        /*
            <meta property="og:site_name" content="YouTube">
            <meta property="og:url" content="https://www.youtube.com/watch?v=DyUU88FTmr8">
            <meta property="og:title" content="Борис Гребенщиков - Время На..........">
            <meta property="og:image" content="https://i.ytimg.com/vi/DyUU88FTmr8/hqdefault.jpg">
            <meta property="og:description" content="Demo 2017">
         *
         * */
        if (count($_title) == 0) {
            $_title = $this->crawler->filter('title')->first()->text();
        } else {
            $_title = count($_title) > 0 ? $_title->attr('content') : '';
        }
        if (count($_img) == 0) {
            $_img = $this->crawler->filter('title')->first()->text();
        } else {
            $_img = count($_img) > 0 ? $_img->attr('content') : '';
        }
        if (count($_site) == 0) {
            $_site = $this->crawler->filter('title')->first()->text();
        } else {
            $_site = count($_site) > 0 ? $_site->attr('content') : '';
        }
        if (count($_description) == 0) {
            $_description = $this->crawler->filter('title')->first()->text();
        } else {
            $_description = count($_description) > 0 ? $_description->attr('content') : '';
        }
    } else {
        /*
            <meta property="og:site_name" content="YouTube">
            <meta property="og:url" content="https://www.youtube.com/watch?v=DyUU88FTmr8">
            <meta property="og:title" content="Борис Гребенщиков - Время На..........">
            <meta property="og:image" content="https://i.ytimg.com/vi/DyUU88FTmr8/hqdefault.jpg">
            <meta property="og:description" content="Demo 2017">
         * */
        try {
            $_img = $this->crawler->filter('head link[rel="image_src"]')->first();
            $_title = $this->crawler->filter('meta[name="title"]')->first();

            $_description = $this->crawler->filter('meta[name="description"]')->first();

            $_img = $this->crawler->filter('head meta[property="og:image"]')->first();
            $_title = $this->crawler->filter('head meta[property="og:title"]')->first();

            if (count($_description) == 0) {
                $_description = $this->crawler->filter('head meta[property="og:description"]')->first();
                if (count($_description) == 0) {
                    $_description = $this->crawler->filter('title')->first()->text();
                } else {
                    $_description = count($_description) > 0 ? $_description->attr('content') : '';
                }
            }
            $_site = $this->crawler->filter('head meta[property="og:site_name"] ')->first();

            if (count($_title) == 0) {
                $_title = $this->crawler->filter('title')->first()->text();
            } else {
                $_title = count($_title) > 0 ? $_title->attr('content') : '';
            }
            if (count($_img) == 0) {
                $_img = $this->crawler->filter('title')->first()->text();
            } else {
                $_img = count($_img) > 0 ? $_img->attr('content') : '';
            }
            if (count($_site) == 0) {
                $_site = $this->crawler->filter('title')->first()->text();
            } else {
                $_site = count($_site) > 0 ? $_site->attr('content') : '';
            }
        } catch (\Exception $e) {
            return '';
        }

        // to base
        $card_content = [
            'image'        => $_img,
            'title'        => $_title,
            'preview_text' => $_description,
            'url'          => $url,
            'site'         => $_site,
        ];
    }
}
