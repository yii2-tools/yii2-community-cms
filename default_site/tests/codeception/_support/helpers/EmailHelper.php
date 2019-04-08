<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 17:49
 */

namespace tests\codeception\_support\helpers;

use Codeception\Module;
use Yii;
use yii\helpers\FileHelper;

class EmailHelper extends RegexHelper
{
    // This regex suited for links in .eml files stored under @runtime/mail (as default)
    //const REGEX_EML_LINK = '/<a(?:[=\n\s]*)href=(?:[^"\']*)"([^"\']*)">/s';
    const REGEX_EML_LINK = '/<a\s*href="(.*)"\s*>/';

    /**
     * Return first link from string
     * @param string $text
     * @return string
     */
    protected function grabLink($text)
    {
        $match = $this->grabFirstMatch(static::REGEX_EML_LINK, $text);
        return $this->normalizeLink($match[1]);
    }

    /**
     * Return all links from string
     * @param string $text
     * @return array
     */
    protected function grabLinks($text)
    {
        $matches = $this->grabMatches(static::REGEX_EML_LINK, $text);
        $links = [];
        foreach ($matches[1] as $match) {
            $links[] = $this->normalizeLink($match);
        }
        return $links;
    }

    /**
     * Remove mailformed char sequences from link string
     * @param string $link
     * @return string
     */
    protected function normalizeLink($link)
    {
        return $link;
    }

    /**
     * Return all links founded in all emails
     * @return array indexed array of links
     */
    public function grabLinksFromEmails()
    {
        $emails = $this->emails();
        $links = [];
        foreach ($emails as $email) {
            $links = array_merge($links, $this->grabLinks($email));
        }
        return $links;
    }

    /**
     * Return the matches of a regex against the raw email
     * @param string $regex
     */
    public function seeInLastEmail($regex)
    {
        $this->grabMatchesFromLastEmail($regex);
    }


    /**
     * Return the matches of a regex against the raw email
     * @param string $regex
     * @return array
     */
    public function grabMatchesFromLastEmail($regex)
    {
        return $this->grabMatches($regex, $this->lastEmail());
    }

    /**
     * Return link from last email
     * @return string
     */
    public function grabLinkFromLastEmail()
    {
        return $this->grabLink($this->lastEmail());
    }

    /**
     * Get the most recent email
     * @return string
     */
    protected function lastEmail()
    {
        $emails = $this->emails();
        return $emails[count($emails) - 1];
    }

    /**
     * Get an array of all the message objects
     * @return array
     **/
    protected function emails()
    {
        $files = FileHelper::findFiles(Yii::getAlias('@runtime/mail'), ['/\.eml/']);
        $this->assertNotEmpty($files, 'No messages received');
        $emails = [];
        foreach ($files as $file) {
            $emails[] = $this->parseEmail(file_get_contents($file));
        }
        // in receive order
        return $emails;
    }

    /**
     * @param string $email
     * @return string
     */
    protected function parseEmail($email)
    {
        $email = preg_replace('/=3D/', '=', $email);
        $email = preg_replace('/=\s+/', '', $email);
        return htmlspecialchars_decode(htmlspecialchars_decode($email));
    }
}