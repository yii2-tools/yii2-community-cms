<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 22:17
 */

namespace tests\codeception\_support\helpers;

use Codeception\Module;

class RegexHelper extends Module
{
    /**
     * Return first match of a regex in string
     * @param string $regex
     * @param string $text
     */
    public function grabFirstMatch($regex, $text)
    {
        preg_match($regex, $text, $matches);
        $this->assertNotEmpty($matches, "No matches found for $regex");
        return $matches;
    }

    /**
     * Return all matches of a regex in string
     * @param string $regex
     * @param string $text
     */
    public function grabMatches($regex, $text)
    {
        preg_match_all($regex, $text, $matches);
        $this->assertNotEmpty($matches, "No matches found for $regex");
        $this->assertNotEmpty(next($matches), "No matches found for $regex");
        return $matches;
    }
}